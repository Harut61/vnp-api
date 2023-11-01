<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Repository\StoryQaIssueRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Filter\SoftDeletedAtFilter;
use App\Annotation\DeletedAtAware;
use App\Entity\Traits as Traits;

/**
 * @ApiResource(
 *   attributes={
 *       "pagination_client_enabled"=true,
 *       "normalization_context"={"groups"={ "relationship:read",  "story_qa_issues:read", "read"}, "skip_null_values" = false, "enable_max_depth"=true},
 *       "denormalization_context"={"groups"={"story_qa_issues:write", "write"},  "enable_max_depth"=true},
 *       "order"={"id"="desc"}
 *})
 * @ApiFilter(SearchFilter::class, properties={
 *     "id"="exact",
 *     "comment"="ipartial"
 * })
 * @ApiFilter(OrderFilter::class, properties={
 *     "id": "DESC",
 *     "comment": "ASC"
 * }, arguments={"orderParameterName"="order"})
 * @ApiFilter(SoftDeletedAtFilter::class, properties={ "deletedAt": "true" })
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ORM\Entity(repositoryClass=StoryQaIssueRepository::class)
 */
class StoryQaIssue
{

    use Traits\IdTrait;
    use Traits\DatesTrait;

    /**
     * @Groups({ "story_qa_issues:read", "story_qa_issues:write"})
     * @ApiSubresource(maxDepth=1)
     * @ORM\ManyToOne(targetEntity=Story::class)
     */
    public $story;

    /**
     * @Groups({ "story_qa_issues:read", "story_qa_issues:write"})
     * @ApiSubresource(maxDepth=1)
     * @ORM\ManyToOne(targetEntity=StoryQaIssuesType::class)
     */
    public $issue_type;

    /**
     * @Groups({ "story_qa_issues:read", "story_qa_issues:write"})
     * @Assert\NotNull
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    public $comment;

    /**
     * @Groups({ "story_qa_issues:read", "story_qa_issues:write"})
     * @ORM\Column(type="boolean", nullable=true)
     */
    public $status;

    /**
     * @var AdminUser
     * @ORM\ManyToOne(targetEntity="AdminUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     * @Groups({ "story_qa_issues:read", "story_qa_issues:write"})
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
