<?php

namespace App\Entity\Traits;

trait TitleBlankTrait
{
    /**
     * @var string
     * @Groups({"relationship:read","general:write", "read", "write", "general:read"})
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    public $title;
}
