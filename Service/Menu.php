<?php
namespace Idk\LegoBundle\Service;


use Idk\LegoBundle\Lib\LayoutItem\LabelItem;
use Idk\LegoBundle\Lib\LayoutItem\MenuItem;
use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Idk\LegoBundle\Lib\Path;

class Menu implements LegoMenuInterface
{

    private $mem;
    private $configuratorBuilder;

    public function __construct(ConfiguratorBuilder $configuratorBuilder) {
        $this->mem = $configuratorBuilder->getMetaEntityManager();
        $this->configuratorBuilder = $configuratorBuilder;
    }

    public function search(){
        return false;
    }

    public function getTemplate(){
        return 'IdkLegoBundle:Layout:_menu.html.twig';
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

        foreach($this->mem->getMetaDataEntities() as $metaDataEntity){

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
