<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;

class TwitterController extends Controller
{
	// careful ! any route anotation added here will be ignored
	 

    public function getWordsAction(Request $request){
    	//$connection = new MongoClient();
    	//$viewHandler = $this->get('fos_rest.view_handler');
        //$view = View::create('{ "hello" : "word"} ');
        //$view->setFormat('json');
        //return $viewHandler->handle($view);
        return new JsonResponse('{ "hello" : "word"}');
    }
}

?>