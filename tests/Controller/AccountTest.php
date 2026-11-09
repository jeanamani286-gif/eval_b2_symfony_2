<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccountTest extends WebTestCase
{
    public function testRegisterThenLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');
        $client->submitForm('Créer mon compte', [
            'registration_form[email]' => 'nouveau@test.fr',
            'registration_form[plainPassword]' => 'secret123',
        ]);

        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'nouveau@test.fr']);
        $this->assertNotNull($user);
        $client->restart();
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'nouveau@test.fr',
            '_password' => 'secret123',
        ]);
        $client->followRedirect();

        $this->assertSelectorTextContains('nav', 'Se déconnecter');
    }

    
    public function testLoginWithWrongPassword(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            '_username' => 'user@test.fr',
            '_password' => 'mauvais-mot-de-passe',
        ]);
        $client->followRedirect();
        $this->assertSelectorTextContains('.flash-error', 'Identifiants invalides.');
    }
}