<?php

namespace Shopsys\FrameworkBundle\DemoData;

use Symfony\Component\EventDispatcher\Event;

class DemoDataPostSaveEvent extends Event
{
    const NAME = 'shopsys.demo_data.post_save';

    /**
     * @var object
     */
    protected $entity;

    /**
     * @var array|string[]
     */
    protected $tags;

    /**
     * @var string|null
     */
    protected $referenceName;

    /**
     * @param object $entity
     * @param string[] $tags
     * @param string|null $referenceName
     */
    public function __construct($entity, array $tags = [], string $referenceName = null)
    {
        $this->entity = $entity;
        $this->tags = $tags;
        $this->referenceName = $referenceName;
    }

    /**
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
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
     * @param string|null $referenceName
     * @return bool
     */
    public function isNamedReference(string $referenceName): bool
    {
        return $this->referenceName === $referenceName;
    }
}
