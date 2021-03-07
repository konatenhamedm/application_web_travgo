<?php

namespace App\Controller;

use App\Entity\Pannes;
use App\Form\PannesType;
use App\Repository\PannesRepository;
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
 * @Route("/pannes")
 */
class PannesController extends AbstractController
{
    /**
     * @Route("/", name="pannes", methods={"GET", "POST"})
     */
    public function index(GridManager $gridManager): Response
    {

        $source = new Entity(Pannes::class);

        //$grid = $this->get('grid');
        $grid = $gridManager->createGrid();
        $tableAlias = $source->getTableAlias();
        $source->manipulateQuery(
            function ($query) use ($tableAlias) {
                /*$query->andWhere($tableAlias . ".active = 1 ");*/
                $query->resetDQLPart('orderBy');
                $query->addOrderBy($tableAlias . '.id', 'DESC');
            }
        );
        $grid->hideColumns('id');
        $grid->hideColumns('active');
        $grid->setSource($source);

        $grid->setRouteUrl($this->generateUrl('pannes'));


        $rowAction = new RowAction('Détails', 'pannes_show');
       
        $grid->addRowAction($rowAction);

        $rowAction = new RowAction('Modifier', 'pannes_edit');
        $grid->addRowAction($rowAction);

   
        /*$rowAction = new RowAction('Supprimer', 'pannes_delete');
        $rowAction->setAttributes(['data-target' => '#stack2']);
        $grid->addRowAction($rowAction);*/

        return $grid->getGridResponse('pannes/index.html.twig');
    }

    /**
     * @Route("/new", name="pannes_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, FormError $formError): Response
    {
        $panne = new Pannes();
        $form = $this->createForm(PannesType::class, $panne, [
            'method' => 'POST',
            'action' => $this->generateUrl('pannes_new')
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('pannes');

            if ($form->isValid()) {
                $em->persist($panne);
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

        return $this->render('pannes/new.html.twig', [
            'panne' => $panne,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="pannes_show", methods={"GET"})
     */
    public function show(Pannes $panne): Response
    {
        return $this->render('pannes/show.html.twig', [
            'panne' => $panne,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="pannes_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Pannes $panne, FormError $formError, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PannesType::class, $panne, [
            'method' => 'POST',
            'action' => $this->generateUrl('pannes_edit', ['id' =>  $panne->getId()])
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('pannes');

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

        return $this->render('pannes/edit.html.twig', [
            'panne' => $panne,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="pannes_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em, Pannes $panne): Response
    {
    

        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'pannes_delete'
                ,   [
                        'id' => $panne->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $em->remove($panne);
            $em->flush();

            $redirect = $this->generateUrl('pannes');

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


            return $this->render('pannes/delete.html.twig', [
                'panne' => $panne,
                'form' => $form->createView(),
            ]);
        }
    }
}
