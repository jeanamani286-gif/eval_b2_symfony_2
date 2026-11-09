<?php

namespace App\Tests\Controller;

use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationEventTest extends WebTestCase
{
    public function testUserRegistersToEvent(): void
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user@test.fr']);
        $client->loginUser($user);

        $event = static::getContainer()->get(EventRepository::class)->findOneBy(['title' => 'Soirée jazz']);
        $eventId = $event->getId();

        $client->request('GET', '/evenement/'.$eventId);
        $client->submitForm('S\'inscrire');

        $this->assertResponseRedirects('/evenement/'.$eventId);
        $client->followRedirect();

        $this->assertSelectorTextContains('.places', '79');

        $crawler = $client->request('GET', '/mes-inscriptions');
        $this->assertCount(2, $crawler->filter('.event-card'));
    }
    public function testUserUnregistersFromEventDetail(): void
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user@test.fr']);
        $client->loginUser($user);

        $event = static::getContainer()->get(EventRepository::class)->findOneBy(['title' => 'Semi-marathon']);
        $eventId = $event->getId();

        $client->request('GET', '/evenement/'.$eventId);
        $client->submitForm('Se désinscrire');

        $this->assertResponseRedirects('http://localhost/evenement/'.$eventId);
        $client->followRedirect();

        $this->assertSelectorTextContains('.places', '300');
    }
}