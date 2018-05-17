<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180517124030 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('ALTER TABLE brand_domains DROP CONSTRAINT "brand_domains_pkey"');
        $this->sql('ALTER TABLE brand_domains RENAME COLUMN multidomain_id TO brand_id');
        $this->sql('
            ALTER TABLE
                brand_domains
            ADD
                CONSTRAINT FK_6B401AE644F5D008 FOREIGN KEY (brand_id) REFERENCES brands (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->sql('CREATE INDEX IDX_6B401AE644F5D008 ON brand_domains (brand_id)');
        $this->sql('ALTER TABLE brand_domains ADD PRIMARY KEY (brand_id, domain_id)');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
