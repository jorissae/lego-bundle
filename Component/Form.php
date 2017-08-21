<?php

namespace Idk\LegoBundle\Component;

use Idk\LegoBundle\ComponentResponse\ErrorComponentResponse;
use Idk\LegoBundle\ComponentResponse\SuccessComponentResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FormType;


class Form extends Component{

    private $form;

    protected function init(){

    }

    protected function requiredOptions(){
        return ['form'];
    }

    public function getTemplate($name = 'index'){
        return 'IdkLegoBundle:Component\\FormComponent:'.$name.'.html.twig';
    }

    public function getTemplateParameters(){
        return ['form' => $this->form->createView(), 'theme' => $this->getOption('theme','IdkLegoBundle:Form:lego_base_fields.html.twig')];
    }

    public function bindRequest(Request $request){
        if($request->get('id')){
            $entity = $this->getConfigurator()->getRepository()->find($request->get('id'));
        }else{
            $entity = $this->getConfigurator()->newInstance();
        }

        if(!$this->getOption('form',null)){
            $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $entity, []);
            foreach($this->get('lego.service.meta_entity_manager')->generateFields($this->getConfigurator()->getEntityName()) as $field){
                $formBuilder->add($field->getName(), null, ['label'=>$field->getHeader()]);
            }
            $this->form = $formBuilder->getForm();
        }else {
            $this->form = $this->get('form.factory')->create($this->getOption('form'), $entity);
        }
        $this->form->handleRequest($request);
        if ('POST' == $request->getMethod() and $this->form->isSubmitted()) {
            if ($this->form->isValid()) {
                $em = $this->getConfigurator()->getEntityManager();
                $em->persist($entity);
                $this->uploader(null,null);
                $em->flush();
                if($request->get('id')){
                    $response = new SuccessComponentResponse('lego.form.success.edit');
                } else {
                    $response = new SuccessComponentResponse('lego.form.success.add');
                    $this->resetForm();
                }
                $response->setRedirect($this->getConfigurator()->getPathRoute('index'));
                return $response;
            } else {
                return new ErrorComponentResponse('lego.form.error');
            }
        }
    }

    private function resetForm(){
        $this->form = $this->get('form.factory')->create($this->getOption('form'), $this->getConfigurator()->newInstance());
    }


    /* todo */
    protected function uploader($configurator,$obj){
        /*if($configurator->getUploadFileGetter()) {
            if(is_array($configurator->getUploadFileGetter())){
                foreach($configurator->getUploadFileGetter() as $method){
                    $uploadFile = call_user_func(array($obj, $method));
                    $this->upload($obj, $uploadFile);
                }
            }else{
                $uploadFile = call_user_func(array($obj, $configurator->getUploadFileGetter()));
                $this->upload($obj, $uploadFile);
            }
        }*/
    }

    /*protected function upload($obj, $uploadFile){
        if($uploadFile instanceof UploadedFile){
            $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
            $uploadableManager->markEntityToUpload($obj, $uploadFile);
        }
    }*/





}
