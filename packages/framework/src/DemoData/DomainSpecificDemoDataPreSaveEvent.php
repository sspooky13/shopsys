<?php

namespace Shopsys\FrameworkBundle\DemoData;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;

class DomainSpecificDemoDataPreSaveEvent extends DemoDataPreSaveEvent
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig
     */
    protected $domainConfig;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param object $entityData
     * @param string[] $tags
     * @param string|null $referenceName
     */
    public function __construct(DomainConfig $domainConfig, $entityData, array $tags = [], string $referenceName = null)
    {
        parent::__construct($entityData, $tags, $referenceName);

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
