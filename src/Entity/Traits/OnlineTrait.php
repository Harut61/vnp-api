<?php

namespace App\Entity\Traits;

trait OnlineTrait
{

    /**
     * @var bool|null
     * @Groups({"read", "write"})
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    public $online = 0;
}
