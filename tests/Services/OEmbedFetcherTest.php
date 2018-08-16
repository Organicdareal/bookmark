<?php
use App\Services\OEmbedFetcher;
use App\Tests\DBWebTest;
use Embed\Exceptions\InvalidUrlException;

/**
 * Created by PhpStorm.
 * User: ggicquel
 * Date: 16/08/2018
 * Time: 14:14
 */
class OEmbedFetcherTest extends DBWebTest
{
    public function testFetchCorrectUrl(){
        $fetcher = new OEmbedFetcher();
        $info = $fetcher->fetchUrl("https://vimeo.com/284217004");

        $this->assertNotNull($info->getCode());
        $this->assertNotNull($info->getTitle());
        $this->assertNotNull($info->getAuthorName());
    }

    public function testFetchInCorrectUrl(){
        $fetcher = new OEmbedFetcher();
        $this->expectException(InvalidUrlException::class);
        $info = $fetcher->fetchUrl("yo");
    }

    public function testNewCorrectUrl(){
        $fetcher = new OEmbedFetcher();
        $video = $fetcher->newUrl("https://vimeo.com/284217004");
        $photo = $fetcher->newUrl("https://flic.kr/p/dcDxiE");

        $classNameVideo = $this->em->getMetadataFactory()->getMetadataFor(get_class($video))->getName();
        $classNamePhoto = $this->em->getMetadataFactory()->getMetadataFor(get_class($photo))->getName();

        $this->assertEquals($classNameVideo, "App\\Entity\\Video");
        $this->assertEquals($classNamePhoto, "App\\Entity\\Photo");
    }

    public function testNewInCorrectUrl(){
        $fetcher = new OEmbedFetcher();
        $this->expectException(InvalidUrlException::class);
        $info = $fetcher->newUrl("yo");
    }
}
