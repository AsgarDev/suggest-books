<?php

namespace App\Controller;

use App\Entity\Suggestion;
use App\Form\SuggestionType;
use App\Message\MailNotification;
use App\Service\SuggestionService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function __invoke(Request $request, SuggestionService $suggestionService): Response
    {
        $form = $suggestionService->createSuggestionForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $suggestion = $form->getData();
            $suggestionService->handleFormSubmission($form);

            $this->addFlash('success', 'Votre suggestion a été envoyée !');
            return $this->redirectToRoute('home');
        }

        return $this->render('home/index.html.twig', [
            'suggestionForm' => $form->createView(),
        ]);
    }
}
