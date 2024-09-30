<?php

namespace App\Tests\Entity;

use App\Entity\Suggestion;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class SuggestionTest extends TestCase
{
    public function testSuggestionCanBeInstantiated(): void
    {
        $suggestion = new Suggestion();
        $this->assertInstanceOf(Suggestion::class, $suggestion);
    }

    public function testSuggester(): void
    {
        $suggestion = new Suggestion();
        $user = new User();

        $suggestion->setSuggester($user);
        $this->assertSame($user, $suggestion->getSuggester());
    }

    public function testDescription(): void
    {
        $suggestion = new Suggestion();
        $description = 'Voici une description de test pour une suggestion de livre.';

        $suggestion->setDescription($description);
        $this->assertSame($description, $suggestion->getDescription());
    }

    public function testCreatedAt(): void
    {
        $suggestion = new Suggestion();
        $dateTime = new \DateTime('2024-09-30 12:00:00');

        $suggestion->setCreatedAt($dateTime);
        $this->assertSame($dateTime, $suggestion->getCreatedAt());
    }

    public function testInvalidDescriptionThrowsError(): void
    {
        $this->expectException(\TypeError::class);

        $suggestion = new Suggestion();

        $suggestion->setDescription(['12345']);
    }
}
