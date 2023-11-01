<?php

namespace App\Entity\Traits;

trait IdTrait
{
    /**
     * @var integer
     * @Groups({"relationship:read", "id:read", "relationship:write", "general:read"})
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    public function getId()
    {
        return $this->id;
    }
}
