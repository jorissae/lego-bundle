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


use Idk\LegoBundle\Lib\LayoutItem\LabelItem;
use Idk\LegoBundle\Lib\LayoutItem\MenuItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Idk\LegoBundle\Lib\Path;

class Menu implements LegoMenuInterface
{

    private $mem;
    private $configuratorBuilder;
    protected $request;

    public function __construct(ConfiguratorBuilder $configuratorBuilder, RequestStack $request) {
        $this->mem = $configuratorBuilder->getMetaEntityManager();
        $this->configuratorBuilder = $configuratorBuilder;
        $this->request = $request;
    }

    public function search(){
        return false;
    }

    public function getTemplate(){
        return '@IdkLego/Layout/_menu.html.twig';
    }

    public function isActif(MenuItem $item){

        if($item->getChildren()){
            foreach($item->getChildren() as $child){
                if($this->isActif($child)){
                    return true;
                }
            }
        }
        if($item->getPath()) {
            $r2 = $this->request->getMasterRequest()->get('_route');
            $r1 = $item->getPath()->getRoute();
            return (substr($r1, 0, strrpos($r1, '_'))) === (substr($r2, 0, strrpos($r2, '_')));
        }
        return false;
    }

    public function getItems(){
        $return = [];
        $return[] = new MenuItem('ADMIN', ['type'=>MenuItem::TYPE_HEADER]);
        $return[] = new MenuItem('Dashboard', [
            'icon' => 'dashboard',
            'path' => new Path('idk_lego_dashboard'),
            'labels'=> [new LabelItem(5, ['css_class'=>'bg-red'])],
            'children' => [new MenuItem('index',['path'=>new Path('idk_lego_dashboard'), 'icon'=>'circle-o'])]
        ]);

        foreach($this->mem->getMetaDataEntities() as $k => $metaDataEntity){
            /* @var \Idk\LegoBundle\Lib\MetaEntity $metaDataEntity */
            if($this->configuratorBuilder->hasAccess($metaDataEntity->getName(),'index')) {
                $return[] = new MenuItem(ucfirst($metaDataEntity->getLibelle()), [
                    'icon' => $metaDataEntity->getIcon(),
                    'path' => $metaDataEntity->getPath($this->configuratorBuilder),
                ]);
            }
        }

        return $return;
    }


}
