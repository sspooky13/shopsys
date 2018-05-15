<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\Annotation;

/**
 * Multidomain annotation
 *
 * This annotation indicates the many-to-one relation to the multidomain.
 *
 * @Annotation
 * @Target("PROPERTY")
 */
class Multidomain
{
    /**
     * @var string
     * @Required
     */
    public $targetEntity;
}
