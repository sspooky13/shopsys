<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20210921183546 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->sql('ALTER TABLE transport_prices ADD amount NUMERIC(20, 6) NOT NULL DEFAULT 0.000000');
        $this->sql('ALTER TABLE transport_prices ADD currency_id INT NOT NULL DEFAULT 1');
        $this->sql('ALTER TABLE transport_prices ALTER amount DROP DEFAULT');
        $this->sql('ALTER TABLE transport_prices ALTER currency_id DROP DEFAULT');

        $this->sql('COMMENT ON COLUMN transport_prices.amount IS \'(DC2Type:big_numbers_decimal)\'');
        $this->sql('
            ALTER TABLE
                transport_prices
            ADD
                CONSTRAINT FK_573018D038248176 FOREIGN KEY (currency_id) REFERENCES currencies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->sql('CREATE INDEX IDX_573018D038248176 ON transport_prices (currency_id)');

        $this->fillTransportPriceCurrencyAndAmountWithCorrectData();
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema): void
    {
    }

    private function fillTransportPriceCurrencyAndAmountWithCorrectData(): void
    {
        $transportPrices = $this->sql('SELECT transport_id, domain_id, price_with_currency_amount, price_with_currency_currency FROM transport_prices')->fetchAll();
        foreach ($transportPrices as $transportPrice) {
            $currencyId = $this->sql('SELECT id FROM currencies WHERE code = :code', [
                'code' => $transportPrice['price_with_currency_currency'],
            ])->fetchColumn();
            $this->sql(
                'UPDATE transport_prices SET currency_id = :currencyId, amount = :amount WHERE transport_id = :transportId AND domain_id = :domainId',
                [
                    'currencyId' => $currencyId,
                    'amount' => $transportPrice['price_with_currency_amount'],
                    'transportId' => $transportPrice['transport_id'],
                    'domainId' => $transportPrice['domain_id'],
                ]
            );
        }
    }
}
