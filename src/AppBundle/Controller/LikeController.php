<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

use BackendBundle\Entity\Publications;
use BackendBundle\Entity\Users;
use BackendBundle\Entity\Likes;

class LikeController extends Controller{
    
    public function likeAction($id = null){
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $pub_repo = $em->getRepository('BackendBundle:Publications');
        $publication = $pub_repo->find($id);
        
        $like = new Likes();
        
        $like->setPublication($publication);
        $like->setUser($user);
        
        $em->persist($like);
        $flush = $em->flush();
        
        if($flush == null){
            $notification = $this->get('app.notification_service');
            $notification->set($publication->getUser(), 'like', $user->getId(), $publication->getId());
            
            $status = 'Le has dado Like!';
        }else{
            $status  = 'Error al guardar tu like :/';
        }
        return new Response($status);
    }
    
    public function unlikeAction($id = null){
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $like_repo = $em->getRepository('BackendBundle:Likes');
        $like = $like_repo->findOneBy(array(
            'user' => $user,
            'publication' => $id
        ));
        
        $em->remove($like);
        $flush = $em->flush();
        
        if($flush == null){
            $status = 'Ya no te gusta esta publicación!';
        }else{
            $status = 'No se ha podido desmarcar el me gusta';
        }
        return new Response($status);
    }
    
    public function likesAction(Request $request, $nickname = null){
        $em = $this->getDoctrine()->getManager();
        if($nickname != null){
            $user_repo = $em->getRepository('BackendBundle:Users');
            $user = $user_repo->findOneBy(array('nick' => $nickname));
        }else{
            $user = $this->getUser();
        }
        
        if(empty($user) || !is_object($user)){
            return $this->redirect($this->generateUrl('home_publications'));
        }
        
        $user_id = $user->getId();
        $dql = "SELECT p FROM BackendBundle:Likes p WHERE p.user = $user_id ORDER BY p.id DESC";
        $query = $em->createQuery($dql);
        
        $paginator = $this->get('knp_paginator');
        $likes = $paginator->paginate($query, $request->query->getInt('page', 1), 5);
        
        return $this->render('AppBundle:Like:likes.html.twig', array(
            'user' => $user,
            'pagination' => $likes
        ));        
    }
}
