<?php

namespace App\Controller;

use App\Entity\Libelle;
use App\Form\LibelleType;
use App\Repository\LibelleRepository;
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
 * @Route("/libelle")
 */
class LibelleController extends AbstractController
{
    /**
     * @Route("/", name="libelles", methods={"GET", "POST"})
     */
    public function index(GridManager $gridManager): Response
    {

        $source = new Entity(Libelle::class);

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
        $grid->hideColumns('active');
        $grid->setSource($source);

        $grid->setRouteUrl($this->generateUrl('libelles'));


        $rowAction = new RowAction('Détails', 'libelle_show');
       
        $grid->addRowAction($rowAction);

        $rowAction = new RowAction('Modifier', 'libelle_edit');
        $grid->addRowAction($rowAction);

   
        /*$rowAction = new RowAction('Supprimer', 'libelle_delete');
        $rowAction->setAttributes(['data-target' => '#stack2']);
        $grid->addRowAction($rowAction);*/

        return $grid->getGridResponse('libelle/index.html.twig');
    }

    /**
     * @Route("/new", name="libelle_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, FormError $formError): Response
    {
        $libelle = new Libelle();
        $form = $this->createForm(LibelleType::class, $libelle, [
            'method' => 'POST',
            'action' => $this->generateUrl('libelle_new')
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('libelles');

            if ($form->isValid()) {
                $em->persist($libelle);
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

        return $this->render('libelle/new.html.twig', [
            'libelle' => $libelle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="libelle_show", methods={"GET"})
     */
    public function show(Libelle $libelle): Response
    {
        return $this->render('libelle/show.html.twig', [
            'libelle' => $libelle,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="libelle_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Libelle $libelle, FormError $formError, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(LibelleType::class, $libelle, [
            'method' => 'POST',
            'action' => $this->generateUrl('libelle_edit', ['id' =>  $libelle->getId()])
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('libelles');

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

        return $this->render('libelle/edit.html.twig', [
            'libelle' => $libelle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="libelle_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em, Libelle $libelle): Response
    {
    

        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'libelle_delete'
                ,   [
                        'id' => $libelle->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $em->remove($libelle);
            $em->flush();

            $redirect = $this->generateUrl('libelles');

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


            return $this->render('libelle/delete.html.twig', [
                'libelle' => $libelle,
                'form' => $form->createView(),
            ]);
        }
    }
}
