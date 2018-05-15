<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Annotation as Shopsys;
use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Entity\MultidomainTrait;
use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\MultidomainInterface;
use Shopsys\FrameworkBundle\Model\Localization\AbstractTranslatableEntity;

/**
 * @ORM\Table(name="brands")
 * @ORM\Entity
 */
class Brand extends AbstractTranslatableEntity implements MultidomainInterface
{
    use MultidomainTrait;

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
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain[]
     *
     * @Shopsys\Domains(targetEntity="Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain")
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
        foreach ($brandData->seoTitles as $domainId => $seoTitle) {
            $brandDomain = $this->domain($domainId);
            /* @var $brandDomain \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain */
            $brandDomain->setSeoTitle($seoTitle);
        }
        foreach ($brandData->seoH1s as $domainId => $seoH1) {
            $brandDomain = $this->domain($domainId);
            /* @var $brandDomain \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain */
            $brandDomain->setSeoH1($seoH1);
        }
        foreach ($brandData->seoMetaDescriptions as $domainId => $seoMetaDescription) {
            $brandDomain = $this->domain($domainId);
            /* @var $brandDomain \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain */
            $brandDomain->setSeoMetaDescription($seoMetaDescription);
        }
        $this->domain();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain
     */
    protected function createDomain()
    {
        return new BrandDomain();
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
