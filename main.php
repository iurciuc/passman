<?php

declare(strict_types=1);

require 'Enum/CommandListInterface.php';
require 'Enum/PasswordCrudCommand.php';
require 'Enum/CommonCommands.php';

const PASSWORDS_FILENAME = 'passwords';

$fileHandle = null;

loginMenu();

while (true) {
    if (!crudMenu()){
        closeApplication();
    }
}

function loginMenu(): void
{
    system('clear');

    if (!file_exists(PASSWORDS_FILENAME)) {
        echo 'There is no passwords storage, creating a new one'.PHP_EOL;

        while(empty($GLOBALS['password'] = readline('Enter a new master password: '))) {
            echo 'Password should be empty!' . PHP_EOL;
        }
    } else {
        do {
            $GLOBALS['password'] = readline('Enter master password: ');
        } while (!isMasterPasswordOk() && print('Master password is incorrect'.PHP_EOL));
    }

    system('clear');
}

function crudMenu(): bool
{
    $answer = menu(
        'You are logged in',
        PasswordCrudCommand::cases()
    );

    system('clear');

    $callback = resolve_action($answer);
    $callback();

    readline('Press any key to continue...');

    system('clear');

    return true;
}

function resolve_action(CommandListInterface $action): callable
{
    return match ($action) {
        CommonCommands::EXIT => closeApplication(...),
        PasswordCrudCommand::SEARCH => searchPasswordByLogin(...),
        PasswordCrudCommand::NEW => addNewPasswordAndLogin(...),
        PasswordCrudCommand::REMOVE => removePasswordByLogin(...),
        PasswordCrudCommand::LIST => printAllLogins(...),
    };
}

function searchPasswordByLogin(): void
{
    $fileData = readPasswordsFile();

    $login = strtolower(readline('Enter login: '));

    if (array_key_exists($login, $fileData)) {
        echo 'Password: '.$fileData[$login].PHP_EOL;
    } else {
        echo 'Login not found'.PHP_EOL;
    }
}

function addNewPasswordAndLogin(): void
{
    $fileData = readPasswordsFile();

    $login = strtolower(readline('Enter login: '));
    $password = readline('Enter password: ');

    $fileData[$login] = $password;

    echo 'Password added'.PHP_EOL;

    saveDataToFile($fileData);
}

function removePasswordByLogin(): void
{
    $fileData = readPasswordsFile();

    $login = strtolower(readline('Enter login: '));

    if (array_key_exists($login, $fileData)) {
        unset($fileData[$login]);
        echo 'Password removed'.PHP_EOL;
    } else {
        echo 'Login not found'.PHP_EOL;
    }

    saveDataToFile($fileData);
}

function printAllLogins(): void
{
    $fileData = readPasswordsFile();

    foreach ($fileData as $login => $password) {
        echo $login.PHP_EOL;
    }
}

/**
 * @param string $title
 * @param CommandListInterface[] $cases
 *
 * @return CommandListInterface
 */
function menu(string $title, array $cases): CommandListInterface
{
    $cases[] = CommonCommands::EXIT;

    echo $title . PHP_EOL;

    foreach ($cases as $case) {
        echo '[' . $case->value . '] ' . $case->getLabel() . PHP_EOL;
    }

    while ($option = readline('Choose your option: ')) {
        foreach ($cases as $case) {
            if ($case->value === $option) {
                return $case;
            }
        }

        echo 'Invalid option' . PHP_EOL;
    }

    return CommonCommands::EXIT;
}

function closeApplication(): void
{
    echo 'Goodbye';
    exit();
}

function isMasterPasswordOk(): bool
{
    try {
        readPasswordsFile();
    } catch (Throwable) {
        return false;
    }

    return true;
}

function readPasswordsFile(): array
{
    if (!file_exists(PASSWORDS_FILENAME)) {
        return [];
    }

    $encryptedFileContent = file_get_contents(PASSWORDS_FILENAME);
    $decryptedFileContent = safeDecrypt($encryptedFileContent, computeKey());

    return json_decode($decryptedFileContent, true, 512, JSON_THROW_ON_ERROR);
}

function saveDataToFile(array $fileData): void
{
    $encryptedFileContent = safeEncrypt(json_encode($fileData, JSON_THROW_ON_ERROR), computeKey());

    file_put_contents(PASSWORDS_FILENAME, json_encode($encryptedFileContent, JSON_THROW_ON_ERROR));
}

function computeKey(): string
{
    return sodium_crypto_pwhash(
        SODIUM_CRYPTO_SECRETBOX_KEYBYTES,
        $GLOBALS['password'],
        '1234567890abcdef',
        SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
        SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
    );
}

function safeEncrypt(string $message, string $key): string
{
    if (mb_strlen($key, '8bit') !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
        throw new RangeException('Key is not the correct size (must be 32 bytes).');
    }

    $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

    $cipher = base64_encode(
        $nonce.
        sodium_crypto_secretbox(
            $message,
            $nonce,
            $key
        )
    );
    sodium_memzero($message);
    sodium_memzero($key);

    return $cipher;
}

function safeDecrypt(string $encrypted, string $key): string
{
    $decoded = base64_decode($encrypted);
    $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
    $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

    $plain = sodium_crypto_secretbox_open(
        $ciphertext,
        $nonce,
        $key
    );

    if (!is_string($plain)) {
        throw new Exception('Invalid MAC');
    }

    sodium_memzero($ciphertext);
    sodium_memzero($key);

    return $plain;
}
