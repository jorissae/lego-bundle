<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Service;


use Couchbase\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Idk\LegoBundle\Annotation\Entity as Annotation;
use Doctrine\Common\Annotations\AnnotationReader;
use Idk\LegoBundle\Configurator\AbstractConfigurator;
use Idk\LegoBundle\Lib\MetaEntity;


class MetaEntityManager implements MetaEntityManagerInterface
{

    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function overrideFieldsBy($className, $fields){
        $columns = [];
        foreach($fields as $k => $field){
            /* @var Annotation\Field $field; */
            $columns[] = $k;
        }
        $originalFields = $this->generateFields($className, $columns);
        foreach($originalFields as $k => $originalField){
            if(isset($fields[$k])) {
                $fields[$k] = $originalField->override($fields[$k]);
            }
        }
        return $fields;
    }

    public function getClassMetaData($classname){
        return $this->em->getClassMetadata($classname);
    }

    public function getAssociationClass($classname, $fieldname){
        if($this->getClassMetaData($classname)->hasAssociation($fieldname)) {
            $mapping = $this->getClassMetaData($classname)->getAssociationMapping($fieldname);
            return $mapping['targetEntity'] ?? null;
        }
        return null;
    }

    public function generateFields($className, array $columns = null, $withoutMethodsFields = false){
        $return = [];
        if(is_array($columns)) {
            foreach ($columns as $column) {
                $return[$column] = new Annotation\Field();
                $return[$column]->setName($column);
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

        if(is_array($columns)) {
            foreach ($columns as $column) {
                $pos = strpos($column, '.');
                if ($pos > 0) {
                    $nameField = substr($column, 0, $pos);
                    $joinColumn = substr($column, $pos + 1);
                    $classTarget = $this->getAssociationClass($className, $nameField);
                    if ($classTarget) {
                        $field = $this->getFieldFromAnnotation($classTarget, $joinColumn);
                        if ($field) {
                            $return[$column] = $field;
                        }
                    }
                }
            }
        }

        if(!$withoutMethodsFields) {
            foreach ($reflectionClass->getMethods() as $k => $m) {
                foreach ($r->getMethodAnnotations($m) as $annotation) {
                    $name = lcfirst(substr($m->getName(), 3));
                    if (get_class($annotation) == Annotation\Field::class and ($columns == null or in_array($name, $columns))) {
                        /* @var Annotation\Field $field */
                        $field = $annotation;
                        $field->setName($name);
                        $return[$name] = $field;
                    }
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

    public function getFieldFromAnnotation($className, $fieldName): Annotation\Field{
        $fields = $this->generateFields($className, [$fieldName]);
        return $fields[$fieldName];
    }

    public function generateFormFields($className, array $columns = null){
        $return = [];

        $fields = $this->generateFields($className,null, true);
        $r = new AnnotationReader();
        $reflectionClass = new \ReflectionClass($className);
        $entityForm = $r->getClassAnnotation($reflectionClass, Annotation\EntityForm::class);
        if($entityForm) {
            foreach ($entityForm->getFields() as $fieldName) {
                $field = new Annotation\Form\FieldForm();
                $field->setField($fields[$fieldName]);
                $return[$fieldName] = $field;
            }
        }else{
            foreach($fields as $field){
                $fieldForm = new Annotation\Form\FieldForm();
                $fieldForm->setField($field);
                $return[$field->getName()] = $fieldForm;
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
        return $return;
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

        $fields = $this->generateFields($className,null, true);
        $r = new AnnotationReader();
        $reflectionClass = new \ReflectionClass($className);
        foreach($reflectionClass->getProperties() as $k => $p) {
            foreach ($r->getPropertyAnnotations($p) as $annotation) {
                $reflectionAnnotationClass = new \ReflectionClass(get_class($annotation));
                if ($reflectionAnnotationClass->isSubclassOf(Annotation\Filter\AbstractFilter::class) and ($columns == null or in_array($p->getName(), $columns))) {
                    /* @var Annotation\Filter\AbstractFilter $filter */
                    $filter = $annotation;
                    $filter->setName($p->getName());
                    if(isset($fields[$p->getName()])) $filter->setField($fields[$p->getName()]);
                    $return[$p->getName()] = $filter;
                }
            }
        }
        return $return;
    }

    public function getMetaDataEntities(): array{
        $return = [];
        foreach($this->em->getMetadataFactory()->getAllMetadata() as $metadata){
            $shortName = $this->getEntityShortName($metadata->getName());
            if($shortName){
                if(key_exists($shortName, $return)){
                    throw new \Exception('
                        The shortname '. $shortName .' of the class '.$metadata->getName().' is already 
                        use in the class '.$return[$shortName]->getName().'. Use class annotation '.Annotation\Entity::class.':
                        @Lego\Entity(name="'.$shortName.'2") for exemple');
                }
                $return[$shortName] = new MetaEntity($shortName, $metadata);
            }
        }
        ksort($return);
        return $return;
    }

    public function getEntityShortName($entityClassName){
        $reflectionClass = new \ReflectionClass($entityClassName);
        $r = new AnnotationReader();
        $annotation = $r->getClassAnnotation($reflectionClass, Annotation\Entity::class);
        if($annotation){
            $shortName = $annotation->getName() ?? strtolower(substr($entityClassName , strrpos($entityClassName, '\\') + 1));
        }
        return $shortName ?? null;
    }

    public function getMetaDataEntity($shortName): MetaEntity{
       return $this->getMetaDataEntities()[$shortName];
    }

    public function getMetaDataEntityByClassName($className): Annotation\Entity{
        $r = new AnnotationReader();
        $reflectionClass = new \ReflectionClass($className);
        return  $r->getClassAnnotation($reflectionClass, Annotation\Entity::class);
    }

    public function getEntityManager(): EntityManagerInterface{
        return $this->em;
    }


}
