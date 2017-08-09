# LleAdminListBundle

## Configuration

dans le fichier app/config/routing.yml

``` yaml
lle_adminlist:
    resource: "@LleAdminListBundle/Resources/config/routing.yml"
```

## Utilisation :

``` php
app/console lle:generate:adminlist entity_name
```

Paramètre : Nom de l'entité à utiliser.

Le générateur créer plusieurs fichier :

- un fichier de configuration src/Lle/AdminBundle/AdminList/ImportSecuAdminListConfigurator.php
- un controleur src/Lle/AdemasBundle/Controller/ImportSecuAdminListController.php
- un formulaire src/Lle/AdemasBundle/Form/ImportSecuAdminType.php

Le fichier de routing est automatiquement mis à jour.

## Documentation de base de l'AdminList :

### Permission support

#### AbstractAdminListConfigurator

  * There is a new method called 'getPermissionDefinition()' (and a matching setter 'setPermissionDefinition()')

    This method should return either null or a PermissionDefinition object that will be used in calls (by AdminList)
    to an AclHelper, applying ACL constraints you want to impose. When you return null (the default return value),
    no restrictions will be applied.

#### AdminList

  * There is a new method called 'setAclHelper()' & 'getAclHelper()'

    The setter method will allow you to set an AclHelper to be used to apply ACL constraints. If it is not set,
    no restrictions will be imposed, even if a PermissionDefinition was set (and vice versa).

### Create your own AdminList

#### Using a Generator

