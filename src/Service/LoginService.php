<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

class LoginService implements LoginInterface
{
    public function __construct(private Security $security)
    {
    }

    public function loginUser(User $user, string $firewallName = 'main'): void
    {
        $this->security->login($user);
    }
}