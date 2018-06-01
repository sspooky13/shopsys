<?php

namespace Shopsys\FrameworkBundle\Command;

use Shopsys\FrameworkBundle\DemoData\DemoDataRegistry;
use Shopsys\FrameworkBundle\Model\Category\CategoryData;
use Shopsys\FrameworkBundle\Model\Category\CategoryDataFactory;
use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;
use Shopsys\FrameworkBundle\Model\Transport\TransportEditData;
use Shopsys\FrameworkBundle\Model\Transport\TransportEditDataFactory;
use Shopsys\FrameworkBundle\Model\Transport\TransportFacade;
use Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategory;
use Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryData;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DemoDataPrototype2Command extends ContainerAwareCommand
{
    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:demo-data-prototype2';

    /**
     * @var \Shopsys\FrameworkBundle\DemoData\DemoDataRegistry
     */
    private $demoDataRegistry;

    public function __construct(DemoDataRegistry $demoDataRegistry)
    {
        $this->demoDataRegistry = $demoDataRegistry;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $loader = new DataFixtureLoader();

        $loader->registerEntityCreator(TransportEditData::class, function (TransportEditData $data) use ($container) {
            return $container->get(TransportFacade::class)->create($data);
        });
        $loader->registerEntityCreator(CategoryData::class, function (CategoryData $data) use ($container) {
            return $container->get(CategoryFacade::class)->create($data);
        });
        $loader->registerEntityCreator(HeurekaCategoryData::class, function (HeurekaCategoryData $data) use ($container) {
            $em = $container->get('doctrine.orm.default_entity_manager');
            $heurekaCategory = new HeurekaCategory($data);
            $em->persist($heurekaCategory);
            $em->flush();

            return $heurekaCategory;
        });

        $loader->registerDataFixture(new TransportDataFixture($this->getContainer()->get(TransportEditDataFactory::class)));
        $loader->registerDataFixture(new CategoryDataFixture($this->getContainer()->get(CategoryDataFactory::class)));
        $loader->registerDataFixture(new HeurekaCategoryDataFixture());

        $loader->load();
    }
}
