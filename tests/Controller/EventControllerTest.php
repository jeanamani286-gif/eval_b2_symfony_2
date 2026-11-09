<?php

namespace App\Tests\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
{
    public function testHomePageShowsUpcomingEvents(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertCount(3, $crawler->filter('.event-card'));
        $this->assertSelectorTextNotContains('body', 'Concert passé');
        $this->assertSelectorTextNotContains('body', 'Tournoi de basket');
    }
    public function testEventDetailPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $client->clickLink('Soirée jazz');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Soirée jazz');
    }
    public function testEventNotFound(): void
    {
        $client = static::createClient();
        $client->request('GET', '/evenement/9999');
        $this->assertResponseStatusCodeSame(404);
    }
    public function testCategoryFilter(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/?categorie=sport');
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('.event-card'));
        $this->assertCount(1, $crawler->filter('a.active'));
        $this->assertSame('Sport', $crawler->filter('a.active')->text());
    }

    public function testEventFormErrors(): void
    {
        $client = static::createClient();
        $repository = static::getContainer()->get(EventRepository::class);
        $countBefore = $repository->count([]);

        $client->request('GET', '/evenement/ajouter');
        $client->submitForm('Enregistrer l\'événement', [
            'event[title]' => '',
            'event[places]' => 0,
        ]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('body', 'Le titre est obligatoire.');
        $this->assertSelectorTextContains('body', 'Le nombre de places doit être supérieur à 0.');
        $this->assertSame($countBefore, $repository->count([]));
    }
}