<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Configurator;

use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Idk\LegoBundle\Annotation\Entity\Field;
use Idk\LegoBundle\Component\Component;
use Idk\LegoBundle\Service\Tag\ComponentChain;
use Symfony\Component\HttpFoundation\Request;
use Idk\LegoBundle\Lib\Path;
use Idk\LegoBundle\Configurator\AbstractDoctrineORMConfigurator;
use Idk\LegoBundle\Component as CPNT;

class DefaultConfigurator extends AbstractDoctrineORMConfigurator
{


    public function buildIndex()
    {
        $this->addIndexComponent(CPNT\Action::class, ['actions' => [CPNT\Action::ADD]]);
        $this->addIndexComponent(CPNT\Filter::class, []);
        $this->addIndexComponent(CPNT\ListItems::class, [
            'entity_actions' => [CPNT\ListItems::ENTITY_ACTION_EDIT, CPNT\ListItems::ENTITY_ACTION_DELETE],
            'bulk_actions' => [CPNT\ListItems::BULK_ACTION_DELETE],
            'fields_exclude' => ['id'],
        ]);

        $this->addAddComponent(CPNT\Action::class, ['actions' => [CPNT\Action::BACK]]);
        $this->addAddComponent(CPNT\Form::class, ['fields_exclude' => ['id']]);

        $this->addEditComponent(CPNT\Action::class, ['actions' => [CPNT\Action::BACK]]);
        $this->addEditComponent(CPNT\Form::class, ['fields_exclude' => ['id']]);

        $this->addShowComponent(CPNT\Action::class, ['actions' => [CPNT\Action::BACK]]);
        $this->addShowComponent(CPNT\Item::class, ['fields_exclude' => ['id']]);
    }
}
