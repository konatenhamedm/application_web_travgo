<?php



/*

 * This file is part of the Symfony MakerBundle package.

 *

 * (c) Fabien Potencier <fabien@symfony.com>

 *

 * For the full copyright and license information, please view the LICENSE

 * file that was distributed with this source code.

 */



namespace App\Maker;



use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;

use Doctrine\Common\Inflector\Inflector;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\MakerBundle\ConsoleStyle;

use Symfony\Bundle\MakerBundle\DependencyBuilder;

use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;

use Symfony\Bundle\MakerBundle\Generator;

use Symfony\Bundle\MakerBundle\InputConfiguration;

use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;

use Symfony\Bundle\MakerBundle\Renderer\FormTypeRenderer;

use Symfony\Bundle\MakerBundle\Str;

use Symfony\Bundle\MakerBundle\Validator;

use Symfony\Bundle\TwigBundle\TwigBundle;

use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Question\Question;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Csrf\CsrfTokenManager;

use Symfony\Component\Validator\Validation;



/**

 * @author Javier Eguiluz <javier.eguiluz@gmail.com>

 * @author Ryan Weaver <weaverryan@gmail.com>

 */

class MakeCrudController extends AbstractMaker

{

    /**

     * @var mixed

     */

    private $doctrineHelper;



    /**

     * @var mixed

     */

    private $formTypeRenderer;



    /**

     * @param DoctrineHelper $doctrineHelper

     * @param FormTypeRenderer $formTypeRenderer

     */

    public function __construct(DoctrineHelper $doctrineHelper, FormTypeRenderer $formTypeRenderer)

    {

        $this->doctrineHelper   = $doctrineHelper;

        $this->formTypeRenderer = $formTypeRenderer;

    }



    public static function getCommandName(): string

    {

        return 'make:setwork.crud';

    }



    /**

     * {@inheritdoc}

     */

    public function configureCommand(Command $command, InputConfiguration $inputConfig)

    {

        $command

            ->setDescription('Creates CRUD for Doctrine entity class')

            ->addArgument('entity-class', InputArgument::OPTIONAL, sprintf('The class name of the entity to create CRUD (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())))

            ->setHelp(file_get_contents(__DIR__ . '/../Resources/help/MakeCrud.txt'))

        ;



        $inputConfig->setArgumentAsNonInteractive('entity-class');

    }



    /**

     * @param InputInterface $input

     * @param ConsoleStyle $io

     * @param Command $command

     */

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)

    {

        if (null === $input->getArgument('entity-class')) {

            $argument = $command->getDefinition()->getArgument('entity-class');



            $entities = $this->doctrineHelper->getEntitiesForAutocomplete();



            $question = new Question($argument->getDescription());

            $question->setAutocompleterValues($entities);



            $value = $io->askQuestion($question);



            $input->setArgument('entity-class', $value);

        }

    }



    /**

     * @param InputInterface $input

     * @param ConsoleStyle $io

     * @param Generator $generator

     */

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)

