<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    public function testGetuserslist()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/users');
    }

    public function testListaccounts()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/accounts');
    }

    public function testListaccountcards()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/cards/{account}');
    }

    public function testCreateaccount()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/account/create');
    }

    public function testEditaccount()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/account/edit');
    }

}
