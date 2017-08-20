<?php
namespace Idk\LegoBundle\Service;


use Symfony\Component\Yaml\Yaml;


class Footer
{

    private $em;
    private $security;

    public function __construct($em, $security) {
        $this->em = $em;
        $this->security = $security;
    }

    public function getTemplate(){
        return 'IdkLegoBundle:Layout:_footer.html.twig';
    }

    public function getVersion(){
        return '0.1 alpha';
    }

    public function getLibelle(){
        return 'Joris Saenger';
    }

}
