<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BackendBundle\Entity\Users;
use BackendBundle\Entity\PrivateMessages;
use AppBundle\Form\PrivateMessageType;

class PrivateMessageController extends Controller {

    public function indexAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        //var_dump($user);
        
        $private_message = new PrivateMessages();
        
        $form = $this->createForm(PrivateMessageType::class, $private_message, array(
            'empty_data' => $user
        ));
        
        $form->handleRequest($request);
        if($form->isSubmitted()){
            if($form->isValid()){
                $file = $form['image']->getData();
                if(!empty($file) && $file != null){
                    $ext = strtolower($file->guessExtension());
                    $formats = ['jpg', 'png', 'jpeg'];
                    if(in_array($ext, $formats)){
                        $file_name = $user->getId().time().'.'.$ext;
                        $file->move("uploads/messages/images", $file_name);
                        $private_message->setImage($file_name);
                    }else{
                        $private_message->setImage(null);
                    }                    
                }else{
                    $private_message->setImage(null);
                }
                //upload document
                
                $doc = $form['file']->getData();
                if(!empty($doc) && $doc != null){
                    $ext = strtolower($doc->guessExtension());
                    $formats = ['pdf', 'txt', 'doc'];
                    if(in_array($ext, $formats)){
                        $doc_name = $user->getId().time().'.'.$ext;
                        $doc->move("uploads/messages/documents", $doc_name);
                        $private_message->setFile($doc_name);
                    }else{
                        $private_message->setFile(null);
                    }                  
                }else{
                    $private_message->setFile(null);
                }
                $private_message->setEmitter($user);
                $private_message->setCreatedAt(new \DateTime('now'));
                $private_message->setReaded(0);
                $em->persist($private_message);
                $flush = $em->flush();
                
                if($flush == null){
                    $status = 'Mensaje privado enviado correctamente';
                    $this->addFlash('success', $status);
                }else{
                    $status = 'El mensaje privado no se ha enviado';
                    $this->addFlash('error', $status);
                }
            }else{
                $status = 'El mensaje privado no se ha enviado';
                $this->addFlash('error', $status);
            }
            return $this->redirectToRoute('private_message_index');
        }
        
        $private_messages = $this->getPrivateMesages($request);
        //llamamos al metodo para setear como leidos todos los mensajes
        $this->setReaded($em, $user);
        
        return $this->render('AppBundle:PrivateMessage:index.html.twig', array(
            'form' => $form->createView(),
            'pagination' => $private_messages
        ));
    }
    
    public function sendedAction(Request $request){
        $private_messages = $this->getPrivateMesages($request, 'sended');
        return $this->render('AppBundle:PrivateMessage:sended.html.twig', array(
            'pagination' => $private_messages
        ));
    }
    
    public function getPrivateMesages($request , $type = null){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        
        $user_id = $user->getId();
        
        if($type == 'sended'){
            $dql = "SELECT p FROM BackendBundle:PrivateMessages p WHERE"
                    . " p.emitter = $user_id ORDER BY p.id DESC";
        }else{
            $dql = "SELECT p FROM BackendBundle:PrivateMessages p WHERE"
                    . " p.receiver = $user_id ORDER BY p.id DESC";
        }
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1), 5);
        
        return $pagination;
    }
    
    public function notReadedAction(){
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->getUser();
        $private_message_repo = $em->getRepository('BackendBundle:PrivateMessages');
        $count_not_readed_msg = count($private_message_repo->findBy(array(
            'receiver' => $user,
            'readed' => 0
        )));
        return new Response($count_not_readed_msg);
    }
    
    public function setReaded($em, $user){
        $private_message_repo = $em->getRepository('BackendBundle:PrivateMessages');
        $messages = $private_message_repo->findBy(array(
            'receiver' => $user,
            'readed' => 0
        ));
        foreach($messages as $msg){
            $msg->setReaded(1);
            $em->persist($msg);
        }
        $flush = $em->flush();
        
        if($flush == null){
            $result = true;
        }else{
            $result = false;
        }
        
        return $result;
    }
}
