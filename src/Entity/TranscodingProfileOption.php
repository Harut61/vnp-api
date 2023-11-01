<?php

namespace App\Entity;

use App\Repository\TranscodingProfileRepository;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Traits as Traits;
use App\Repository\AdminUserRepository; // A custom constraint
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\Serializer\Annotation\Groups; // A custom constraint
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use App\Filter\SoftDeletedAtFilter;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Enums\VodStatusEnum;
use DH\DoctrineAuditBundle\Annotation as Audit;
use App\Annotation\DeletedAtAware;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;

/**
 * @ORM\Entity()
 * @Audit\Auditable
 * @UniqueEntity(fields={"title"}, message="The title '{{ value }}' is already taken",groups={"write", "transcoding_profile_option:write"})
 * @ApiResource(
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "transcoding_profile_option:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"transcoding_profile_option:write", "write"},  "enable_max_depth"=true},
 *         "order"={"id"="desc"}
 * })
 *
 * @ApiFilter(SearchFilter::class, properties={
 *     "title"="ipartial"
 * })
 *
* @ApiFilter(SoftDeletedAtFilter::class, properties={ "deletedAt": "true" })
 * @ApiFilter(PropertyFilter::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 *
 */
class TranscodingProfileOption
{
    use Traits\IdTrait;
    use Traits\TitleTrait;
    use Traits\Slug;
    use Traits\DatesTrait;
    use Traits\DescriptionTrait;

    /**
     * @var int
     * @Groups({ "transcoding_profile_option:read", "transcoding_profile_option:write"})
     * @ORM\Column(type="integer", length=255, nullable=true)
     * @Assert\Type(type="integer")
     */
    public $fps;

    /**
     * @var int
     * @Groups({ "transcoding_profile_option:read", "transcoding_profile_option:write"})
     * @Assert\Type(type="string")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $audioCodec;


    /**
     * @var int
     * @Groups({ "transcoding_profile_option:read", "transcoding_profile_option:write"})
     * @Assert\Type(type="string")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $videoCodec;


    /**
     * @var int
     * @Groups({ "transcoding_profile_option:read", "transcoding_profile_option:write"})
     * @Assert\Type(type="integer")
     * @ORM\Column(type="integer", length=255,nullable=true)
     */
    public $videoWidth;

    /**
     * @var int
     * @Groups({ "transcoding_profile_option:read", "transcoding_profile_option:write"})
     * @Assert\Type(type="integer")
     * @ORM\Column(type="integer", length=255,nullable=true)
     */
    public $videoHeight;

    /**
     * bits/second
     * @var int
     * @Groups({ "transcoding_profile_option:read", "transcoding_profile_option:write"})
     * @Assert\Type(type="integer")
     * @ORM\Column(type="integer", length=255, nullable=true)
     */
    public $videoBitrate;


    /**
     * bits/second
     * @var int
     * @Groups({ "transcoding_profile_option:read", "transcoding_profile_option:write"})
     * @Assert\Type(type="integer")
     * @ORM\Column(type="integer", length=255, nullable=true)
     */
    public $audioBitrate;

    /**
     * @var int
     * @Groups({ "transcoding_profile_option:read", "transcoding_profile_option:write"})
     * @Assert\Type(type="string")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $profile;

    /**
     * @var int
     * @Groups({ "transcoding_profile_option:read", "transcoding_profile_option:write"})
     * @Assert\Type(type="string")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $container = "MP4";
    
}
