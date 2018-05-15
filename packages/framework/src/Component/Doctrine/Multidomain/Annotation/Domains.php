<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Annotation;

/**
 * Domains annotation
 *
 * This annotation indicates the one-to-many relation to the domains.
 *
 * @Annotation
 * @Target("PROPERTY")
 */
class Domains
{
    /**
     * @var string
     * @Required
     */
    public $targetEntity;
}
