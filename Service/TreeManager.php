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




use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Idk\LegoBundle\Model\LegoTreeInterface;

class TreeManager{

    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }


    public function removeNode(LegoTreeInterface $node){
        $nodesToMove = $this->getQueryBuilder($node)->where('n.right > :rgt')->setParameters(['rgt' => $node->getRight()])->getQuery()->getResult();
        $nodesToDelete = $this->getQueryBuilder($node)->where('n.right <= :rgt')->andWhere('n.left >= lft')->setParameters(['rgt' => $node->getRight(),'lft'=>$node->getLeft()])->getQuery()->getResult();
        $size = $node->getRight() - $node->getLeft() + 1;
        foreach($nodesToMove as $n){
            /* @var LegoTreeInterface $n */
            if($n->getLeft() > $node->getLeft()){
                $n->setLeft($n->getLeft() - $size);
            }
            $n->setRight($n->getRight() - $size);
            $this->em->persist($n);
        }
        foreach($nodesToDelete as $n){
            $this->em->remove($n);
        }
    }

    public function addNode(LegoTreeInterface $node){
        $size = $node->getRight() - $node->getLeft() + 1;
        $nodesToMove = $this->getQueryBuilder($node)->where('n.right > :rgt')->setParameters(['rgt' => $node->getRight()])->getQuery()->getResult();
        foreach($nodesToMove as $n){
            /* @var LegoTreeInterface $n */
            if($n->getLeft() >= $node->getLeft()){
                $n->setLeft($n->getLeft() + $size);
            }
            $n->setRight($n->getRight() + $size);
            $this->em->persist($n);
        }
        $this->em->persist($node);
    }


    //@TODO not work (prototype)
    public function moveAfterNode(LegoTreeInterface $node, LegoTreeInterface $afterNode){
        $nodesToMove = $this->getQueryBuilder($node)->where('n.right > :rgt')->setParameters(['rgt' => $node->getRight()])->getQuery()->getResult();
        $nodesToReplace = $this->getQueryBuilder($node)->where('n.right <= :rgt')->andWhere('n.left >= lft')->orderBy('n.left')->setParameters(['rgt' => $node->getRight(),'lft'=>$node->getLeft()])->getQuery()->getResult();
        $size = $node->getRight() - $node->getLeft() + 1;
        $newRight = $size-1;
        foreach($nodesToMove as $n) {
            /* @var LegoTreeInterface $n */
            if ($n->getLeft() > $node->getLeft()) {
                $n->setLeft($n->getLeft() - $size);
            }
            $n->setRight($n->getRight() - $size);
            $this->em->persist($n);
        }


        $p = $afterNode->getRight() +1;
        $node->setLeft($p);
        $node->setRight($newRight);
        $this->addNode($node);
        foreach($nodesToReplace as $n){
            $size = $n->getRight() - $n->getLeft();
            $n->setLeft($p);
            $n->setRight($p + $size);
            if($size > 1) $p+=1; else $p+=2;
        }
    }

    private function getQueryBuilder(LegoTreeInterface $node, $alias = 'n'): QueryBuilder{
        return $this->em->getRepository(get_class($node))->createQueryBuilder($alias);
    }
}
