<?php
declare(strict_types=1);
namespace Idk\LegoBundle\Action;



use Doctrine\ORM\EntityManagerInterface;
use Idk\LegoBundle\ComponentResponse\MessageComponentResponse;
use Idk\LegoBundle\Configurator\AbstractConfigurator;
use Idk\LegoBundle\Service\ConfiguratorBuilder;
use Idk\LegoBundle\Service\MetaEntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractFormAction extends AbstractAction
{

    protected $formFactory;

    public function __construct(ConfiguratorBuilder $configuratorBuilder, FormFactoryInterface $formFactory)
    {
        parent::__construct($configuratorBuilder);
        $this->formFactory = $formFactory;
    }

    protected function createForm(string $type, $data = null, array $options = array()): FormInterface
    {
        return $this->formFactory->create($type, $data, $options);
    }


    protected function createFormBuilder($data = null, array $options = array()): FormBuilderInterface
    {
        return $this->formFactory->createBuilder(FormType::class, $data, $options);
    }

}