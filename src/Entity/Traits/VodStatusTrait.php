<?php

namespace App\Entity\Traits;

use App\Enums\VodStatusEnum;

trait VodStatusTrait
{

    /**
     * @var string
     * @Groups({"relationship:read", "read", "write"})
     * @Assert\Choice(callback={"App\Enums\VodStatusEnum", "getConstants"})
     * @ORM\Column( type="string", nullable=false)
     */
    public $status = VodStatusEnum::INITIALIZED;


    /**
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $status = strtoupper($status);

        if (!\in_array($status, VodStatusEnum::getConstants())) {
            throw new \InvalidArgumentException("The value you selected is not a valid choice.");
        }

        $this->status = $status;

        return $this;
    }
}
