<?php

namespace Idk\LegoBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Idk\LegoBundle\Entity\AttachableFile;
use Idk\LegoBundle\Entity\AttachableFolder;
use Idk\LegoBundle\Form\Attachable\FolderType;

/**
 * The admin list controller for Attribut
 * @Route("/admin/attachable")
 */
class AttachableController extends Controller
{


     /**
     * The index action
     *
     * @Route("/show", name="lleadminlistbundle_attachable_show_folder")
     */
    public function showFolderAction(){
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $response = new JsonResponse();
        $response->setData(array('stat'=>false));
        if ($request->isMethod('POST')) {
            $data = $request->request->get('data');
            $folder = $em->getRepository('LleAdminListBundle:AttachableFolder')->find($data['id']);
            $item = $em->getRepository($data['class'])->find($data['itemId']);
            if($folder){
                $folders = $folder->getFolders();
                $files = $folder->getFiles();
            }elseif(isset($data['zoneCode']) && $data['zoneCode']){
                $folder = new AttachableFolder();
                $folder->setZoneCode($data['zoneCode']);
                $folders = $em->getRepository('LleAdminListBundle:AttachableFolder')->findRacineByClassAndItemId($data['class'],$data['itemId'],$data['zoneCode']);
                $files = $em->getRepository('LleAdminListBundle:AttachableFile')->findRacineByClassAndItemId($data['class'],$data['itemId'],$data['zoneCode']);
            }else{
                $folder = new AttachableFolder();
                $folders = $em->getRepository('LleAdminListBundle:AttachableFolder')->findRacineByClassAndItemId($data['class'],$data['itemId']);
                $files = $em->getRepository('LleAdminListBundle:AttachableFile')->findRacineByClassAndItemId($data['class'],$data['itemId']);
            }
        }
        $form = $this->createForm(new FolderType(), new AttachableFolder());
        $displayFolder = false;
        if(method_exists($item,'getAttachableWithFolder')){
            $displayFolder = $item->getAttachableWithFolder((isset($data['zoneCode'])? $data['zoneCode']:null));
        }
        $clickableFile = false;
        if(method_exists($item,'getAttachableClickableFile')){
            $clickableFile = $item->getAttachableClickableFile((isset($data['zoneCode'])? $data['zoneCode']:null));
        }
        $template = $this->render('LleAdminListBundle:Attachable:_folder.html.twig', array(
            'folders' => $folders,
            'files' => $files,
            'form'=> $form->createView(),
            'curFolder' => $folder,
            'itemId' => $data['itemId'],
            'class' => $data['class'],
            'displayFolder'=>$displayFolder,
            'options'=> ['clickableFile'=>$clickableFile],
        ));
        return $template;
    }

    /**
     * The index action
     *
     * @Route("/showzone", name="lleadminlistbundle_attachable_show_zone")
     */
    public function showZoneAction(){
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $response = new JsonResponse();
        $response->setData(array('stat'=>false));
        if ($request->isMethod('POST')) {
            $data = $request->request->get('data');
            $item = $em->getRepository($data['class'])->find($data['itemId']);
            $curFolder = new AttachableFolder();
            $folders = $em->getRepository('LleAdminListBundle:AttachableFolder')->findRacineByClassAndItemId($data['class'],$data['itemId'],$data['zoneCode']);
            $files = $em->getRepository('LleAdminListBundle:AttachableFile')->findRacineByClassAndItemId($data['class'],$data['itemId'],$data['zoneCode']);
            $curFolder->setZoneCode($data['zoneCode']);
        }
        $form = $this->createForm(new FolderType(), new AttachableFolder());
        $displayFolder = false;
        if(method_exists($item,'getAttachableWithFolder')){
            $displayFolder = $item->getAttachableWithFolder($data['zoneCode']);
        }
        $clickableFile = false;
        if(method_exists($item,'getAttachableClickableFile')){
            $clickableFile = $item->getAttachableClickableFile($data['zoneCode']);
        }
        $template = $this->render('LleAdminListBundle:Attachable:_folder.html.twig', array(
            'folders' => $folders,
            'files' => $files,
            'form'=> $form->createView(),
            'curFolder' => $curFolder,
            'itemId' => $data['itemId'],
            'class' => $data['class'],
            'displayFolder'=>$displayFolder,
            'options'=> ['clickableFile'=>$clickableFile],
        ));
        return $template;
    }


