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
use SimpleXMLElement;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class XmlDriver extends FileDriver
{
    /**
     * Load metadata for a multidomain class
     *
     * @param string $className
     * @param mixed  $config
     *
     * @throws \Exception
     * @return MultidomainMetadata|null
     */
    protected function loadMultidomainMetadata($className, $config)
    {
        if (!$config) {
            return;
        }

        $xml = new SimpleXMLElement($config);

        $xml->registerXPathNamespace('shopsys', 'shopsys');

        $nodeList = $xml->xpath('//shopsys:multidomain');
        if (0 == count($nodeList)) {
            return;
        }

        if (1 < count($nodeList)) {
            throw new \Exception('Configuration defined twice');
        }

        $node = $nodeList[0];

        $classMetadata = new MultidomainMetadata($className);

        $multidomainField = (string)$node['field'];

        $propertyMetadata = new PropertyMetadata(
            $className,
            // defaults to multidomain
            !empty($multidomainField) ? $multidomainField : 'domains'
        );

        // default targetEntity
        $targetEntity = $className . 'Domain';

        $multidomainTargetEntity = (string)$node['target-entity'];
        $classMetadata->targetEntity = !empty($multidomainTargetEntity) ? $multidomainTargetEntity : $targetEntity;
        $classMetadata->domains = $propertyMetadata;
        $classMetadata->addPropertyMetadata($propertyMetadata);

        $currentDomainId = (string)$node['current-domainId'];
        if (!empty($currentDomainId)) {
            $propertyMetadata = new PropertyMetadata($className, $currentDomainId);

            $classMetadata->currentDomainId = $propertyMetadata;
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        $fallbackDomainId = (string)$node['fallback-domainId'];
        if (!empty($fallbackDomainId)) {
            $propertyMetadata = new PropertyMetadata($className, $fallbackDomainId);

            $classMetadata->fallbackDomainId = $propertyMetadata;
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        return $classMetadata;
    }

    /**
     * Load metadata for a domain class
     *
     * @param string $className
     * @param mixed  $config
     *
     * @throws \Exception
     * @return DomainMetadata|null
     */
    protected function loadDomainMetadata($className, $config)
    {
        if (!$config) {
            return;
        }

        $xml = new SimpleXMLElement($config);
        $xml->registerXPathNamespace('shopsys', 'shopsys');
        $nodeList = $xml->xpath('//shopsys:multidomain');

        if (0 == count($nodeList)) {
            return;
        }

        if (1 < count($nodeList)) {
            throw new \Exception('Configuration defined twice');
        }

        $nodeMultidomain = $nodeList[0];

        $multidomainField = (string)$nodeMultidomain['field'];

        $multidomainTargetEntity = (string)$nodeMultidomain['target-entity'];

        $domainId = (string)(string)$nodeMultidomain['domainId'];

        $classMetadata = new DomainMetadata($className);

        $propertyMetadata = new PropertyMetadata(
            $className,
            // defaults to multidomain
            !empty($multidomainField) ? $multidomainField : 'multidomain'
        );

        $targetEntity = 'Domain' === substr($className, -11) ? substr($className, 0, -11) : null;

        $classMetadata->targetEntity = !empty($multidomainTargetEntity) ? $multidomainTargetEntity : $targetEntity;
        $classMetadata->multidomain = $propertyMetadata;
        $classMetadata->addPropertyMetadata($propertyMetadata);

        if ($domainId) {
            $propertyMetadata = new PropertyMetadata($className, $domainId);

            $classMetadata->domainId = $propertyMetadata;
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        // Set to default if no domainId property has been set
        if (!$classMetadata->domainId) {
            $propertyMetadata = new PropertyMetadata($className, 'domainId');

            $classMetadata->domainId = $propertyMetadata;
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        return $classMetadata;
    }

    /**
     * Parses the given mapping file.
     *
     * @param string $file
     *
     * @return mixed
     */
    protected function parse($file)
    {
        return file_get_contents($file);
    }
}
