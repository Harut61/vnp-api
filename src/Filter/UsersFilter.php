<?php

namespace App\Filter;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Doctrine\Common\Annotations\Reader;

class UsersFilter extends SQLFilter
{
    /** @var  Reader|null $reader */
    protected $reader;

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (empty($this->reader)) {
            return '';
        }

        // The Doctrine filter is called for any query on any entity
        // Check if the current entity is "user aware" (marked with an annotation)
        $usersAware = $this->reader->getClassAnnotation(
            $targetEntity->getReflectionClass(),
            'App\\Annotation\\UsersAware'
        );

        if (!$usersAware) {
            return '';
        }

        $fieldName = $usersAware->usersFieldName;

        try {
            // Don't worry, getParameter automatically quotes parameters
            $userId = $this->getParameter('id');
        } catch (\InvalidArgumentException $e) {
            // No user id has been defined
            return '';
        }

        if (empty($fieldName) || empty($userId)) {
            return '';
        }

        $query = sprintf('%s.%s = %s', $targetTableAlias, $fieldName, $userId);

        return $query;
    }

    public function setAnnotationReader(Reader $reader)
    {
        $this->reader = $reader;
    }
}
