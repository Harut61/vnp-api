<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use App\Entity\MediaObject;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Storage\StorageInterface;

final class ResolveMediaObjectContentUrlSubscriber implements EventSubscriberInterface
{
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                'onPreSerialize', EventPriorities::PRE_WRITE
            ],
        ];
    }

    public function onPreSerialize(ViewEvent $event): void
    {
        $controllerResult = $event->getControllerResult();

        $objects = $controllerResult;

        if (!is_iterable($objects)) {
            $objects = [$objects];
        }

        foreach ($objects as $object ) {
            if ($object instanceof MediaObject && $event->getRequest()->getMethod() === "POST") {
                $this->decorateContentUrl($object);
            }
        }
    }

    private function decorateContentUrl($mediaObject)
    {
        if (!$mediaObject) {
            return;
        }

        if (is_iterable($mediaObject)) {
            foreach ($mediaObject as $key => $object) {
                $object->contentUrl = $this->storage->resolveUri($object, 'file');
                $object->contentUrl = str_replace(getenv("API_ENTRYPOINT"), "",$object->contentUrl);
                $mediaObject[$key] = $object;
            }
        } else {
            $mediaObject->contentUrl = $this->storage->resolveUri($mediaObject, 'file');
            $mediaObject->contentUrl = str_replace(getenv("API_ENTRYPOINT"), "",$mediaObject->contentUrl);
        }
    }

}
