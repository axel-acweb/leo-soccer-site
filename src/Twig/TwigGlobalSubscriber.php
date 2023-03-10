<?php

namespace App\Twig;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

use App\Service\SysParams;
use App\Services\GroupStagesService;
use Doctrine\ORM\EntityManagerInterface;

class TwigGlobalSubscriber implements EventSubscriberInterface {

    private $twig, $em;

    public function __construct( Environment $twig, EntityManagerInterface $em ) {
        $this->twig = $twig;
        $this->em = $em;
    }

    public function injectGlobalVariables() {
        $this->twig->addGlobal('results', GroupStagesService::canDisplayGroupStages($this->em));
    }

    public static function getSubscribedEvents() {
        return [ KernelEvents::CONTROLLER =>  'injectGlobalVariables' ];
    }

    public function onKernelRequest()
    {
    }
}