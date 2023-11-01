<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\AdminUser;
use App\Entity\EndUser;
use App\Entity\HighLevelSubjectTag;
use App\Entity\LineUp;
use App\Entity\Show;
use App\Entity\Source;
use App\Entity\SourceVideo;
use App\Entity\Story;
use App\Enums\SourceVideoStatusEnum;
use App\Enums\StoryStatusEnum;
use App\Enums\VodStatusEnum;
use App\Filter\NullFilter;
use App\Util\AwsSqsUtil;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class VneIntegrationSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var AwsSqsUtil
     */
    private  $awsSqsUtil;

    public function __construct(ContainerInterface $container, AwsSqsUtil $awsSqsUtil)
    {
        $this->container = $container;
        $this->awsSqsUtil = $awsSqsUtil;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [ 'sendMessage', EventPriorities::POST_WRITE ],

        ];
    }

    /**
     * @param ViewEvent $event
     */
    public function sendMessage(ViewEvent $event)
    {

        $object = $event->getControllerResult();

        if(in_array($_ENV["APP_ENV"], ["test", "local"]) ) {
            return;
        }
        
        if ( in_array($event->getRequest()->getMethod(), ["POST", "PATCH", "PUT"]) &&  ($object instanceof Story || $object instanceof SourceVideo || $object instanceof Show || $object instanceof Source || $object instanceof LineUp)) {

            $entity = (new \ReflectionClass($object))->getShortName();
            $queueUrl = $this->awsSqsUtil->getQueueUrl($_ENV["VNE_INTEGRATION_SQS_QUEUE_NAME"]);

            $this->awsSqsUtil->sendMessage($queueUrl ,
                json_encode([
                    "id"=>$object->getId(),
                    "type" => $entity,
                    "method" => $event->getRequest()->getMethod()
                ])
            );
        }

        if ( in_array($event->getRequest()->getMethod(), ["POST", "PATCH", "PUT"]) &&  ($object instanceof EndUser)) {
            $entity = (new \ReflectionClass($object))->getShortName();
            $queueUrl = $this->awsSqsUtil->getQueueUrl($_ENV["VNE_INTEGRATION_SQS_QUEUE_NAME"]);

            if($object->signUpStatus == true){
                $this->awsSqsUtil->sendMessage($queueUrl ,
                    json_encode([
                        "id"=>$object->getId(),
                        "type" => $entity,
                        "method" => $event->getRequest()->getMethod(),
                        "prefered_lineup_duration" => $object->preferedLineupDuration
                    ])
                );
            }
        }

        if ( in_array($event->getRequest()->getMethod(), ["PATCH", "PUT"]) &&  ($object instanceof Story)) {
            $entity = (new \ReflectionClass($object))->getShortName();
            $queueUrl = $this->awsSqsUtil->getQueueUrl($_ENV["VNE_INTEGRATION_SQS_QUEUE_NAME"]);

            if($object->storyStatus == StoryStatusEnum::GENERATED){
                $this->awsSqsUtil->sendMessage($queueUrl ,
                    json_encode([
                        "id"=>$object->getId(),
                        "type" => $entity,
                        "method" => $event->getRequest()->getMethod()
                    ])
                );
            }
        }
    }
}
