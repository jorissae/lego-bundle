<?php

namespace {{ namespace }}\Controller;


use AppBundle\Configurator\{{ entity_class }}Configurator as Configurator;
use Idk\LegoBundle\Controller\LegoController;
use Idk\LegoBundle\Traits\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * The admin list controller for {{ entity_class }}
 * @Route("/{{ entity_class|lower }}")
 */
class {{ entity_class }}LegoController extends LegoController
{

    use Controller;

    const LEGO_CONFIGURATOR = Configurator::class;

}
