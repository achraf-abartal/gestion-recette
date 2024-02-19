<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Repository\RecetteRepository;
use App\Service\Recette\RecetteServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/api/v1/')]

class RecetteController extends AbstractController
{
    public function __construct(
        private readonly RecetteServiceInterface $recetteService,
        private readonly EntityManagerInterface $entityManager,
        private readonly RecetteRepository        $recetteRepository,
    )
    {
    }
    #[Route('', name: 'list_app',methods: 'GET')]
    public function getAll(): Response
    {
        $recettes = $this->recetteRepository->findAll();

        return $this->render('recette/list.html.twig', [
            'recettes' => $recettes,
        ]);
    }
    #[Route('recette',name: "add_recette", methods: 'POST')]
    public function addRecette(Request $request):Response
    {
        $Recette = new Recette();

        $form = $this->createFormBuilder($Recette)
            ->add('descriptions', TextType::class)
            ->add('date', DateTimeImmutable::class)
            ->add('save', SubmitType::class, ['label' => 'Valider'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $Recette = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($Recette);
            $em->flush();

        }

        return $this->render('recette/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('recette/{id}', methods: 'PUT')]
    public function update(int $id, Recette $recette,Request $request): Response
    {
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('list_app', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recette/edit.html.twig', [
            'recette' => $recette,
            'form' => $form,
        ]);
    }

    #[Route('recette/{id}', methods: 'DELETE')]
    public function remove(int $id): Response
    {
        $recette = $this->recetteRepository->find($id);

        $this->entityManager->remove($recette);
        $this->entityManager->flush();
        return $this->redirectToRoute('list_app');
    }

    #[Route('recette/{id}',name: 'list_recette', methods: 'GET')]
    public function getOne(int $id): Response
    {
        $recettes = $this->recetteRepository->findBy($id);

        return $this->render('recette/list.html.twig', [
            'recettes' => $recettes,
        ]);
    }


}
