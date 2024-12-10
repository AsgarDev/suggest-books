<?php

namespace App\Service;

use App\Entity\Suggestion;
use App\Entity\User;
use App\Form\SuggestionType;
use App\Message\MailNotification;
use Doctrine\ORM\EntityManagerInterface;
use Predis\ClientInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

class SuggestionService
{
    private const RECENT_SUGGESTIONS_KEY = 'recent_suggestions';
    private const MAX_RECENT_SUGGESTIONS = 5;

    public function __construct(
        private FormFactoryInterface $formFactory,
        private EntityManagerInterface $entityManager,
        private Security $security,
        private MessageBusInterface $bus,
        private ClientInterface $redis,
    ) {}

    public function createSuggestionForm(Suggestion $suggestion = null): FormInterface
    {
        return $this->formFactory->create(SuggestionType::class, $suggestion ?? new Suggestion());
    }

    public function handleFormSubmission(FormInterface $form): void
    {
        $suggestion = $form->getData();
        $user = $this->security->getUser();
        if (!$user instanceof User || !$suggestion instanceof Suggestion) {
            return;
        }
        $suggestion->setSuggester($user);

        $this->entityManager->persist($suggestion);
        $this->entityManager->flush();

        if (!$suggestion->getId() || !$suggestion->getSuggester() instanceof User || !$suggestion->getSuggester()->getEmail()) {
            return;
        }

        $this->updateRecentSuggestions($suggestion);

        $this->bus->dispatch(new MailNotification(
            $suggestion->getDescription(),
            $suggestion->getId(),
            $suggestion->getSuggester()->getEmail()
        ));
    }

    private function updateRecentSuggestions(Suggestion $suggestion): void
    {
        $recentSuggestions = $this->redis->lrange(self::RECENT_SUGGESTIONS_KEY, 0, -1);

        $recentSuggestions[] = json_encode([
            'id' => $suggestion->getId(),
            'description' => $suggestion->getDescription(),
            'submitted_at' => $suggestion->getCreatedAt()->format('d-m-Y H:i:s'),
        ]);

        $recentSuggestions = array_slice($recentSuggestions, -self::MAX_RECENT_SUGGESTIONS);

        $this->redis->del([self::RECENT_SUGGESTIONS_KEY]);
        foreach ($recentSuggestions as $suggestion) {
            $this->redis->rpush(self::RECENT_SUGGESTIONS_KEY, $suggestion);
        }
    }

    public function getRecentSuggestions(): array
    {
        return array_map(
            fn ($item) => json_decode($item, true),
            $this->redis->lrange(self::RECENT_SUGGESTIONS_KEY, 0, -1)
        );
    }
}
