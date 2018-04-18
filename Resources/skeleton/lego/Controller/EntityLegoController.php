<?= "<?php" ?>

namespace <?= $namespace; ?>;


use App\Configurator\<?= $entity_class ?>Configurator as Configurator;
use Idk\LegoBundle\Controller\LegoController;
use Idk\LegoBundle\Traits\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * The LEGO controller for <?= $entity_class ?><?= "\n" ?>
 * @Route("/<?= strtolower($entity_class) ?>")
 */
class <?= $entity_class ?>LegoController extends LegoController
{

    use Controller;

    const LEGO_CONFIGURATOR = Configurator::class;

}
