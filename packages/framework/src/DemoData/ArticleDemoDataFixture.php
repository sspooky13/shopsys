<?php

namespace Shopsys\FrameworkBundle\DemoData;

use Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Article\Article;
use Shopsys\FrameworkBundle\Model\Article\ArticleData;
use Shopsys\FrameworkBundle\Model\Article\ArticleDataFactory;
use Shopsys\FrameworkBundle\Model\Article\ArticleFacade;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ArticleDemoDataFixture implements DemoDataFixtureInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Article\ArticleFacade
     */
    private $articleFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade
     */
    private $persistentReferenceFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Article\ArticleDataFactory
     */
    private $articleDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    private $eventDispatcher;

    public function __construct(
        ArticleFacade $articleFacade,
        PersistentReferenceFacade $persistentReferenceFacade,
        ArticleDataFactory $articleDataFactory,
        Domain $domain,
        EventDispatcher $eventDispatcher
    ) {
        $this->articleFacade = $articleFacade;
        $this->persistentReferenceFacade = $persistentReferenceFacade;
        $this->articleDataFactory = $articleDataFactory;
        $this->domain = $domain;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     */
    public function createEntity(DomainConfig $domainConfig, array $tags = [], string $referenceName = null)
    {
        $data = $this->articleDataFactory->createDefault();
        $event = new DomainSpecificDemoDataPreSaveEvent($domainConfig, $data, $tags, $referenceName);
        $this->eventDispatcher->dispatch(DemoDataPreSaveEvent::NAME, $event);
        if ($event->isEntityCreatable()) {
            $entity = $this->articleFacade->create($data);
            if ($referenceName !== null) {
                $this->persistentReferenceFacade->persistReference($referenceName, $entity);
            }
            $event = new DomainSpecificDemoDataPostSaveEvent($domainConfig, $entity, $tags, $referenceName);
            $this->eventDispatcher->dispatch(DemoDataPostSaveEvent::NAME, $event);
        }
    }

    public function load(): void
    {
        $builder = new DemoDataBuilder(
            function () {
                return $this->articleDataFactory->createDefault();
            },
            function ($data) {
                return $this->articleFacade->create($data);
            }
        );

        foreach ($this->domain->getAll() as $domainConfig) {
            $domainBuilder = $builder->setDomain($domainConfig);
            $domainBuilder->tag('top_menu')
                ->add(2);
            $domainBuilder->tag('footer')
                ->add()
                ->addNamed('article_terms_and_conditions_' . $domainConfig->getId());
            $domainBuilder
                ->addNamed('article_privacy_policy_' . $domainConfig->getId())
                ->addNamed('article_cookies_' . $domainConfig->getId());
        }

        $builder->create();

        foreach ($this->domain->getAll() as $domainConfig) {
            $this->createEntity($domainConfig, 'top_menu');
            $this->createEntity($domainConfig, 'top_menu');
            $this->createEntity($domainConfig, 'footer');
            $this->createEntity($domainConfig, 'footer', 'article_terms_and_conditions_' . $domainConfig->getId());
            $this->createEntity($domainConfig, [], 'article_privacy_policy_' . $domainConfig->getId());
            $this->createEntity($domainConfig, [], 'article_cookies_' . $domainConfig->getId());

            // Maybe set properties directly here?
            $this->createEntity(['position' => Article::PLACEMENT_TOP_MENU, 'domainId' => $domainConfig->getId()]);
        }
    }

    public function getDependencies(): array
    {
        return [];
    }
}
