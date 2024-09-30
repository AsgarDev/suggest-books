<?php

declare(strict_types=1);

namespace App\Service;

interface CreatedAtInterface
{
    public function getCreatedAt(): \DateTimeInterface;
    public function setCreatedAt(\DateTimeInterface $createdAt): self;
}
