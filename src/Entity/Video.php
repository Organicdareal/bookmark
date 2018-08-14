<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VideoRepository")
 */
class Video extends Link implements ContainerSize
{

    /**
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\Column(type="integer")
     */
    private $width;

    /**
     * @ORM\Column(type="integer")
     */
    private $height;





    public function __construct(\DateTimeInterface $date, string $author, string $title, string $url, int $width, int $height, int $duration)
    {
        parent::__construct($date, $author, $title, $url);
        $this->width = $width;
        $this->height = $height;
        $this->duration = $duration;
    }






    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;
        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;
        return $this;
    }
}
