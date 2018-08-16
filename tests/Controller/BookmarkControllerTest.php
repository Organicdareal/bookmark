<?php

namespace App\Tests\Controller;

use App\Tests\DBWebTest;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Test\FormBuilderInterface;

class BookmarkControllerTest extends DBWebTest
{

    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertContains('My Bookmarks', $crawler->filter('h1')->text());
    }

    public function testNewGetForm()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/new_bookmark');
        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertContains('Created form', $responseData["message"]);
        $this->assertContains('form', $responseData["form"]);
    }

//    public function testNewInsertCorrectLink()
//    {
//        $client = static::createClient();
//
//        $crawler = $client->xmlHttpRequest('POST', '/new_bookmark');
//        $response = $client->getResponse();
//        $responseData = json_decode($response->getContent(), true);
//
//        $form = $crawler->selectButton('OK')->form();
//        $form['url'] = 'https://vimeo.com/284322748';
//        $client->submit($form);
//
//    }

//    public function testNewInsertBadLink()
//    {
//        $client = static::createClient();
//        $crawler = $client->request('POST', '/new_bookmark');
//        $response = $client->getResponse();
//
//        $this->assertSame(200, $response->getStatusCode());
//        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
//        $this->assertJson($response->getContent());
//        $responseData = json_decode($response->getContent(), true);
//        $this->assertContains('Created form', $responseData["message"]);
//        $this->assertContains('form', $responseData["form"]);
//    }

//    public function testEdit()
//    {
//        $client = static::createClient();
//        $crawler = $client->request('GET', '/');
//
//        $this->assertSame(200, $client->getResponse()->getStatusCode());
//        $this->assertContains('Hello World', $crawler->filter('h1')->text());
//    }
//
//    public function testView()
//    {
//        $client = static::createClient();
//        $crawler = $client->request('GET', '/');
//
//        $this->assertSame(200, $client->getResponse()->getStatusCode());
//        $this->assertContains('Hello World', $crawler->filter('h1')->text());
//    }
//
//    public function testDelete()
//    {
//        $client = static::createClient();
//        $crawler = $client->request('GET', '/');
//
//        $this->assertSame(200, $client->getResponse()->getStatusCode());
//        $this->assertContains('Hello World', $crawler->filter('h1')->text());
//    }
}
