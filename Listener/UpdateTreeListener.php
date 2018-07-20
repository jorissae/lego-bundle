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

use Idk\LegoBundle\Model\LegoTreeInterface;
use Idk\LegoBundle\Service\TreeManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Idk\LegoBundle\Annotation\Entity as Annotation;
use Doctrine\Common\Annotations\AnnotationReader;

class UpdateTreeListener
{
    private $tm;

    public function __construct(TreeManager $tm)
    {
        $this->tm = $tm;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if($entity instanceof LegoTreeInterface) {
            if ($entity->getParent()) {
                $this->tm->setEntityManager($args->getEntityManager());
                $children = $this->tm->getChildren($entity->getParent());
                $last = $children[count($children) - 1];
                $entity->setLeft($last->getRight() + 1);
                $entity->setRight($last->getRight() + 2);
                //$this->tm->addNode($entity);
            } else {
                $entity->setLeft(0);
                $entity->setRight(1);
                $entity->setLevel(0);
            }
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        die('update');
    }

}