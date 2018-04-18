<?php
namespace Idk\LegoBundle\Maker;
use Doctrine\Common\Annotations\Annotation;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
final class MakeLego extends AbstractMaker
{
    private $fileManager;
    public function __construct(FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
    }
    public static function getCommandName(): string
    {
        return 'make:lego';
    }
    public function configureCommand(Command $command, InputConfiguration $inputConf)
    {
        $command
            ->setDescription('Creates a new controller class')
            ->addArgument('controller-class', InputArgument::OPTIONAL, sprintf('Choose a name for your controller class (e.g. <fg=yellow>%sController</>)', Str::asClassName(Str::getRandomTerm())))
            ->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeController.txt'))
        ;
    }
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $controllerClassNameDetails = $generator->createClassNameDetails(
            $input->getArgument('controller-class'),
            'Controller\\',
            'Controller'
        );
        $templateName = Str::asFilePath($controllerClassNameDetails->getRelativeNameWithoutSuffix()).'/index.html.twig';
        $controllerPath = $generator->generateClass(
            $controllerClassNameDetails->getFullName(),
            'controller/Controller.tpl.php',
            [
                'route_path' => Str::asRoutePath($controllerClassNameDetails->getRelativeNameWithoutSuffix()),
                'route_name' => Str::asRouteName($controllerClassNameDetails->getRelativeNameWithoutSuffix()),
                'twig_installed' => $this->isTwigInstalled(),
                'template_name' => $templateName,
            ]
        );
        if ($this->isTwigInstalled()) {
            $generator->generateFile(
                'templates/'.$templateName,
                'controller/twig_template.tpl.php',
                [
                    'controller_path' => $controllerPath,
                ]
            );
        }
        $generator->writeChanges();
        $this->writeSuccessMessage($io);
        $io->text('Next: Open your new controller class and add some pages!');
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
    private function isTwigInstalled()
    {
        return class_exists(TwigBundle::class);
    }
}