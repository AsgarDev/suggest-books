<?php

namespace App\MessageHandler;

use App\Message\MailNotification;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler()]
readonly class MailNotificationHandler
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function __invoke(MailNotification $message): void
    {
        dump($message);
        $email = (new Email())
        ->from($message->getFrom())
        ->to('test@example.com')
        ->subject(sprintf('New suggestion #%s - %s', $message->getId(), $message->getFrom()))
        ->html('<p>' . $message->getDescription() . '</p>');

        //sleep(10); // to test rabbitmq
        $this->mailer->send($email);
    }
}
