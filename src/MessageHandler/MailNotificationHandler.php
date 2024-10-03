<?php

namespace App\MessageHandler;

use App\Message\MailNotification;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler()]
readonly class MailNotificationHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
    )
    {
    }

    public function __invoke(MailNotification $message): void
    {
        $email = (new Email())
        ->from($message->getFrom())
        ->to('test@example.com')
        ->subject(sprintf('New suggestion #%s - %s', $message->getId(), $message->getFrom()))
        ->html('<p>' . $message->getDescription() . '</p>');

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
