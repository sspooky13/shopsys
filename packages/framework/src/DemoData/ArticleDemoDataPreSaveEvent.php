<?php

namespace Shopsys\FrameworkBundle\DemoData;

/**
 * @method \Shopsys\FrameworkBundle\Model\Article\ArticleData getEntityData()
 */
class ArticleDemoDataPreSaveEvent extends DomainSpecificDemoDataPreSaveEvent
{
    const NAME = parent::NAME . '.article';
}
