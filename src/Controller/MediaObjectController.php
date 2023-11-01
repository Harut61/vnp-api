<?php

namespace App\Controller;
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Entity\MediaObject;
use App\Form\MediaObjectType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;

final class MediaObjectController
{
    private $validator;
    private $entityManager;
    private $factory;
    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $factory, ValidatorInterface $validator)
    {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->factory = $factory;
    }
    public function __invoke(Request $request, TokenStorageInterface $tokenStorage): MediaObject
    {
        $mediaObject = new MediaObject();
        $form = $this->factory->create(MediaObjectType::class, $mediaObject);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $token = $tokenStorage->getToken();

            if (!$token)
            {
                throw new ValidationException($this->validator->validate($mediaObject));
            }
            $mediaObject->createdBy = $token->getUser();

            $this->entityManager->persist($mediaObject);
            $this->entityManager->flush();

//          Prevent the serialization of the file property
            $mediaObject->file = null;

            return $mediaObject;
        }
        // This will be handled by API Platform and returns a validation error.
        throw new ValidationException($this->validator->validate($mediaObject));
    }
    public function remove($obj, PropertyMapping $mapping)
    {
        $name = $mapping->getFileName($obj);
        // the non-strict comparison is done on purpose: we want to skip
        // null and empty filenames
        if (null == $name) {
            return false;
        }
        return $this->doRemove($mapping, $mapping->getUploadDir($obj), $name);
    }
}