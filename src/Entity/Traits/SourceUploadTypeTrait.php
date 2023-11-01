<?php

namespace App\Entity\Traits;

use App\Enums\SourceUploadTypeEnum;

trait SourceUploadTypeTrait
{

    /**
     * @var string
     * @Groups({"relationship:read", "read", "write"})
     * @Assert\Choice(callback={"App\Enums\SourceUploadTypeEnum", "getConstants"})
     * @ORM\Column( type="string", nullable=false)
     *  @ORM\Column(type="string", length=20, options={"default"=SourceUploadTypeEnum::MANUAL })
     */
    public $uploadedType = SourceUploadTypeEnum::MANUAL;


    /**
     * @param $uploadedType
     * @return $this
     */
    public function setUploadedType($uploadedType)
    {
        $uploadedType = strtoupper($uploadedType);

        if (!\in_array($uploadedType, SourceUploadTypeEnum::getConstants())) {
            throw new \InvalidArgumentException("The value you selected is not a valid choice.");
        }

        $this->uploadedType = $uploadedType;

        return $this;
    }
}
