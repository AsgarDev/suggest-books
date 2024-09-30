<?php

namespace App\Message;

readonly class MailNotification
{
    public function __construct(private string $description, private int $id, private string $from)
    {
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFrom(): string
    {
        return $this->from;
    }
}
