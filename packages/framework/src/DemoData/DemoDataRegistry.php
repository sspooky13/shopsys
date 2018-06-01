<?php

namespace Shopsys\FrameworkBundle\DemoData;

class DemoDataRegistry
{
    /**
     * @var \Shopsys\FrameworkBundle\DemoData\DemoDataFixtureInterface[]
     */
    private $demoDataFixtures;

    /**
     * @param \Shopsys\FrameworkBundle\DemoData\DemoDataFixtureInterface[] $demoDataFixtures
     */
    public function __construct(array $demoDataFixtures)
    {
        $this->demoDataFixtures = $demoDataFixtures;
    }

    /**
     * @return \Shopsys\FrameworkBundle\DemoData\DemoDataFixtureInterface[]
     */
    public function getDemoDataFixtures()
    {
        return $this->demoDataFixtures;
    }
}
