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
 * Class metadata for domains
 *
 * @see MergeableClassMetadata
 */
class DomainMetadata extends MergeableClassMetadata
{
    /**
     * @var string
     */
    public $targetEntity;

    /**
     * @var PropertyMetadata
     */
    public $multidomain;

    /**
     * @var PropertyMetadata
     */
    public $domainId;

    /**
     * Validate the metadata
     */
    public function validate()
    {
        if (!$this->multidomain) {
            throw new MappingException(sprintf('No multidomain specified for %s', $this->name));
        }

        if (!$this->targetEntity) {
            throw new MappingException(sprintf('No multidomain targetEntity specified for %s', $this->name));
        }

        if (!$this->domainId) {
            throw new MappingException(sprintf('No domainId specified for %s', $this->name));
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

        if ($object->multidomain) {
            $this->multidomain = $object->multidomain;
        }

        if ($object->domainId) {
            $this->domainId = $object->domainId;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            $this->targetEntity,
            $this->multidomain ? $this->multidomain->name : null,
            $this->domainId ? $this->domainId->name : null,
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
            $multidomain,
            $domainId,
            $parent
        ) = unserialize($str);

        parent::unserialize($parent);

        if ($multidomain) {
            $this->multidomain = $this->propertyMetadata[$multidomain];
        }
        if ($domainId) {
            $this->domainId = $this->propertyMetadata[$domainId];
        }
    }
}
