<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NewEventTest extends WebTestCase
{
    public function testAddEventWithImage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/evenement/ajouter');
        $client->submitForm('Enregistrer l\'événement', [
            'event[title]' => 'Course de test',
            'event[date]' => '2030-01-01T20:00',
            'event[information]' => 'Une course pour le test.',
            'event[category]' => 'sport',
            'event[places]' => 20,
            'event[image]' => dirname(__DIR__, 2).'/public/images/sport.jpg',
        ]);

        $this->assertResponseRedirects('/');
        $crawler = $client->followRedirect();

        $this->assertSelectorTextContains('.flash', 'Votre événement a bien été ajouté.');
        $this->assertCount(3, $crawler->filter('.event-card'));
        $this->assertSelectorTextNotContains('body', 'Course de test');
    
        }
}