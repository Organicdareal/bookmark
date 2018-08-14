<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\KeywordRepository")
 * @ORM\Table(name="keyword", uniqueConstraints={
 *       @ORM\UniqueConstraint(name="keyword_unique", columns={"content"})
 *       }
 * )
 */
class Keyword
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $content;

    /**
     * @ORM\ManyToMany(targetEntity=Link::class, inversedBy="keywords")
     * @ORM\JoinTable(name="link_keywords")
     */
    private $links;





    public function __construct()
    {
        $this->links = new ArrayCollection();
    }






    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getLinks()
    {
        return $this->links;
    }

    public function addLink(Link $link): self
    {
        if (!$this->links->contains($link)) {
            $this->links->add($link);
        }

        return $this;
    }

    public function removeLink(Link $link): self
    {
        if ($this->links->contains($link)) {
            $this->links->removeElement($link);
        }

        return $this;
    }
}
