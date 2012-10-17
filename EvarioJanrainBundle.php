<?php

namespace Evario\JanrainBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Evario\JanrainBundle\DependencyInjection\Security\Factory\EvarioJanrainFactory;

class EvarioJanrainBundle extends Bundle
{
  public function build(ContainerBuilder $container)
  {
    parent::build($container);

    $extension = $container->getExtension('security');
    $extension->addSecurityListenerFactory(new EvarioJanrainFactory());
  }
}
