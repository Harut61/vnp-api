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
 * @ORM\Entity(repositoryClass=TranscodingProfileRepository::class)
 * @Audit\Auditable
 * @UniqueEntity(fields={"title"}, message="The title '{{ value }}' is already taken",groups={"write", "transcoding_profile:write"})
 * @ApiResource(
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "transcoding_profile:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"transcoding_profile:write", "write"},  "enable_max_depth"=true},
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
class TranscodingProfile
{
    use Traits\IdTrait;
    use Traits\TitleTrait;
    use Traits\DatesTrait;

    /**
     * @var bool|null
     * @Groups({"transcoding_profile:write", "transcoding_profile:read"})
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    public $isDefault = false;

    /**
     * @Groups({"transcoding_profile:write", "transcoding_profile:read"})
     * @ORM\ManyToMany(targetEntity="TranscodingProfileOption")
     * @ApiSubresource(maxDepth=1)
     */
    public $profiles = [];
}
