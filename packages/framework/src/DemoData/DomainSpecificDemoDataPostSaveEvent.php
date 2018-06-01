<?php

namespace Shopsys\FrameworkBundle\DemoData;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;

class DomainSpecificDemoDataPostSaveEvent extends DemoDataPostSaveEvent
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig
     */
    protected $domainConfig;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param object $entity
     * @param string[] $tags
     * @param string|null $referenceName
     */
    public function __construct(DomainConfig $domainConfig, $entity, array $tags = [], string $referenceName = null)
    {
        parent::__construct($entity, $tags, $referenceName);

        $this->domainConfig = $domainConfig;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig
     */
    public function getDomainConfig(): DomainConfig
    {
        return $this->domainConfig;
    }
}
