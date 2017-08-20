<?php

namespace Idk\LegoBundle\Twig;


class FilterTwigExtension extends \Twig_Extension
{
    public function __construct()
    {
    }
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('secToHumanTime', array($this, 'convertSecToHumanTime')),
            new \Twig_SimpleFilter('tel', array($this, 'formatTel')),
        );
    }

    public function formatTel($phone_number)
    {
        $phone_number = preg_replace('/\D/','',$phone_number);
        
        if (strlen($phone_number)==9 && $phone_number[0] !=  0) {
            $phone_number = '0'.$phone_number;
        }

        $nb_let = strlen($phone_number);
        $ret = $phone_number;
        if ($nb_let == 10) {
            $ret = '';
            for ($i=0; $i < $nb_let; $i+=2) { 
                $ret .= substr($phone_number, $i, 2).' ';
            }
        }
        return $ret;
    }

    public function convertSecToHumanTime($sec)
    {
        $time = $sec;

        $h = floor($time / (60 * 60));
        $time -= $h * (60 * 60);

        $i = floor($time / 60);
        $time -= $i * 60;

        $s = floor($time);
        $time -= $s;

        $ret = null;
        $ret .= ($h)? $h.' h ':null;
        $ret .= ($i)? $i :null;
        $ret .= ($i && $s) ? ' min ' :null;
        $ret .= ($s)? $s.' s':null;
        return $ret;
    }

    public function getName()
    {
        return 'lego_format';
    }
}
