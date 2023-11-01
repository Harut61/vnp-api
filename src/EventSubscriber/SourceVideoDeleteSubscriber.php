<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\AdminUser;
use App\Entity\SourceVideo;
use App\Enums\SourceVideoStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SourceVideoDeleteSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [ 'markUpStatusUpdate', EventPriorities::PRE_WRITE ],
            Events::prePersist => [ 'markUpStatusUpdate', Events::prePersist ],
        ];
    }

    /**
     * @param ViewEvent $event
     *  Update password.
     */
    public function markUpStatusUpdate(ViewEvent $event)
    {
        $object = $event->getControllerResult();
        $postParams = json_decode($event->getRequest()->getContent(), true);

        if(!in_array($event->getRequest()->getMethod() ,[Request::METHOD_PATCH , Request::METHOD_POST])) {
            return ;
        }

        /** @var $object SourceVideo */
        if ($object instanceof SourceVideo) {
            if(!$object->getId())
            {
                return;
            }
            /** @var EntityManagerInterface $em */
            $em = $this->container->get('doctrine')->getManager();

            if(array_key_exists("status",$postParams) && SourceVideoStatusEnum::MARKED_UP_FINISHED === $postParams["status"]){
                $object->markUpEnd = new \DateTime();
                $object->beingMarkedUpBy = [];
                $object->markUpStatus = false;
                $em->persist($object);
                $em->flush();
                return;
            }

            $repoSourceVideo = $em->getRepository(SourceVideo::class);

            $sourceVideo = $repoSourceVideo->find($object->getId());

            if( (array_key_exists("markUpStatus",$postParams) && $postParams["markUpStatus"] == true) && empty($sourceVideo->beingMarkedUpBy)) {
                /** @var AdminUser $user */
                $user = $this->container->get('security.token_storage')->getToken()->getUser();

                $conn = $em->getConnection();
                $ids = $conn->fetchAssociative('SELECT GROUP_CONCAT(id) as ids FROM source_videos WHERE JSON_EXTRACT(being_marked_up_by, "$.id") = '. $user->getId());
                if(!empty($ids) && !empty($ids["ids"])){
                    $ids = $ids["ids"];
                    $res = $conn->executeQuery("update source_videos set being_marked_up_by = '[]', mark_up_status = 0, status = '". SourceVideoStatusEnum::READY_FOR_MARKER ."'  where id in ($ids)");
                }
                $em->getConnection()->beginTransaction();
                $object->status = SourceVideoStatusEnum::BEING_MARKED_UP;

                if(empty($object->markUpStart)){
                    $object->markUpStart = new \DateTime();
                }
                $object->beingMarkedUpBy =
                    [
                        "id" => $user->getId(),
                        "fullName" => $user->fullName
                    ];
                $object->markUpStatus = true;
                $em->persist($object);
                $em->flush();
                $em->getConnection()->commit();
            } else if (array_key_exists("markUpStatus",$postParams) && $postParams["markUpStatus"] == false
                && array_key_exists("status",$postParams) && $postParams["status"] == SourceVideoStatusEnum::PARTIALLY_MARKED_UP ) {
                $em->getConnection()->beginTransaction();
                $object->beingMarkedUpBy = [];
                $object->markUpStatus = false;
                $object->status = SourceVideoStatusEnum::PARTIALLY_MARKED_UP;
                $em->persist($object);
                $em->flush();
                $em->getConnection()->commit();
            } else if (array_key_exists("markUpStatus",$postParams) && $postParams["markUpStatus"] == false ){
                $em->getConnection()->beginTransaction();
                $object->beingMarkedUpBy = [];
                $object->markUpStatus = false;
                $object->status = SourceVideoStatusEnum::READY_FOR_MARKER;
                $em->persist($object);
                $em->flush();
                $em->getConnection()->commit();
            }


        }
    }
}
