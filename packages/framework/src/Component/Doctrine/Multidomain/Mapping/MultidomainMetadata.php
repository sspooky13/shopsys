<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Mapping;

use Metadata\MergeableClassMetadata;
use Metadata\MergeableInterface;

/**
 * Class metadata for multidomain entities
 *
 * @see MergeableClassMetadata
 */
class MultidomainMetadata extends MergeableClassMetadata
{
    /**
     * @var string
     */
    public $targetEntity;

    /**
     * @var PropertyMetadata
     */
    public $currentDomainId;

    /**
     * @var PropertyMetadata
     */
    public $fallbackDomainId;

    /**
     * @var PropertyMetadata
     */
    public $domains;

    /**
     * Validate the metadata
     */
    public function validate()
    {
        if (!$this->domains) {
            throw new MappingException(sprintf('No domains specified for %s', $this->name));
        }

        if (!$this->targetEntity) {
            throw new MappingException(sprintf('No domains targetEntity specified for %s', $this->name));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function merge(MergeableInterface $object)
    {
        if (!$object instanceof self) {
            throw new \InvalidArgumentException(sprintf('$object must be an instance of %s.', __CLASS__));
        }

        parent::merge($object);

        if ($object->targetEntity) {
            $this->targetEntity = $object->targetEntity;
        }

        if ($object->currentDomainId) {
            $this->currentDomainId = $object->currentDomainId;
        }

        if ($object->fallbackDomainId) {
            $this->fallbackDomainId = $object->fallbackDomainId;
        }

        if ($object->domains) {
            $this->domains = $object->domains;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            $this->targetEntity,
            $this->currentDomainId ? $this->currentDomainId->name : null,
            $this->fallbackDomainId ? $this->fallbackDomainId->name : null,
            $this->domains ? $this->domains->name : null,
            parent::serialize(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($str)
    {
        list(
            $this->targetEntity,
            $currentDomainId,
            $fallbackDomainId,
            $domains,
            $parent
        ) = unserialize($str);

        parent::unserialize($parent);

        if ($currentDomainId) {
            $this->currentDomainId = $this->propertyMetadata[$currentDomainId];
        }
        if ($fallbackDomainId) {
            $this->fallbackDomainId = $this->propertyMetadata[$fallbackDomainId];
        }
        if ($domains) {
            $this->domains = $this->propertyMetadata[$domains];
        }
    }
}
