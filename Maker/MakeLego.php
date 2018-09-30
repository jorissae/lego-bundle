<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Maker;
use Doctrine\Common\Annotations\Annotation;
use Idk\LegoBundle\Service\MetaEntityManager;
use Idk\LegoBundle\Service\Tag\InjectorChain;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassSourceManipulator;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Validator\Validation;
/**
 * @author Joris Saenger <joris.saenger@gmail.com>
 */
final class MakeLego extends AbstractMaker
{
    private $fileManager;
    private $mem;
    private $entityHelper;
    private $injectorChain;

    public function __construct(FileManager $fileManager, MetaEntityManager $mem, DoctrineHelper $entityHelper, InjectorChain $injectorChain)
    {
        $this->fileManager = $fileManager;
        $this->mem = $mem;
        $this->entityHelper = $entityHelper;
        $this->injectorChain = $injectorChain;
    }
    public static function getCommandName(): string
    {
        return 'make:lego';
    }
    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates a new controller class')
            ->addArgument('entity-class', InputArgument::OPTIONAL, sprintf('The class name of the entity to create CRUD (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())))
            ->addArgument('form', InputArgument::OPTIONAL, sprintf('Do you want generate FormType'))
            ->addArgument('namespace-controller', InputArgument::OPTIONAL, sprintf('Namespace for controller App/Controller/[...]/MyController.php ?'))
            ->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeLego.txt'))
        ;

        $inputConfig->setArgumentAsNonInteractive('entity-class');
        $inputConfig->setArgumentAsNonInteractive('form');
        $inputConfig->setArgumentAsNonInteractive('namespace-controller');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        if (null === $input->getArgument('entity-class')) {
            $argument = $command->getDefinition()->getArgument('entity-class');
            $entities = $this->entityHelper->getEntitiesForAutocomplete();
            $question = new Question($argument->getDescription());
            $question->setAutocompleterValues($entities);
            $value = $io->askQuestion($question);
            $input->setArgument('entity-class', $value);
        }
        if (null === $input->getArgument('form')) {
            $argument = $command->getDefinition()->getArgument('form');
            $question = new ConfirmationQuestion($argument->getDescription(), false);
            $value = $io->askQuestion($question);
            $input->setArgument('form', $value);
        }
        if (null === $input->getArgument('namespace-controller')) {
            $argument = $command->getDefinition()->getArgument('namespace-controller');
            $question = new Question($argument->getDescription(), null);
            $finder = $this->fileManager->createFinder('src/Controller/');
            $controllerNamespaces = [null];
            foreach($finder->directories() as $dir){
                /* @var SplFileInfo $dir */
                $controllerNamespaces[] = $dir->getBasename();
            }
            $question->setAutocompleterValues($controllerNamespaces);
            $value = $io->askQuestion($question);
            $input->setArgument('namespace-controller', $value);
        }
    }

    private function createConfigurator(InputInterface $input, ConsoleStyle $io, Generator $generator, $form){
        $configuratorClassNameDetails = $generator->createClassNameDetails(
            $input->getArgument('entity-class'),
            'Configurator\\',
            'Configurator'
        );
        $generator->generateClass(
            $configuratorClassNameDetails->getFullName(),
            $this->getSkeletonTemplate('lego/Configurator/LegoConfigurator.php'),
            [
                'namespace' => 'App',
                'entity_class' => $input->getArgument('entity-class'),
                'controller_route' => ($input->getArgument('namespace-controller'))? $input->getArgument('namespace-controller').'_'.$input->getArgument('entity-class'):$input->getArgument('entity-class'),
                'generate_admin_type' => $form
            ]
        );
        $generator->writeChanges();
        $this->writeSuccessMessage($io);
        return $configuratorClassNameDetails->getFullName();
    }

    private function createController(InputInterface $input, ConsoleStyle $io, Generator $generator){
        $controllerClassNameDetails = $generator->createClassNameDetails(
            $input->getArgument('entity-class'),
            $input->getArgument('namespace-controller')? 'Controller\\'.$input->getArgument('namespace-controller').'\\':'Controller\\',
            'LegoController'
        );
        $generator->generateClass(
            $controllerClassNameDetails->getFullName(),
            $this->getSkeletonTemplate('lego/Controller/EntityLegoController.php'),
            [
                'namespace' => 'App',
                'traits' => $this->injectorChain->getControllerTraits(),
                'entity_class' => $input->getArgument('entity-class')
            ]
        );
        $generator->writeChanges();
        $this->writeSuccessMessage($io);
        return  $controllerClassNameDetails->getFullName();
    }

    private function createFormType(InputInterface $input, ConsoleStyle $io, Generator $generator, $entityClassDetails){
        $formTypeClassNameDetails = $generator->createClassNameDetails(
            $input->getArgument('entity-class'),
            'Form\\',
            'Type'
        );
        $generator->generateClass(
            $formTypeClassNameDetails->getFullName(),
            $this->getSkeletonTemplate('lego/Form/EntityLegoType.php'),
            [
                'namespace' => 'App',
                'entity_class' => $input->getArgument('entity-class'),
                'fields' => $this->mem->generateFormFields($entityClassDetails->getFullName())
            ]
        );
        $generator->writeChanges();
        $this->writeSuccessMessage($io);
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {

        $entityClassDetails = $generator->createClassNameDetails(
            Validator::entityExists($input->getArgument('entity-class'), $this->entityHelper->getEntitiesForAutocomplete()),
            'Entity\\'
        );

        $entityPath = $this->getPathOfClass($entityClassDetails->getFullName());

        $io->text('Creat a configurator for ' . $entityClassDetails->getFullName(). ' ' .
            ($input->getArgument('form')? 'with form':'without form'));


        $configurator = $this->createConfigurator($input, $io, $generator, $input->getArgument('form'));
        $this->createController($input, $io, $generator);
        if($input->getArgument('form')) {
            $this->createFormType($input, $io, $generator,$entityClassDetails);
        }


        $io->text('Next: Open file '.$entityPath.' then add annotation @Lego\Entity(config="'.$configurator.'") of course use Idk\LegoBundle\Annotation\Entity as Lego;');
    }
    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(
        // we only need doctrine/annotations, which contains
        // the recipe that loads annotation routes
            Annotation::class,
            'annotations'
        );
    }

    private function getSkeletonTemplate($templateName){
        return __DIR__.'/../Resources/skeleton/'.$templateName;
    }
    private function isTwigInstalled()
    {
        return class_exists(TwigBundle::class);
    }

    private function getPathOfClass(string $class): string
    {
        return (new \ReflectionClass($class))->getFileName();
    }
}