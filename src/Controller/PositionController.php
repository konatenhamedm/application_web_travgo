<?php

namespace App\Controller;

use App\Entity\Position;
use App\Form\PositionType;
use App\Repository\PositionRepository;
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
 * @Route("/position")
 */
class PositionController extends AbstractController
{
    /**
     * @Route("/", name="positions", methods={"GET", "POST"})
     */
    public function index(GridManager $gridManager): Response
    {

        $source = new Entity(Position::class);

        //$grid = $this->get('grid');
        $grid = $gridManager->createGrid();
        $tableAlias = $source->getTableAlias();
        $source->manipulateQuery(
            function ($query) use ($tableAlias) {
               /* $query->andWhere($tableAlias . ".active = 1 ");*/
                $query->resetDQLPart('orderBy');
                $query->addOrderBy($tableAlias . '.id', 'DESC');
            }
        );
        $grid->hideColumns('id');
        $grid->hideColumns('active');
        $grid->setSource($source);

        $grid->setRouteUrl($this->generateUrl('positions'));


        $rowAction = new RowAction('Détails', 'position_show');
       
        $grid->addRowAction($rowAction);

        $rowAction = new RowAction('Modifier', 'position_edit');
        $grid->addRowAction($rowAction);

   
        /*$rowAction = new RowAction('Supprimer', 'position_delete');
        $rowAction->setAttributes(['data-target' => '#stack2']);
        $grid->addRowAction($rowAction);*/

        return $grid->getGridResponse('position/index.html.twig');
    }

    /**
     * @Route("/new", name="position_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, FormError $formError): Response
    {
        $position = new Position();
        $form = $this->createForm(PositionType::class, $position, [
            'method' => 'POST',
            'action' => $this->generateUrl('position_new')
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('positions');

            if ($form->isValid()) {
                $em->persist($position);
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

        return $this->render('position/new.html.twig', [
            'position' => $position,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="position_show", methods={"GET"})
     */
    public function show(Position $position): Response
    {
        return $this->render('position/show.html.twig', [
            'position' => $position,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="position_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Position $position, FormError $formError, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PositionType::class, $position, [
            'method' => 'POST',
            'action' => $this->generateUrl('position_edit', ['id' =>  $position->getId()])
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('positions');

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

        return $this->render('position/edit.html.twig', [
            'position' => $position,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="position_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em, Position $position): Response
    {
    

        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'position_delete'
                ,   [
                        'id' => $position->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $em->remove($position);
            $em->flush();

            $redirect = $this->generateUrl('positions');

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


            return $this->render('position/delete.html.twig', [
                'position' => $position,
                'form' => $form->createView(),
            ]);
        }
    }
}
