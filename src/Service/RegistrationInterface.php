<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;

interface RegistrationInterface
{
    public function registerUser(User $user, string $plainPassword): void;
}
