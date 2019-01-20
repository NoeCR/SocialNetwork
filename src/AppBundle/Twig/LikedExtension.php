<?php
namespace AppBundle\Twig;
use Symfony\Bridge\Doctrine\RegistryInterface;

class LikedExtension extends \Twig_Extension{
   
    protected $doctrine;
    
    public function __construct(RegistryInterface $doctrine) {
        $this->doctrine = $doctrine;
    }
    
    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('liked', array($this, 'likedFilter'))
        );
    }
    
    public function likedFilter($user, $publication){
        $like_repo = $this->doctrine->getRepository('BackendBundle:Likes');
        
        $pub_liked = $like_repo->findOneBy(array(
            'user' => $user,
            'publication' => $publication
        ));
        
        if(!empty($pub_liked) && is_object($pub_liked)){
            $result = true;
        }else{
            $result = false;
        }
        return $result;
    }
    
    public function getName() {
        return 'liked_extension';
    }
}
