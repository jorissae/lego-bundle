<?php

namespace {{ namespace }}\Configurator;

{% if generate_admin_type %}
use {{ namespace }}\Form\{{ entity_class }}LegoType;
{% endif %}
use {{ namespace }}\Entity\{{ entity_class }};

use Idk\LegoBundle\Configurator\AbstractDoctrineORMConfigurator;
use Idk\LegoBundle\Component as CPNT;
/**
 * The admin list configurator for {{ entity_class }}
 */
class {{ entity_class }}Configurator extends AbstractDoctrineORMConfigurator
{

    public function buildIndex()
    {
        $this->addIndexComponent(CPNT\Action::class, ['actions' => [CPNT\Action::ADD]]);
        $this->addIndexComponent(CPNT\Filter::class, []);
        $this->addIndexComponent(CPNT\ListItems::class, [
            'entity_actions' => [CPNT\ListItems::ENTITY_ACTION_EDIT, CPNT\ListItems::ENTITY_ACTION_DELETE],
            'bulk_actions' => [CPNT\ListItems::BULK_ACTION_DELETE]
        ]);

        $this->addAddComponent(CPNT\Action::class, ['actions' => [CPNT\Action::BACK]]);
        $this->addAddComponent(CPNT\Form::class, ['form' => {{ entity_class }}LegoType::class]);

        $this->addEditComponent(CPNT\Action::class, ['actions' => [CPNT\Action::BACK]]);
        $this->addEditComponent(CPNT\Form::class, ['form' => {{ entity_class }}LegoType::class]);

        $this->addShowComponent(CPNT\Action::class, ['actions' => [CPNT\Action::BACK]]);
        $this->addShowComponent(CPNT\Item::class, []);
    }
}
