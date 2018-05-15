<?php

namespace Shopsys\FrameworkBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\AbstractMigration;

class Version20180515142532 extends AbstractMigration
{
    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->sql('ALTER TABLE brand_domains DROP CONSTRAINT "brand_domains_pkey"');
        $this->sql('ALTER TABLE brand_domains ADD id SERIAL NOT NULL');
        $this->sql('ALTER TABLE brand_domains ADD multidomain_id INT NOT NULL DEFAULT 0');
        $this->sql('UPDATE brand_domains SET multidomain_id = brand_id');
        $this->sql('ALTER TABLE brand_domains ALTER multidomain_id DROP DEFAULT');
        $this->sql('
            ALTER TABLE
                brand_domains
            ADD
                CONSTRAINT FK_6B401AE6F4407C2B FOREIGN KEY (multidomain_id) REFERENCES brands (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->sql('CREATE INDEX IDX_6B401AE6F4407C2B ON brand_domains (multidomain_id)');
        $this->sql('CREATE UNIQUE INDEX brand_domains_uniq_domains ON brand_domains (multidomain_id, domain_id)');
        $this->sql('ALTER TABLE brand_domains ADD PRIMARY KEY (id)');
        $this->sql('ALTER TABLE brand_domains DROP brand_id');
    }

    /**
     * @param \Doctrine\DBAL\Schema\Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
