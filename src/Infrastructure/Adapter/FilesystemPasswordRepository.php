<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter;

use App\Domain\Infrastructure\PasswordRepositoryInterface;
use App\Infrastructure\Encryptor\SodiumEncryptor;
use App\Infrastructure\EncryptorInterface;
use App\Infrastructure\FilesystemInterface;
use DomainException;

readonly class FilesystemPasswordRepository implements PasswordRepositoryInterface
{
    public function __construct(
        private FilesystemInterface $filesystem,
        private ?string $storagePath,
    ) {
    }

    public function create(string $login, string $password): void
    {
        if ($this->find($login)) {
            throw new \RuntimeException('Login already exists');
        }

        $all = $this->findAll();
        $all[$login] = $password;
        $this->persist($all);
    }

    public function remove(string $login): bool
    {
        if ($this->find($login)) {
            $all = $this->findAll();
            unset($all[$login]);
            $this->persist($all);

            return true;
        }

        return false;
    }

    public function findAll(): array
    {
        if (!$this->filesystem->exists($this->storagePath)) {
            return [];
        }

        $jsonContent = $this->filesystem->read($this->storagePath);

        return json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
    }

    protected function persist(array $fileData): void
    {
        $json = json_encode($fileData, JSON_THROW_ON_ERROR);

        $this->filesystem->write($this->storagePath, json_encode($json, JSON_THROW_ON_ERROR));
    }

    public function find(string $login): array
    {
        $password = $this->findAll()[$login] ?? false;

        if ($password) {
            return [
                'login' => $login,
                'password' => $password,
            ];
        }

        throw new \DomainException('Login not found');
    }
}
