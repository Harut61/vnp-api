<?php

namespace App\Entity\Traits;

trait DescriptionTrait
{

    /**
     * @var string
     * @Groups({"read", "write"})
     * @ORM\Column( type="text", nullable=true)
     */
    public $description;
}
