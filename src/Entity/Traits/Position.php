<?php

namespace App\Entity\Traits;

trait Position
{

    /**
     * @var integer|null
     * @Groups({"read", "write"})
     * @ORM\Column(type="integer")
     */
    public $position = 0;
}
