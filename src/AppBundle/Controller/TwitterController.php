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
    	//$viewHandler = $this->get('fos_rest.view_handler');
        //$view = View::create('{ "hello" : "word"} ');
        //$view->setFormat('json');
        //return $viewHandler->handle($view);
    	$manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
        $query = new \MongoDB\Driver\Query(array('tweetDate' => '4 nov. 2014'));
        $cursor = $manager->executeQuery('paperman.course', $query);
        //print(json_encode($cursor->toArray()));
        return new JsonResponse( json_encode( $cursor->toArray() ) );
        #return new JsonResponse('{ "hello" : "word"}');
    }
}

?>