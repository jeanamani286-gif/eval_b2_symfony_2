<?php

namespace App\Tests\Controller;

use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ValidateTest extends WebTestCase
{
    public function testVisitorIsRedirectedFromValidation(): void
    {
        $client = static::createClient();
        $client->request('GET', '/validate');

        $this->assertResponseRedirects('/login');
    }
    public function testUserCannotAccessValidation(): void
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user@test.fr']);
        $client->loginUser($user);

        $client->request('GET', '/validate');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testUserCannotRefuseEvent(): void
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user@test.fr']);
        $client->loginUser($user);

        $event = static::getContainer()->get(EventRepository::class)->findOneBy(['title' => 'Tournoi de basket']);
        $client->request('POST', '/validate/'.$event->getId().'/refuse');

        $this->assertResponseStatusCodeSame(403);
    }
    public function testAdminRefusesEvent(): void
    {
        $client = static::createClient();
        $admin = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'admin@test.fr']);
        $client->loginUser($admin);

        $event = static::getContainer()->get(EventRepository::class)->findOneBy(['title' => 'Tournoi de basket']);
        $eventId = $event->getId();

        $client->request('POST', '/validate/'.$eventId.'/refuse');
        $this->assertResponseRedirects('/validate');

        $crawler = $client->followRedirect();

        $this->assertSelectorTextContains('.flash', 'a été refusé');
        $this->assertCount(0, $crawler->filter('td a:contains("Tournoi de basket")'));

        $client->request('GET', '/evenement/'.$eventId);
        $this->assertResponseStatusCodeSame(404);
    }
}