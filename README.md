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
Work directly WITH USER LOGIN :-D
                
SF 4.1 and more

IDK LEGO BUNDLE V 0.1 alpha (do not use it in production)

You want an very customable without to see internal code, Stay here !
There is nothing you can't to do.

Build your pages simply by adding configurable components.
Add a filter, add a list, add a form then go

1: composer create-project symfony/website-skeleton empty
2: In framework.yml:
```yaml
freamworks:
  default_locale: fr
  translator:
      fallbacks: ['fr']
  templating:
      engines: ['twig']
```
3: add in composer.json
```json
"repositories": [
        {
            "url": "https://github.com/prestigejo/lego-bundle.git",
            "type": "git"
        }

    ],
    "require": {
        "prestigejo/legobundle": "dev-master",
    }
```
4: If do not use FOSuser create App\Entity\User and in security.yml 
```yaml
security:
    providers:
        main:
            entity: { class: App\Entity\User, property: username }
    encoders:
        App\Entity\User: { id: lego.security.password_encoder}

    firewalls:
        ...
        login:
            pattern: ^/login$
            security: false
        main:
            pattern: ^/
            anonymous: ~
            form_login:
                login_path: idk_lego_security_login
                check_path: idk_lego_security_check
                default_target_path: idk_lego_dashboard
            logout:
                path: /logout
                target: /login
            remember_me:
                secret: "secret"
                lifetime: 2232000

    access_control:
            - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/, role: ROLE_USER }
```
4 bis: With fos_user (or another):
```yaml
idk_lego:
    route_login: fos_user_security_check
```  
5: create user 
```  
php bin/console idk:user:create
```  
or (fosUser)
```  
php bin/console fos:user:create 
```  
7: Create lego page
```  
php bin/console make:lego 
```  
8: composer require server

9: bin/console server:run 0.0.0.0:8000


 ===> For Symfony 4.X

TODO 
- order menu item
- order item in itemList (XHR)
CURRENT v alpha 0.1.2

- Log history [X] other bundle
- Media [X] other bundle
- Mail [ ] other bundle
- Import [ ] other bundle
- Pagination [X]
- Field-route [X]
- Onglet component [X]
- Default LayoutBase [X]
- Custom Bulk Action [X]
- Ressource Action [X]
- Menu auto [X]
- Right Bar [X]
- Sub-Filter and Xhr filter [X]
- Upload File [X]
- EntityAction and ListAction auto [X]
- Rupteur [X]
- Group [ ]
- Form auto [X]
- Call template (ViewParams)[X] all object pass to the view is a ViewParams
- Url object (LegoPath) [X] all the url have to do with LegoPath
- Header and Menu object [X]
- Multi same component (CustomComponent or Multi ActionComponent) [X]
- Skeleton [X] //  SF4 maker [X]
- Reload line with item action [X]
- Move component [X]
- Dashboard [X]
- Widget Systeme [X]
- Check double bindRequest in subComponent [ ]
- ExportField [X]
- Flex [X]
- Doc [ ]
- Roles [X]
- Tree show [X]

v beta

- work without fosuser (optional) [X]
- Gestion ROLE [X]

Symfony 4

-require translator
```yaml
default_locale: fr
    translator:
       fallbacks: ['fr']
```

Next todo:

Opti menu OK
custom routeprefix OK
double config global OK
export by component OK
querybuilder filter list OK
connect or not OK
Check all type Form and branch dateTimeform
(Sub-)Filter OK, 
4eme argument de addComponent ???
retoure sur l bonne page apres edit ou new depuis un esub list.
Multi-widget xhr-widget, Group bs, fos group, Custom action, itemAction and bulkAction

Optimisation todo:

 Use voter ,Filter, Tree, Action, tbody imbrique (breaker), Upload file+, getValue et setValue de Field, refactoring getType de Configurator, style tree, try gridstack, widget config

Bug todo:

- sort component is sort execution component !

Your config.yaml

```yaml
idk_lego:
    skin: skin-yellow
    layout: ::lego.html.twig
    service_menu_class: App\Service\Menu
    service_header_class: App\Service\Header
    service_footer_class: App\Service\Footer
```


