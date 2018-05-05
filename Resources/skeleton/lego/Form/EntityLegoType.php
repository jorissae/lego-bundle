<?= "<?php" ?>

namespace <?= $namespace ?>;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

<?php foreach($fields as $field){
if($field->getUseClass()){ echo "use ".$field->getUseClass().";\n"; }
} ?>

/**
 * The type for <?= $entity_class ?>
 */
class <?= $entity_class ?>Type extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$builder->add('name_field',null,[]);
<?php foreach($fields as $field){ ?>
<?php
$options = null;
foreach($field->getOptions() as $k => $o){
    $options .= "'".$k."'" ." => '". $o ."',";
}
$options = substr($options,0, -1);
$options = '['.$options.']';
?>
        $builder->add('<?= $field->getName().'\', '.(($field->getType())? $field->getShortType():'null').', '.$options ?>);
<?php } ?>
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return '<?= strtolower($entity_class) ?>_form';
    }
}
