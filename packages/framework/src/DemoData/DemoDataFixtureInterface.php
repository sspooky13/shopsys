<?php

namespace Shopsys\FrameworkBundle\DemoData;

interface DemoDataFixtureInterface
{
    public function load(): void;

    /**
     * @return string[] service IDs of demo data fixture this class depends on
     */
    public function getDependencies(): array;
}
