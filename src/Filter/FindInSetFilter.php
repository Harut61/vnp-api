<?php

namespace App\Filter;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Doctrine\Common\Annotations\Reader;

class FindInSetFilter extends SQLFilter
{
    /** @var  Reader|null $reader */
    protected $reader;
    public $genresIds = "";

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (empty($this->reader)) {
            return '';
        }

        // The Doctrine filter is called for any query on any entity
        // Check if the current entity is "user aware" (marked with an annotation)
        $usersAware = $this->reader->getClassAnnotation(
            $targetEntity->getReflectionClass(),
            'App\\Annotation\\FindInSetAware'
        );

        if (!$usersAware) {
            return '';
        }

        $fieldName = $usersAware->fieldName;


        if (empty($fieldName) || empty($this->genresIds)) {
            return '';
        }

        $query = sprintf(' FIND_IN_SET(%s, %s.%s) ', $this->genresIds, $targetTableAlias, $fieldName);


        return $query;
    }

    public function setAnnotationReader(Reader $reader)
    {
        $this->reader = $reader;
    }
}
