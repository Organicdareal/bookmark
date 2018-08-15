<?php

namespace App\Form\DataTransformer;

use App\Entity\Keyword;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Created by PhpStorm.
 * User: ggicquel
 * Date: 15/08/2018
 * Time: 17:13
 */
class StringToKeywordTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function transform($tags)
    {
        return $tags;
    }

    public function reverseTransform($tags)
    {
        $tagCollection = new ArrayCollection();

        $tagsRepository = $this->manager->getRepository(Keyword::class);

        foreach ($tags as $tag) {

            $tagInRepo = $tagsRepository->findOneBy(['content' => $tag->getContent()]);

            if ($tagInRepo !== null) {
                // Add tag from repository if found
                $tagCollection->add($tagInRepo);
            }
            else {
                // Otherwise add new tag
                $tagCollection->add($tag);
            }
        }

        return $tagCollection;
    }
}