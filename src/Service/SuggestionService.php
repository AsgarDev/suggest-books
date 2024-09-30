<?php

namespace App\Service;

use App\Entity\Suggestion;
use App\Entity\User;
use App\Form\SuggestionType;
use App\Message\MailNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

class SuggestionService
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EntityManagerInterface $entityManager,
        private Security $security,
        private MessageBusInterface $bus,
    ) {}

    public function createSuggestionForm(Suggestion $suggestion = null): FormInterface
    {
        if ($suggestion === null) {
            $suggestion = new Suggestion();
        }

        return $this->formFactory->create(SuggestionType::class, $suggestion);
    }

    public function handleFormSubmission(FormInterface $form): void
    {
        $suggestion = $form->getData();
        $user = $this->security->getUser();
        if ($user instanceof User) {
            $suggestion->setSuggester($user);
        }

        $this->entityManager->persist($suggestion);
        $this->entityManager->flush();

        $this->sendNotification($suggestion);
    }

    private function sendNotification(Suggestion $suggestion): void
    {
        if ($suggestion->getId() && $suggestion->getSuggester() && $suggestion->getSuggester()->getEmail()) {
            $this->bus->dispatch(new MailNotification(
                $suggestion->getDescription(),
                $suggestion->getId(),
                $suggestion->getSuggester()->getEmail()
            ));
        }
    }
}
