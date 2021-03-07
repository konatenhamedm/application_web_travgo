<?php

namespace App\Controller;

use App\Entity\Lignes;
use App\Form\LignesType;
use App\Repository\LignesRepository;
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
 * @Route("/lignes")
 */
class LignesController extends AbstractController
{
    /**
     * @Route("/", name="lignes", methods={"GET", "POST"})
     */
    public function index(GridManager $gridManager): Response
    {

        $source = new Entity(Lignes::class);

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

        $grid->setRouteUrl($this->generateUrl('lignes'));


        $rowAction = new RowAction('Détails', 'lignes_show');
       
        $grid->addRowAction($rowAction);

        $rowAction = new RowAction('Modifier', 'lignes_edit');
        $grid->addRowAction($rowAction);

   
        /*$rowAction = new RowAction('Supprimer', 'lignes_delete');
        $rowAction->setAttributes(['data-target' => '#stack2']);
        $grid->addRowAction($rowAction);*/

        return $grid->getGridResponse('lignes/index.html.twig');
    }

    /**
     * @Route("/new", name="lignes_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, FormError $formError): Response
    {
        $ligne = new Lignes();
        $form = $this->createForm(LignesType::class, $ligne, [
            'method' => 'POST',
            'action' => $this->generateUrl('lignes_new')
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('lignes');

            if ($form->isValid()) {
                $em->persist($ligne);
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

        return $this->render('lignes/new.html.twig', [
            'ligne' => $ligne,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="lignes_show", methods={"GET"})
     */
    public function show(Lignes $ligne): Response
    {
        return $this->render('lignes/show.html.twig', [
            'ligne' => $ligne,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="lignes_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Lignes $ligne, FormError $formError, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(LignesType::class, $ligne, [
            'method' => 'POST',
            'action' => $this->generateUrl('lignes_edit', ['id' =>  $ligne->getId()])
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('lignes');

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

        return $this->render('lignes/edit.html.twig', [
            'ligne' => $ligne,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="lignes_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em, Lignes $ligne): Response
    {
    

        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'lignes_delete'
                ,   [
                        'id' => $ligne->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $em->remove($ligne);
            $em->flush();

            $redirect = $this->generateUrl('lignes');

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


            return $this->render('lignes/delete.html.twig', [
                'ligne' => $ligne,
                'form' => $form->createView(),
            ]);
        }
    }
}
