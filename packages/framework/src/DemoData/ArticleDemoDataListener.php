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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ArticleDemoDataListener implements EventSubscriberInterface
{
    protected $textIterator = 0;
    protected $nameIteratorByDomainId = [];

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ArticleDemoDataPreSaveEvent::NAME => 'preSave',
        ];
    }

    /**
     * @param \Shopsys\FrameworkBundle\DemoData\ArticleDemoDataPreSaveEvent $event
     */
    public function preSave(ArticleDemoDataPreSaveEvent $event): void
    {
        $data = $event->getEntityData();
        $domainConfig = $event->getDomainConfig();

        $data->name = $this->getName($event);
        $data->placement = $this->getPlacement($event);
        $data->text = $this->getText();
        $data->domainId = $domainConfig->getId();
    }

    /**
     * @param \Shopsys\FrameworkBundle\DemoData\ArticleDemoDataPreSaveEvent $event
     * @return string
     */
    protected function getName(ArticleDemoDataPreSaveEvent $event): string
    {
        $domainConfig = $event->getDomainConfig();
        $domainId = $domainConfig->getId();
        $locale = $domainConfig->getLocale();

        if ($event->isNamedReference('article_terms_and_conditions_' . $domainId)) {
            return t('Terms and conditions', [], null, $locale);
        } elseif ($event->isNamedReference('article_privacy_policy_' . $domainId)) {
            return t('Privacy policy', [], null, $locale);
        } elseif ($event->isNamedReference('article_cookies_' . $domainId)) {
            return t('Cookies information', [], null, $locale);
        }

        if (!array_key_exists($domainId, $this->nameIteratorByDomainId)) {
            $this->nameIteratorByDomainId[$domainId] = 0;
        }

        $names = [
            t('News', [], 'demo', $locale),
            t('Shopping guide', [], 'demo', $locale),
            t('Contact', [], 'demo', $locale),
        ];

        throw new \Exception('Undefined name');
    }

    /**
     * @param \Shopsys\FrameworkBundle\DemoData\ArticleDemoDataPreSaveEvent $event
     * @return string
     */
    protected function getPlacement(ArticleDemoDataPreSaveEvent $event): string
    {
        if ($event->isTaggedAs('top_menu')) {
            return Article::PLACEMENT_TOP_MENU;
        } elseif ($event->isTaggedAs('footer')) {
            return Article::PLACEMENT_FOOTER;
        }

        return Article::PLACEMENT_NONE;
    }

    /**
     * @return string
     */
    protected function getText(): string
    {
        $texts = [
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus felis nisi, tincidunt sollicitudin augue eu, laoreet blandit sem. Donec rutrum augue a elit imperdiet, eu vehicula tortor porta. Vivamus pulvinar sem non auctor dictum. Morbi eleifend semper enim, eu faucibus tortor posuere vitae. Donec tincidunt ipsum ullamcorper nisi accumsan tincidunt. Aenean sed velit massa. Nullam interdum eget est ut convallis. Vestibulum et mauris condimentum, rutrum sem congue, suscipit arcu.\nSed tristique vehicula ipsum, ut vulputate tortor feugiat eu. Vivamus convallis quam vulputate faucibus facilisis. Curabitur tincidunt pulvinar leo, eu dapibus augue lacinia a. Fusce sed tincidunt nunc. Morbi a nisi a odio pharetra laoreet nec eget quam. In in nisl tortor. Ut fringilla vitae lectus eu venenatis. Nullam interdum sed odio a posuere. Fusce pellentesque dui vel tortor blandit, a dictum nunc congue.',
            'Morbi posuere mauris dolor, quis accumsan dolor ullamcorper eget. Phasellus at elementum magna, et pretium neque. Praesent tristique lorem mi, eget varius quam aliquam eget. Vivamus ultrices interdum nisi, sed placerat lectus fermentum non. Phasellus ac quam vitae nisi aliquam vestibulum. Sed rhoncus tortor a arcu sagittis placerat. Nulla lectus nunc, ultrices ac faucibus sed, accumsan nec diam. Nam auctor neque quis tincidunt tempus. Nunc eget risus tristique, lobortis metus vitae, pellentesque leo. Vivamus placerat turpis ac dolor vehicula tincidunt. Sed venenatis, ante id ultrices convallis, lacus elit porttitor dolor, non porta risus ipsum ac justo. Integer id pretium quam, id placerat nulla.',
            'Donec at dolor mi. Nullam ornare, massa in cursus imperdiet, felis nisl auctor ante, vel aliquet tortor lacus sit amet ipsum. Proin ultrices euismod elementum. Integer sodales hendrerit tortor, vel semper turpis interdum eu. Phasellus quam tortor, feugiat vel condimentum vel, tristique et ipsum. Duis blandit lectus in odio cursus rutrum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aliquam pulvinar massa at imperdiet venenatis. Maecenas convallis lobortis quam in fringilla. Mauris gravida turpis eget sapien imperdiet pulvinar. Nunc velit urna, fringilla nec est sit amet, accumsan varius nunc. Morbi sed tincidunt diam, sit amet laoreet nisl. Nulla tempus id lectus non lacinia.\n\nVestibulum interdum adipiscing iaculis. Nunc posuere pharetra velit. Nunc ac ante non massa scelerisque blandit sit amet vel velit. Integer in massa sed augue pulvinar malesuada. Pellentesque laoreet orci augue, in fermentum nisl feugiat ut. Nunc congue et nisi a interdum. Aenean mauris mi, interdum vel lacus et, placerat gravida augue. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Sed sagittis ipsum et consequat euismod. Praesent a ipsum dapibus, aliquet justo a, consectetur magna. Phasellus imperdiet tempor laoreet. Sed a accumsan lacus, accumsan faucibus dolor. Praesent euismod justo quis ipsum aliquam suscipit. Sed quis blandit urna.',
        ];

        return $texts[$this->textIterator++ % count($texts)];
    }
}