The [LleGeneratorBundle](https://github.com/Kunstmaan/KunstmaanGeneratorBundle) offers a generator to generate an AdminList for your entity. It will generate the required classes and settings based on your Entity class.

For more information, see the AdminList generator [documentation](https://github.com/Lle/KunstmaanGeneratorBundle/blob/master/Resources/doc/GeneratorBundle.md#adminlist).

#### Manually

Below you will find a how-to on how to create your own AdminList for your Entity manually. We also offer an [AdminList Generator](https://github.com/Lle/KunstmaanGeneratorBundle/blob/master/Resources/doc/GeneratorBundle.md#adminlist) to do this for you in the [KunstmaanGeneratorBundle](https://github.com/Kunstmaan/KunstmaanGeneratorBundle).

You will need to create 3 classes. An AdminListConfigurator, AdminListController and an AdminType. Let's assume you have already created an Entity called Document (with fields Title, Type and Reviewed) located in your Entity folder and its corresponding Repository.

##### Classes

###### Configurator

As its name implies the configurator will configure the listed fields and filters in your AdminList.

Create your DocumentAdminListConfigurator class in the AdminList folder in your Bundle and import your Entity class and the [FilterTypes](#adminlist-filters) you want to use to filter your AdminList.

``` php
use Your\Bundle\Entity\Document;
use Lle\AdminListBundle\AdminList\FilterType\ORM\BooleanFilterType;
use Lle\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;

class DocumentAdminListConfigurator extends AbstractSettingsAdminListConfigurator
{
```

Next we add the buildFilters() method and supply the fields we want to filter on. Our document has a title, type and a boolean telling us the document has been reviewed.

The first parameter of addFilter() method is the fieldname, the second parameter is the FilterType you want to use to filter this field with. The last parameter is the label for the filter.


``` php
    public function buildFilters()
    {
        $this->addFilter('title', new StringFilterType('title'), 'Title');
        $this->addFilter('type', new StringFilterType('type'), 'Type');
        $this->addFilter('reviewed', new BooleanFilterType('reviewed'), 'Reviewed');
    }
```

The buildFields() method will allow you to configure which fields will be displayed in the list and is independent from the form used to edit your Entity.

The first parameter of the addField() method is the fieldname, second one is the column header and the last parameter you see here allows you to enable sorting for this field.

``` php
    public function buildFields()
    {
        $this->addField('title', 'Title', true);
        $this->addField('type', 'Type', true);
        $this->addField('reviewed', 'Reviewed', false);
    }
```

And at last we add our Entity name

``` php
    public function getEntityName()
    {
        return 'Document';
    }
}
```

###### Controller

The controller will allow you to list, add, edit and delete your Entity. There's also a method to export the list of entities.

Create your DocumentAdminListController in your Controller folder and import your Entity class and the FilterTypes you want to use to filter your AdminList with.

``` php
use Your\Bundle\Form\DocumentType;
use Your\Bundle\AdminList\DocumentAdminListConfigurator;

use Lle\AdminListBundle\Controller\AdminListController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DocumentAdminController extends AdminListController
{
    /**
     * @var AdminListConfiguratorInterface
     */
    private $configurator;

    /**
     * @return AdminListConfiguratorInterface
     */
    public function getAdminListConfigurator()
    {
        if (!isset($this->configurator)) {
            $this->configurator = new DocumentAdminListConfigurator($this->getEntityManager());
        }
        return $this->configurator;
    }
```

The first method will simply list your Entities.

``` php
    /**
     * @Route("/", name="yourbundle_admin_document")
     * @Template("LleAdminListBundle:Default:list.html.twig")
     */
    public function indexAction(Request $request)
    {
        return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }
```

The add action method will build the form to add a new entity.

``` php
    /**
     * The add action
     *
     * @Route("/add", name="yourbundle_admin_document_add")
     * @Method({"GET", "POST"})
     * @Template("LleAdminListBundle:Default:add_or_edit.html.twig")
     * @return array
     */
    public function addAction(Request $request)
    {
        return parent::doAddAction($this->getAdminListConfigurator(), $request);
    }
```

The edit action method will build and process the edit form.

``` php
    /**
     * @param $id
     *
     * @throws NotFoundHttpException
     * @internal param $eid
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="yourbundle_admin_document_edit")
     * @Method({"GET", "POST"})
     * @Template("LleAdminListBundle:Default:add_or_edit.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        return parent::doEditAction($this->getAdminListConfigurator(), $id, $request);
    }
```

The delete action will handle the deletion of your Entity.

``` php
    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws NotFoundHttpException
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="yourbundle_admin_document_delete")
     * @Method({"GET", "POST"})
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }
```

To export your Entities, there's the export action method.

``` php
    /**
     * @Route("/export.{_format}", requirements={"_format" = "csv"}, name="yourbundle_document_export")
     * @Method({"GET", "POST"})
     *
     * @param $_format
     *
     * @return array
     */
    public function exportAction(Request $request, $_format) {
        $em = $this->getEntityManager();
        return parent::doExportAction(new DocumentAdminListConfigurator($em), $_format, $request);
    }
}
```

###### Form

The form Type class will create the form for the Entity when adding or editing one.

``` php
 use Symfony\Component\Form\AbstractType;
 use Symfony\Component\Form\FormBuilderInterface;

 class DocumentType extends AbstractType
 {
 ```

Add your fields to the buildForm() method to add them to the add and edit form.

The add method's first parameter is the fieldname, the second one is the [field type](http://symfony.com/doc/2.0/reference/forms/types.html) and at last an array of additional options.

``` php
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array("required" => true));
        $builder->add('type', 'text', array("required" => true));
        $builder->add('reviewed', 'checkbox', array("required" => false));
    }
```

And include the following methods.

``` php
    public function getName()
    {
        return 'document';
    }
}
```

##### Routing

Add the following lines to your routing.yml.

``` yaml
YourBundle_documents:
    resource: "@YourBundle/Controller/DocumentAdminController.php"
    type: annotation
    prefix: /{_locale}/admin/documents
    requirements:
        _locale: %requiredlocales%
```

### AdminList Filters

The AdminList has by default several filters : String, Boolean, Date and Number, Entity, Tree, Many, AutoComplete

## Documentation de la couche 2le :

### Services 2le

### Widgets 2le

#### lle_date
#### lle_number
#### lle_auto_completion
#### lle_dependent_select

Lle Dependent Select :
- Adminlist :
```php
$builder->add('mammographe', 'lle_dependent_select', array('label'=>'Mammographe','route'=>'lleademasbundle_admin_mammographe_dependent_combobox','class'=>'LleAdemasBundle:Mammographe','dependent'=>'radiologue'));
```
- Controller :
```php
/**
     * The dependent select action
     *
     * @Route("/dcb", name="lleademasbundle_admin_mammographe_dependent_combobox")
     */
    public function DependentComboboxAction(Request $request)
    {
        $id = $request->request->get('id');
        if(!$id) return new JsonResponse();
        $em = $this->getEntityManager();
        $medecin = $em->getRepository('LleAdemasBundle:Medecin')->find($id);
        $default = $medecin->getMammographeDefaut();
        $cabinet = $medecin->getCabinet();
        $machines = array();
        $return = array();
        if($cabinet) $machines = $cabinet->getMammographes();
        foreach($machines as $machine){
            if ($machine->getActif()) {
                $selected = ($default && $default->getId() == $machine->getId());
                $return[] = array('id'=>$machine->getId(),'nom'=>(string)$machine,'selected'=>$selected);
            }
        }
        return new JsonResponse($return);
    }
```

Ajouter le parametre route et la classe

```php
array('route'=>'lleademasbundle_admin_medecin_auto_complete','class'=>'LleAdemasBundle:Medecin')); (voir autocompletefilterType)
```

Autocomplete ne prend que l'information "route" mais la route \*\_admin\_\*\_auto_complete est cree dans l'adminlist (ici bien).  
Le repository de bien peu avoir une methode "autocomplete($term)" ou "autoCompleteQuery" avec \_\_toString().  
Si  ces methodes n'existent pas, la recherche sera effectuée sur le champ retourné par getAutocompleteField du configurator qui retourne le champ le plus probable si vous ne le surchargez pas vous même. (c'est peux etre trop magique ;-)).  
Dans ce cas \_\_toString n'est pas necessaire.

* Par autoCompleteQuery (BienRepository)

``` php
public function autoCompleteQuery($term){
    return $this->createQueryBuilder('b')->where('b.libelle LIKE :term')->setParameter('term', '%'.$term.'%')->getQuery();
}
```
* Par autoComplete

``` php
public function autocomplete($term){
    $entites = $this->createQueryBuilder('b')->where('b.libelle LIKE :term')->setParameter('term', '%'.$term.'%')->getQuery()->getResult();
    foreach($entites as $entity){
      $return[] = array('label'=>$entity->getNom() . ' ' . $entity->getPrenom() . ' de '.$entity->getSociete()->getNom(),'value'=>$entity->getId());
    }
    return $return;
}
```
* Par getAutocompleteField (BienAdminListConfigurator)

``` php
public function getAutocompleteField(){
    return 'libelle';
}
```
* Ou avec aucune methode: Par exemple libelle sera systématiquement trouvé s'il existe comme étant le champ Autocomplete.  
Dans ce cas , retourner 'libelle' dans getAutoCompleteField est inutile.

Les valeurs:

1. Dans le cas d'une recherche avec autoComplete: vous definissez vous même avec label et value vos valeurs
2. Dans le cas d'une recherche avec autoCompleteQuery: la value est l'id et le label est __toString()
3. Dans le cas d'une surcharge getAutocomplteField: le label  est get(getAutocomplteField()) et la value l'id
4. Sinon le label est le plus probable et la value l'id

> DANS LA MAJORITE DES CAS VOUS N'AUREZ CAS METTRE LA ROUTE.

#### lle_markdown
Editeur hallo et conversion HTML en markdow
Exemple Type de champ pour formulaire :

``` php
$builder->add('mandat_md', 'lle_markdown');
```

Inclure dans layout :
``` html
    <script src="/bundles/lleadminlist/js/hallo/hallo.js"></script>
    <script src="/bundles/lleadminlist/js/hallo/toMarkdown.js"></script>
    <script src="/bundles/lleadminlist/js/hallo/showdown.js"></script>
    <script src="/bundles/lleadminlist/js/hallo/adminlist.js"></script>    
```    

### Configuration de l'AdminList

#### Field

##### Groupe show
$this->addShowGroup(4);
cree un col-md-4 pour tous les prochain showField
``` php
$this->addShowGroup(4);

$this->addShowField('adresse1', array('edit_in_place'=>true));
$this->addShowField('typeBien',array('edit_in_place'=>array('class'=>'LleAdminBundle:TypeBien')));
$this->addShowField('description', array('edit_in_place'=>true));
$this->addShowField('descriptionCourte', array('edit_in_place'=>true));

$this->addShowGroup(4);

$this->addShowField('virtuel', array('edit_in_place'=> true));
$this->addShowField('proprietaire', array('edit_in_place'=>array('class'=>'LleAdminBundle:Contact')));
$this->addShowGroup(4);
$this->addShowField('surface',array('edit_in_place'=>true));
$this->addShowField('valeurestimee',array('edit_in_place'=>true));
```

##### editinplace
Required: <script src="/bundles/lleadminlist/js/adminlist.js"></script>
3 edit in place sont supporté (bool,object,text)
le generateur trouvera automatiquement si il doit utiliser bool ou text avec cette configuration:
``` php
$this->addShowField('valeurestimee',array('edit_in_place'=>true));
$this->addField('valeurestimee',array('edit_in_place'=>true,'reload'=>'tr')); //l'option tr indique de recharger toute la ligne
```
pour utiliser l'edit_in_place combobox(object):
``` php
$this->addShowField('typeBien',array('edit_in_place'=>array('class'=>'LleAdminBundle:TypeBien')));
```
dans doEditInPlaceAction il y a 4 parametres:
 -class: la class (type repository)
 -id: id de l'entite edité
 -columnName: champ de l'entite
 -value : nouvelle valeur
Pour que l'edit in place reste fonctionelle dans twig avec surcharge (pour le champ "etat"):
 -pour un showField: {{ show_value(adminlist,'etat',item) }}
 -pour un field : {{ value(adminlist,'etat',item) }}
 Ces deux fonctions appel finalement la même fonction en transformant 'etat' en line (object Field issus de showFields[] ou fields[])
{{ render_field_value(adminlist,line,item) }}

Il existe aussi les fonction row et show_row qui affiche en plus le libelle.
Il est possible de desactiver l'affichage automatique avec auto_display = false.

??? Diference entre showField et Field:
C'est simple dans votre configuratorAdminList vous avez deux methode buildFields et showFields avec des appel addShowFields et addFields
si un champ est en edit in place avec un addFields un {{ show_value() }} l'affichera normalement mais un {{ value() }} en edit in place. En faite un champ est configurer de maniere different pour la vue show et la vue list(index) et separer dans deux array distinct show_field et field
Tres utile si vous voulez que les champ soit en edit in place sur l'index mais normal dans le show.

> ATTENTION: si vous utilisé {{ show_value(adminlist,'etat',item) }} et que etat n'est pas configurer dans votre configurator dans addShowField() le champ sera crée et afficher avec une configuration par defaut.

Amélioration futur
``` php
array('reload'=>'callback','callback'=>'myfunctionJS')
```
Le reload callback est utile pour des rechargment specifique et ne demenderais pas beacuoup de temps a etre implementé.

##### Personalisation des td dans la liste:
Il existe le template:

``` php
$this->addField('icon', array('template'=>'LleAdminBundle:Bien:_icon.html.twig','label'=>'Icon'))
```

Mais pour eviter d'avoir plus de fichier vous pouvez aussi utilisé la toute nouvelle valeur "custom":

``` php
$this->addField('icon',array('custom'=>'<img src="{{ item.iconSrc }}" alt="icon bien"/>'));
```

vous avez dans custom 3 variables: label item et value

##### link _to
Cree un lien sur un td de l'admin list:
link_to = self cree un lien sur lui même (objet de l'adminlist et de la ligne cliqué)
link_to = array('route'=>'ici',params'=>array('id')) cree un lien vers la route ici avec comme parametre id de l'objet de la celulle
link_to = ici cree un lien sur la route "ici" avec comme parametre l'id de l'objet de la ligne

Exemple:
``` php
$this->addField('proprietaire', array('label'=>'Propriétaire','link_to'=>array('route'=>'lleadminbundle_admin_contact_show','params'=>array('id'))));
$this->addField('proprietaire', array('label'=>'Propriétaire','link_to'=>'self'));
$this->addField('proprietaire', array('label'=>'Propriétaire','link_to'=>'lle_site_homepage'));
```

#### Filters

##### PeriodeFilter

``` php
$choicesPeriode = array(
    array('table'=>'LleAlefBundle:Periode'),
    array('table'=>'LleAlefBundle:Semaine','from'=>'getFrom','to'=>'getTo','label'=>'Semaine','methode'=>'findAll') //default search
);
$choicesperiode est optionelles.
$this->addMainFilter('Periode', new ORM\PeriodeFilterType('debut', array('choices'=>$choicesPeriode)));
```

##### ManyFilter (obsolete) utilisé EntityFilter avec field (entity.id)

Fichiers requis:

``` html
    <script src="/bundles/lleadminlist/js/select2.js"></script>
    <link rel="stylesheet" href="/bundles/lleadminlist/css/select2.css" />
```

Le filtre Many n'integre pas encore l'option multiple (le travail est commencé mais la query ne le gere pas  - manque de cas concret)

Exemple:

``` php
$this->addFilter('quartier', new ORM\TreeFilterType('quartier',array('table'=>'LleAdminBundle:Quartier','em'=>$this->em,'multiple'=>true)), 'Localisation');
$this->addFilter('proprietaire', new ORM\EntityFilterType('proprietaire',array('em'=>$this->em,'table'=>'LleAdminBundle:Contact','method'=>'findAll')),'Proprietaire');
$this->addFilter('auteur', new lib\ManyFilterType('auteur',array('join'=>'auteurs','em'=>$this->em,'table'=>'LleAurmBundle:Auteur','methode'=>'findAllOrderByAlpha')),'Auteur');
$this->addMainFilter('bien', new ORM\AutoCompleteFilterType('mandat.bien',array('route'=>'lleadminbundle_admin_bien_auto_complete')), 'Bien');
```

ManyFilter ou EntityFilter ?
ManyFilter est obsolete depuis que les jointures sont automatique

##### Filter Hidden
Si FilterBuilder::isHidden == true le filtre est cacher la methode isHidden de FilterBuilder verifie que tous les FilterType ajouté avec addFilter dans le configuator soit isHidden() = true

``` php
$this->addFilter('LotInvitation', new ORM\EntityFilterType('lotInvitation',array('em'=>$this->em,'table'=>'LleAdemasBundle:LotInvitation','method'=>'findAll','hidden'=>true)),'Lot Invitation', array(), true);
```

Pour cela il suffit d epasser hidden=>true dans $config.

pour pointer sur un module avec des donnés filtre dans l'url:
``` php
$this->addItemAction('Voir', array('callback'=>function($item){
            return array('path'=>'lleademasbundle_admin_lotpatiente','params'=>array(
                'filter_value_LotInvitation_main'=>$item->getId(),
                'filter_uniquefilterid[]'=> 'LotInvitation',
                'filter_columnname[]'=> 'LotInvitation',
            ));
}, 'see');
```

#### Template a afficher après une édition

Par défault le template chargé après avoir cliqué sur modifier dans un formulaire est l'index avec
la liste des entities, vous pouvez surcharger la méthode getTemplateAfterEdit() et lui retourner
le template 'show'.

``` php
public function getTemplateAfterEdit()
{
    return 'show';
}
```

#### sublist

Genere une sous liste dans la page show
Surcharge showSubList() dans votre configurator:
public function showSubLists(){
    $this->addShowSubList('Lle\AdminBundle\AdminList\MandatAdminListConfigurator',array('key'=>'bien','title'=>'Offres rattachées au bien'));
}
Dans doShow la methode generateSublist va etre apellé et generer la variable subadminlist
les methode appeler sur "configurator" (ici MandatAdminListConfigurator) sont:
    -  setParentConfig($config)
        avec $config('item'=>$entity,'key'=>'bien')
        vous pouvez appeler getParentConfig pour retrouver ces données

    - subQuery(QueryBuilder $queryBuilder)
        elle ajoute le where pour liée la sublist a l'objet show avec getparentConfig
        $queryBuilder->andWhere('b.'.$parentConfig['key'].' = :parent_item')->setParameter('parent_item',$parentConfig['item'])
        Il est donc possible en surchargent cette methode de personalisé la liaison (list-sublist) comme vous voulez.
Pour afficher une sublist dans twig:
{{ sub_adminlist_widget(subadminlists[0],adminlist,item) }}
il est possible de changer la clef de tableau avec l'option name
Recuperer variable sublist[] dans controller: $subAdminlists = $configurator->generateSubLists($this,$entity);

il est possible d'envoyer une requete à votre sublist sur la query "sublist_{{ sublist.name }}"

FONCTIONNEMENT INTERNE:
ajout au configurateur d'objet Sublist
dans le ss conttroler show appel de generateShowSublist
dans le configurateur de niveau doctrine boucle sur les sublists et hydrate leur vue et leur subConfigurateur avec setparentConfig puis la requet
dans twig appel show sublist avec sub_adminlist_widget
la query de l'adminlist appel subQuery qui adapte la query avec parentConfig le reste fonctionne normalement

#### onglet automatique
``` php
public function showOnglets(){
    $this->addShowOnglet('Attributs',array('route'=>'lleadminbundle_admin_bien_attributs','params'=>array('id'=>'getId')));
    $this->addShowOnglet('Photos',array('template'=>'LleAdminBundle:Bien:_photos.html.twig')); //template var: adminlist et item
    $this->addShowOnglet('Photos',array('widget'=>'attach_browser'));
}
```
crée un onglet ajax avec cible lleadminbundle_admin_bien_attributs et en params {'id':item.id}
Widget existent: attach_browser, attach_dropzone

#### dialog
Required: <script src="/bundles/lleadminlist/js/adminlist.js"></script>
css jquery ui
la class open-dialog-form ouvre un formulaire et l'envoi à href=""

``` html
<a class="open-dialog-form btn btn-warning"
data-target="#dialog-form"
href="{{ path("lleademasbundle_admin_lotreinvitation_sortir",{"id":row.id}) }}">Integrer/Désintégrer un dossier</a>
<div class="hidden">
    <div  id="dialog-form">
      <form method="post">
          <label for="name">Code Ademas</label>
          <input type="text" name="code" value="">
      </form>
    </div>
</div>
```

### Log
Les logs sont visible sur /admin/log et de façon individuele pour toute entite loggable dans l'adminlist.


#### lle_tabs (systeme d'onglet)
pour faire de l'ajax href doit contenir une URL.
.lle-tabs-v pour vertical
Required: <script src="/bundles/lleadminlist/js/adminlist.js"></script>
<link rel="stylesheet" href="/bundles/lleadminlist/css/style.css">
css jquery ui
``` html
<div class="lle-tabs">
  <ul>
    <li><a href="#tabs-1">Nunc tincidunt</a></li>
    <li><a href="#tabs-2">Proin dolor</a></li>
    <li><a href="#tabs-3">Aenean lacinia</a></li>
  </ul>
  <div id="tabs-1">
    <p>Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.</p>
  </div>
  <div id="tabs-2">
    <p>Morbi tincidunt, dui sit amet facilisis feugiat, odio metus gravida ante, ut pharetra massa metus id nunc. Duis scelerisque molestie turpis. Sed fringilla, massa eget luctus malesuada, metus eros molestie lectus, ut tempus eros massa ut dolor. Aenean aliquet fringilla sem. Suspendisse sed ligula in ligula suscipit aliquam. Praesent in eros vestibulum mi adipiscing adipiscing. Morbi facilisis. Curabitur ornare consequat nunc. Aenean vel metus. Ut posuere viverra nulla. Aliquam erat volutpat. Pellentesque convallis. Maecenas feugiat, tellus pellentesque pretium posuere, felis lorem euismod felis, eu ornare leo nisi vel felis. Mauris consectetur tortor et purus.</p>
  </div>
  <div id="tabs-3">
    <p>Mauris eleifend est et turpis. Duis id erat. Suspendisse potenti. Aliquam vulputate, pede vel vehicula accumsan, mi neque rutrum erat, eu congue orci lorem eget lorem. Vestibulum non ante. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce sodales. Quisque eu urna vel enim commodo pellentesque. Praesent eu risus hendrerit ligula tempus pretium. Curabitur lorem enim, pretium nec, feugiat nec, luctus a, lacus.</p>
    <p>Duis cursus. Maecenas ligula eros, blandit nec, pharetra at, semper at, magna. Nullam ac lacus. Nulla facilisi. Praesent viverra justo vitae neque. Praesent blandit adipiscing velit. Suspendisse potenti. Donec mattis, pede vel pharetra blandit, magna ligula faucibus eros, id euismod lacus dolor eget odio. Nam scelerisque. Donec non libero sed nulla mattis commodo. Ut sagittis. Donec nisi lectus, feugiat porttitor, tempor ac, tempor vitae, pede. Aenean vehicula velit eu tellus interdum rutrum. Maecenas commodo. Pellentesque nec elit. Fusce in lacus. Vivamus a libero vitae lectus hendrerit hendrerit.</p>
  </div>
</div>
```

Si vous crée des onglet dans une page chargé en ajax appelez lle.init_tabs()

#### Attributs
Required:
lle_adminlist:
    resource: "@LleAdminListBundle/Resources/config/routing.yml"
<script src="/bundles/lleadminlist/js/adminlist.js"></script>
<link rel="stylesheet" href="/bundles/lleadminlist/css/style.css">
La gestion des attribut ce fait avec 3 class
la classe mere qui contiens les attributs
la classe de relation qui fait la relation entre mere et attribut et qui donne la valeur
la classe attribut doit etendre de Lle\AdminListBundle\Entity\AbstractAttribut
la classe de relationAttribut doit etendre de Lle\AdminListBundle\Entity\AbstractAttributRelation et relationne l'objet a l'atribut avec des variable nommé $attribut et $item

La classe mere doit contenire la methode getAttributValues() qui retourne un tableau de valeur avec comme clef l'id de l'attribut elle implements
Lle\AdminListBundle\Interface\Iattributable.

``` php
public function getAttributValues(){
        $return = array();
        foreach($this->bienAttributs as $val){
            $return[$val->getAttribut()->getId()] = $val->getValue();
        }
        return $return;
}
```

le configurator de l'adminlist doit definire:
getRepositoryNameAttribut qui retourne la class d'attribut
getRepositoryNameAttributRelation qui retourne la classe de relation
--------------------------------
``` php
public function getRepositoryNameAttribut(){
    return 'LleAdminBundle:Attribut';
}

public function getRepositoryNameAttributRelation(){
    return 'LleAdminBundle:BienAttribut';
}
```
--------------------------------
Dans un formualire le champ widget de attribut s'utilise avec le type lle_attribut_widget
Afficher la valeur d'un attribut: {{ attribut(adminlist, monEntity, attribut) }}

La liste des attribut ce fait avec un adminlist normal
Il faudra just mettre $this->addField('options', array('label'=>'Valeurs','sort'=> true,'attribut_options'=>true));
il est possible d'utilise {{ attribut_options(adminlist, attribut) }} pour afficher les options (editable) mais adminlist doit etre l'admin list de la class d'attribut concerné.
Vous pouvez avoir plusieu clas d'attribut et attributRelation pour des objet different.

### Attachable et Browser onglet widget
required:
routing:
_liip_imagine:
    resource: "@LiipImagineBundle/Resources/config/routing.xml"
css and js:
<link rel="stylesheet" href="/bundles/lleadminlist/css/style.css">
<link rel="stylesheet" href="/bundles/lleadminlist/css/dropzone.css" >
<script src="/bundles/lleadminlist/js/adminlist.js"></script>
<script src="/bundles/lleadminlist/js/attachable/dropzone.js"></script>
bower install dropzone
Dossier:
Le dossier LleAttachable dans web/uploads en chmod 777
Le dossier media dans web en chmod 777

Afficher le browser avec $this->addShowOnglet('Photos',array('widget'=>'attach_browser')); dans la config (voir onglet)
ou n'importe ou avec {{ attach_browser(adminlist,item) }}

Il existe des methode pour mieu comuniquer entre objet et fichier a placer dans l'objet dit attachable:
-getAttachableDirectory(AttachableFile $file)  donne le chemain réel du fichier l'exemple si dessous mais le fichier dans
    web/upoads/LleAttachable/{id}/nom réel du fichier (ATTENTION le chemain virtuelle du fichier ne change pas)
    public function getAttachableDirectory(AttachableFile $file){
            return '/'.$this->id;
    }

-getAttachableNameFile($filename) renome le fichier (par defaut hah md5 sur time() et $filename), la ethode suivant permet de garder le nom originale
    public function getAttachableNameFile($filename){
        return strtolower($filename);
    }

-getAttachableZones() crée un niveau supplementaire avec des zones non persistante (utile pour crée un 1er niveau d'arboressence dynamique).
    public function getAttachableZones(){
        return array('1'=>'Dossier1','2'=>'Dossier2');
    }
    ou avec des objets (implements Lle\AdminListBundle\Interfaces\IzoneAttachable)
    public function getAttachableZones()
    {
        return $this->getTypeBien()->getZones();
    }

-getAttachableWithFolder() retourne un boolean pour activer ou non les folder (par defaut false)
    public function getAttachableWithFolder($zoneCode){
        return true;
    }

-getAttachableData($path) obligatoire pour la syncronisation de lle_attachement si getAttachableDirectory est definie
Permet alors de retrouver l'id de l'objet d'attache et la zone a partire du chemain du fichier qui prend racinde dans Symfony (web/uploads/LleAttachement/ClassBien/bien_58/chambre)
    public function getAttachableData($path,$filename)
    {
        list($web,$uploads,$attach,$class,$id,$zone) = explode('/', $path);
        return array('id'=>str_replace('bien_', '', $id),'zone'=>$zone);
    }

-getAttachableClickableFile($zonecode) return true si les vignette sont clickable et false si non.

Amelioration ??!!!! un browser sans attachement n'existe pas mais le rendre independent d'un item est facile a dev vue qu'il etais comme ça a la base.


TWIG --> attachement()
attachement retourne LleAdminListBundle:AttachableFile[], LleAdminListBundle:AttachableFile ou null
Afficher image twig:
afficher une image de zone "principal" de l'objet row:
{% set pictur = attachement(row,'image','principal','first') %}
{% if pictur %} <img src="{{ pictur.url|imagine_filter('bien_list') }}"/> {% endif %}
recupere plusieur images (10) de la zone album d el'objet row
{% set picturs = attachement(row,'image','album',10) %}
recupere tous les pdf de l'objet row
{% set pdfs = attachement(row,'pdf') %}
recupere tous les fichier de l'objet row
{% set files = attachement(row) %}


Service --> recuperé 5 PDF d'un Bien dans la zone 51 et recuperer leur appercue (tte les option sont .... optionelle ...)
$am = $this->get('lle_attachement_manager');
$am->setItem($this->item->getBien());
$pdfs = $am->get(array('type'=>'pdf','zone'=>'51','limit'=>5));
$imgs = array();
foreach($pdfs as $pdf){
    $imgs[] = $am->miniaturiser($pdf);
}

!!! Miniaturiser() ne gere que les PDF pour le moment sinon il retourne void


### Formulaire
Dans le configurateur il existe 7 methodes pour gérer les formulaires:

- formFields, newFormFields et editFormFields
    avec $this->addNewFormField('nom') et $this->addNewFormGroup(6) vous pouvez créer des colonne et choisire l'ordre des champs. dans le formulaire d'ajout (new) et d'edition (edit).
    Il est possible de modifier les deux formulaire en même temps dans formFields avec $this->addFormField() et $this->addFormGroup()
    !!! ATTENTION tous les champs du formulaire serons afficher même si il ne sont pas dans la config newFormFields pour retirer ou ajouter des champs voyer votre formulaire ...Type.php
    *add(New|Edit)FormFields(array('name','adresse')); pour ajouter x champ en une ligne.

- getEditFormTheme
    modifie le theme du formulaire d'edition

- getNewFormTheme
    modifie le theme du formulaire d'ajout
    public function getNewFormTheme(){
        return 'LleAdminListBundle:Form:lle_base_fields.html.twig';
    }

- setEditAdminType, setNewAdminType et setAdminType
    dans le constructeur de votre configurator definissez votre formulaire pour l'edition, l'ajout ou les deux.

exemple:
``` php
public function formFields(){
        $this->addFormGroup(4);
        $this->addFormFields(array(
            'proprietaire',
            'typeBien',
            'libelle',
            'agence',
        ));
}
```

### Workflow
Dans la configuration ajouter la methode getFieldWorkflow()
``` php
public function getFieldWorkflow(){
        return 'etat';
}
```
ou
``` php
public function getFieldWorkflow(){
        return 'etat.libelle';
}

Dans le controller ajouter la méthode workflowAction(CLASSNAME $entity) si elle n'existe pas encore
        ```````````````````````````````````````````````````````````````
/**
 * The workflow action
 *
 *
 * @Route("/ajax/workflow/{id}", name="llealefbundle_admin_contrat_wf")
 * @Method({"GET", "POST"})
 * @return array
 */
public function workflowAction(Contrat $entity)
{
    return parent::doWorkflowAction($this->getAdminListConfigurator(), $entity);
}

Dans le security ajouter le role "ROLE_ADMIN_CONTRAT_WF"
        ````````````````````````````````````````````````

le workflow est sur getEtat et on retrouve un etat a partire de son libelle (par defaut).

L'entité qui sert de workflow (Status, Etat) doit implementé Lle\AdminListBundle\Interfaces\Iworkflowable et ces methode

``` php
public function __toString(){
    return $this->libelle;
}

public function getTransitionsPossible($objet){
    $e = $objet->getEtat()->getLibelle();
    if($e == 'Brouillon'){
        return array('Généré');
    }
    elseif($e == 'Généré'){
        return array('Brouillon','Signé');
    }
    elseif($e == 'Signé'){
        return array('Généré',' Réceptionné');
    }
    elseif($e == 'Signé'){
        return array('Généré',' Réceptionné');
    }
}

//Valeur par defaut
public function getDefault(){
    return 'Brouillon';
}
```

getCssClass n'est pas obligatoire mais il ajoute la class du workflow a la ligne, si getCssClass n'existe pas c'est 'wf-'.getCode() qui est utilisé sinon rien

``` php
public function getCssClass(){
    return 'etat-'.strtolower(str_replace(array('é',' '),array('e','-'),$this->libelle));
}
```

Il est possible de surcharger la methode classLine($item) dans le configurator qui permet d'ajouter une class Css selon l'entité
pour la surchargé il est recommendé d'utilisé

``` php
public function classLine($item){
    $classes = parent::classLine($item);
    $classes .= ' maClass';
    return $clases;
}
```

Il est possible d'utilisé des Traits pour la methode getTransitionsPossible($objet) ces derniers devrons etre stocker ainssi
Lle\AdminListBundle\Traits\TwfMonTrait
l'utilisation des workflows est souvent specifique les traits sont utile pour des wf redondant et simple.

Enfin le changement d'un workflow genere l'evenement lle.adminlist.workflow_change;
l'objet event recuperer contiens les methodes:
-Iworkflowable getFromWork()
-Iworkflowable getToWork()
-MIXT getItem()
-MIXT getUser()

ATTENTION:
Si vous surcharger la methode public function decorateNewEntity($item) n'oublier pas d'appeler le parent
``` php
public function decorateNewEntity($item){
    $item = parent::decorateNewEntity($item);
}
```


### ACTIONS BULK ITEM et LIST

Deux manières : utiliser un "type" ou utiliser l'option route / route_callback

- Le type "toggle" inverse la valeur du parametre "field" qui doit être un booléen

``` php
public function buildItemActions(){
    $this->addItemAction('valider',array('type'=>'toggle','field'=>'valide','if'=>array('method'=>'getValide','value'=>true)));
}
```

- Appel d'une action via route ou route_callback

``` php
public function buildItemActions(){
    $this->addItemAction('reset',array('route_callback'=>function($item){
        return array('path'=>'lle_reset','params'=>array('id'=>$item->getId()));
    }))
}
```

- Déclenchement d'un item action en mode XHR ( rechargement de ligne de l'objet )

Il faut passer le paramètre xhr=>true dans le configurator

``` php
$this->addItemAction('Copier théorique', array('xhr'=>true,'route'=>'llealefbundle_admin_plage_recopier_theorique_single','if'=>$ifEIP));
```

Et dans l'action (controller):

``` php
return $this->getLineResponse($this->getAdminListConfigurator(),$item);
```

- Le type "change" change la valeur de field par value ou la valeur value de $_POST si elle existe (les setter de type entity sont gérée automatiquement contenté vous d'envoyer un id, vous pouvez couplé change avec choices)

exemple choices 'choices'=> array('id'=>'libelle')

Dans le bulk action vous pouver recuperer de la façon suivante :

``` php
$ids = $this->getRequest()->request->get('ids');
$value = $this->getRequest()->request->get('value');
```
``` php
public function buildListActions(){
    // Exemple avec un champ
    $this->addBulkAction('Valider le réalisé',array('type'=>'change','field'=>'valide','value'=>true));

    // Exemple avec 2 champs
    $this->addBulkAction('Valider le réalisé',array('type'=>'change','field'=>array('valide', 'publie'),'value'=>array(true, false)));
}
```
ATTENTION:
le bulk actions de type 'change' utilise findByIds($ids). Il faut que le repository possède cette méthode.


### INSTALLER datetimepicker

Les widgets lle_datetime et les 'editinplace' de type datetime ne sont possible qu'avec datetimepicker qu'il faut nécessairement installer :

1. ajouter la ligne dans bower.json: "jqueryui-timepicker-addon": "1.5.4",
2. puis dans gruntfile section bower_concat / all.MainFiles ajouter : 'jqueryui-timepicker-addon': ['dist/jquery-ui-timepicker-addon.css','dist/jquery-ui-timepicker-addon.js','dist/i18n/jquery-ui-timepicker-fr.js']

### AJOUTS DE BLOCS HTML

Vous pouvez ajouter des blocs HTML par template ou par controller. La liste des position est disponible dans la class HtmlElement.

``` php
public function buildHtml(){
    $this->addController('after_list','LleAlefBundle:PlageAdminList:ciTotal');
    $this->addController('before_filter','LleAlefBundle:PlageAdminList:ciSemaine');
    $this->addTemplate('bedore_filter', 'LleAlefBundle:Plage:_template.html.twig');
}
```

Que ce soit par template ou controller vous aurez toujours la variable $adminlist
Si la position rend la chose possible vous aurez aussi $item (par exemple la position 'footer_show')

### Filter
Valeur par defaut (defaultFilter)

``` php
public function defaultValueFilter(){
    $date = new \DateTime();
    $date->modify('-1 week');
    $s = $this->em->getRepository('LleAlefBundle:Semaine')->getSemaineByDate($date);
    return array(
        'filter_value_debut' => array('from'=> $s->getLundi()->format('d/m/Y'),'to'  => $s->getDimanche()->format('d/m/Y')),
        'filter_choice_debut'=> array('semaine' => $s->getId()),
    );
}
```

Recuperer valeur (retourne null si non definie)
{% set currentSemaineId = adminlist.valueFilter('filter_choice_debut[semaine]') %} //id
{% set currentSemaine = adminlist.valueFilter('filter_choice_debut') %} //tableau

Faire un lien:
<a href="{{ path('llealefbundle_admin_plage',{
    'filter':true,
    'filter_value_debut[from]':semaine.lundi|date('d/m/Y'),
    'filter_value_debut[to]':semaine.dimanche|date('d/m/Y'),
    'filter_choice_debut[semaine]':semaine.id
    }) }}" class="badge {% if currentSemaineId == semaine.id %} bg-orange {% else %} bg-blue {% endif %}">
    {{ nbPlage }} plage{% if nbPlage > 1 %}s{% endif %} à valider pour la semaine {{ semaine.no }}
</a>
ATTENTION a passer filter:true

### Rupteurs
Ajoute des rupteurs

``` php
public function buildRupteurs()
{
    $this->addRupteur('contrat.id',array('header'=>'<b>Contrat: {{ item.contrat }}</b>'));
    $this->addRupteur('debut',array(
        'header'=>'<b>{{ item.debut|date("d/m/Y") }}</b>',
        'active'=> false,
        'order' => 'DESC',
        'content_order_by'=> array(array('nom','asc')),
        'callback'=>function($item){
            return $item->getDebut()->format('d/m/y');
    }));
}
```

Vous pouvez definir eun template pour header et footer
header aura l'item qui declanche le rupteur {{ item }}
footer aura la liste des items {{ items }}
Faite bien la difference entre content_order by qui ordonne la liste dans un rupteur order qui determine si le rupteur est croissant ou decroissant !
content_order_by a ete cree pour palié labsence d'un getBuildQuery et d ela gestion d'ordre. il est a proscrire d'un nouvelle adminlist
ATTENTION: content_order_by ne dois pas etre utilisé si vous voulez des doubles rupteur
Exemple de liens pour activer un rupteur:

``` php
$this->addListAction('Afficher par contrat', array('position'=>'bottom','route'=>'llealefbundle_admin_plage','params'=>array('rupteurs'=>'debut')));
$this->addListAction('Afficher par date et contrat', array('position'=>'bottom','route'=>'llealefbundle_admin_plage','params'=>array('rupteurs'=>'contrat_id/debut')));
```

ATTENTION: les rupteurs actife cree des orderBy apres l'appel d'adaptQuery et part du principe que vous savez ce que vous faites.
Les appel orderBy sur adaptQuery sont donc a evité si les rupteur sont utilisé. Il existe d'autre methode d'ordonnée (je crois) la liste mais cette partie du bundle est encore d'origine kunstmann.

Le jours ou vous aurez besoin de fair un orderBy + double rupteur call me ;-)
