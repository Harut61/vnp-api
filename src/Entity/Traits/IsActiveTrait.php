<?php

namespace App\Entity\Traits;

trait IsActiveTrait
{

    /**
     * @var bool|null
     * @Groups({"read", "write"})
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    public $isActive = false;
}
