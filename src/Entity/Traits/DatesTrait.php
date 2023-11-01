<?php

namespace App\Entity\Traits;

use Gedmo\Mapping\Annotation as Gedmo;

trait DatesTrait
{
    /**
     * @var \DateTime Creation time
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Groups({"general:read", "read"})
     */
    private $createdAt;

    /**
     * @var \DateTime Update time
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update" )
     * @Groups({"general:read", "read"})
     */
    private $updatedAt;

    /**
     * @var \DateTime Deletion time
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     * @Groups({"general:read", "read"})
     */
    private $deletedAt;

    /**
     * @return string
     */
    public function getDeletedAt()
    {
        return $this->formatDateTime($this->deletedAt);
    }

    /**
     * @param \DateTime $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->formatDateTime($this->createdAt);
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->formatDateTime($this->updatedAt);
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param \DateTime|null $dateTime
     * @return string | null
     */
    public function formatDateTime($dateTime)
    {
        return ($dateTime) ? $dateTime->format('d M Y H:i') : $dateTime;
    }

    /**
     * intervalToSeconds
     *
     * @param  \DateInterval $interval
     * @return int
     */
    public function intervalToSeconds(\DateInterval $interval) {
        return $interval->days * 86400 + $interval->h * 3600 + $interval->i * 60 + $interval->s;
    }

    public function secondsToMin($seconds)
    {
        $minutes = floor($seconds/60);
        $secondsleft = $seconds%60;
        if($minutes<10)
            $minutes = "0" . $minutes;
        if($secondsleft<10)
            $secondsleft = "0" . $secondsleft;
        return floatval("$minutes.$secondsleft") ;
    }
}