    /**
     * The index action
     *
     * @Route("/deletefolder/{id}", defaults={"id" = 0}, name="lleadminlistbundle_attachable_delete_folder")
     */
    public function deleteFolderAction($id){
        $request = $this->getRequest();
        $data = $request->request->get('data');
        $em = $this->getDoctrine()->getManager();
        $folder = $em->getRepository('LleAdminListBundle:AttachableFolder')->find($id);
        $parent = $folder->getFolder();
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($folder);
        $em->flush();
        if($parent){
            $parent = $folder->getFolder();
            $folders = $parent->getFolders();
            $files = $parent->getFiles();
        }elseif($data['zoneCode']){
            $parent = new AttachableFolder();
            $parent->setZoneCode($data['zoneCode']);
            $folders = $em->getRepository('LleAdminListBundle:AttachableFolder')->findRacineByClassAndItemId($data['class'],$data['itemId'],$data['zoneCode']);
            $files = $em->getRepository('LleAdminListBundle:AttachableFile')->findRacineByClassAndItemId($data['class'],$data['itemId'],$data['zoneCode']);
        } else{
            $parent = new AttachableFolder();
            $folders = $em->getRepository('LleAdminListBundle:AttachableFolder')->findRacine();
            $files = $em->getRepository('LleAdminListBundle:AttachableFile')->findRacine();
        }
        $form = $this->createForm(new FolderType(), new AttachableFolder());
        $displayFolder = false;
        $item = $em->getRepository($data['class'])->find($data['itemId']);
        if(method_exists($item,'getAttachableWithFolder')){
            $displayFolder = $item->getAttachableWithFolder($parent->getZoneCode());
        }
        $clickableFile = false;
        if(method_exists($item,'getAttachableClickableFile')){
            $clickableFile = $item->getAttachableClickableFile($data['zoneCode']);
        }
        $template = $this->render('LleAdminListBundle:Attachable:_folder.html.twig', array(
            'folders' => $folders,
            'files' => $files,
            'form'=> $form->createView(),
            'curFolder' => $parent,
            'itemId' => $data['itemId'],
            'class' => $data['class'],
            'displayFolder'=>$displayFolder,
            'options'=> ['clickableFile'=>$clickableFile],
        ));
        return $template;


    }



     /**
     * The index action
     *
     * @Route("/addfolder/{id}", defaults={"id" = 0}, name="lleadminlistbundle_attachable_add_folder")
     */
    public function addFolderAction($id){
        $em = $this->getDoctrine()->getManager();
        $class = $this->getRequest()->request->get('class');
        $realClass = $em->getClassMetadata($class)->getName();
        $itemId = $this->getRequest()->request->get('itemId');
        $zoneCode = $this->getRequest()->request->get('zoneCode');
        $parent = $em->getRepository('LleAdminListBundle:AttachableFolder')->find($id);
        $folder = new AttachableFolder();
        $form = $this->createForm(new FolderType(),$folder);
        $form->handleRequest($this->getRequest());
        $em = $this->getDoctrine()->getManager();
        if($parent){
            $folder->setFolder($parent);
            $folder->setZoneCode($parent->getZoneCode());
        }elseif($zoneCode){
            $folder->setZoneCode($zoneCode);
        }
        $folder->setClass($class);
        $folder->setRealClass($realClass);
        $folder->setItemId($itemId);
        $em->persist($folder);
        $em->flush();
        $response = new JsonResponse();
        $response->setData(array(
            'name' => $folder->getNom(),
        ));
        $form = $this->createForm(new FolderType(), new AttachableFolder());
        $folders = $folder->getFolders();
        $files = $folder->getFiles();
        $item = $em->getRepository($class)->find($itemId);
        $displayFolder = false;
        if(method_exists($item,'getAttachableWithFolder')){
            $displayFolder = $item->getAttachableWithFolder($zoneCode);
        }
        $clickableFile = false;
        if(method_exists($item,'getAttachableClickableFile')){
            $clickableFile = $item->getAttachableClickableFile($zoneCode);
        }
        $template = $this->render('LleAdminListBundle:Attachable:_folder.html.twig', array(
            'folders' => $folders,
            'files' => $files,
            'form'=> $form->createView(),
            'curFolder'=>$folder,
            'class' => $class,
            'itemId'=>$itemId,
            'displayFolder'=>$displayFolder,
            'options'=> ['clickableFile'=>$clickableFile],
        ));
        return $template;
    }


