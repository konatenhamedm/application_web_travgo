<?php

namespace App\Controller;

use App\Entity\Voyages;
use App\Form\VoyagesType;
use App\Repository\VoyagesRepository;
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
 * @Route("/voyages")
 */
class VoyagesController extends AbstractController
{
    /**
     * @Route("/", name="voyages", methods={"GET", "POST"})
     */
    public function index(GridManager $gridManager): Response
    {

        $source = new Entity(Voyages::class);

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
        // dd($grid->get);
        $grid->setSource($source);

        $grid->setRouteUrl($this->generateUrl('voyages'));

        $rowAction = new RowAction('Détails', 'voyages_show');
       
        $grid->addRowAction($rowAction);

        $rowAction = new RowAction('Modifier', 'voyages_edit');
        $grid->addRowAction($rowAction);

   
        /*$rowAction = new RowAction('Supprimer', 'voyages_delete');
        $rowAction->setAttributes(['data-target' => '#stack2']);
        $grid->addRowAction($rowAction);*/

        return $grid->getGridResponse('voyages/index.html.twig');
    }

    /**
     * @Route("/new", name="voyages_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, FormError $formError): Response
    {
        $voyage = new Voyages();
        $form = $this->createForm(VoyagesType::class, $voyage, [
            'method' => 'POST',
            'action' => $this->generateUrl('voyages_new')
        ]);

        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('voyages');
           // dd($request);
            if ($form->isValid()) {

                $em->persist($voyage);
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

        return $this->render('voyages/new.html.twig', [
            'voyage' => $voyage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="voyages_show", methods={"GET"})
     */
    public function show(Voyages $voyage): Response
    {
        return $this->render('voyages/show.html.twig', [
            'voyage' => $voyage,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="voyages_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Voyages $voyage, FormError $formError, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(VoyagesType::class, $voyage, [
            'method' => 'POST',
            'action' => $this->generateUrl('voyages_edit', ['id' =>  $voyage->getId()])
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('voyages');

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

        return $this->render('voyages/edit.html.twig', [
            'voyage' => $voyage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="voyages_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em, Voyages $voyage): Response
    {
    

        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'voyages_delete'
                ,   [
                        'id' => $voyage->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $em->remove($voyage);
            $em->flush();

            $redirect = $this->generateUrl('voyages');

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


            return $this->render('voyages/delete.html.twig', [
                'voyage' => $voyage,
                'form' => $form->createView(),
            ]);
        }
    }
}
