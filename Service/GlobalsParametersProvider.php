<?php
namespace Idk\LegoBundle\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GlobalsParametersProvider
{

    private $params;

    public function __construct(ParameterBagInterface $params) {
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getSkin()
    {
        return $this->params->get('lego.skin');
    }

    /**
     * @return mixed
     */
    public function getLayout()
    {
        return $this->params->get('lego.layout');
    }

    /**
     * @return mixed
     */
    public function getLayoutLogin()
    {
        return $this->params->get('lego.layout_login');
    }

    public function getRouteLogin(){
        return $this->params->get('lego.route.login');
    }

    public function getRouteLogout(){
        return $this->params->get('lego.route.logout');
    }

    /**
     * @return mixed
     */
    public function getUserClass()
    {
        return $this->params->get('lego.user.class');
    }









}
