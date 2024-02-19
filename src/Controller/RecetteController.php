<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Service\Recette\RecetteServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/api/v1/')]

class RecetteController extends AbstractController
{
    public function __construct(
        private readonly RecetteServiceInterface $recetteService
    )
    {
    }

    #[Route('recette', methods: 'POST')]
    public function addRecette(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent());

        return $this->recetteService->add($data);
    }

    #[Route('recette/{id}', methods: 'PUT')]
    public function update(int $id, Request $request): Response
    {
        $data = json_decode($request->getContent());

        return $this->recetteService->update($id, $data);
    }

    #[Route('recette/{id}', methods: 'DELETE')]
    public function remove(int $id): Response
    {
        $this->recetteService->remove($id);

        return new JsonResponse('Supprimé avec succès', Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('recette/{id}', methods: 'GET')]
    public function getOne(int $id): Response
    {
        $recette = $this->recetteService->getOne($id);

        return new JsonResponse($recette, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('recettes', methods: 'GET')]
    public function getAll(): Response
    {
        $recettes = $this->recetteService->getAll();

        return new JsonResponse($recettes, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
