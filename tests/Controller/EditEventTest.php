<?php

namespace App\Tests\Controller;

use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EditEventTest extends WebTestCase
{
    public function testVisitorCannotEdit(): void
    {
        $client = static::createClient();
        $event = static::getContainer()->get(EventRepository::class)->findOneBy(['title' => 'Semi-marathon']);

        $client->request('GET', '/evenement/'.$event->getId());
        $this->assertSelectorNotExists('a:contains("Modifier")');

        $client->request('GET', '/evenement/'.$event->getId().'/modifier');
        $this->assertResponseRedirects('/login');
    }
    public function testUserCannotEditOthersEvent(): void
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user@test.fr']);
        $client->loginUser($user);

        $event = static::getContainer()->get(EventRepository::class)->findOneBy(['title' => 'Semi-marathon']);

        $client->request('GET', '/evenement/'.$event->getId());
        $this->assertSelectorNotExists('a:contains("Modifier")');

        $client->request('GET', '/evenement/'.$event->getId().'/modifier');
        $this->assertResponseStatusCodeSame(403);
    }
    public function testAdminCanEdit(): void
    {
        $client = static::createClient();
        $admin = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'admin@test.fr']);
        $client->loginUser($admin);

        $event = static::getContainer()->get(EventRepository::class)->findOneBy(['title' => 'Semi-marathon']);

        $client->request('GET', '/evenement/'.$event->getId());
        $this->assertSelectorExists('a:contains("Modifier")');

        $client->request('GET', '/evenement/'.$event->getId().'/modifier');
        $this->assertResponseIsSuccessful();
    }
    public function testAuthorCanEditTitle(): void
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user@test.fr']);
        $client->loginUser($user);

        $event = static::getContainer()->get(EventRepository::class)->findOneBy(['title' => 'Soirée jazz']);
        $eventId = $event->getId();

        $client->request('GET', '/evenement/'.$eventId.'/modifier');
        $client->submitForm('Enregistrer l\'événement', [
            'event[title]' => 'Soirée jazz modifiée',
        ]);

        $this->assertResponseRedirects('/evenement/'.$eventId);

        $client->followRedirect();

        $this->assertSelectorTextContains('.flash', 'L\'événement a été modifié.');
        $this->assertSelectorTextContains('h1', 'Soirée jazz modifiée');
    }
}