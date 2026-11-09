<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MyEventsTest extends WebTestCase
{
    public function testVisitorIsRedirectedToLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/mes-inscriptions');

        $this->assertResponseRedirects('/login');
    }

    public function testUserSeesHisEvent(): void
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user@test.fr']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/mes-inscriptions');

        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.event-card'));
        $this->assertSelectorTextContains('body', 'Semi-marathon');
    }

    public function testUnsubscribeFromMyEvents(): void
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user@test.fr']);
        $client->loginUser($user);

        $client->request('GET', '/mes-inscriptions');
        $client->submitForm('Se désinscrire');
        $this->assertResponseRedirects('http://localhost/mes-inscriptions');

        $crawler = $client->followRedirect();

        $this->assertSelectorTextContains('.flash', 'Vous êtes désinscrit de cet événement.');
        $this->assertCount(0, $crawler->filter('.event-card'));
        $this->assertSelectorTextContains('.empty', 'Vous n\'êtes inscrit à aucun événement pour le moment.');
    }
}