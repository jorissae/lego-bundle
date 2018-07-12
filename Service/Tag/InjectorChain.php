<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Service\Tag;


use Idk\LegoBundle\Service\LegoInjectorInterface;

class InjectorChain
{
    private $injectors = [];

    const CONTROLLER = 'controller';

    public function __construct(iterable $injectors)
    {
        foreach($injectors as $injector){
            $this->injectors[] = $injector;
        }
    }

    public function addInjector(LegoInjectorInterface $injector)
    {
        $this->injectors[] = $injector;
    }


    public function getInjectors():iterable{
        return $this->injectors;
    }

    public function getControllerTraits(){
        $traits = [];
        foreach($this->getInjectors() as $injector){
            foreach($injector->getTraits() as $k => $trait){
                if($k === self::CONTROLLER){
                    $traits[] = $trait;
                }
            }
        }
        return $traits;
    }
}