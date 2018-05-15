<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Annotation as Shopsys;
use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\DomainInterface;
use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\MultidomainInterface;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractDomain implements DomainInterface
{
    /**
     * ID
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

    /**
     * Multidomain model
     *
     * Mapping provided by implementation
     */
    protected $multidomain;

    /**
     * Domain id
     *
     * @ORM\Column(name="domain_id", type="integer")
     * @Shopsys\DomainId
     */
    protected $domainId;

    /**
     * Get the ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the multidomain object
     *
     * @return MultidomainInterface
     */
    public function getMultidomain()
    {
        return $this->multidomain;
    }

    /**
     * Set the multidomain object
     *
     * @param MultidomainInterface $multidomain
     * @return self
     */
    public function setMultidomain(MultidomainInterface $multidomain = null)
    {
        if ($this->multidomain == $multidomain) {
            return $this;
        }

        $old = $this->multidomain;
        $this->multidomain = $multidomain;

        if ($old !== null) {
            $old->removeDomain($this);
        }

        if ($multidomain !== null) {
            $multidomain->addDomain($this);
        }

        return $this;
    }

    /**
     * Get the domainId
     *
     * @return string
     */
    public function getDomainId()
    {
        return $this->domainId;
    }

    /**
     * Set the domainId
     *
     * @param string $domainId
     * @return self
     */
    public function setDomainId($domainId)
    {
        $this->domainId = $domainId;
        return $this;
    }
}
