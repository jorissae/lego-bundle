<?= "<?php" ?>

namespace <?= $namespace; ?>;


use App\Configurator\<?= $entity_class ?>Configurator as Configurator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The LEGO controller for <?= $entity_class ?><?= "\n" ?>
 * @Route("/<?= strtolower($entity_class) ?>")
 */
class <?= $entity_class ?>LegoController extends Controller
{

    <?php foreach($traits as $trait){ ?>use <?= $trait ?>;<?php } ?>

    const LEGO_CONFIGURATOR = Configurator::class;

}
