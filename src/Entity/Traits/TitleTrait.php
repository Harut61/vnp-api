<?php

namespace App\Entity\Traits;

trait TitleTrait
{
    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Groups({"relationship:read","general:write", "read", "write", "general:read"})
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    public $title;
}
