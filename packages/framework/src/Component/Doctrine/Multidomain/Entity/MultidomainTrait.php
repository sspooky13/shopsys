<?php

namespace Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Entity;

use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Annotation as Shopsys;
use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\DomainInterface;

trait MultidomainTrait
{
    /**
     * Copied and replaced from @see \Shopsys\FrameworkBundle\Model\Localization\AbstractTranslatableEntity
     *
     * @Shopsys\DomainId()
     */
    protected $currentDomainId;

    /**
     * Copied and replaced from @see \Shopsys\FrameworkBundle\Model\Localization\AbstractTranslatableEntity
     *
     * @var \Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\DomainInterface
     */
    protected $currentDomain;

    /**
     * Get the domains
     *
     * @return \Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\DomainInterface[]|\Doctrine\Common\Collections\ArrayCollection
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

    /**
     * Copied and replaced from @see \Shopsys\FrameworkBundle\Model\Localization\AbstractTranslatableEntity
     *
     * @return string|null
     */
    protected function getCurrentDomainId()
    {
        return $this->currentDomainId;
    }

    /**
     * Copied and replaced from @see \Shopsys\FrameworkBundle\Model\Localization\AbstractTranslatableEntity
     *
     * @param string $domainId
     * @return \Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\DomainInterface|null
     */
    protected function findDomain($domainId)
    {
        foreach ($this->getDomains() as $domain) {
            if ($domain->getDomainId() === $domainId) {
                return $domain;
            }
        }

        return null;
    }

    /**
     * Copied and replaced from @see \Shopsys\FrameworkBundle\Model\Localization\AbstractTranslatableEntity
     *
     * @param string|null $domainId
     * @return \Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\DomainInterface
     */
    protected function domain($domainId = null)
    {
        if ($domainId === null) {
            $domainId = $this->getCurrentDomainId();
        }

        if (!$domainId) {
            throw new \Exception('Implicit domain ID not set!');
        }

        if ($this->currentDomain && $this->currentDomain->getDomainId() === $domainId) {
            return $this->currentDomain;
        }

        $domain = $this->findDomain($domainId);
        if ($domain === null) {
            $domain = $this->createDomain();
            $domain->setDomainId($domainId);
            $this->addDomain($domain);
        }

        $this->currentDomain = $domain;
        return $domain;
    }

    /**
     * Copied and replaced from @see \Shopsys\FrameworkBundle\Model\Localization\AbstractTranslatableEntity
     *
     * @return \Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\DomainInterface
     */
    abstract protected function createDomain();
}
