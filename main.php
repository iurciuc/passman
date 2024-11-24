<?php

declare(strict_types=1);

const MASTER_PASSWORD_HASH = '$argon2i$v=19$m=2048,t=4,p=3$akxIa0p0Vk9QR01YekhlZw$w67NbEQkciuCDmc65CiKCgSEI1XKyQOHdIBLIrH2itA';
const PASSWORDS_FILENAME = 'passwords.json';

loginMenu();
crudMenu();

function loginMenu(): void
{
    system('clear');

    echo 'Welcome to the password manager'.PHP_EOL;
    echo '[1] Enter master password'.PHP_EOL;
    echo '[0] Exit'.PHP_EOL;

    $answer = ask(['1', '0']);

    if ($answer === '1') {
        do {
            $GLOBALS['password'] = readline('Enter master password: ');
        } while (!isMasterPasswordOk($GLOBALS['password']) && print('Master password is incorrect'.PHP_EOL));
    }

    if ($answer === '0') {
        closeApplication();
    }

    system('clear');
}

function crudMenu(): void
{
    echo 'You are logged in'.PHP_EOL;
    echo '[1] Find password by login'.PHP_EOL;
    echo '[2] Add new  password and login'.PHP_EOL;
    echo '[3] Remove password by login'.PHP_EOL;
    echo '[4] Print all logins'.PHP_EOL;
    echo '[0] Exit'.PHP_EOL;

    $answer = ask(['1', '2', '3', '4', '0']);

    system('clear');

    $fileData = readPasswordsFile();

    if ($answer === '1') {
        findPasswordByLogin($fileData);
    }

    if ($answer === '2') {
        $fileData = addNewPasswordAndLogin($fileData);
    }

    if ($answer === '3') {
        $fileData = removePasswordByLogin($fileData);
    }

    if ($answer === '4') {
        printAllLogins($fileData);
    }

    if ($answer === '0') {
        closeApplication();
    }

    saveDataToFile($fileData);
}

function findPasswordByLogin(array $fileData): void
{
    $login = strtolower(readline('Enter login: '));

    if (array_key_exists($login, $fileData)) {
        echo 'Password: '.$fileData[$login].PHP_EOL;
    } else {
        echo 'Login not found'.PHP_EOL;
    }
}

function addNewPasswordAndLogin(array $fileData): array
{
    $login = readline('Enter login: ');
    $password = readline('Enter password: ');

    $fileData[$login] = $password;

    echo 'Password added'.PHP_EOL;

    return $fileData;
}

function removePasswordByLogin(array $fileData): array
{
    $login = readline('Enter login: ');

    if (array_key_exists($login, $fileData)) {
        unset($fileData[$login]);
        echo 'Password removed'.PHP_EOL;
    } else {
        echo 'Login not found'.PHP_EOL;
    }

    return $fileData;
}

function printAllLogins(array $fileData): void
{
    foreach ($fileData as $login => $password) {
        echo $login.PHP_EOL;
    }
}

function ask(array $options): string
{
    $option = readline('Choose your option: ');

    while (true) {
        if (in_array($option, $options, true)) {
            break;
        }

        echo 'Invalid option' . PHP_EOL;
        $option = readline('Choose your option: ');
    }

    return $option;
}

function closeApplication(): void
{
    echo 'Goodbye';
    exit();
}

function isMasterPasswordOk(string $password): bool
{
    return password_verify($password, MASTER_PASSWORD_HASH);
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
