<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Security;

use Symfony\Component\Security\Core\User\AdvancedUserInterface as BaseAdvancedUserInterface;

interface AdvancedUserInterface extends BaseAdvancedUserInterface
{
    /**
     * @param \DateTime $lastActivity
     */
    public function setLastActivity(\DateTime $lastActivity): void;
}
