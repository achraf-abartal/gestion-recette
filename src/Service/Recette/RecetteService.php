<?php
namespace App\Service\Recette;
use App\Entity\Recette;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class RecetteService implements RecetteServiceInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RecetteRepository        $recetteRepository,
        private readonly Security               $security,
        private readonly NormalizerInterface    $normalizer,
        private readonly ValidatorInterface     $validator,

    )
    {
    }


    public function serializer(Recette $recette): ?string
    {
        return json_encode($this->normalizer->normalize($recette));
    }

    public function validate(Recette $recette): array
    {
        $errors = $this->validator->validate($recette);

        $errorsArray = [];
        $errorsResponse = [];

        if (count($errors) > 0) {
            for ($i = 0; $i < count($errors); $i++) {
                $errorsArray[] = $errors[$i]->getMessage();
            }

            $errorsResponse['errors'] = $errorsArray;
        }
        return $errorsResponse;
    }
    public function add($data): JsonResponse
    {
        $Recette = new Recette();
        $Recette->setDescription($data->description);


        $errors = $this->validate($Recette);
        if (!empty($errors))
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json']);

        $this->entityManager->persist($Recette);
        $this->entityManager->flush();

        return new JsonResponse('Ajouter avec succès', Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
        public function update(int $id, $data): JsonResponse
    {
        $recette = $this->recetteRepository->find($id);
        if(empty($recette))
            return new JsonResponse('NOT FOUND', Response::HTTP_NOT_FOUND, ['Content-Type' => 'application/json']);

        $recette->setDescription($data->description);

        $errors = $this->validate($recette);
        if (!empty($errors))
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json']);

        $this->entityManager->persist($recette);
        $this->entityManager->flush();

        return new JsonResponse('Modifié avec succès', Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
    public function remove(int $id): void
    {
        $recette = $this->recetteRepository->find($id);

        $this->entityManager->remove($recette);
        $this->entityManager->flush();
    }
    public function getOne(int $id): array
    {
        return $this->normalizer->normalize($this->recetteRepository->getOneById($id, $this->security->getUser()));
    }
    public function getAll(): array
    {
        return $this->normalizer->normalize($this->recetteRepository->getAll($this->security->getUser()));
    }


}