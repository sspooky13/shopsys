<?php

namespace Shopsys\FrameworkBundle\Component\Doctrine\Multidomain;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compile the driverchain
 *
 * @author Sander Marechal
 */
class DriverChainCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $driver = $container->getDefinition('shopsys.multidomain.driver_chain');

        foreach ($container->getParameter('doctrine.entity_managers') as $name => $manager) {
            $adapter = new Definition(
                'Metadata\\Driver\\DriverInterface',
                [
                    new Reference(sprintf('doctrine.orm.%s_metadata_driver', $name)),
                ]
            );

            $class = 'Shopsys\\FrameworkBundle\\Component\\Doctrine\\Multidomain\\Mapping\\Driver\\DoctrineAdapter';
            $method = 'fromMetadataDriver';

            if (method_exists($adapter, 'setFactory')) {
                $adapter->setFactory([$class, $method]);
            } else {
                $adapter->setFactoryClass($class);
                $adapter->setFactoryMethod($method);
            }

            $driver->addMethodCall('addDriver', [$adapter]);
        }
    }
}
