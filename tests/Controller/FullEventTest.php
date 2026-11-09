<?php

namespace App\Tests\Controller;

use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FullEventTest extends WebTestCase
{
    public function testFullEvent(): void
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user@test.fr']);
        $client->loginUser($user);

        $event = static::getContainer()->get(EventRepository::class)->findOneBy(['title' => 'Initiation à la poterie']);
        $client->request('GET', '/evenement/'.$event->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.badge-full');
        $this->assertSelectorTextNotContains('body', 'S\'inscrire');
    }
}