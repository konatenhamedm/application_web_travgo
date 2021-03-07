<?php

namespace App\Controller;

use App\Entity\Chauffeurs;
use App\Form\ChauffeursType;
use App\Repository\ChauffeursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\DataGrid\RowAction;
use App\Service\FormError;
use APY\DataGridBundle\Grid\Source\Entity;
use Doctrine\ORM\EntityManagerInterface;
use APY\DataGridBundle\Grid\GridManager;

/**
 * @Route("/chauffeurs")
 */
class ChauffeursController extends AbstractController
{
    /**
     * @Route("/", name="chauffeurs", methods={"GET", "POST"})
     */
    public function index(GridManager $gridManager): Response
    {

        $source = new Entity(Chauffeurs::class);

        //$grid = $this->get('grid');
        $grid = $gridManager->createGrid();
        $tableAlias = $source->getTableAlias();
/*        jhfhfjkh*/
        $source->manipulateQuery(
            function ($query) use ($tableAlias) {
             /*   $query->andWhere($tableAlias . ".active = 1 ");*/
                $query->resetDQLPart('orderBy');
                $query->addOrderBy($tableAlias . '.id', 'DESC');
            }
        );
        $grid->hideColumns('id');
        $grid->hideColumns('active');
        $grid->setSource($source);

        $grid->setRouteUrl($this->generateUrl('chauffeurs'));


        $rowAction = new RowAction('Détails', 'chauffeurs_show');
       
        $grid->addRowAction($rowAction);

        $rowAction = new RowAction('Modifier', 'chauffeurs_edit');
        $grid->addRowAction($rowAction);

   
        /*$rowAction = new RowAction('Supprimer', 'chauffeurs_delete');
        $rowAction->setAttributes(['data-target' => '#stack2']);
        $grid->addRowAction($rowAction);*/

        return $grid->getGridResponse('chauffeurs/index.html.twig');
    }

    /**
     * @Route("/new", name="chauffeurs_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, FormError $formError): Response
    {
        $chauffeur = new Chauffeurs();
        $form = $this->createForm(ChauffeursType::class, $chauffeur, [
            'method' => 'POST',
            'action' => $this->generateUrl('chauffeurs_new')
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('chauffeurs');

            if ($form->isValid()) {
                $em->persist($chauffeur);
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

        return $this->render('chauffeurs/new.html.twig', [
            'chauffeur' => $chauffeur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="chauffeurs_show", methods={"GET"})
     */
    public function show(Chauffeurs $chauffeur): Response
    {
        return $this->render('chauffeurs/show.html.twig', [
            'chauffeur' => $chauffeur,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="chauffeurs_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Chauffeurs $chauffeur, FormError $formError, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ChauffeursType::class, $chauffeur, [
            'method' => 'POST',
            'action' => $this->generateUrl('chauffeurs_edit', ['id' =>  $chauffeur->getId()])
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('chauffeurs');

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

        return $this->render('chauffeurs/edit.html.twig', [
            'chauffeur' => $chauffeur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="chauffeurs_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em, Chauffeurs $chauffeur): Response
    {
    

        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'chauffeurs_delete'
                ,   [
                        'id' => $chauffeur->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $em->remove($chauffeur);
            $em->flush();

            $redirect = $this->generateUrl('chauffeurs');

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


            return $this->render('chauffeurs/delete.html.twig', [
                'chauffeur' => $chauffeur,
                'form' => $form->createView(),
            ]);
        }
    }
}
