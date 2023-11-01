<?php
namespace App\Services\Handlers;

use App\Entity\AppSetting;
use App\Enums\AppSettingEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class AppSettingHandlers
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var ObjectRepository $repo */
    public $repo;

    /**
     * AppSettingHandlers constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repo = $entityManager->getRepository(AppSetting::class);
    }

    public function getAllowedTranscodingParallel()
    {
        return $this->getAllowedTranscodingByTitle(AppSettingEnum::TRANSCODING_PARALLEL);
    }

    public function getAllowedTranscodingPerDay()
    {
        return $this->getAllowedTranscodingByTitle(AppSettingEnum::TRANSCODING_PER_DAY);
    }


    public function getAllowedTranscodingByTitle($title)
    {
        /** @var AppSetting $res */
        $res = $this->repo->findOneBy(["title"=> $title]);
        return $res->paramValue;
    }

    public function isAllowedTranscoding($processing)
    {
        $perMonth = $this->getAllowedTranscodingPerDay();

    }

    /**
     * @param AppSetting $appSetting
     */
    public function save(AppSetting $appSetting)
    {
        $this->entityManager->persist($appSetting);
        $this->entityManager->flush();
    }

}