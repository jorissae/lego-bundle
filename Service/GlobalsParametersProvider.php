<?php
namespace Idk\LegoBundle\Service;




class GlobalsParametersProvider
{

    private $skin;
    private $layout;
    private $layoutLogin;
    private $routeLogin;
    private $routeLogout;
    private $userClass;

    public function __construct($skin, $layout, $layoutLogin, $routeLogin, $routeLogout, $userClass) {
        $this->skin = $skin;
        $this->layout = $layout;
        $this->layoutLogin = $layoutLogin;
        $this->routeLogin = $routeLogin;
        $this->routeLogout = $routeLogout;
        $this->userClass = $userClass;
    }

    /**
     * @return mixed
     */
    public function getSkin()
    {
        return $this->skin;
    }

    /**
     * @return mixed
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @return mixed
     */
    public function getLayoutLogin()
    {
        return $this->layoutLogin;
    }

    public function getRouteLogin(){
        return $this->routeLogin;
    }

    public function getRouteLogout(){
        return $this->routeLogout;
    }

    /**
     * @return mixed
     */
    public function getUserClass()
    {
        return $this->userClass;
    }

    /**
     * @param mixed $userClass
     */
    public function setUserClass($userClass): void
    {
        $this->userClass = $userClass;
    }









}
