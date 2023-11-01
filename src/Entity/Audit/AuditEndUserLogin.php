<?php

namespace App\Entity\Audit;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups; // A custom constraint
// DON'T forget the following use statement!!!
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use App\Entity\Traits as Traits;
use Gedmo\Mapping\Annotation as Gedmo;
use DH\DoctrineAuditBundle\Annotation as Audit;
use App\Annotation\UsersAware;
/**
 * Audit
 * @ORM\Table(name="audit_end_users_login")
 * @ORM\Entity
 *  @ApiResource(
 *     collectionOperations={
 *         "get"={"defaults"={"_cache_by_user"=false}}
 *     },
 *     itemOperations={
 *         "get"={"defaults"={"_cache_by_user"=false}}
 *     },
 *     attributes={
 *         "pagination_client_enabled"=true,
 *         "normalization_context"={"groups"={ "id:read",  "audit:read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"audit:write"},  "enable_max_depth"=true},
 *         "order"={"id"="desc"}
 *     })
 *  @ApiFilter(SearchFilter::class, properties={"type"="partial","blameId"="partial"})
 *  @ApiFilter(OrderFilter::class, properties={"createdAt"}, arguments={"orderParameterName"="order"})
 *  @ApiFilter(DateFilter::class, properties={"createdAt"})
 *  @UsersAware(usersFieldName="blame_id")
 */
class AuditEndUserLogin
{
    use Traits\IdTrait;

    /**
     * @var string
     * @Groups({ "audit:read", "audit:write"})
     * @ORM\Column(name="type", type="string", length=10, nullable=false)
     */
    public $type;

    /**
     * @var string
     * @Groups({ "audit:read", "audit:write"})
     * @ORM\Column(name="action", type="string", length=100, nullable=false)
     */
    public $action;

    /**
     * @var string|null
     * @Groups({ "audit:read", "audit:write"})
     * @ORM\Column(name="blame_id", type="string", length=255, nullable=true)
     */
    public $userId;

    /**
     * @var string|null
     * @Groups({ "audit:read", "audit:write"})
     * @ORM\Column(name="ip", type="string", length=45, nullable=true)
     */
    public $ip;
    /**
     * @var \DateTime Creation time
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Groups({ "audit:read", "audit:write"})
     */
    public $createdAt;

}