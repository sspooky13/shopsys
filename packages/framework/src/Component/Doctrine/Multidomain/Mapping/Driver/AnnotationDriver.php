<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Mapping\Driver;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Metadata\Driver\DriverInterface;
use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Mapping\DomainMetadata;
use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Mapping\MultidomainMetadata;
use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Mapping\PropertyMetadata;

/**
 * Load domain metadata from annotations
 */
class AnnotationDriver implements DriverInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * Constructor
     *
     * @param Reader $reader
     * @param ClassMetadataFactory $factory Doctrine's metadata factory
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        if ($class->implementsInterface('Shopsys\\FrameworkBundle\\Component\\Doctrine\\Multidomain\\MultidomainInterface')) {
            return $this->loadMultidomainMetadata($class);
        }

        if ($class->implementsInterface('Shopsys\\FrameworkBundle\\Component\\Doctrine\\Multidomain\\DomainInterface')) {
            return $this->loadDomainMetadata($class);
        }
    }

    /**
     * Load metadata for a multidomain class
     *
     * @param \ReflectionClass $class
     * @return MultidomainMetadata
     */
    private function loadMultidomainMetadata(\ReflectionClass $class)
    {
        $classMetadata = new MultidomainMetadata($class->name);

        foreach ($class->getProperties() as $property) {
            if ($property->class !== $class->name) {
                continue;
            }

            $propertyMetadata = new PropertyMetadata($class->name, $property->getName());

            if ($this->reader->getPropertyAnnotation($property, 'Shopsys\\FrameworkBundle\\Component\\Doctrine\\Multidomain\\Annotation\\CurrentDomainId')) {
                $classMetadata->currentDomainId = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }

            if ($this->reader->getPropertyAnnotation($property, 'Shopsys\\FrameworkBundle\\Component\\Doctrine\\Multidomain\\Annotation\\FallbackDomainId')) {
                $classMetadata->fallbackDomainId = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }

            $annotation = $this->reader->getPropertyAnnotation($property, 'Shopsys\\FrameworkBundle\\Component\\Doctrine\\Multidomain\\Annotation\\Domains');
            if ($annotation) {
                $classMetadata->targetEntity = $annotation->targetEntity;
                $classMetadata->domains = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }
        }

        return $classMetadata;
    }

    /**
     * Load metadata for a domain class
     *
     * @param \ReflectionClass $class
     * @return DomainMetadata
     */
    private function loadDomainMetadata(\ReflectionClass $class)
    {
        $classMetadata = new DomainMetadata($class->name);

        foreach ($class->getProperties() as $property) {
            if ($property->class !== $class->name) {
                continue;
            }

            $propertyMetadata = new PropertyMetadata($class->name, $property->getName());

            $annotation = $this->reader->getPropertyAnnotation($property, 'Shopsys\\FrameworkBundle\\Component\\Doctrine\\Multidomain\\Annotation\\Multidomain');
            if ($annotation) {
                $classMetadata->targetEntity = $annotation->targetEntity;
                $classMetadata->multidomain = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }

            if ($this->reader->getPropertyAnnotation($property, 'Shopsys\\FrameworkBundle\\Component\\Doctrine\\Multidomain\\Annotation\\DomainId')) {
                $classMetadata->domainId = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }
        }

        return $classMetadata;
    }
}