    /**
     * The index action
     *
     * @Route("/deletefile", name="lleadminlistbundle_attachable_delete_file")
     */
    public function deleteAction(){
        $em = $this->getDoctrine()->getManager();
        //$filename =  $this->getRequest()->request->get('file');
        $fileId =  $this->getRequest()->request->get('id');
        $file = $em->getRepository('LleAdminListBundle:AttachableFile')->find($fileId);
        $em->remove($file);
        $em->flush();
        $response = new JsonResponse();
        $response->setData(array(
            'status' => 'ok',
        ));
        return $response;
    }


     /**
     * The index action
     *
     * @Route("/uploader/{id}", defaults={"id" = 0}, name="lleadminlistbundle_attachable_uploader")
     */
    public function uploaderAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $class = $this->getRequest()->request->get('class');
        $realClass = $em->getClassMetadata($class)->getName();
        $itemId = $this->getRequest()->request->get('itemId');
        $zoneCode =  $this->getRequest()->request->get('zoneCode');
        $item = $em->getRepository($class)->find($itemId);
        $request = $this->getRequest();
        $folder = $em->getRepository('LleAdminListBundle:AttachableFolder')->find($id);
        ini_set('post_max_size', '100M');
        ini_set('upload_max_filesize', '100M');
        $derns = null;
        foreach ($request->files as $f) {
            $filename = basename($f->getClientOriginalName());
            $size = $f->getClientSize();
            $mimeType = $f->getMimeType();
            $extension = $this->ext($filename);
            $mtype = explode('/',$mimeType);
            $type = $mtype[0];
            $subType = (isset($mtype[1]))? $mtype[1]:null;
            $file = new AttachableFile();
            if($folder){
                $file->setFolder($folder);
                $file->setZoneCode($folder->getZoneCode());
            }else if($zoneCode){
                $file->setZoneCode($zoneCode);
            }
            $file->setType($type);
            $file->setSubType($subType);
            $file->setNom($filename);
            $file->setTaille($size);
            $file->setClass($class);
            $file->setRealClass($realClass);
            $file->setItemId($itemId);
            if(method_exists($item,'getAttachableNameFile')){
                $file->setFichier($item->getAttachableNameFile($filename));
            }else{
                $file->setFichier(md5(time()).md5($filename).$extension);
            }
            if(method_exists($item,'getAttachableDirectory')){
                $path = $item->getAttachableDirectory($file);
                $file->setPath($path);
                $f->move($file->getUploadDir(),$file->getFichier());
            }else{
                $f->move($file->getUploadDir(),$file->getFichier());
            }
            $em->persist($file);
            $em->flush();
            $derns = $file;
        }
        $response = new JsonResponse();
        $response->setData(array(
            'preview' => $file->getPreview(),
            'id'  => $file->getId(),
            'url' =>$file->getUrl(),
        ));
        return $response;
    }

    private function ext($filename){
        return substr($filename, strrpos($filename, '.'));
    }

}
