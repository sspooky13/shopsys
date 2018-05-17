<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Shopsys\FrameworkBundle\Model\Localization\AbstractTranslatableEntity;

/**
 * @ORM\Table(name="brands")
 * @ORM\Entity
 */
class Brand extends AbstractTranslatableEntity
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandTranslation[]
     *
     * @Prezent\Translations(targetEntity="Shopsys\FrameworkBundle\Model\Product\Brand\BrandTranslation")
     */
    protected $translations;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain[]|\Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain", mappedBy="brand", fetch="EXTRA_LAZY")
     */
    protected $domains;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData $brandData
     */
    public function __construct(BrandData $brandData)
    {
        $this->name = $brandData->name;
        $this->translations = new ArrayCollection();
        $this->domains = new ArrayCollection();
        $this->setTranslations($brandData);
        $this->setDomains($brandData);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData $brandData
     */
    public function edit(BrandData $brandData)
    {
        $this->name = $brandData->name;
        $this->setTranslations($brandData);
        $this->setDomains($brandData);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData $brandData
     */
    protected function setTranslations(BrandData $brandData)
    {
        foreach ($brandData->descriptions as $locale => $description) {
            $brandTranslation = $this->translation($locale);
            /* @var $brandTranslation \Shopsys\FrameworkBundle\Model\Product\Brand\BrandTranslation */
            $brandTranslation->setDescription($description);
        }
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandTranslation
     */
    protected function createTranslation()
    {
        return new BrandTranslation();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\BrandData $brandData
     */
    protected function setDomains(BrandData $brandData)
    {
        $domainIds = array_unique(array_merge(
            array_keys($brandData->seoTitles),
            array_keys($brandData->seoH1s),
            array_keys($brandData->seoMetaDescriptions)
        ));
        foreach ($domainIds as $domainId) {
            $brandDomain = $this->domain($domainId);
            $brandDomain->setSeoTitle($brandData->seoTitles[$domainId]);
            $brandDomain->setSeoH1($brandData->seoH1s[$domainId]);
            $brandDomain->setSeoMetaDescription($brandData->seoMetaDescriptions[$domainId]);
        }
    }

    /**
     * @param int|null $domainId
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain
     */
    protected function domain(int $domainId = null)
    {
        if (!$domainId) {
            throw new \Exception('Implicit domain ID not implemented.');
        }

        foreach ($this->domains as $domain) {
            if ($domain->getDomainId() === $domainId) {
                return $domain;
            }
        }

        $domain = new BrandDomain($this, $domainId);
        $this->domains[$domainId] = $domain;

        return $domain;
    }

    /**
     * @param int|null $domainId
     * @return string|null
     */
    public function getSeoTitle(int $domainId = null)
    {
        return $this->domain($domainId)->getSeoTitle();
    }

    /**
     * @param int|null $domainId
     * @return string|null
     */
    public function getSeoMetaDescription(int $domainId = null)
    {
        return $this->domain($domainId)->getSeoMetaDescription();
    }

    /**
     * @param int|null $domainId
     * @return string|null
     */
    public function getSeoH1(int $domainId = null)
    {
        return $this->domain($domainId)->getSeoH1();
    }

    /**
     * @param string $locale
     * @return string
     */
    public function getDescription($locale = null)
    {
        return $this->translation($locale)->getDescription();
    }
}
