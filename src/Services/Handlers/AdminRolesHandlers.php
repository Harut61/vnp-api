<?php
namespace App\Services\Handlers;

use App\Entity\AdminRoles;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class AdminRolesHandlers
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var ObjectRepository $repo */
    public $repo;

    /**
     * AdminRolesHandlers constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repo = $entityManager->getRepository(AdminRoles::class);
    }


    /**
     * @param AdminRoles $adminRoles
     */
    public function save(AdminRoles $adminRoles)
    {
        $this->entityManager->persist($adminRoles);
        $this->entityManager->flush();
    }

}