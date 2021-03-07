<?php

namespace App\Controller;

use App\Entity\Typedestinations;
use App\Form\TypedestinationsType;
use App\Repository\TypedestinationsRepository;
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
 * @Route("/typedestinations")
 */
class TypedestinationsController extends AbstractController
{
    /**
     * @Route("/", name="typedestinations", methods={"GET", "POST"})
     */
    public function index(GridManager $gridManager): Response
    {

        $source = new Entity(Typedestinations::class);

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

        $grid->setRouteUrl($this->generateUrl('typedestinations'));


        $rowAction = new RowAction('Détails', 'typedestinations_show');
       
        $grid->addRowAction($rowAction);

        $rowAction = new RowAction('Modifier', 'typedestinations_edit');
        $grid->addRowAction($rowAction);

   
        /*$rowAction = new RowAction('Supprimer', 'typedestinations_delete');
        $rowAction->setAttributes(['data-target' => '#stack2']);
        $grid->addRowAction($rowAction);*/

        return $grid->getGridResponse('typedestinations/index.html.twig');
    }

    /**
     * @Route("/new", name="typedestinations_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, FormError $formError): Response
    {
        $typedestination = new Typedestinations();
        $form = $this->createForm(TypedestinationsType::class, $typedestination, [
            'method' => 'POST',
            'action' => $this->generateUrl('typedestinations_new')
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('typedestinations');

            if ($form->isValid()) {
                $em->persist($typedestination);
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

        return $this->render('typedestinations/new.html.twig', [
            'typedestination' => $typedestination,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="typedestinations_show", methods={"GET"})
     */
    public function show(Typedestinations $typedestination): Response
    {
        return $this->render('typedestinations/show.html.twig', [
            'typedestination' => $typedestination,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="typedestinations_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Typedestinations $typedestination, FormError $formError, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TypedestinationsType::class, $typedestination, [
            'method' => 'POST',
            'action' => $this->generateUrl('typedestinations_edit', ['id' =>  $typedestination->getId()])
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('typedestinations');

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

        return $this->render('typedestinations/edit.html.twig', [
            'typedestination' => $typedestination,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="typedestinations_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em, Typedestinations $typedestination): Response
    {
    

        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'typedestinations_delete'
                ,   [
                        'id' => $typedestination->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $em->remove($typedestination);
            $em->flush();

            $redirect = $this->generateUrl('typedestinations');

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


            return $this->render('typedestinations/delete.html.twig', [
                'typedestination' => $typedestination,
                'form' => $form->createView(),
            ]);
        }
    }
}
