<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Bulk;


use Doctrine\ORM\EntityManagerInterface;
use Idk\LegoBundle\Service\AbstractBulkAction;
use Symfony\Component\HttpFoundation\Request;

class BulkDelete extends AbstractBulkAction
{
    
    private $em;
    
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    public function execute(iterable $entities, Request $request){
        foreach($entities as $entity){
            $this->em->remove($entity);
            $this->i++;
        }
        $this->em->flush();
    }

    public function getSuccess(){
        return ['lego.delete_entities', ['%nb%' => $this->i]];
    }

}
