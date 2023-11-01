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

/**
 * Audit
 * @ORM\Table(name="audit_story_types")
 * @ORM\Entity
 * @UniqueEntity("slug")
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
 *  @ApiFilter(SearchFilter::class, properties={"type"="partial","object"="partial","blameId"="partial","blameUser"="partial"})
 *  @ApiFilter(OrderFilter::class, properties={"createdAt"}, arguments={"orderParameterName"="order"})
 *  @ApiFilter(DateFilter::class, properties={"createdAt"})
 */
class AuditStoryType extends BaseAudit
{

    /**
     * @Groups({"id:read"})
     * @var string
     ** @ORM\ManyToOne(targetEntity="App\Entity\StoryType")
     * @MaxDepth(1)
     */
    public $object;
}
