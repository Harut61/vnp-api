<?php

namespace App\Filter;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Query\SqlWalker;

class DeletedAtFilter extends SQLFilter
{
    protected $reader;

    public $trash = false;
    public $logs = false;
    public $isMaster = false;
    public $targetTableAlias = false;

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (empty($this->reader) || $this->logs) {
            return '';
        }

        // The Doctrine filter is called for any query on any entity
        // Check if the current entity is "user aware" (marked with an annotation)
        $deletedAtAware = $this->reader->getClassAnnotation(
            $targetEntity->getReflectionClass(),
            'App\\Annotation\\DeletedAtAware'
        );

        if (!$deletedAtAware || !$targetEntity->isRootEntity()) {
            return '';
        }

        $fieldName = $deletedAtAware->deletedAtFieldName;


        if (empty($fieldName)) {
            return '';
        }

        $query = "";
        if ($this->isMaster || $this->targetTableAlias === $targetTableAlias ) {
            if ($this->trash) {
                $query = sprintf('%s.%s IS NOT NULL', $targetTableAlias, $fieldName);
                $this->isMaster = false;
                $this->targetTableAlias = $targetTableAlias;
            } else {
                $query = sprintf('%s.%s is NULL', $targetTableAlias, $fieldName);
                $this->isMaster = false;
                $this->targetTableAlias = $targetTableAlias;
            }
        }

        return $query;
    }

    public function setAnnotationReader(Reader $reader)
    {
        $this->reader = $reader;
    }
}
