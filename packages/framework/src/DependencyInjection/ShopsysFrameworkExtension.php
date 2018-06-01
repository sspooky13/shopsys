<?php

namespace Shopsys\FrameworkBundle\DependencyInjection;

use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\EventListener\MultidomainListener;
use Shopsys\FrameworkBundle\Component\Environment\EnvironmentType;
use Shopsys\FrameworkBundle\Component\Grid\InlineEdit\GridInlineEditInterface;
use Shopsys\FrameworkBundle\DemoData\DemoDataFixtureInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ShopsysFrameworkExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('paths.yml');

        if ($container->getParameter('kernel.environment') === EnvironmentType::TEST) {
            $loader->load('services_test.yml');
        }

        $container->getDefinition(MultidomainListener::class)
            ->addMethodCall('setCurrentDomainId', [1])
            ->addMethodCall('setFallbackDomainId', [1]);

        $container->registerForAutoconfiguration(GridInlineEditInterface::class)
            ->addTag('shopsys.grid_inline_edit');
        $container->registerForAutoconfiguration(DemoDataFixtureInterface::class)
            ->addTag('shopsys.demo_data_fixture');
    }
}
