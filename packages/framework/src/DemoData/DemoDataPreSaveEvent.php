<?php

namespace Shopsys\FrameworkBundle\DemoData;

use Symfony\Component\EventDispatcher\Event;

class DemoDataPreSaveEvent extends Event
{
    const NAME = 'shopsys.demo_data.pre_save';

    /**
     * @var object
     */
    private $entityData;

    /**
     * @var array|string[]
     */
    private $tags;

    /**
     * @var string|null
     */
    private $referenceName;

    /**
     * @var bool
     */
    private $entityCreatable;

    /**
     * @param object $entityData
     * @param string[] $tags
     * @param string|null $referenceName
     */
    public function __construct($entityData, array $tags = [], string $referenceName = null)
    {
        $this->entityData = $entityData;
        $this->tags = $tags;
        $this->referenceName = $referenceName;
        $this->entityCreatable = true;
    }

    /**
     * @return object
     */
    public function getEntityData()
    {
        return $this->entityData;
    }

    /**
     * @param string|string[] $tag
     * @return bool
     */
    public function isTaggedAs($tag): bool
    {
        return in_array($tag, $this->tags, true);
    }

    /**
     * @param string $referenceName
     * @return bool
     */
    public function isNamedReference(string $referenceName): bool
    {
        return $this->referenceName === $referenceName;
    }

    /**
     * @return bool
     */
    public function isEntityCreatable(): bool
    {
        return $this->entityCreatable;
    }

    public function stopEntityCreation(): void
    {
        $this->stopPropagation();
        $this->entityCreatable = false;
    }
}
