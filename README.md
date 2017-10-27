```
                            88                                     
                            88                                     
                            88                                     
                            88  ,adPPYba,  ,adPPYb,d8  ,adPPYba,   
                            88 a8P_____88 a8"    `Y88 a8"     "8a  
                            88 8PP""""""" 8b       88 8b       d8  
                            88 "8b,   ,aa "8a,   ,d88 "8a,   ,a8"  
                            88  `"Ybbd8"'  `"YbbdP"Y8  `"YbbdP"'   
                                           aa,    ,88              
                                            "Y8bbdP"        
                            -------------------------------------
                                  Light - Easy - Good - Open 
```                
IDK LEGO BUNDLE V 0.1 alpha

Build your pages simply by adding configurable components.
Add a filter, add a list, add a form then go

 ===> For Symfony 3.X

TODO 

- DOC

CURRENT

- Log history [ ]
- Pagination [X]
- Field-route [X]
- Onglet component [ ]
- Default LayoutBase [X]
- Custom Bulk Action [ ]
- Right Bar [ ]
- Sub-Filter and Xhr filter [ ]
- Upload File [X]
- EntityAction and ListAction auto [ ]
- Rupteur [X]
- Group [ ]
- Form auto [X]
- Call template (ViewParams)[ ]
- Url object (LegoPath) [X]
- Header and Menu object [X]
- Multi component (CustomComponent or Multi ActionComponent) [ ]
- Skeleton [ ]
- Reload line with item action [ ]
- Move component [X]
- Dashboard [ ]
- Macro [ ]
- Widget Systeme [ ]
- Check double bindRequest in subComponent [ ]
- ExportField [X]

Important :

- Check templateField, LegoViewParams

Optimisation :

Pager,  Filter, Action, tbody imbrique, Upload file+

Exemple configurator

```php
<?php

namespace AppBundle\Configurator;

use AppBundle\Entity\Jeu;
use AppBundle\Form\JeuType;

use Idk\LegoBundle\Lib\Actions\BulkAction;
use Idk\LegoBundle\Configurator\AbstractDoctrineORMConfigurator;
use Idk\LegoBundle\Component as CPNT;


class JeuConfigurator extends AbstractDoctrineORMConfigurator
{

    const ENTITY_CLASS_NAME = Jeu::class;
    const TITLE = 'Gestion des jeux';

    public function buildAll(){

        //Index
        $this->addIndexComponent(CPNT\Action::class,['actions'=>[CPNT\Action::ADD, CPNT\Action::EXPORT_CSV, CPNT\Action::EXPORT_XLSX]]);
        $this->addIndexComponent(CPNT\Custom::class, ['src'=>'AppBundle:JeuLego:showid']);
        $this->addIndexComponent(CPNT\Filter::class,[]);
        $showItem = $this->addIndexComponent(CPNT\Item::class,['fields'=> ['editeur' ,'name', 'nbPlayer', 'age']]);
        $showItem->add('editeur.id', ['label'=>'Id editeur']);
        $list = $this->addIndexComponent(CPNT\ListItems::class,  [
            'fields'=> ['id', 'editeur', 'name', 'nbPlayer', 'age'],
            'entity_actions' => [CPNT\ListItems::ENTITY_ACTION_EDIT, CPNT\ListItems::ENTITY_ACTION_DELETE, CPNT\ListItems::ENTITY_ACTION_SHOW],
            'bulk_actions' => [CPNT\ListItems::BULK_ACTION_DELETE, new BulkAction('Mon action', ['choices'=> ['A'=>'Action A', 'B'=>'Action B'], 'route'=>'app_jeulego_bulk'])]
        ]);
        $list->add('editeur.id', ['label'=>'Id editeur']);
        $this->addIndexComponent(CPNT\ListItems::class,[
            'fields'=>['name'],
            'entity_actions' => [CPNT\ListItems::ENTITY_ACTION_EDIT, CPNT\ListItems::ENTITY_ACTION_DELETE],
        ], EditeurConfigurator::class);

        //Add
        $this->addAddComponent(CPNT\Action::class,['actions'=> [CPNT\Action::BACK]]);
        $this->addAddComponent(CPNT\Form::class, ['form' => JeuType::class]);

        //Edit
        $this->addEditComponent(CPNT\Action::class,['actions'=> [CPNT\Action::BACK]]);
        $this->addEditComponent(CPNT\Form::class, ['form' => JeuType::class]);

        //Show
        $this->addShowComponent(CPNT\Action::class,['actions'=> [CPNT\Action::BACK]]);
        $this->addShowComponent(CPNT\Item::class,['fields'=> ['name', 'nbPlayer', 'age']]);
        $this->addShowComponent(CPNT\ListItems::class,[
            'fields'=>['name'],
            'entity_actions' => [CPNT\ListItems::ENTITY_ACTION_EDIT, CPNT\ListItems::ENTITY_ACTION_DELETE],
        ], EditeurConfigurator::class);
    }
}

```

The Entity for exemple
```php
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Idk\LegoBundle\Annotation\Entity as Lego;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Jeu
 *
 * @ORM\Table(name="jeu")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JeuRepository")
 */
class Jeu
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Lego\Field(path="show", twig="jeu_{{ value }}")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     * @Lego\Field(label="Nom", edit_in_place=true, path={"route":"app_jeulego_show", "params"={"id":"id"}})
     * @Lego\Filter\StringFilter()
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="nbPlayer", type="integer")
     * @Assert\NotBlank()
     * @Lego\Field(label="Nombre de joueur")
     * @Lego\Filter\NumberRangeFilter(label="Nombre de joueur")
     */
    private $nbPlayer;

    /**
     * @var int
     *
     * @ORM\Column(name="age", type="integer")
     * @Assert\NotBlank()
     * @Lego\Field(label="Age")
     * @Lego\Filter\NumberRangeFilter()
     */
    private $age;

    /**
     * @var Editeur
     *
     * @ORM\ManyToOne(targetEntity="Editeur")
     * @Lego\Field(label="Editeur",  path={"route":"app_editeurlego_show", "params"={"id":"editeur.id"}})
     * @ORM\JoinColumn(name="editeur_id", referencedColumnName="id")
     */
    private $editeur;
    
    /* ... */
 ?>
 ```
