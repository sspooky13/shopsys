<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Metadata\PropertyMetadata;
use Shopsys\FrameworkBundle\Component\Domain\Domain;

/**
 * Loads current domain ID for Brands on demand
 *
 * @see EventSubscriber
 */
class BrandDomainIdListener implements EventSubscriber
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Metadata\PropertyMetadata[]
     */
    private $currentDomainIdsMetadata;

    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
        $this->currentDomainIdsMetadata = [
            new PropertyMetadata(Brand::class, 'currentDomainId'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [Events::postLoad];
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        if (!$this->domain->isCurrentDomainKnown()) {
            return;
        }

        $entity = $args->getEntity();

        foreach ($this->currentDomainIdsMetadata as $propertyMetadata) {
            if ($entity instanceof $propertyMetadata->class) {
                $propertyMetadata->setValue($entity, $this->domain->getId());
            }
        }
    }
}
