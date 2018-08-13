<?= "<?php" ?>

namespace <?= $namespace ?>;

<?php if($generate_admin_type) {?>
use App\Form\<?= $entity_class ?>Type;
<?php } ?>
use App\Entity\<?= $entity_class ?>;
use Idk\LegoBundle\Configurator\AbstractDoctrineORMConfigurator;
use Idk\LegoBundle\Component as CPNT;
/**
 * The LEGO configurator for <?= $entity_class ?>
 */
class <?= $entity_class ?>Configurator extends AbstractDoctrineORMConfigurator
{

    const ENTITY_CLASS_NAME = <?= $entity_class ?>::class;
    const TITLE = 'Gestion des <?= strtolower($entity_class) ?>s';

    public function buildIndex()
    {
        $this->addIndexComponent(CPNT\Action::class, ['actions' => [CPNT\Action::ADD]]);
        $this->addIndexComponent(CPNT\Filter::class, []);
        $this->addIndexComponent(CPNT\ListItems::class, [
            'entity_actions' => [CPNT\ListItems::ENTITY_ACTION_EDIT, CPNT\ListItems::ENTITY_ACTION_DELETE],
            'bulk_actions' => [CPNT\ListItems::BULK_ACTION_DELETE]
        ]);

        $this->addAddComponent(CPNT\Action::class, ['actions' => [CPNT\Action::BACK]]);
        $this->addAddComponent(CPNT\Form::class, [<?php if ($generate_admin_type){?>'form' => <?= $entity_class ?>Type::class<?php } ?>]);

        $this->addEditComponent(CPNT\Action::class, ['actions' => [CPNT\Action::BACK]]);
        $this->addEditComponent(CPNT\Form::class, [<?php if( $generate_admin_type){?>'form' => <?= $entity_class ?>Type::class<?php } ?>]);

        $this->addShowComponent(CPNT\Action::class, ['actions' => [CPNT\Action::BACK]]);
        $this->addShowComponent(CPNT\Item::class, []);
    }

    static public function getControllerPath()
    {
        return 'app_<?= strtolower($controller_route) ?>lego';
    }
}
