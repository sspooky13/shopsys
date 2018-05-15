<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\DomainInterface;
use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\MultidomainInterface;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractMultidomain implements MultidomainInterface
{
    /**
     * ID
     *
     * Mapping provided by implementation
     */
    protected $id;

    /**
     * Domains
     *
     * Mapping provided by implementation
     */
    protected $domains;

    /**
     * Get the domains
     *
     * @return ArrayCollection
     */
    public function getDomains()
    {
        return $this->domains;
    }

    /**
     * Add a domain
     *
     * @param DomainInterface $domain
     * @return self
     */
    public function addDomain(DomainInterface $domain)
    {
        if (!$this->domains->contains($domain)) {
            $this->domains[$domain->getDomainId()] = $domain;
            $domain->setMultidomain($this);
        }

        return $this;
    }

    /**
     * Remove a domain
     *
     * @param DomainInterface $domain
     * @return self
     */
    public function removeDomain(DomainInterface $domain)
    {
        if ($this->domains->removeElement($domain)) {
            $domain->setMultidomain(null);
        }

        return $this;
    }
}
