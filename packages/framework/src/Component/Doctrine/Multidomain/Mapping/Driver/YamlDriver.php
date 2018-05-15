<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Mapping\Driver;

use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Mapping\DomainMetadata;
use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Mapping\MultidomainMetadata;
use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Mapping\PropertyMetadata;
use Symfony\Component\Yaml\Yaml;

/**
 * Loads multidomain metadata from Yaml mapping files.
 *
 * @author Joris van de Sande <joris.van.de.sande@freshheads.com>
 */
class YamlDriver extends FileDriver
{
    /**
     * Load metadata for a multidomain class
     *
     * @param string $className
     * @param mixed $config
     * @return MultidomainMetadata|null
     */
    protected function loadMultidomainMetadata($className, $config)
    {
        if (!isset($config[$className])
            || !isset($config[$className]['shopsys'])
            || !array_key_exists('multidomain', $config[$className]['shopsys'])
        ) {
            return;
        }

        $classMetadata = new MultidomainMetadata($className);

        $multidomain = $config[$className]['shopsys']['multidomain'] ?: [];

        $propertyMetadata = new PropertyMetadata(
            $className,
            // defaults to multidomain
            isset($multidomain['field']) ? $multidomain['field'] : 'domains'
        );

        // default targetEntity
        $targetEntity = $className . 'Domain';

        $classMetadata->targetEntity = isset($multidomain['targetEntity']) ? $multidomain['targetEntity'] : $targetEntity;
        $classMetadata->domains = $propertyMetadata;
        $classMetadata->addPropertyMetadata($propertyMetadata);

        if (isset($multidomain['currentDomainId'])) {
            $propertyMetadata = new PropertyMetadata($className, $multidomain['currentDomainId']);

            $classMetadata->currentDomainId = $propertyMetadata;
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        if (isset($multidomain['fallbackDomainId'])) {
            $propertyMetadata = new PropertyMetadata($className, $multidomain['fallbackDomainId']);

            $classMetadata->fallbackDomainId = $propertyMetadata;
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        return $classMetadata;
    }

    /**
     * Load metadata for a domain class
     *
     * @param string $className
     * @param mixed $config
     * @return DomainMetadata|null
     */
    protected function loadDomainMetadata($className, $config)
    {
        if (!isset($config[$className])
            || !isset($config[$className]['shopsys'])
            || !array_key_exists('multidomain', $config[$className]['shopsys'])
        ) {
            return;
        }

        $classMetadata = new DomainMetadata($className);

        $multidomain = $config[$className]['shopsys']['multidomain'] ?: [];

        $propertyMetadata = new PropertyMetadata(
            $className,
            // defaults to multidomain
            isset($multidomain['field']) ? $multidomain['field'] : 'multidomain'
        );

        $targetEntity = 'Domain' === substr($className, -11) ? substr($className, 0, -11) : null;

        $classMetadata->targetEntity = isset($multidomain['targetEntity']) ? $multidomain['targetEntity'] : $targetEntity;
        $classMetadata->multidomain = $propertyMetadata;
        $classMetadata->addPropertyMetadata($propertyMetadata);

        $domainId = isset($multidomain['domainId']) ? $multidomain['domainId'] : 'domainId';
        $propertyMetadata = new PropertyMetadata($className, $domainId);
        $classMetadata->domainId = $propertyMetadata;
        $classMetadata->addPropertyMetadata($propertyMetadata);

        return $classMetadata;
    }

    /**
     * Parses the given mapping file.
     * @param string $file
     * @return mixed
     */
    protected function parse($file)
    {
        return Yaml::parse(file_get_contents($file));
    }
}
