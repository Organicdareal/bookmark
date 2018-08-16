<?php

namespace App\Tests\Controller;

use App\Entity\Link;
use App\Entity\Video;
use App\Tests\DBWebTest;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Test\FormBuilderInterface;

class BookmarkControllerFunctionalTest extends DBWebTest
{
    protected $video;
    protected $repo;
    protected $objectManager;

    public function setUp()
    {
        parent::setUp();
        $this->video = new Video(new \DateTime("now"),
            "test",
            "test",
            "https://vimeo.com/284322748",
            1080, 720, 300);
        $this->repo = $this->getMockBuilder(ObjectRepository::class)->getMock();
        $this->repo->expects($this->any())
            ->method('find')
            ->willReturn($this->video);

        $this->objectManager = $this->getMockBuilder(ObjectManager::class)->getMock();
        $this->objectManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->repo);
    }

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

    public function testEdit()
    {
        $this->markTestIncomplete("em and respository mocks aren't used");

        $client = static::createClient();
        $client->getContainer()->set('my.link.repository', $this->objectManager);
        $crawler = $client->request('GET', '/edit_bookmark/34');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testView()
    {
        $this->markTestIncomplete("em and respository mocks aren't used");
        $client = static::createClient();
        $client->getContainer()->set('my.link.repository', $this->objectManager);
        $crawler = $client->request('GET', '/bookmark/34');
        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertContains('Created form', $responseData["message"]);
        $this->assertContains('form', $responseData["form"]);
    }

//    public function testDelete()
//    {
//        $client = static::createClient();
//        $crawler = $client->request('GET', '/');
//
//        $this->assertSame(200, $client->getResponse()->getStatusCode());
//        $this->assertContains('Hello World', $crawler->filter('h1')->text());
//    }
}
