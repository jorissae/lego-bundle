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

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

class Footer implements LegoFooterInterface
{

    private $em;
    private $security;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $security) {
        $this->em = $em;
        $this->security = $security;
    }

    public function getTemplate(){
        return '@IdkLego/Layout/_footer.html.twig';
    }

    public function getVersion(){
        return '0.1 alpha';
    }

    public function getLibelle(){
        return 'Joris Saenger';
    }

}
