<?php

namespace Idk\LegoBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Idk\LegoBundle\Entity\AbstractAttribut;

/**
 * The admin list controller for Attribut
 * @Route("/admin/attribut")
 */
class AttributController extends Controller
{

    /**
     * The index action
     *
     * @Route("/addoption/{id}", name="lleadminlistbundle_attribut_add_option")
     */
    public function addOptionAction(Request $request,$id)
    {
        $em = $this->getEntitymanager();
        $attribut = $em->getRepository($request->request->get('cls'))->find($id);
        $options = $attribut->getOptions();
        if($attribut->isList()){
            if(isset($options['list']) and is_array($options['list'])){
                $options['list'][] = $request->request->get('val');
            }else{
                $options['list'] = array($request->request->get('val'));
            }
        }elseif($attribut->isNumber()){
            $options['min'] = (float)$request->request->get('min');
            $options['max'] = (float)$request->request->get('max');
            $options['step'] = (float)$request->request->get('step');
            $options['unite'] = trim($request->request->get('unite'));
            $options['slide'] = $request->request->get('slide');
            $options['multiplicator'] = $request->request->get('multiplicator');
        }
        $attribut->setOptions($options);
        $em->persist($attribut);
        $em->flush();

        return $this->redirect($this->generateUrl('lleadminbundle_admin_attribut'));
    }

    /**
     * The index action
     *
     * @Route("/removeoption/{id}", name="lleadminlistbundle_attribut_remove_option")
     */
    public function removeOptionAction(Request $request,$id)
    {
        $data = $request->request->get('data');
        $value = $data['val'];
        $em = $this->getEntitymanager();
        $attribut = $em->getRepository($data['cls'])->find($id);
        $options = $attribut->getOptions();
        if($attribut->getWidget() == 'list'){
            $k = array_search($value,$options['list']);
            unset($options['list'][$k]);
        }
        $attribut->setOptions($options);
        $em->persist($attribut);
        $em->flush();

        return new Response(json_encode(array('status'=>'ok')));
    }

    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }
}
