<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MenuTest extends WebTestCase
{
    public function testMenuForVisitor(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertSelectorTextNotContains('nav', 'Mes inscriptions');
        $this->assertSelectorTextNotContains('nav', 'Validation');
    }
    public function testMenuForUser(): void
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user@test.fr']);
        $client->loginUser($user);
        $client->request('GET', '/');

        $this->assertSelectorTextContains('nav', 'Mes inscriptions');
        $this->assertSelectorTextNotContains('nav', 'Validation');
    }
    public function testMenuForAdmin(): void
    {
        $client = static::createClient();
        $admin = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'admin@test.fr']);
        $client->loginUser($admin);
        $client->request('GET', '/');

        $this->assertSelectorTextContains('nav', 'Validation');
    }
}