<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use BackendBundle\Entity\Users;
use BackendBundle\Entity\Following;

class FollowingController extends Controller {

    public function __construct() {
        
    }

    public function followAction(Request $request) {
        $user = $this->getUser();
        $followed_id = $request->get('followed');

        $em = $this->getDoctrine()->getManager();

        $user_repo = $em->getRepository('BackendBundle:USers');
        $followed = $user_repo->find($followed_id);

        $following = new Following();
        $following->setUser($user);
        $following->setFollowed($followed);

        $em->persist($following);
        $flush = $em->flush();

        if ($flush == null) {
            $status = "Ahora estas siguiendo a usuario";
        } else {
            $status = "No se ha podido seguir a usuario";
        }
        return new Response($status);
    }

    public function unfollowAction(Request $request) {
        $user = $this->getUser();
        $followed_id = $request->get('followed');

        $em = $this->getDoctrine()->getManager();
        $following_repo = $em->getRepository('BackendBundle:Following');
        $followed = $following_repo->findOneBy(array('user' => $user, 'followed' => $followed_id));

        $em->remove($followed);
     
        $flush = $em->flush();

        if ($flush == null) {
            $status = "Has dejado de seguir a usuario";
        } else {
            $status = "No se ha podido dejar de seguir a usuario";
        }
        return new Response($status);
    }

    public function followingAction(Request $request, $nickname = null){
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
        $dql = "SELECT p FROM BackendBundle:Following p WHERE p.user = $user_id ORDER BY p.id DESC";
        $query = $em->createQuery($dql);
        
        $paginator = $this->get('knp_paginator');
        $following = $paginator->paginate($query, $request->query->getInt('page', 1), 5);
        
        return $this->render('AppBundle:Following:following.html.twig', array(
            'type' => 'following',
            'profile_user' => $user,
            'pagination' => $following
        ));        
    }
    
    public function followedAction(Request $request, $nickname = null){
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
        $dql = "SELECT p FROM BackendBundle:Following p WHERE p.followed = $user_id ORDER BY p.id DESC";
        $query = $em->createQuery($dql);
        
        $paginator = $this->get('knp_paginator');
        $followed = $paginator->paginate($query, $request->query->getInt('page', 1), 5);
        
        return $this->render('AppBundle:Following:following.html.twig', array(
            'type' => 'followed',
            'profile_user' => $user,
            'pagination' => $followed
        ));        
    }
}
