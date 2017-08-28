<?php
namespace Idk\LegoBundle\Service;




class GlobalsParametersProvider
{

    private $skin;
    private $layout;
    private $layoutLogin;

    public function __construct($skin, $layout, $layoutLogin) {
        $this->skin = $skin;
        $this->layout = $layout;
        $this->layoutLogin = $layoutLogin;
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






}
