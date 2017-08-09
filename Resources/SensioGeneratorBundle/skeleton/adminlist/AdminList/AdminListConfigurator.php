<?php

namespace {{ namespace }}\AdminList;

use Doctrine\ORM\EntityManager;

{% if generate_admin_type %}
use {{ namespace }}\Form\{{ entity_class }}AdminType;
{% endif %}
use Idk\LegoBundle\AdminList\FilterType\ORM;
use Idk\LegoBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Doctrine\ORM\QueryBuilder;

/**
 * The admin list configurator for {{ entity_class }}
 */
class {{ entity_class }}AdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     */
    public function __construct($container)
    {
        parent::__construct($container);
{% if generate_admin_type %}
        $this->setAdminType(new {{ entity_class }}AdminType());
{% endif %}
        /* 
        Ne pas utilisé de fonction 'add*' ici
        Use:
            buildFilters()
            buildFields()
            buildExportFields()
            showFields()
            showSubLists()
            showOnglets()
            editFormFields()
            newFormFields()
            formFields()
            buildItemActions()
            buildListActions()
            buildBulkActions()
            buildRupteurs()
            buildHtml()
        */
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
{% for fieldName, data in fields %}

        $this->addField('{{ fieldName }}', array('label'=>'{{ data.fieldTitle }}','sort'=> true {% if loop.first %} ,'link_to'=>'self'{% endif %}));
{% endfor %}
    }



        /**
     * Configure the visible field in show
     */
    public function showFields()
    {
        $this->addShowGroup(6); //groupe de 6 colonnes (col-md-6)
{% for fieldName, data in fields %}
        $this->addShowField('{{ fieldName }}', array('label'=>'{{ data.fieldTitle }}'));
{% endfor %}
    }


        /**
     * Configure the ordrer and group of form
     */
    public function formFields()
    {
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
{% for fieldName, data in fields %}
        $this->addMainFilter('{{ data.fieldTitle }}', new {{ data.filterType }}('{{ fieldName }}'));
{% endfor %}
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        return '{{ bundle.getName() }}';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return '{{ entity_class }}';
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Gestion des {{ label |lower}}';
    }

    /* //avec variable template = 'show|list|add|edit'
    public function getScriptTemplate(){
      return '{{ bundle.getName() }}:{{ entity_class }}:_script.html.twig';
    }
    */
}
