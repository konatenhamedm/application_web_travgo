<?php

namespace App\Controller;

use App\Entity\Arrets;
use App\Form\ArretsType;
use App\Repository\ArretsRepository;
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
 * @Route("/arrets")
 */
class ArretsController extends AbstractController
{
    /**
     * @Route("/", name="arrets", methods={"GET", "POST"})
     */
    public function index(GridManager $gridManager): Response
    {

        $source = new Entity(Arrets::class);

        //$grid = $this->get('grid');
        $grid = $gridManager->createGrid();
        $tableAlias = $source->getTableAlias();
        $source->manipulateQuery(
            function ($query) use ($tableAlias) {/*
                $query->andWhere($tableAlias . ".active = 1 ");*/
                $query->resetDQLPart('orderBy');
                $query->addOrderBy($tableAlias . '.id', 'DESC');
            }
        );
        $grid->hideColumns('id');
        $grid->hideColumns('active');
        $grid->setSource($source);

        $grid->setRouteUrl($this->generateUrl('arrets'));


        $rowAction = new RowAction('Détails', 'arrets_show');
       
        $grid->addRowAction($rowAction);

        $rowAction = new RowAction('Modifier', 'arrets_edit');
        $grid->addRowAction($rowAction);

   
        /*$rowAction = new RowAction('Supprimer', 'arrets_delete');
        $rowAction->setAttributes(['data-target' => '#stack2']);
        $grid->addRowAction($rowAction);*/

        return $grid->getGridResponse('arrets/index.html.twig');
    }

    /**
     * @Route("/new", name="arrets_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, FormError $formError): Response
    {
        $arret = new Arrets();
        $form = $this->createForm(ArretsType::class, $arret, [
            'method' => 'POST',
            'action' => $this->generateUrl('arrets_new')
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('arrets');

            if ($form->isValid()) {
                $em->persist($arret);
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

        return $this->render('arrets/new.html.twig', [
            'arret' => $arret,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="arrets_show", methods={"GET"})
     */
    public function show(Arrets $arret): Response
    {
        return $this->render('arrets/show.html.twig', [
            'arret' => $arret,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="arrets_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Arrets $arret, FormError $formError, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ArretsType::class, $arret, [
            'method' => 'POST',
            'action' => $this->generateUrl('arrets_edit', ['id' =>  $arret->getId()])
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('arrets');

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

        return $this->render('arrets/edit.html.twig', [
            'arret' => $arret,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="arrets_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em, Arrets $arret): Response
    {
    

        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'arrets_delete'
                ,   [
                        'id' => $arret->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $em->remove($arret);
            $em->flush();

            $redirect = $this->generateUrl('arrets');

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


            return $this->render('arrets/delete.html.twig', [
                'arret' => $arret,
                'form' => $form->createView(),
            ]);
        }
    }
}
