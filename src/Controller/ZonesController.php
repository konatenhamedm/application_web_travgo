<?php

namespace App\Controller;

use App\Entity\Zones;
use App\Form\ZonesType;
use App\Repository\ZonesRepository;
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
 * @Route("/zones")
 */
class ZonesController extends AbstractController
{
    /**
     * @Route("/", name="zones", methods={"GET", "POST"})
     */
    public function index(GridManager $gridManager): Response
    {

        $source = new Entity(Zones::class);

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

        $grid->setRouteUrl($this->generateUrl('zones'));


        $rowAction = new RowAction('Détails', 'zones_show');
       
        $grid->addRowAction($rowAction);

        $rowAction = new RowAction('Modifier', 'zones_edit');
        $grid->addRowAction($rowAction);

   
        /*$rowAction = new RowAction('Supprimer', 'zones_delete');
        $rowAction->setAttributes(['data-target' => '#stack2']);
        $grid->addRowAction($rowAction);*/

        return $grid->getGridResponse('zones/index.html.twig');
    }

    /**
     * @Route("/new", name="zones_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, FormError $formError): Response
    {
        $zone = new Zones();
        $form = $this->createForm(ZonesType::class, $zone, [
            'method' => 'POST',
            'action' => $this->generateUrl('zones_new')
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('zones');

            if ($form->isValid()) {
                $em->persist($zone);
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

        return $this->render('zones/new.html.twig', [
            'zone' => $zone,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="zones_show", methods={"GET"})
     */
    public function show(Zones $zone): Response
    {
        return $this->render('zones/show.html.twig', [
            'zone' => $zone,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="zones_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Zones $zone, FormError $formError, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ZonesType::class, $zone, [
            'method' => 'POST',
            'action' => $this->generateUrl('zones_edit', ['id' =>  $zone->getId()])
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('zones');

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

        return $this->render('zones/edit.html.twig', [
            'zone' => $zone,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="zones_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em, Zones $zone): Response
    {
    

        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'zones_delete'
                ,   [
                        'id' => $zone->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $em->remove($zone);
            $em->flush();

            $redirect = $this->generateUrl('zones');

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


            return $this->render('zones/delete.html.twig', [
                'zone' => $zone,
                'form' => $form->createView(),
            ]);
        }
    }
}
