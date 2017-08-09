<?php

namespace Idk\LegoBundle\Twig;


//uniquement formatage utilisabale avec custom construc appler dans adminlist.php.
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

        //$days = floor($time / (60 * 60 * 24));
        //$time -= $days * (60 * 60 * 24);

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
        /*$dtF = new \DateTime();
        $dtT = new \DateTime();
        $dtF->setTimestamp(0);
        $dtT->setTimestamp($sec);
        $interval = $dtF->diff($dtT);
        $ret = null;
        $ret .= ($interval->y)? $interval->y.' ans ':null;
        $ret .= ($interval->m)? $interval->m.' mois ':null;
        $ret .= ($interval->d)? $interval->d.' jours ':null;
        $ret .= ($interval->h)? $interval->h.' h ':null;
        $ret .= ($interval->i)? $interval->i.' min ':null;
        $ret .= ($interval->s)? $interval->s.' s':null;
        return $ret;*/
    }

    public function getName()
    {
        return 'adminlist_format';
    }
}
