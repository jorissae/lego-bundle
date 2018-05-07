<?php
namespace Idk\LegoBundle\Service;


use Couchbase\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Idk\LegoBundle\Annotation\Entity as Annotation;
use Doctrine\Common\Annotations\AnnotationReader;
use Idk\LegoBundle\Lib\MetaEntity;


class MetaEntityManager implements MetaEntityManagerInterface
{

    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function generateFields($className, array $columns = null, $withoutMethodsFields = false){
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

    public function getField($className, $fieldName): Annotation\Field{
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
        $r = new AnnotationReader();
        $return = [];
        foreach($this->em->getMetadataFactory()->getAllMetadata() as $metadata){
            $reflectionClass = new \ReflectionClass($metadata->getName());
            $annotation = $r->getClassAnnotation($reflectionClass, Annotation\Entity::class);
            if($annotation){
                $shortName = $annotation->getName() ?? strtolower(substr($metadata->getName() , strrpos($metadata->getName(), '\\') + 1));
                if(key_exists($shortName, $return)){
                    throw new \Exception('
                        The shortname '. $shortName .' of the class '.$metadata->getName().' is already 
                        use in the class '.$return[$shortName]->getName().'. Use class annotation '.Annotation\Entity::class.':
                        @Lego\Entity(name="'.$shortName.'2") for exemple');
                }
                $return[$shortName] = new MetaEntity($shortName, $metadata, $annotation);
            }
        }
        return $return;
    }

    public function getMetaDataEntity($shortName): MetaEntity{
       return $this->getMetaDataEntities()[$shortName];
    }

    public function getEntityManager(): EntityManagerInterface{
        return $this->em;
    }


}
