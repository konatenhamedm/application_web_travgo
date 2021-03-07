<?php

namespace App\Controller;

use App\Entity\Personnes;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\DataGrid\RowAction;
use App\Service\FormError;
use APY\DataGridBundle\Grid\Source\Entity;
use Doctrine\ORM\EntityManagerInterface;
use APY\DataGridBundle\Grid\GridManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/utilisateur")
 */
class UtilisateurController extends AbstractController
{
    /**
     * @Route("/", name="utilisateur_index", methods={"GET", "POST"})
     */
    public function index(GridManager $gridManager): Response
    {

        $source = new Entity(Utilisateur::class);

        //$grid = $this->get('grid');
        $grid = $gridManager->createGrid();
        $grid->hideColumns('id');
        $grid->setSource($source);

        $grid->setRouteUrl($this->generateUrl('utilisateur_index'));


        $rowAction = new RowAction('Détails', 'utilisateur_show');
       
        $grid->addRowAction($rowAction);

        $rowAction = new RowAction('Modifier', 'utilisateur_edit');
        $grid->addRowAction($rowAction);

   
        /*$rowAction = new RowAction('Supprimer', 'utilisateur_delete');
        $rowAction->setAttributes(['data-target' => '#stack2']);
        $grid->addRowAction($rowAction);*/

        return $grid->getGridResponse('utilisateur/index.html.twig');
    }

    /** 
     * @Route("/paginate", name="utilisateur_paginate") 
     */ 
    public function paginateAction(Request $request) 
    { 
        $length = $request->get('length'); 
        $length = $length && ($length!=-1)?$length:0; 
  
        $start = $request->get('start'); 
        $start = $length?($start && ($start!=-1)?$start:0)/$length:0; 
  
        $search = $request->get('search'); 
        $filters = [ 
            'query' => @$search['value'] 
        ]; 
  
        $utilisateurs = $this->getDoctrine()->getRepository('App:Utilisateur')->search($filters, $start, $length); 
  
        $output = array( 
            'data'            => array(), 
            'recordsFiltered' => count($this->getDoctrine()->getRepository('App:Utilisateur')->search($filters, 0, false)), 
            'recordsTotal'    => count($this->getDoctrine()->getRepository('App:Utilisateur')->search(array(), 0, false)) 
        ); 

        foreach ($utilisateurs as $utilisateur) { 
            $output['data'][] = [ 
                'id' => $utilisateur->getId(), 
                'mail' => $utilisateur->getEmail(), 
                'nom' => $utilisateur->getEmploye()->getNom(), 
                'prenom' => $utilisateur->getEmploye()->getPrenom(), 
                'fonction' => $utilisateur->getEmploye()->getFonction()->getLibelle(), 
            ]; 
        } 
  
        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']); 
    } 

    /**
     * @Route("/new", name="utilisateur_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, FormError $formError,UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur, [
            'method' => 'POST',
            'action' => $this->generateUrl('utilisateur_new')
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('utilisateur_index');
            $utilisateur->setPassword(
                $passwordEncoder->encodePassword(
                    $utilisateur,
                    $form->get('password')->getData()
                )
            );
            if ($form->isValid()) {
                $em->persist($utilisateur);
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

        return $this->render('utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="utilisateur_show", methods={"GET"})
     */
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="utilisateur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Utilisateur $utilisateur,UserPasswordEncoderInterface $passwordEncoder,RequestStack $path,FormError $formError, EntityManagerInterface $em): Response
    {

        $paths = $path->getCurrentRequest()->getRequestUri();
        $template_name = explode("/", $paths);
        /*dd($template_name[2]);*/
        $form = $this->createForm(UtilisateurType::class, $utilisateur, [
            'method' => 'POST',
            'action' => $this->generateUrl('utilisateur_edit', ['id' =>  $template_name[2]])
        ]);
        $form->handleRequest($request);

        $isAjax = $request->isXmlHttpRequest();

        if ($form->isSubmitted()) {

            $response = [];
            $redirect = $this->generateUrl('utilisateur_index');
            $utilisateur->setPassword(
                $passwordEncoder->encodePassword(
                    $utilisateur,
                    $form->get('password')->getData()
                )
            );
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

        return $this->render('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="utilisateur_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em, Utilisateur $utilisateur): Response
    {
    

        $form = $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                'utilisateur_delete'
                ,   [
                        'id' => $utilisateur->getId()
                    ]
                )
            )
            ->setMethod('DELETE')
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $em->remove($utilisateur);
            $em->flush();

            $redirect = $this->generateUrl('utilisateur_index');

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


            return $this->render('utilisateur/delete.html.twig', [
                'utilisateur' => $utilisateur,
                'form' => $form->createView(),
            ]);
        }
    }
}
