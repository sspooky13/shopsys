<?php

namespace Shopsys\ShopBundle\Model\Feed\Standard;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Model\Category\CategoryFacade;
use Shopsys\ShopBundle\Model\Pricing\Currency\Currency;
use Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\ShopBundle\Model\Product\Collection\ProductCollectionFacade;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\ShopBundle\Model\Product\Product;

class StandardFeedItemFactory
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser
     */
    private $productPriceCalculationForUser;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Collection\ProductCollectionFacade
     */
    private $productCollectionFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculationForUser
     * @param \Shopsys\ShopBundle\Model\Product\Collection\ProductCollectionFacade $productCollectionFacade
     * @param \Shopsys\ShopBundle\Model\Category\CategoryFacade $categoryFacade
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade $currencyFacade
     */
    public function __construct(
        ProductPriceCalculationForUser $productPriceCalculationForUser,
        ProductCollectionFacade $productCollectionFacade,
        CategoryFacade $categoryFacade,
        CurrencyFacade $currencyFacade
    ) {
        $this->productPriceCalculationForUser = $productPriceCalculationForUser;
        $this->productCollectionFacade = $productCollectionFacade;
        $this->categoryFacade = $categoryFacade;
        $this->currencyFacade = $currencyFacade;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $products
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\ShopBundle\Model\Feed\Standard\StandardFeedItem[]
     */
    public function createItems(array $products, DomainConfig $domainConfig)
    {
        $productDomainsByProductId = $this->productCollectionFacade->getProductDomainsIndexedByProductId(
            $products,
            $domainConfig
        );
        $imagesByProductId = $this->productCollectionFacade->getImagesUrlsIndexedByProductId($products, $domainConfig);
        $urlsByProductId = $this->productCollectionFacade->getAbsoluteUrlsIndexedByProductId($products, $domainConfig);
        $paramsByProductIdAndName = $this->productCollectionFacade->getProductParameterValuesIndexedByProductIdAndParameterName(
            $products,
            $domainConfig
        );
        $currency = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($domainConfig->getId());

        $items = [];
        foreach ($products as $product) {
            $mainCategory = $this->categoryFacade->getProductMainCategoryByDomainId($product, $domainConfig->getId());
            $productDomain = $productDomainsByProductId[$product->getId()];

            $items[] = new StandardFeedItem(
                $product->getId(),
                $product->getName($domainConfig->getLocale()),
                $productDomain->getDescription(),
                $urlsByProductId[$product->getId()],
                $imagesByProductId[$product->getId()],
                $this->getProductPrice($product, $domainConfig->getId())->getPriceWithVat(),
                $currency->getCode(),
                $product->getEan(),
                $product->getCalculatedAvailability()->getDispatchTime(),
                $this->getProductManufacturer($product),
                $this->getProductCategoryText($product, $domainConfig),
                $this->getProductParamsIndexedByParamName($product, $paramsByProductIdAndName),
                $product->getPartno(),
                $this->findProductMainVariantId($product),
                $product->isSellingDenied(),
                $mainCategory->getId()
            );
        }

        return $items;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string|null
     */
    private function getProductCategoryText(Product $product, DomainConfig $domainConfig)
    {
        $pathFromRootCategoryToMainCategory = $this->categoryFacade->getCategoryNamesInPathFromRootToProductMainCategoryOnDomain(
            $product,
            $domainConfig
        );

        return implode(' | ', $pathFromRootCategoryToMainCategory);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return string|null
     */
    private function getProductManufacturer(Product $product)
    {
        $manufacturer = null;
        if ($product->getBrand() !== null) {
            $manufacturer = $product->getBrand()->getName();
        }

        return $manufacturer;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param string[][] $paramsByProductIdAndName
     * @return string[]
     */
    private function getProductParamsIndexedByParamName(Product $product, $paramsByProductIdAndName)
    {
        if (array_key_exists($product->getId(), $paramsByProductIdAndName)) {
            $paramsByName = $paramsByProductIdAndName[$product->getId()];
        } else {
            $paramsByName = [];
        }

        return $paramsByName;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param int
     * @return \Shopsys\ShopBundle\Model\Product\Pricing\ProductPrice
     */
    private function getProductPrice(Product $product, $domainId)
    {
        return $this->productPriceCalculationForUser->calculatePriceForUserAndDomainId(
            $product,
            $domainId,
            null
        );
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return int|null
     */
    private function findProductMainVariantId(Product $product)
    {
        if ($product->isVariant()) {
            $mainVariant = $product->getMainVariant();
            return $mainVariant->getId();
        }

        return null;
    }
}
