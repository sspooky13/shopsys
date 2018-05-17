<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopsys\FrameworkBundle\Component\Doctrine\Multidomain;

/**
 * Interface for multidomain entities
 */
interface MultidomainInterface
{
    /**
     * Get all domains
     *
     * @return \Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\DomainInterface[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getDomains();

    /**
     * Add a new domain
     *
     * @param \Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\DomainInterface $domain
     * @return self
     */
    public function addDomain(DomainInterface $domain);

    /**
     * Remove a domain
     *
     * @param \Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\DomainInterface $domain
     * @return self
     */
    public function removeDomain(DomainInterface $domain);
}
