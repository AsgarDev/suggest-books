<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;

interface LoginInterface
{
    public function loginUser(User $user, string $firewallName = 'main'): void;
}
