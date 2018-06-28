<?php

namespace Shopsys\FrameworkBundle\Component\Environment;

class EnvironmentType
{
    const DEVELOPMENT = 'dev';
    const PRODUCTION = 'prod';
    const TEST = 'test';

    const ALL = [self::DEVELOPMENT, self::PRODUCTION, self::TEST];

    /**
     * @param string $environment
     * @return bool
     */
    public static function isDebug(string $environment): bool
    {
        return true;
    }
}