Configurateur (optional):
```php
<?php

namespace App\Configurator;


use App\Entity\LiaisonPlayDuration;
use App\Entity\Play;
use Idk\LegoBundle\Configurator\AbstractDoctrineORMConfigurator;
use Idk\LegoBundle\Component as CPNT;

/**
 * The lego config for Play
 */
class PlayConfigurator extends AbstractDoctrineORMConfigurator
{


    public function build(){
        //21 lignes for very complex Administrators items Play
        
        //INDEX with Actions, Filters, Lists, Filters, Lists
        
        $actions = $this->addIndexComponent(CPNT\Action::class, ['actions' => [CPNT\Action::ADD]]);
        $filter = $this->addIndexComponent(CPNT\Filter::class,[]);

        $list = $this->addIndexComponent(CPNT\ListItems::class,  [
            'fields'=> ['name', 'pictur', 'age', 'description'],
            'can_modify_nb_entity_per_page' => true,
            'entity_per_page' => 5,
            'entity_actions' => [CPNT\ListItems::ENTITY_ACTION_EDIT, CPNT\ListItems::ENTITY_ACTION_DELETE]
        ]);
        
        
        $actions->add(CPNT\Action::EXPORT($list, 'xlsx'));
        $actions->add(CPNT\Action::EXPORT($list));
        $filter->addComponent($list);
        
        $filter2 = $this->addIndexComponent(CPNT\Filter::class,[]);
        
        $list->add('nbPlayer',['label'=>'NBJ', 'edit_in_place'=>true]);
        $list->addPredefinedBulkAction(CPNT\ListItems::BULK_ACTION_DELETE);

        $list2 = $this->addIndexComponent(CPNT\ListItems::class,  [
            'fields'=> ['name', 'pictur'],
            'can_modify_nb_entity_per_page' => false,
            'entity_per_page' => 5,
            'dql' => 'b.age = 21',
            'entity_actions' => [CPNT\ListItems::ENTITY_ACTION_EDIT, CPNT\ListItems::ENTITY_ACTION_DELETE]
        ]);
        $list2->addPredefinedBulkAction(CPNT\ListItems::BULK_ACTION_DELETE);
        
        $actions->add(CPNT\Action::EXPORT($list2));
        
        $filter2->addComponent($list2);
        
        //SHOW with Value items, filters of sublist , sublist
        $this->addShowComponent(CPNT\Item::class,[]);

        $filter3 = $this->addShowComponent(CPNT\Filter::class,['fields'=>['nbPlayer']], LiaisonPlayDuration::class);
        $list =  $this->addShowComponent(CPNT\ListItems::class,['fields'=>['duration.duration', 'nbPlayer']], LiaisonPlayDuration::class);
        $filter3->addComponent($list);
        
        
        //NEW with Action back and form
        $this->addAddComponent(CPNT\Action::class, ['actions' => [CPNT\Action::BACK]]);
        $this->addAddComponent(CPNT\Form::class, []);

        //EDIT with Action back and form
        $this->addEditComponent(CPNT\Action::class, ['actions' => [CPNT\Action::BACK]]);
        $this->addEditComponent(CPNT\Form::class, []);
    }


    public function getEntityName()
    {
        return Play::class;
    }

    public function getTitle()
    {
        return 'Gestion des jeux';
    }

    static public function getControllerPath()
    {
        return 'app_backend_playlego';
    }
}
```

Entity
```php
<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Idk\LegoBundle\Annotation\Entity as Lego;

/**
 * Play
 *
 * @Lego\Entity(
 *     config="App\Configurator\PlayConfigurator",
 *     title="Jeu",
 *     permissions={"edit"="ROLE_USER"})
 * @ORM\Table(name="jeu")
 * @ORM\Entity(repositoryClass="App\Repository\PlayRepository")
 * @Lego\EntityExport(fields={"id", "name"})
 */
class Play
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Lego\Field(label="Nom",path="show", edit_in_place=true)
     * @Lego\Filter\StringFilter()
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="nbPlayer", type="integer")
     * @Lego\Field(label="Nombre de joueurs")
     * @Lego\Filter\NumberRangeFilter()
     */
    private $nbPlayer;

    /**
     * @var int
     *
     * @ORM\Column(name="note", type="integer")
     * @Lego\Field(label="Note")
     * @Lego\Form\NoteForm()
     * @Lego\Filter\NumberRangeFilter()
     */
    private $note;

    /**
     * @var int
     *
     * @ORM\Column(name="age", type="integer")
     * @Lego\Field(label="Age", sort=true)
     * @Lego\Filter\NumberRangeFilter()
     */
    private $age;

    /**
     * @var Author
     *
     * @ORM\ManyToMany(targetEntity="Category")
     * @Lego\Form\EntityForm(class="App\Entity\Category", multiple=true)
     * @Lego\Field(label="Catégorie", edit_in_place=false)
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $categories;

    /**
     * @var Editor
     *
     * @ORM\ManyToOne(targetEntity="Editor")
     * @Lego\Field(label="Editeur",  edit_in_place={"reload":"field"}, path={"route":"app_backend_editorlego_show", "params"={"id":"editor.id"}})
     * @ORM\JoinColumn(name="editeur_id", referencedColumnName="id")
     */
    private $editor;

    /**
     * @var string
     *
     * @Lego\File(directory="public/uploads/play")
     * @Lego\Form\FileForm()
     * @Lego\Field(label="Image", image={"directory":"/uploads/play","width":"100px"})
     * @ORM\Column(name="pictur", type="string", nullable=true)
     */
    private $pictur;

    /**
     * @var string
     *
     * @Lego\Field(label="Description")
     * @ORM\Column(name="description", type="string")
     */
    private $description;


    /**
     * @var ArrayCollection
     *
     * @Lego\Field(label="Durées")
     * //@Lego\Form\CollectionForm(entity="App\Entity\LiaisonPlayDuration")
     * @Lego\Form\ManyToManyJoinForm(entity="App\Entity\LiaisonPlayDuration")
     * @ORM\OneToMany(targetEntity="App\Entity\LiaisonPlayDuration",orphanRemoval=true, mappedBy="play", cascade={"persist","remove"})
     */
    private $durations;


```

The controller (optional)
```
<?php

namespace App\Controller\Backend;

use App\Configurator\PlayConfigurator as Configurator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The admin list controller for Jeu
 * @Route("/jeu")
 */
class PlayLegoController extends Controller
{

    use \Idk\LegoBundle\Traits\ControllerTrait;

    const LEGO_CONFIGURATOR = Configurator::class;

}

```

The configurator is set in Controller and Entity

The entity is set in configurator but the Entity know the configurator

This is unless soon with last refactoring:
```php
//In controller
const LEGO_CONFIGURATOR = Configurator::class;

//in configurator
public function getEntityName()
{
    return Play::class;
}
```


Exemple multi config
```php
<?php
/**
 * Editor
 *
 * @ORM\Table(name="editor")
 * @Lego\Entity(title="Editeur",
 *     configs={
 *     {"name":"editor","class":"App\Configurator\EditorConfigurator","title":"Editeur 1"},
 *     {"name":"editor2","class":"App\Configurator\Editor2Configurator","title":"Editeur 2"}})
 * @ORM\Entity(repositoryClass="App\Repository\EditorRepository")
 */
class Editor{}

```


