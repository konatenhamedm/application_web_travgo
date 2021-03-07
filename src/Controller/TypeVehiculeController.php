<?php

namespace App\Controller;

use App\Entity\TypeVehicule;
use App\Form\TypeVehiculeType;
use App\Repository\TypeVehiculeRepository;
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
 * @Route("/typevehicules")
 */
class TypeVehiculeController extends AbstractController
{
    /**
     * @Route("/", name="typevehicules", methods={"GET", "POST"})
     *
     */
    public function index(GridManager $gridManager): Response
    {

        $source = new Entity(TypeVehicule::class);

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

        $grid->setRouteUrl($this->generateUrl('typevehicules'));


        $rowAction = new RowAction('Détails', 'type_vehicule_show');
       
        $grid->addRowAction($rowAction);

        $rowAction = new RowAction('Modifier', 'type_vehicule_edit');
        $grid->addRowAction($rowAction);

   
        /*$rowAction = new RowAction('Supprimer', 'type_vehicule_delete');
        $rowAction->setAttributes(['data-target' => '#stack2']);
        $grid->addRowAction($rowAction);*/

        return $grid->getGridResponse('type_vehicule/index.html.twig');
    }

    /**
     * @Route("/new", name="type_vehicule_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, FormError $formError): Response
    {
        $typeVehicule = new TypeVehicule();
        $form = $this->createForm(TypeVehiculeType::class, $typeVehicule, [
            'method' => 'POST',
            'action' => $this->generateUrl('type_vehicule_new')
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('typevehicules');

            if ($form->isValid()) {
                $em->persist($typeVehicule);
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

        return $this->render('type_vehicule/new.html.twig', [
            'type_vehicule' => $typeVehicule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="type_vehicule_show", methods={"GET"})
     */
    public function show(TypeVehicule $typeVehicule): Response
    {
        return $this->render('type_vehicule/show.html.twig', [
            'type_vehicule' => $typeVehicule,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="type_vehicule_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TypeVehicule $typeVehicule, FormError $formError, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TypeVehiculeType::class, $typeVehicule, [
            'method' => 'POST',
            'action' => $this->generateUrl('type_vehicule_edit', ['id' =>  $typeVehicule->getId()])
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('typevehicules');

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

        return $this->render('type_vehicule/edit.html.twig', [
            'type_vehicule' => $typeVehicule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="type_vehicule_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em, TypeVehicule $typeVehicule): Response
    {
    

        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'type_vehicule_delete'
                ,   [
                        'id' => $typeVehicule->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $em->remove($typeVehicule);
            $em->flush();

            $redirect = $this->generateUrl('typevehicules');

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


            return $this->render('type_vehicule/delete.html.twig', [
                'type_vehicule' => $typeVehicule,
                'form' => $form->createView(),
            ]);
        }
    }
}
