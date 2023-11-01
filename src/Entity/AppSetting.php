<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Traits as Traits;
use App\Enums\StoryQAStatusEnum;
use App\Enums\StoryStatusEnum;
use App\Enums\StoryTaggingStatusEnum;
use App\Repository\AdminUserRepository; // A custom constraint
// DON'T forget the following use statement!!!
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiSubresource;
use DH\DoctrineAuditBundle\Annotation as Audit;
use App\Filter\SoftDeletedAtFilter;
use App\Enums\GenderEnum;
use App\Annotation\DeletedAtAware;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;

/**
 * @Audit\Auditable
 * @ORM\Table(name="app_setting")
 * @UniqueEntity(fields={"title"}, message="The title '{{ value }}' is already taken",groups={"write", "app_setting:write"})
 * @ApiResource(
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "app_setting:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"app_setting:write", "write"},  "enable_max_depth"=true},
 *         "order"={"id"="desc"}
 * })
 * @ApiFilter(SearchFilter::class, properties={
 *     "title"="ipartial",
 *     "slug"="exact",
 *     "isActive"="exact",
 *     "id"="exact",
 * })
 * @ApiFilter(OrderFilter::class, properties={"id": "DESC", "position": "ASC","title": "ASC"}, arguments={"orderParameterName"="order"})
* @ApiFilter(SoftDeletedAtFilter::class, properties={ "deletedAt": "true" })
 * @ApiFilter(PropertyFilter::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 * @ORM\Entity()
 */
class AppSetting
{
    use Traits\IdTrait;
    use Traits\TitleTrait;
    use Traits\DatesTrait;
    use Traits\Slug;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Groups({"relationship:read","general:write", "read", "write", "general:read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $paramValue;

    /**
     * @var AdminUser
     * @ORM\ManyToOne(targetEntity="AdminUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     * @Groups({"app_setting:read", "app_setting:write"})
     * @ApiSubresource(maxDepth=1)
     * * @ApiProperty(
     *     attributes={
     *         "swagger_context"={
     *             "type"="string",
     *             "example"="/admin_users/{id}",
     *         },
     *     },
     * )
     */
    public $createdBy;



}
