<?php

namespace BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user_repo = $em->getRepository("BackendBundle:Users");  
        $user = $user_repo->find(1);
        
        echo "Beinevnido: " . $user->getName() . " " . $user->getSurname() . " " . $user->getEmail();
        var_dump($user);
        die();
        return $this->render('BackendBundle:Default:index.html.twig');
    }
}
