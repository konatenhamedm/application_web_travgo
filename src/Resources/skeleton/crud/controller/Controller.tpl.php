<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use <?= $entity_full_class_name ?>;
use <?= $form_full_class_name ?>;
<?php if (isset($repository_full_class_name)): ?>
use <?= $repository_full_class_name ?>;
<?php endif ?>
use Symfony\Bundle\FrameworkBundle\Controller\<?= $parent_class_name ?>;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\DataGrid\RowAction;
use App\Service\FormError;
use APY\DataGridBundle\Grid\Source\Entity;
use Doctrine\ORM\EntityManagerInterface;
use APY\DataGridBundle\Grid\GridManager;

/**
 * @Route("<?= $route_path ?>")
 */
class <?= $class_name ?> extends <?= $parent_class_name; ?><?= "\n" ?>
{
    /**
     * @Route("/", name="<?= $route_name ?>_index", methods={"GET", "POST"})
     */
<?php if (isset($repository_full_class_name)): ?>
    public function index(GridManager $gridManager): Response
    {

        $source = new Entity(<?= $entity_class_name ?>::class);

        //$grid = $this->get('grid');
        $grid = $gridManager->createGrid();

        $grid->setSource($source);

        $grid->setRouteUrl($this->generateUrl('<?= $route_name ?>_index'));


        $rowAction = new RowAction('Détails', '<?= $route_name ?>_show');
       
        $grid->addRowAction($rowAction);

        $rowAction = new RowAction('Modifier', '<?= $route_name ?>_edit');
        $grid->addRowAction($rowAction);

   
        /*$rowAction = new RowAction('Supprimer', '<?= $route_name ?>_delete');
        $rowAction->setAttributes(['data-target' => '#stack2']);
        $grid->addRowAction($rowAction);*/

        return $grid->getGridResponse('<?= $templates_path ?>/index.html.twig');
    }
<?php else: ?>
    public function index(GridManager $gridManager): Response
    {
       $source = new Entity(<?= $entity_class_name ?>::class);

        $grid = $this->get('grid');

        $grid->setSource($source);

        $grid->setRouteUrl($this->generateUrl('<?= $route_name ?>_index'));


        $rowAction = new RowAction('Détails', '<?= $route_name ?>_show');
       
        $grid->addRowAction($rowAction);

        $rowAction = new RowAction('Modifier', '<?= $route_name ?>_edit');
        $grid->addRowAction($rowAction);

   
        /*$rowAction = new RowAction('Supprimer', '<?= $route_name ?>_delete');
        $rowAction->setAttributes(['data-target' => '#stack2']);
        $grid->addRowAction($rowAction);*/

        return $grid->getGridResponse('<?= $templates_path ?>/index.html.twig');
    }
<?php endif ?>

    /**
     * @Route("/new", name="<?= $route_name ?>_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, FormError $formError): Response
    {
        $<?= $entity_var_singular ?> = new <?= $entity_class_name ?>();
        $form = $this->createForm(<?= $form_class_name ?>::class, $<?= $entity_var_singular ?>, [
            'method' => 'POST',
            'action' => $this->generateUrl('<?= $route_name ?>_new')
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('<?= $route_name ?>_index');

            if ($form->isValid()) {
                $em->persist($<?= $entity_var_singular ?>);
                $em->flush();

                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);

                
            } else {
                $message = $formError->all($form);
                $statut = 0;
                if (!$isAjax) {
                  $this->addFlash('warning', $message);
                }
                
            }


            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }

        return $this->render('<?= $templates_path ?>/new.html.twig', [
            '<?= $entity_twig_var_singular ?>' => $<?= $entity_var_singular ?>,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{<?= $entity_identifier ?>}/show", name="<?= $route_name ?>_show", methods={"GET"})
     */
    public function show(<?= $entity_class_name ?> $<?= $entity_var_singular ?>): Response
    {
        return $this->render('<?= $templates_path ?>/show.html.twig', [
            '<?= $entity_twig_var_singular ?>' => $<?= $entity_var_singular ?>,
        ]);
    }

    /**
     * @Route("/{<?= $entity_identifier ?>}/edit", name="<?= $route_name ?>_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, <?= $entity_class_name ?> $<?= $entity_var_singular ?>, FormError $formError, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(<?= $form_class_name ?>::class, $<?= $entity_var_singular ?>, [
            'method' => 'POST',
            'action' => $this->generateUrl('<?= $route_name ?>_edit', ['<?= $entity_identifier ?>' =>  $<?= $entity_var_singular ?>->get<?= ucfirst($entity_identifier) ?>()])
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('<?= $route_name ?>_index');

            if ($form->isValid()) {
                $em->flush();
                $message       = 'Opération effectuée avec succès';
                $statut = 1;
                $this->addFlash('success', $message);

                
            } else {
                $message = $formError->all($form);
                $statut = 0;
                if (!$isAjax) {
                  $this->addFlash('warning', $message);
                }
                
            }


            if ($isAjax) {
                return $this->json( compact('statut', 'message', 'redirect'));
            } else {
                if ($statut == 1) {
                    return $this->redirect($redirect);
                }
            }
        }

        return $this->render('<?= $templates_path ?>/edit.html.twig', [
            '<?= $entity_twig_var_singular ?>' => $<?= $entity_var_singular ?>,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{<?= $entity_identifier ?>}/delete", name="<?= $route_name ?>_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em, <?= $entity_class_name ?> $<?= $entity_var_singular ?>): Response
    {
    

        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                '<?= $route_name ?>_delete'
                ,   [
                        '<?= $entity_identifier ?>' => $<?= $entity_var_singular ?>->get<?= ucfirst($entity_identifier) ?>()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $em->remove($<?= $entity_var_singular ?>);
            $em->flush();

            $redirect = $this->generateUrl('<?= $route_name ?>_index');

            $message = 'Opération effectuée avec succès';

            $response = [
                'statut'   => 1,
                'message'  => $message,
                'redirect' => $redirect,
            ];

            $this->addFlash('success', $message);

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($redirect);
            } else {
                return $this->json($response);
            }


            return $this->render('<?= $templates_path ?>/delete.html.twig', [
                '<?= $entity_twig_var_singular ?>' => $<?= $entity_var_singular ?>,
                'form' => $form->createView(),
            ]);
        }
    }
}
