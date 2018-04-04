<?php

namespace Shopsys\FrameworkBundle\Component\Environment;

use Composer\IO\IOInterface;
use Composer\Script\Event;

class Environment
{
    const ENVIRONMENT_PRODUCTION = 'prod';
    const ENVIRONMENT_DEVELOPMENT = 'dev';
    const ENVIRONMENT_TEST = 'test';

    const FILE_DEVELOPMENT = 'DEVELOPMENT';
    const FILE_PRODUCTION = 'PRODUCTION';
    const FILE_TEST = 'TEST';

    /**
     * @param \Composer\Script\Event $event
     * @param \Shopsys\FrameworkBundle\Component\Environment\EnvironmentInterface $environment
     */
    public static function checkEnvironment(Event $event)
    {
        $io = $event->getIO();
        /* @var $io \Composer\IO\IOInterface */
        if ($io->isInteractive() && self::getEnvironmentSetting(false) === null) {
            if ($io->askConfirmation('Build in production environment? (Y/n): ', true)) {
                self::createFile(self::getRootDir($environment) . '/' . self::FILE_PRODUCTION);
            } else {
                self::createFile(self::getRootDir($environment) . '/' . self::FILE_DEVELOPMENT);
            }
        }
        self::printEnvironmentInfo($io, $environment);
    }

    /**
     * @param bool $console
     * @return string
     */
    public static function getEnvironment($console, EnvironmentInterface $environment)
    {
        $environmentSetting = self::getEnvironmentSetting($console, $environment);
        return $environmentSetting ?: self::ENVIRONMENT_PRODUCTION;
    }

    /**
     * @param string $environment
     */
    public static function isEnvironmentDebug($environment)
    {
        return $environment === self::ENVIRONMENT_DEVELOPMENT;
    }

    /**
     * @param \Composer\IO\IOInterface $io
     */
    public static function printEnvironmentInfo(IOInterface $io, EnvironmentInterface $environment)
    {
        $io->write("\nEnvironment is <info>" . self::getEnvironment(false) . "</info>\n", $environment);
    }

    /**
     * @param string $filepath
     */
    private static function createFile($filepath)
    {
        $file = fopen($filepath, 'w');
        fclose($file);
    }

    /**
     * @return string
     */
    private static function getRootDir(EnvironmentInterface $environment)
    {
        return $environment->getRootDir();
    }

    /**
     * @param bool $ignoreTestFile
     * @return string|null
     */
    private static function getEnvironmentSetting($ignoreTestFile, EnvironmentInterface $environment)
    {
        if (!$ignoreTestFile && is_file(self::getRootDir($environment) . '/' . self::FILE_TEST)) {
            return self::ENVIRONMENT_TEST;
        } elseif (is_file(self::getRootDir($environment) . '/' . self::FILE_DEVELOPMENT)) {
            return self::ENVIRONMENT_DEVELOPMENT;
        } elseif (is_file(self::getRootDir($environment) . '/' . self::FILE_PRODUCTION)) {
            return self::ENVIRONMENT_PRODUCTION;
        }
        return null;
    }
}
