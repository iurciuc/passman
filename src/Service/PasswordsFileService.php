<?php

declare(strict_types=1);

namespace App\Service;

class PasswordsFileService
{
    private const string PASSWORDS_FILENAME = 'passwords';

    public function __construct(
        private readonly FileEncryptorService $fileEncryptorService
    ) {}

    public function fileExists(): bool
    {
        return file_exists(self::PASSWORDS_FILENAME);
    }

    public function readPasswordsFile(string $password): array
    {
        if (!file_exists(self::PASSWORDS_FILENAME)) {
            return [];
        }

        $encryptedFileContent = file_get_contents(self::PASSWORDS_FILENAME);
        $decryptedFileContent = $this->fileEncryptorService->safeDecrypt($encryptedFileContent, $password);

        return json_decode($decryptedFileContent, true, 512, JSON_THROW_ON_ERROR);
    }

    public function saveDataToFile(array $fileData, string $password): void
    {
        $json = json_encode($fileData, JSON_THROW_ON_ERROR);

        $encryptedFileContent = $this->fileEncryptorService->safeEncrypt($json, $password);

        file_put_contents(self::PASSWORDS_FILENAME, json_encode($encryptedFileContent, JSON_THROW_ON_ERROR));
    }
}
