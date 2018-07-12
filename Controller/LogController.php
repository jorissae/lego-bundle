<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The Lego log controller
 * @Route("/admin/log")
 */
class LogController extends Controller
{
    
    /**
     * The export action
     *
     * @param string $_format
     *
     * @Route("/", name="idklegobundle_log")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function indexAction(Request $request)
    {
        return $this->container->get('log_manager')->getLogs(array(),$this->getRequest()->query->get('page',1),20);
    }


    /**
     * The export action
     *
     * @param string $_format
     *
     * @Route("/revert/{class}/{id}/{version}", name="idklegobundle_log_revert")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function revertAction($class,$id,$version){
        $em = $this->getEntityManager();
        $repo = $em->getRepository($class);
        $entity = $repo->find($id);
        $repoLog = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');
        $repoLog->revert($entity,$version);
        $referer = $this->getRequest()->headers->get('referer');  
        $em->persist($entity);
        $em->flush(); 
        return new RedirectResponse($referer);
    }

    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }
}