    {

        $entityClass = trim(strtr($input->getArgument('entity-class'), '/', '\\'), '\\');

        $entityClassParts = explode('\\', $entityClass);

        $entityName = end($entityClassParts);

        array_pop($entityClassParts);

        $baseFolder = implode('\\', $entityClassParts);



        $entityClassDetails = $generator->createClassNameDetails(

            Validator::entityExists($entityName, $this->doctrineHelper->getEntitiesForAutocomplete()),

            'Entity\\'

        );



        $entityDoctrineDetails = $this->doctrineHelper->createDoctrineDetails($entityClassDetails->getFullName());



        $repositoryVars = [];



        if (null !== $entityDoctrineDetails->getRepositoryClass()) {

            $repositoryClassDetails = $generator->createClassNameDetails(

                '\\' . $entityDoctrineDetails->getRepositoryClass(),

                'Repository\\',

                'Repository'

            );



            $repositoryVars = [

                'repository_full_class_name' => $repositoryClassDetails->getFullName(),

                'repository_class_name'      => $repositoryClassDetails->getShortName(),

                'repository_var'             => lcfirst(Inflector::singularize($repositoryClassDetails->getShortName())),

            ];

        }



        $controllerClassDetails = $generator->createClassNameDetails(

            $entityClassDetails->getRelativeNameWithoutSuffix() . 'Controller',

            ($baseFolder ? 'Controller\\'.$baseFolder : 'Controller\\'),

            'Controller'

        );





      



        $iter = 0;

        do {

            $formClassDetails = $generator->createClassNameDetails(

                $entityClassDetails->getRelativeNameWithoutSuffix() . ($iter ?: '') . 'Type',

                'Form\\',

                'Type'

            );

            ++$iter;

        } while (class_exists($formClassDetails->getFullName()));



        $entityVarPlural   = lcfirst(Inflector::pluralize($entityClassDetails->getShortName()));

        $entityVarSingular = lcfirst(Inflector::singularize($entityClassDetails->getShortName()));



        $entityTwigVarPlural   = Str::asTwigVariable($entityVarPlural);

        $entityTwigVarSingular = Str::asTwigVariable($entityVarSingular);



        $baseFolderFSlah = strtr($baseFolder, '\\', '/');



        $routeName     = Str::asRouteName(strtolower(trim($baseFolder.'\\'.Str::asFilePath($controllerClassDetails->getRelativeNameWithoutSuffix()), '\\')));

        $templatesPath = strtolower(trim($baseFolderFSlah.'/'.Str::asFilePath($controllerClassDetails->getRelativeNameWithoutSuffix()), '/'));





        $routePath = '/'.strtolower(trim($baseFolderFSlah.Str::asRoutePath($controllerClassDetails->getRelativeNameWithoutSuffix()), '/'));





       





       



        $generator->generateController(

            $controllerClassDetails->getFullName(),

            __DIR__ . '/../Resources/skeleton/crud/controller/Controller.tpl.php',

            array_merge([

                'entity_full_class_name'   => $entityClassDetails->getFullName(),

                'entity_class_name'        => $entityClassDetails->getShortName(),

                'form_full_class_name'     => $formClassDetails->getFullName(),

                'form_class_name'          => $formClassDetails->getShortName(),

                'route_path'               => $routePath,

                'route_name'               => $routeName,

                'templates_path'           => $templatesPath,

                'entity_var_plural'        => $entityVarPlural,

                'entity_twig_var_plural'   => $entityTwigVarPlural,

                'entity_var_singular'      => $entityVarSingular,

                'entity_twig_var_singular' => $entityTwigVarSingular,

                'entity_identifier'        => $entityDoctrineDetails->getIdentifier(),

            ],

                $repositoryVars

            )

        );



        $this->formTypeRenderer->render(

            $formClassDetails,

            $entityDoctrineDetails->getFormFields(),

            $entityClassDetails

        );



        $templates = [

            /*'_delete_form' => [

            'route_name' => $routeName,

            'entity_twig_var_singular' => $entityTwigVarSingular,

            'entity_identifier' => $entityDoctrineDetails->getIdentifier(),

            ],*/

            'delete' => [

                'route_name'               => $routeName,

                'entity_twig_var_singular' => $entityTwigVarSingular,

                'entity_identifier'        => $entityDoctrineDetails->getIdentifier(),

            ],

            '_form'  => [],

            'edit'   => [

                'entity_class_name'        => $entityClassDetails->getShortName(),

                'entity_twig_var_singular' => $entityTwigVarSingular,

                'entity_identifier'        => $entityDoctrineDetails->getIdentifier(),

                'route_name'               => $routeName,

            ],

            'index'  => [

                'entity_class_name'        => $entityClassDetails->getShortName(),

                'entity_twig_var_plural'   => $entityTwigVarPlural,

                'entity_twig_var_singular' => $entityTwigVarSingular,

                'entity_identifier'        => $entityDoctrineDetails->getIdentifier(),

                'entity_fields'            => $entityDoctrineDetails->getDisplayFields(),

                'route_name'               => $routeName,

            ],

            'new'    => [

                'entity_class_name' => $entityClassDetails->getShortName(),

                'route_name'        => $routeName,

            ],

            'show'   => [

                'entity_class_name'        => $entityClassDetails->getShortName(),

                'entity_twig_var_singular' => $entityTwigVarSingular,

                'entity_identifier'        => $entityDoctrineDetails->getIdentifier(),

                'entity_fields'            => $entityDoctrineDetails->getDisplayFields(),

                'route_name'               => $routeName,

            ],

        ];



        foreach ($templates as $template => $variables) {

            $generator->generateTemplate(

                $templatesPath . '/' . $template . '.html.twig',

                __DIR__ . '/../Resources/skeleton/crud/templates/' . $template . '.tpl.php',

                $variables

            );

        }



        $generator->writeChanges();



        $this->writeSuccessMessage($io);



        $io->text(sprintf('Next: Check your new CRUD by going to <fg=yellow>%s/</>', $routePath));

    }



    /**

     * {@inheritdoc}

     */

    public function configureDependencies(DependencyBuilder $dependencies)

    {

        $dependencies->addClassDependency(

            Route::class,

            'router'

        );



        $dependencies->addClassDependency(

            AbstractType::class,

            'form'

        );



        $dependencies->addClassDependency(

            Validation::class,

            'validator'

        );



        $dependencies->addClassDependency(

            TwigBundle::class,

            'twig-bundle'

        );



        $dependencies->addClassDependency(

            DoctrineBundle::class,

            'orm-pack'

        );



        $dependencies->addClassDependency(

            CsrfTokenManager::class,

            'security-csrf'

        );



        $dependencies->addClassDependency(

            ParamConverter::class,

            'annotations'

        );

    }

}

