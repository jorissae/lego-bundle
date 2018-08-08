<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Listener;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Idk\LegoBundle\Annotation\Entity as Annotation;
use Doctrine\Common\Annotations\AnnotationReader;

class UploadFileListener
{
    private $kernelRootPath;

    public function __construct($kernelRootPath)
    {
        $this->kernelRootPath = $kernelRootPath;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }

    private function uploadFile($entity)
    {

        $r = new AnnotationReader();
        $reflectionClass = new \ReflectionClass(get_class($entity));
        foreach($reflectionClass->getProperties() as $k => $p) {
            foreach ($r->getPropertyAnnotations($p) as $annotation) {
                if (get_class($annotation) == Annotation\File::class) {
                    /* @var Annotation\File $field */
                    $getter = 'get'.ucfirst($p->getName());
                    $setter = 'set'.ucfirst($p->getName());
                    $file =  $entity->$getter();
                    if($file instanceof UploadedFile) {
                        $filename = md5(uniqid()).'.'.$file->guessExtension();
                        $file->move($this->kernelRootPath.'/'.$annotation->getDirectory(), $filename);
                        $entity->$setter($filename);
                    }
                }
            }
        }
    }
}