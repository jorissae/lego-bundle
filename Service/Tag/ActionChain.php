<?php
namespace Idk\LegoBundle\Service\Tag;


use Idk\LegoBundle\Action\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Idk\LegoBundle\Configurator\AbstractConfigurator;

class ActionChain
{
    private $actions = [];

    public function __construct(iterable $actions)
    {
        foreach($actions as $action){
            $this->actions[get_class($action)] = $action;
        }
    }

    public function addWidget($action)
    {
        $this->actions[get_class($action)] = $action;
    }

    public function getResponse(string $class, AbstractConfigurator $configurator, Request $request) : Response{
        $this->actions[$class]->setConfigurator($configurator);
        return $this->actions[$class]->getResponse($request);
    }

    public function getActions():iterable{
        return $this->actions;
    }
}