<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Component;
use Idk\LegoBundle\Configurator\AbstractConfigurator;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\QueryBuilder;

interface BrickInterface{


    function build(array $options, AbstractConfigurator $configurator, $suffixRoute);
    public function getSuffixRoute();
    public function getValId();
    public function addListenQueryParameter($queryParametersGlobal, $queryParametersComponent);
    public function addCanCatchQuery(BrickInterface $component);
    public function isMovable();
    public function getListenParamsForReload();
    public function getAllQueryParams();
    public function bindRequest(Request $request);
    public function initQueryParameters(Request $request);
    public function hasQueryListen(Request $request, $key);
    public function getClass();
    public function xhrBindRequest(Request $request);
    public function catchQuerybuilder(QueryBuilder $queryBuilder);
    public function getRequest();
    public function getOption($key, $default = null);
    public function getPartial($name);
    public function getConfigurator(): AbstractConfigurator;
    public function getConfiguratorBuilder();
    public function getTemplateAllParameters();
    public function canCatchQuery(BrickInterface $component);
    public function getComponentSessionStorage($key, $default = null);
    public function setComponentSessionStorage($key, $value);
    public function setComponentSessionStorages($storage);
    public function getPath(string $suffix = 'component', $params = []);
    public function getUrl(array $params = []);
    public function getId();
    public function gid($id);
    public function setOption($key, $value);
    public function initWithComponents(iterable $components):void;
    public function isDisplayIn($cid = null);
    public function setDisplayIn($cid);

}
