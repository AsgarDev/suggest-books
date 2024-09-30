<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\CreatedAtInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CreatedAtListener
{
    public function __construct(
        private EntityManagerInterface $em,
    )
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof CreatedAtInterface) {
            $entity->setCreatedAt(new \DateTime());
        }
    }
}
