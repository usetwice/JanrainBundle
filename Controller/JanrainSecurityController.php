<?php

namespace Evario\JanrainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/secure")
 */
class JanrainSecurityController extends Controller
{

  /**
   * Janrain check login action
   * @Route("/janrain-check", name="janrain.check", options={"expose"=true})
   */
  public function janrain_checkAction()
  {

  }
}