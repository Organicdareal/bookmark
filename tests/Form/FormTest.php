<?php

namespace App\Tests\Form;

use App\Entity\Keyword;
use App\Entity\Photo;
use App\Entity\Video;
use App\Form\LinkType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Symfony\Component\Form\Test\TypeTestCase;

class FormTest extends TypeTestCase
{
    /**
     * helper to acccess EntityManager
     */
    protected $em;
    protected $photo;
    protected $video;
    protected $keywords;

    /**
     * Before each test we start a new transaction
     * everything done in the test will be canceled ensuring isolation et speed
     */
    protected function setUp()
    {
        parent::setUp();
        $this->em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->setMethods(array("getMetadataFactory", "getMetadataFor", "getName", "getClassMetadata"))
            ->getMock();

        $this->photo = $this->getMockBuilder(Photo::class)
            ->disableOriginalConstructor()->getMock()
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->video = $this->getMockBuilder(Video::class)
            ->disableOriginalConstructor()->getMock()
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->keywords = new ArrayCollection();
        $tag1 = new Keyword();
        $tag1->setContent("test1");
        $tag2 = new Keyword();
        $tag2->setContent("test2");
        $this->keywords->add($tag1);
        $this->keywords->add($tag2);
    }

    public function testSubmitCorrectForms()
    {
        $this->markTestIncomplete("em doesn't return class in form");
        $formDataURL = array(
            'url' => 'https://vimeo.com/284322748',
            'keywords' => $this->keywords,
        );
        $formDataPhoto = array(
            'url' => 'https://flic.kr/p/5iJut',
            'title' => 'test',
            'author' => 'test',
            'date' => 'test',
            'width' => 1080,
            'height' => 720,
            'keywords' => $this->keywords,
        );
        $formDataVideo = array(
            'url' => 'https://vimeo.com/284322748',
            'title' => 'test',
            'author' => 'test',
            'date' => 'test',
            'width' => 1080,
            'height' => 720,
            'duration' => 300,
            'keywords' => $this->keywords,
        );

        $form = $this->factory->create(LinkType::class, null, array(
            'entity_manager' => $this->em,
        ));
        $form->submit($formDataURL);
        $this->assertTrue($form->isSynchronized());
        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formDataURL) as $key) {
            $this->assertArrayHasKey($key, $children);
        }

//        $form = $this->factory->create(LinkType::class, $this->photo, array(
//            'entity_manager' => $this->em,
//        ));
//        $form->submit($formDataPhoto);
//        $this->assertTrue($form->isSynchronized());
//
//        $form = $this->factory->create(LinkType::class, $this->video, array(
//            'entity_manager' => $this->em,
//        ));
//        $form->submit($formDataVideo);
//        $this->assertTrue($form->isSynchronized());
    }

//    public function testSubmitIncorrectForms()
//    {
//        $formDataURL = array(
//            'url' => null,
//            'keywords' => $this->keywords,
//        );
//        $formDataPhoto = array(
//            'url' => 'https://flic.kr/p/5iJut',
//            'title' => 'test',
//            'author' => 'test',
//            'date' => 'test',
//            'width' => 1080,
//            'height' => 720,
//            'keywords' => $this->keywords,
//        );
//        $formDataVideo = array(
//            'url' => 'https://vimeo.com/284322748',
//            'title' => 'test',
//            'author' => 'test',
//            'date' => 'test',
//            'width' => 1080,
//            'height' => 720,
//            'keywords' => $this->keywords,
//        );
//
//        $form = $this->factory->create(LinkType::class, null, array(
//            'entity_manager' => $this->em,
//        ));
//        $form->submit($formDataURL);
//        $this->assertTrue($form->isSynchronized());
//
//        $form = $this->factory->create(LinkType::class, $this->photo, array(
//            'entity_manager' => $this->em,
//        ));
//        $form->submit($formDataPhoto);
//        $this->assertTrue($form->isSynchronized());
//
//        $form = $this->factory->create(LinkType::class, $this->video, array(
//            'entity_manager' => $this->em,
//        ));
//        $form->submit($formDataVideo);
//        $this->assertTrue($form->isSynchronized());
//    }
}
