<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Interface for container informations
 */
interface ContainerSize
{

    public function getWidth(): ?int;

    public function setWidth(int $width);

    public function getHeight(): ?int;

    public function setHeight(int $height);
}
