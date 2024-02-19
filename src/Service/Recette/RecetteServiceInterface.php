<?php

namespace App\Service\Recette;

use App\Entity\Recette;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\JsonResponse;

interface RecetteServiceInterface
{
    public function add($data): JsonResponse;

    public function update(int $id, $data): JsonResponse;

    public function remove(int $id): void;

    public function getOne(int $id): array;

    public function getAll(): array;
}