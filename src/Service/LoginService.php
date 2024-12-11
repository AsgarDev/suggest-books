<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Security\UserAuthenticator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

readonly class LoginService implements LoginInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private UserAuthenticatorInterface $userAuthenticator,
        private UserAuthenticator $authenticator
    )
    {
    }

    public function loginUser(User $user, string $firewallName = 'main'): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            throw new \LogicException('No current request found.');
        }

        $this->userAuthenticator->authenticateUser(
            $user,
            $this->authenticator,
            $request
        );
    }
}