<?php
namespace App\EventListener;

use DH\DoctrineAuditBundle\Event\CreateSchemaListener;
use DH\DoctrineAuditBundle\Helper\UpdateHelper;
use DH\DoctrineAuditBundle\Manager\AuditManager;
use DH\DoctrineAuditBundle\Reader\AuditReader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\Event\GenerateSchemaTableEventArgs;
use Doctrine\ORM\Tools\ToolEvents;
use Exception;

class AuditCreateSchemaListener extends CreateSchemaListener
{
    public function __construct(AuditManager $manager, AuditReader $reader)
    {
        parent::__construct($manager, $reader);
    }

    public function postGenerateSchemaTable(GenerateSchemaTableEventArgs $eventArgs): void
    {
        $metadata = $eventArgs->getClassMetadata();

        // check inheritance type and returns if unsupported
        if (!\in_array($metadata->inheritanceType, [
            ClassMetadataInfo::INHERITANCE_TYPE_NONE,
            ClassMetadataInfo::INHERITANCE_TYPE_JOINED,
            ClassMetadataInfo::INHERITANCE_TYPE_SINGLE_TABLE,
        ], true)) {
            throw new Exception(sprintf('Inheritance type "%s" is not yet supported', $metadata->inheritanceType));
        }

        // check reader and manager entity managers and returns if different
        if ($this->reader->getEntityManager() !== $this->manager->getConfiguration()->getEntityManager()) {
            return;
        }

        // check if entity or its children are audited
        if (!$this->manager->getConfiguration()->isAuditable($metadata->name)) {
            $audited = false;
            if (
                $metadata->rootEntityName === $metadata->name &&
                ClassMetadataInfo::INHERITANCE_TYPE_SINGLE_TABLE === $metadata->inheritanceType
            ) {
                foreach ($metadata->subClasses as $subClass) {
                    if ($this->manager->getConfiguration()->isAuditable($subClass)) {
                        $audited = true;
                    }
                }
            }
            if (!$audited) {
                return;
            }
        }

        $updater = new UpdateHelper($this->manager, $this->reader);
        // $updater->createAuditTable($eventArgs->getClassTable(), $eventArgs->getSchema());
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            ToolEvents::postGenerateSchemaTable,
        ];
    }
}
