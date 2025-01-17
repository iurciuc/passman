<?php

declare(strict_types=1);

namespace App\Domain\Infrastructure;

interface PasswordRepositoryInterface
{
    public function find(string $login): array;
    public function create(string $login, string $password): void;
    public function remove(string $login): bool;
    public function findAll(): array;
}
