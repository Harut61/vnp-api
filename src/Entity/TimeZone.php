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
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;

use App\Annotation\DeletedAtAware;
/**
 * @Audit\Auditable
 * @ORM\Table(name="time_zones")
 * @UniqueEntity(fields={"title"}, message="The title '{{ value }}' is already taken",groups={"write", "time_zone:write"})
 * @ApiResource(
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "relationship:read",  "time_zone:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"time_zone:write", "write"},  "enable_max_depth"=true},
 *         "order"={"position"="DESC"}
 * })
 * @ApiFilter(PropertyFilter::class)
 * @ApiFilter(SearchFilter::class, properties={
 *     "title"="ipartial",
 *     "isActive"="exact",
 *     "id"="exact",
 * })
 * @ApiFilter(OrderFilter::class, properties={"id": "DESC", "position": "ASC","title": "ASC"}, arguments={"orderParameterName"="order"})
* @ApiFilter(SoftDeletedAtFilter::class, properties={ "deletedAt": "true" })
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 * @ORM\Entity()
 */
class TimeZone
{
    use Traits\IdTrait;
    use Traits\TitleTrait;
    use Traits\Position;
    use Traits\DatesTrait;

    /**
     * This time rely on UTC
     * @var string
     * @Groups({"relationship:read","time_zone:write", "time_zone:read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $standardTime;

    /**
     * This time rely on UTC
     * @var string
     * @Groups({"relationship:read","time_zone:write",  "time_zone:read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $dayLightSavingTime;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Groups({"relationship:read","time_zone:write",  "time_zone:read"})
     * @ORM\Column( type="string", length=255, nullable=true)
     */
    public $phpTimeZone;

    

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Groups({"relationship:read","time_zone:write",  "time_zone:read"})
     * @ORM\Column( type="string", length=255, nullable=true)
     */
    public $phpTimeZoneDLS;

}
