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
use App\Validator\Constraints\ValidRoles;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use App\Filter\SoftDeletedAtFilter;
use App\Entity\Traits as Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\MappedSuperclass
 */
class BaseAudit
{
    use Traits\IdTrait;

    /**
     * @var string
     * @Groups({ "audit:read", "audit:write"})
     * @ORM\Column(name="type", type="string", length=10, nullable=false)
     */
    public $type;


    /**
     * @var string|null
     * @Groups({ "audit:write"})
     * @ORM\Column(name="discriminator", type="string", length=255, nullable=true)
     */
    public $discriminator;

    /**
     * @var string|null
     * @Groups({ "audit:write"})
     * @ORM\Column(name="transaction_hash", type="string", length=40, nullable=true)
     */
    public $transactionHash;

    /**
     * @Groups({ "audit:read", "audit:write"})
     * @ORM\Column(name="diffs", type="json", nullable=true)
     */
    public $diffs;

    /**
     * @var string|null
     * @Groups({ "audit:read", "audit:write"})
     * @ORM\Column(name="blame_id", type="string", length=255, nullable=true)
     */
    public $blameId;

    /**
     * @var string|null
     * @Groups({ "audit:read", "audit:write"})
     * @ORM\Column(name="blame_user", type="string", length=255, nullable=true)
     */
    public $blameUser;

    /**
     * @var string|null
     * @Groups({  "audit:write"})
     * @ORM\Column(name="blame_user_fqdn", type="string", length=255, nullable=true)
     */
    public $blameUserFqdn;

    /**
     * @var string|null
     * @Groups({ "audit:write"})
     * @ORM\Column(name="blame_user_firewall", type="string", length=100, nullable=true)
     */
    public $blameUserFirewall;

    /**
     * @var string|null
     * @Groups({ "audit:read", "audit:write"})
     * @ORM\Column(name="ip", type="string", length=45, nullable=true)
     */
    public $ip;

    /**
     * @Groups({ "audit:read", "audit:write"})
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    public $createdAt;

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->formatDateTime($this->createdAt);
    }


    /**
     * @param \DateTime|null $dateTime
     * @return string | null
     */
    public function formatDateTime($dateTime)
    {
        return ($dateTime) ? $dateTime->format('d M Y H:i') : $dateTime;
    }
}
