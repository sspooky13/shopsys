<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Component\Money;

use Doctrine\ORM\Mapping as ORM;
use Litipk\BigNumbers\Decimal;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;

abstract class AbstractMoneyWithCurrency
{
    /**
     * @var \Litipk\BigNumbers\Decimal
     * @ORM\Column(type="big_numbers_decimal", precision=20, scale=6)
     */
    protected Decimal $amount;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency")
     * @ORM\JoinColumn(nullable=false)
     */
    protected Currency $currency;

    /**
     * @return \Shopsys\FrameworkBundle\Component\Money\BetterMoney
     */
    public function getPriceWithCurrency(): BetterMoney
    {
        return new BetterMoney($this->amount, $this->currency);
    }
}
