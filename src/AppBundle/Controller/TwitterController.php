<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;

class TwitterController extends Controller
{
	// careful ! any route anotation added here will be ignored
    public function getWordsAction($type, Request $request){
    	$viewHandler = $this->get('fos_rest.view_handler');
    	// Création d'une vue FOSRestBundle
        $view = View::create('{ "hello" : "word"} ');
        $view->setFormat('json');
        // Gestion de la réponse
        return $viewHandler->handle($view);
        //return new JsonResponse('{ "hello" : "word"}');
    }
}

?>