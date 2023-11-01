<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits as Traits;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo; // A custom constraint
// DON'T forget the following use statement!!!
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Annotation\DeletedAtAware;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Annotation\ApiFilter;
/**
 * UsersRoles.
 *
 * @ORM\Table(name="admin_roles")
 * @ORM\Entity
 *  @UniqueEntity(fields={"code"}, message="Role Already Exist!")
 *  @ApiResource(
 *     collectionOperations={
 *         "get"={"defaults"={"_cache_by_user"=false}},
 *         "post"
 *     },
 *     itemOperations={
 *         "get"={"defaults"={"_cache_by_user"=false}},
 *         "put",
 *         "patch"
 *     },
 *     attributes={
 *          "pagination_client_enabled"=true,
 *          "order"={"id"="desc"},
 *          "normalization_context"={"groups"={ "relationship:read",  "general:read", "admin_roles:read"}},
 *          "denormalization_context"={"groups"={"general:write","admin_roles:write", "relationship:write"},  "enable_max_depth"=true }
 *     })
 * @ApiFilter(PropertyFilter::class)
 *     @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @DeletedAtAware(deletedAtFieldName="deleted_at")
 */
class AdminRoles
{
    use Traits\IdTrait;
    use Traits\TitleTrait;
    use Traits\DatesTrait;

    /**
     * @var string|null
     * @Assert\NotBlank
     * @Groups({"admin_roles:read", "admin_roles:write"})
     * @ORM\Column(name="code", type="string", length=255, nullable=true, unique=true)
     */
    public $code;


    /**
     *
     * @ORM\ManyToMany(targetEntity="AdminUser" , mappedBy="adminRoles")
     * @Groups({"admin_roles:read", "admin_roles:write"})
     *
     */
    public $users;
}
