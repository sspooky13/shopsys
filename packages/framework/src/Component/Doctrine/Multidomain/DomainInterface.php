<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopsys\FrameworkBundle\Component\Doctrine\Multidomain;

/**
 * Interface for domain entities
 */
interface DomainInterface
{
    /**
     * Get the multidomain object
     *
     * @return MultidomainInterface
     */
    public function getMultidomain();

    /**
     * Set the multidomain object
     *
     * @param MultidomainInterface $multidomain
     * @return self
     */
    public function setMultidomain(MultidomainInterface $multidomain = null);

    /**
     * Get the domainId
     *
     * @return string
     */
    public function getDomainId();

    /**
     * Set the domainId
     *
     * @param string $domainId
     * @return self
     */
    public function setDomainId($domainId);
}
