<?php

namespace Shopsys\FrameworkBundle\DemoData;

use Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Category\CategoryDataFactory;
use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;
use Symfony\Component\EventDispatcher\EventDispatcher;

class CategoryDemoDataFixture implements DemoDataFixtureInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Article\ArticleFacade
     */
    private $categoryFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade
     */
    private $persistentReferenceFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Article\ArticleDataFactory
     */
    private $categoryDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    private $eventDispatcher;

    public function __construct(
        CategoryFacade $categoryFacade,
        PersistentReferenceFacade $persistentReferenceFacade,
        CategoryDataFactory $categoryDataFactory,
        Domain $domain,
        EventDispatcher $eventDispatcher
    ) {
        $this->categoryFacade = $categoryFacade;
        $this->persistentReferenceFacade = $persistentReferenceFacade;
        $this->categoryDataFactory = $categoryDataFactory;
        $this->domain = $domain;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string[] $tags
     * @param string|null $referenceName
     */
    public function createEntity(array $tags = [], string $referenceName = null)
    {
        $data = $this->categoryDataFactory->createDefault();
        $event = new DemoDataPreSaveEvent($data, $tags, $referenceName);
        $this->eventDispatcher->dispatch(DemoDataPreSaveEvent::NAME, $event);
        if ($event->isEntityCreatable()) {
            $entity = $this->categoryFacade->create($data);
            if ($referenceName !== null) {
                $this->persistentReferenceFacade->persistReference($referenceName, $entity);
            }
            $event = new DemoDataPostSaveEvent($entity, $tags, $referenceName);
            $this->eventDispatcher->dispatch(DemoDataPostSaveEvent::NAME, $event);
        }
    }

    public function load(): void
    {
        // using some kind of a builder with a create and save closures
        $builder = new DemoDataBuilder(
            function () {
                return $this->categoryDataFactory->createDefault();
            },
            function ($data) {
                return $this->categoryFacade->create($data);
            }
        );

        $builder->tag('electronics')
            ->addNamed('category_parent')
            ->tag('child')
                ->add(6);
        $builder->tag('not_electronics')
            ->add(4);
        $builder->create();

        // or without the builder
        $this->createEntity([], 'category_parent');
        for ($i = 0; $i < 4; $i++) {
            $this->createEntity();
        }
        for ($i = 0; $i < 6; $i++) {
            $this->createEntity('child');
        }
    }

    public function getDependencies(): array
    {
        return [];
    }
}
