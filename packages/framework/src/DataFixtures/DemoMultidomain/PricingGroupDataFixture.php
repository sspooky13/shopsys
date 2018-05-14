<?php

namespace Shopsys\FrameworkBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade;

class PricingGroupDataFixture extends AbstractReferenceFixture
{
    /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade */
    private $pricingGroupFacade;

    const PRICING_GROUP_ORDINARY_DOMAIN_2 = 'pricing_group_ordinary_domain_2';
    const PRICING_GROUP_VIP_DOMAIN_2 = 'pricing_group_vip_domain_2';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    private $setting;

    public function __construct(PricingGroupFacade $pricingGroupFacade, Domain $domain, Setting $setting)
    {
        $this->pricingGroupFacade = $pricingGroupFacade;
        $this->domain = $domain;
        $this->setting = $setting;
    }

    public function load(ObjectManager $manager)
    {
        $this->setting->clearCache();

        $pricingGroupData = new PricingGroupData();

        $pricingGroupData->name = 'Obyčejný zákazník';
        $domainId = 2;
        $this->createPricingGroup($pricingGroupData, $domainId, self::PRICING_GROUP_ORDINARY_DOMAIN_2);

        $pricingGroupData->name = 'VIP zákazník';
        $this->createPricingGroup($pricingGroupData, $domainId, self::PRICING_GROUP_VIP_DOMAIN_2);

        foreach ($this->domain->getAllIds() as $domainId) {
            if ($domainId > 2) {
                $pricingGroupData->name = 'Default';
                $pricingGroup = $this->pricingGroupFacade->create($pricingGroupData, $domainId);
                $this->setting->setForDomain(Setting::DEFAULT_PRICING_GROUP, $pricingGroup->getId(), $domainId);
            }
        }

        $this->setting->clearCache();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
     * @param int $domainId
     * @param string $referenceName
     */
    private function createPricingGroup(
        PricingGroupData $pricingGroupData,
        $domainId,
        $referenceName
    ) {
        $pricingGroup = $this->pricingGroupFacade->create($pricingGroupData, $domainId);
        $this->addReference($referenceName, $pricingGroup);
    }
}
