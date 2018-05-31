<?php

namespace Shopsys\FrameworkBundle\Command;

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

class DemoDataPrototypeCommand extends ContainerAwareCommand
{

    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:demo-data-prototype';

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

class DataFixtureLoader
{
    private $dataFixtures = [];
    private $dataFixtureExtensions = [];
    private $entityCreators = [];

    public function registerDataFixture(DataFixture $dataFixture): void
    {
        $this->dataFixtures[] = $dataFixture;
    }

    public function registerEntityCreator(string $dataObjectClass, callable $entityCreator): void
    {
        $this->entityCreators[$dataObjectClass] = $entityCreator;
    }

    public function load(): void
    {
        foreach ($this->dataFixtures as $dataFixture) {
            $entitiesData = $dataFixture->load();

            foreach ($this->dataFixtureExtensions as $dataFixtureExtension) {
                $entitiesData = $dataFixtureExtension->load($entitiesData);
            }

            foreach ($entitiesData as $dataObject) {
                $this->createEntity($dataObject);
            }
        }
    }

    private function createEntity($dataObject)
    {
        // not a good way because the data object can be extended
        return $this->entityCreators[get_class($dataObject)]($dataObject);
    }
}

interface DataFixture
{
    /**
     * @return object[] data objects
     */
    public function load(): array;
}

interface DataFixtureExtension
{
    /**
     * @param object[] $entitiesData data objects
     * @return object[] data objects
     */
    public function load(array $entitiesData): array;
}

class TransportDataFixture implements DataFixture
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportEditDataFactory
     */
    private $dataFactory;

    public function __construct(TransportEditDataFactory $dataFactory)
    {
        $this->dataFactory = $dataFactory;
    }

    public function load(): array
    {
        $transportData = $this->dataFactory->createDefault();

        $transportData->transportData->name = ['en' => 'Banana transport'];
        $transportData->pricesByCurrencyId = [1 => 100, 2 => 4];

        return [$transportData];
    }
}

class CategoryDataFixture implements DataFixture
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryDataFactory
     */
    private $dataFactory;

    public function __construct(CategoryDataFactory $dataFactory)
    {
        $this->dataFactory = $dataFactory;
    }

    public function load(): array
    {
        $categoryData = $this->dataFactory->createDefault();

        $categoryData->name = ['en' => 'Banana category'];

        return [$categoryData];
    }
}

class HeurekaCategoryDataFixture implements DataFixture
{
    public function load(): array
    {
        $heurekaCategoriesData = [];

        $firsHeurekaCategoryData = new HeurekaCategoryData();
        $firsHeurekaCategoryData->id = 1;
        $firsHeurekaCategoryData->name = 'Autobaterie';
        $firsHeurekaCategoryData->fullName = 'Heureka.cz | Auto-moto | Autodoplňky | Autobaterie';

        $heurekaCategoriesData[] = $firsHeurekaCategoryData;

        $secondHeurekaCategoryData = new HeurekaCategoryData();
        $secondHeurekaCategoryData->id = 2;
        $secondHeurekaCategoryData->name = 'Bublifuky';
        $secondHeurekaCategoryData->fullName = 'Heureka.cz | Dětské zboží | Hračky | Hry na zahradu | Bublifuky';

        $heurekaCategoriesData[] = $secondHeurekaCategoryData;

        $thirdHeurekaCategoryData = new HeurekaCategoryData();
        $thirdHeurekaCategoryData->id = 3;
        $thirdHeurekaCategoryData->name = 'Cukřenky';
        $thirdHeurekaCategoryData->fullName = 'Heureka.cz | Dům a zahrada | Domácnost | Kuchyně | Stolování | Cukřenky';

        $heurekaCategoriesData[] = $thirdHeurekaCategoryData;

        return $heurekaCategoriesData;
    }
}
