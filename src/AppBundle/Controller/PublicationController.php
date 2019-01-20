<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\PublicationType;
use BackendBundle\Entity\Publications;

class PublicationController extends Controller{
    
    public function indexAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $publication = new Publications();
        
        $form = $this->createForm(PublicationType::class, $publication);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted()){
            if($form->isValid()){
                
                //upload imagen
                $file = $form['image']->getData();
                if(!empty($file) && $file != null){
                    $ext = strtolower($file->guessExtension());
                    $formats = ['jpg', 'png', 'jpeg'];
                    if(in_array($ext, $formats)){
                        $file_name = $user->getId().time().'.'.$ext;
                        $file->move("uploads/publications/images", $file_name);
                        $publication->setImage($file_name);
                    }else{
                        $publication->setImage(null);
                    }                    
                }else{
                    $publication->setImage(null);
                }
                //upload document
                
                $doc = $form['document']->getData();
                if(!empty($doc) && $doc != null){
                    $ext = strtolower($doc->guessExtension());
                    $formats = ['pdf', 'txt', 'doc'];
                    if(in_array($ext, $formats)){
                        $doc_name = $user->getId().time().'.'.$ext;
                        $doc->move("uploads/publications/documents", $doc_name);
                        $publication->setDocument($doc_name);
                    }else{
                        $publication->setDocument(null);
                    }                  
                }else{
                    $publication->setDocument(null);
                }
                $publication->setUser($user);
                $publication->setCreatedAt(new \DateTime('now'));
                
                $em->persist($publication);
                $flush = $em->flush();
                if($flush == null){
                    $status = 'Publicado correctamente';
                    $this->addFlash('success', $status);
                }else{
                    $status = 'Error al añadir la publicación';
                    $this->addFlash('error', $status);
                }
            }else{
                $satatus = 'No se ha podido publicar, el formulario no es valido';
                $this->addFlash('error', $status);
            }
            return $this->redirectToRoute('home_publications');
        }
        if (is_object($this->getUser())) {
            $publications = $this->getPublications($request);

             return $this->render('AppBundle:Publication:home.html.twig', array(
                 'form' => $form->createView(),
                 'pagination' => $publications
             ));
        }
        return $this->redirect('login');
    }
    
    public function getPublications(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        
        $publications_repo = $em->getRepository('BackendBundle:Publications');
        $following_repo = $em->getRepository('BackendBundle:Following');
        
        $following = $following_repo->findBy(array('user' => $user));
        $following_array = array();
        
        foreach ($following as $follow){
            array_push($following_array, $follow->getFollowed());
        }
        
        $query = $publications_repo->createQueryBuilder('p')
                ->where('p.user = (:user_id) OR p.user IN (:following)')
                ->setParameter('user_id', $user->getId())
                ->setParameter('following', $following_array)
                ->orderBy('p.id', 'DESC')
                ->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1), 5);
        
        return $pagination;
    }
    
    public function removePublicationAction(Request $request, $id = null){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $publication_repo = $em->getRepository('BackendBundle:Publications');
        $publication = $publication_repo->find($id);
        if($user->getId() == $publication->getUser()->getId()){
            $em->remove($publication);
            $flush = $em->flush();
            
            if($flush == null){
                $status = 'Publicación eliminada correctamente';
                //$this->addFlash('succcess', $status);
            }else{
                $status = 'No se ha podido eliminar la publicación';
                //$this->addFlash('error', $status);
            }            
        }else{
            $status = 'No tiene permisos para eliminar esta publicación';
            //$this->addFlash('error', $status);
        }             
        return new Response($status);
    }
}
