<?php

namespace App\Controller;

use App\Entity\Vehicules;
use App\Form\VehiculesType;
use App\Repository\VehiculesRepository;
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
 * @Route("/vehicules")
 */
class VehiculesController extends AbstractController
{
    /**
     * @Route("/", name="vehicules", methods={"GET", "POST"})
     */
    public function index(GridManager $gridManager): Response
    {

        $source = new Entity(Vehicules::class);

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

        $grid->setRouteUrl($this->generateUrl('vehicules'));


        $rowAction = new RowAction('Détails', 'vehicules_show');
       
        $grid->addRowAction($rowAction);

        $rowAction = new RowAction('Modifier', 'vehicules_edit');
        $grid->addRowAction($rowAction);

   
        /*$rowAction = new RowAction('Supprimer', 'vehicules_delete');
        $rowAction->setAttributes(['data-target' => '#stack2']);
        $grid->addRowAction($rowAction);*/

        return $grid->getGridResponse('vehicules/index.html.twig');
    }

    /**
     * @Route("/new", name="vehicules_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, FormError $formError): Response
    {
        $vehicule = new Vehicules();
        $form = $this->createForm(VehiculesType::class, $vehicule, [
            'method' => 'POST',
            'action' => $this->generateUrl('vehicules_new')
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('vehicules');

            if ($form->isValid()) {
                $em->persist($vehicule);
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

        return $this->render('vehicules/new.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="vehicules_show", methods={"GET"})
     */
    public function show(Vehicules $vehicule): Response
    {
        return $this->render('vehicules/show.html.twig', [
            'vehicule' => $vehicule,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="vehicules_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Vehicules $vehicule, FormError $formError, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(VehiculesType::class, $vehicule, [
            'method' => 'POST',
            'action' => $this->generateUrl('vehicules_edit', ['id' =>  $vehicule->getId()])
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('vehicules');

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

        return $this->render('vehicules/edit.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="vehicules_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em, Vehicules $vehicule): Response
    {
    

        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'vehicules_delete'
                ,   [
                        'id' => $vehicule->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $em->remove($vehicule);
            $em->flush();

            $redirect = $this->generateUrl('vehicules');

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


            return $this->render('vehicules/delete.html.twig', [
                'vehicule' => $vehicule,
                'form' => $form->createView(),
            ]);
        }
    }
}
