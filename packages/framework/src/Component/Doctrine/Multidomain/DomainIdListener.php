<?php

/*
 * (c) Prezent Internet B.V. <info@prezent.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shopsys\FrameworkBundle\Component\Doctrine\Multidomain;

use Shopsys\FrameworkBundle\Component\Doctrine\Multidomain\EventListener\MultidomainListener;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Inject current locale in the MultidomainListener
 *
 * @see EventSubscriberInterface
 */
class DomainIdListener implements EventSubscriberInterface
{
    /**
     * @var MultidomainListener
     */
    private $multidomainListener;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(MultidomainListener $multidomainListener, Domain $domain)
    {
        $this->multidomainListener = $multidomainListener;
        $this->domain = $domain;
    }

    /**
     * Set request locale
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // Request::getBasePath() never contains script file name (/index.php)
        $url = $request->getSchemeAndHttpHost() . $request->getBasePath();

        foreach ($this->domain->getAll() as $domainConfig) {
            if ($domainConfig->getUrl() === $url) {
                $this->multidomainListener->setCurrentDomainId($domainConfig->getId());
                return;
            }
        }
    }

    /**
     * @param \Symfony\Component\Console\Event\ConsoleCommandEvent $event
     */
    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $this->multidomainListener->setCurrentDomainId(1);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 10]],
            ConsoleEvents::COMMAND => [['onConsoleCommand', 10]],
        ];
    }
}
