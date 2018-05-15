<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Metadata\MetadataFactory;
use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Mapping\DomainMetadata;
use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Mapping\MultidomainMetadata;

/**
 * Load domains on demand
 *
 * @see EventSubscriber
 */
class MultidomainListener implements EventSubscriber
{
    /**
     * @var int DomainId to use for domains
     */
    private $currentDomainId = 1;

    /**
     * @var int DomainId to use when the current domainId is not available
     */
    private $fallbackDomainId = 1;

    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * Constructor
     *
     * @param MetadataFactory $factory
     */
    public function __construct(MetadataFactory $factory)
    {
        $this->metadataFactory = $factory;
    }

    /**
     * Get the current domainId
     *
     * @return string
     */
    public function getCurrentDomainId()
    {
        return $this->currentDomainId;
    }

    /**
     * Set the current domainId
     *
     * @param string $currentDomainId
     * @return self
     */
    public function setCurrentDomainId($currentDomainId)
    {
        $this->currentDomainId = $currentDomainId;
        return $this;
    }

    /**
     * Get the fallback domainId
     *
     * @return string
     */
    public function getFallbackDomainId()
    {
        return $this->fallbackDomainId;
    }

    /**
     * Set the fallback domainId
     *
     * @param string $fallbackDomainId
     * @return self
     */
    public function setFallbackDomainId($fallbackDomainId)
    {
        $this->fallbackDomainId = $fallbackDomainId;
        return $this;
    }

    /**
     * Getter for metadataFactory
     *
     * @return MetadataFactory
     */
    public function getMetadataFactory()
    {
        return $this->metadataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
            Events::postLoad,
        ];
    }

    /**
     * Add mapping to multidomain entities
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $reflClass = $classMetadata->reflClass;

        // Condition of $classMetadata->isMappedSuperclass was added for weird reasons
        if (!$reflClass || $classMetadata->isMappedSuperclass) {
            return;
        }

        if ($reflClass->implementsInterface('Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\MultidomainInterface')) {
            $this->mapMultidomain($classMetadata);
        }

        if ($reflClass->implementsInterface('Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\DomainInterface')) {
            $this->mapDomain($classMetadata);
        }
    }

    /**
     * Add mapping data to a multidomain entity
     *
     * @param ClassMetadata $mapping
     */
    private function mapMultidomain(ClassMetadata $mapping)
    {
        $metadata = $this->getMultidomainMetadata($mapping->name);

        if ($metadata->targetEntity
            && $metadata->domains
            && !$mapping->hasAssociation($metadata->domains->name)
        ) {
            $targetMetadata = $this->getMultidomainMetadata($metadata->targetEntity);

            $mapping->mapOneToMany([
                'fieldName' => $metadata->domains->name,
                'targetEntity' => $metadata->targetEntity,
                'mappedBy' => $targetMetadata->multidomain->name,
                'fetch' => ClassMetadataInfo::FETCH_EXTRA_LAZY,
                'indexBy' => $targetMetadata->domainId->name,
                'cascade' => ['persist', 'merge', 'remove'],
                'orphanRemoval' => true,
            ]);
        }
    }

    /**
     * Add mapping data to a domain entity
     *
     * @param ClassMetadata $mapping
     */
    private function mapDomain(ClassMetadata $mapping)
    {
        $metadata = $this->getMultidomainMetadata($mapping->name);

        // Map multidomain relation
        if ($metadata->targetEntity
            && $metadata->multidomain
            && !$mapping->hasAssociation($metadata->multidomain->name)
        ) {
            $targetMetadata = $this->getMultidomainMetadata($metadata->targetEntity);

            $mapping->mapManyToOne([
                'fieldName' => $metadata->multidomain->name,
                'targetEntity' => $metadata->targetEntity,
                'inversedBy' => $targetMetadata->domains->name,
                'joinColumns' => [[
                    'name' => 'multidomain_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                    'nullable' => false,
                ]],
            ]);
        }

        // Map domainId field
        if (!$mapping->hasField($metadata->domainId->name)) {
            $mapping->mapField([
                'fieldName' => $metadata->domainId->name,
                'type' => 'integer',
            ]);
        }

        // Map unique index
        $columns = [
            $mapping->getSingleAssociationJoinColumnName($metadata->multidomain->name),
            'domain_id', // WEIRD HACK, originally $metadata->domainId->name,
        ];

        if (!$this->hasUniqueConstraint($mapping, $columns)) {
            $constraints = isset($mapping->table['uniqueConstraints']) ? $mapping->table['uniqueConstraints'] : [];
            $constraints[$mapping->getTableName() . '_uniq_domains'] = [
                'columns' => $columns,
            ];

            $mapping->setPrimaryTable([
                'uniqueConstraints' => $constraints,
            ]);
        }
    }

    /**
     * Get multidomain metadata
     *
     * @param string $className
     * @return MultidomainMetadata|DomainMetadata
     */
    public function getMultidomainMetadata($className)
    {
        if (array_key_exists($className, $this->cache)) {
            return $this->cache[$className];
        }

        $metadata = $this->metadataFactory->getMetadataForClass($className);
        if ($metadata) {
            if (!$metadata->reflection->isAbstract()) {
                $metadata->validate();
            }
        }

        $this->cache[$className] = $metadata;
        return $metadata;
    }

    /**
     * Check if an unique constraint has been defined
     *
     * @param ClassMetadata $mapping
     * @param array $columns
     * @return bool
     */
    private function hasUniqueConstraint(ClassMetadata $mapping, array $columns)
    {
        if (!array_diff($mapping->getIdentifierColumnNames(), $columns)) {
            return true;
        }

        if (!isset($mapping->table['uniqueConstraints'])) {
            return false;
        }

        foreach ($mapping->table['uniqueConstraints'] as $constraint) {
            if (!array_diff($constraint['columns'], $columns)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Load domains
     *
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $metadata = $this->getMultidomainMetadata(get_class($entity));

        if ($metadata instanceof MultidomainMetadata) {
            if ($metadata->fallbackDomainId) {
                $metadata->fallbackDomainId->setValue($entity, $this->getFallbackDomainId());
            }

            if ($metadata->currentDomainId) {
                $metadata->currentDomainId->setValue($entity, $this->getCurrentDomainId());
            }
        }
    }
}
