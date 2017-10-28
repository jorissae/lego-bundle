<?php
namespace Idk\LegoBundle\Service;


use Idk\LegoBundle\Annotation\Entity as Annotation;
use Doctrine\Common\Annotations\AnnotationReader;




class MetaEntityManager
{

    private $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function generateFields($className, array $columns = null){
        $return = [];
        if(is_array($columns)) {
            foreach ($columns as $column) {
                $return[$column] = null;
            }
        }
        $r = new AnnotationReader();
        $reflectionClass = new \ReflectionClass($className);
        foreach($reflectionClass->getProperties() as $k => $p) {
            foreach ($r->getPropertyAnnotations($p) as $annotation) {
                if (get_class($annotation) == Annotation\Field::class and ($columns == null or in_array($p->getName(), $columns))) {
                    /* @var Annotation\Field $field */
                    $field = $annotation;
                    $field->setName($p->getName());
                    $return[$p->getName()] = $field;
                }
            }
        }

        $trashcan = [];
        foreach($return as $k => $field){
            if(!$field) $trashcan[] = $k;
        }
        foreach($trashcan as $key) unset($return[$key]);
        return $return;
    }

    public function generateFormFields($className, array $columns = null){
        $return = [];

        $fields = $this->generateFields($className);
        $r = new AnnotationReader();
        $reflectionClass = new \ReflectionClass($className);
        $entityForm = $r->getClassAnnotation($reflectionClass, Annotation\EntityForm::class);
        if($entityForm) {
            foreach ($entityForm->getFields() as $fieldName) {
                $field = new Annotation\Form\FieldForm();
                $field->setField($fields[$fieldName]);
                $return[$fieldName] = $field;
            }
        }
        foreach($reflectionClass->getProperties() as $k => $p) {
            foreach ($r->getPropertyAnnotations($p) as $annotation) {
                $reflectionAnnotationClass = new \ReflectionClass(get_class($annotation));
                if ($reflectionAnnotationClass->isSubclassOf(Annotation\Form\AbstractForm::class) and ($columns == null or in_array($p->getName(), $columns))) {
                    /* @var Annotation\Form\AbstractForm $fieldForm */
                    $fieldForm = $annotation;
                    $fieldForm->setName($p->getName());
                    if(isset($fields[$p->getName()])) $annotation->setField($fields[$p->getName()]);
                    $return[$p->getName()] = $fieldForm;
                }
            }
        }
        if(count($return)){
            return $return;
        }else{
            foreach($fields as $field){
                $fieldForm = new Annotation\Form\FieldForm();
                $fieldForm->setField($field);
                $return[$field->getName()] = $fieldForm;
            }
            return $return;
        }
    }

    public function generateExportFields($className, array $columns = null){
        $return = [];
        $r = new AnnotationReader();
        $reflectionClass = new \ReflectionClass($className);
        $entityExport = $r->getClassAnnotation($reflectionClass, Annotation\EntityExport::class);
        if($entityExport) {
            foreach ($entityExport->getFields() as $fieldName) {
                $field = new Annotation\FieldExport();
                $field->setName($fieldName);
                $return[$fieldName] = $field;
            }
        }
        foreach($reflectionClass->getProperties() as $k => $p) {
            foreach ($r->getPropertyAnnotations($p) as $annotation) {
                if (get_class($annotation) == Annotation\FieldExport::class and ($columns == null or in_array($p->getName(), $columns))) {
                    /* @var Annotation\FieldExport $field */
                    $field = $annotation;
                    $field->setName($p->getName());
                    $return[$p->getName()] = $field;
                }
            }
        }
        return $return;
    }

    public function generateFilters($className, array $columns = null){
        $return = [];
        $r = new AnnotationReader();
        $reflectionClass = new \ReflectionClass($className);
        foreach($reflectionClass->getProperties() as $k => $p) {
            foreach ($r->getPropertyAnnotations($p) as $annotation) {
                $reflectionAnnotationClass = new \ReflectionClass(get_class($annotation));
                if ($reflectionAnnotationClass->isSubclassOf(Annotation\Filter\AbstractFilter::class) and ($columns == null or in_array($p->getName(), $columns))) {
                    /* @var Annotation\Filter\AbstractFilter $filter */
                    $filter = $annotation;
                    $filter->setName($p->getName());
                    $return[$p->getName()] = $filter;
                }
            }
        }
        return $return;
    }


}
