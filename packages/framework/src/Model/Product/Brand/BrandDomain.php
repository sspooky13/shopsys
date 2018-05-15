<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Annotation as Shopsys;
use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Entity\AbstractDomain;

/**
 * @ORM\Table(name="brand_domains")
 * @ORM\Entity
 */
class BrandDomain extends AbstractDomain
{
    /**
     * @Shopsys\Multidomain(targetEntity="Shopsys\FrameworkBundle\Model\Product\Brand\Brand")
     */
    protected $multidomain;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $seoTitle;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $seoMetaDescription;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $seoH1;

    /**
     * @return string|null
     */
    public function getSeoTitle()
    {
        return $this->seoTitle;
    }

    /**
     * @param string|null $seoTitle
     */
    public function setSeoTitle($seoTitle)
    {
        $this->seoTitle = $seoTitle;
    }

    /**
     * @return string|null
     */
    public function getSeoMetaDescription()
    {
        return $this->seoMetaDescription;
    }

    /**
     * @param string|null $seoMetaDescription
     */
    public function setSeoMetaDescription($seoMetaDescription)
    {
        $this->seoMetaDescription = $seoMetaDescription;
    }

    /**
     * @return string|null
     */
    public function getSeoH1()
    {
        return $this->seoH1;
    }

    /**
     * @param string|null $seoH1
     */
    public function setSeoH1($seoH1)
    {
        $this->seoH1 = $seoH1;
    }
}
