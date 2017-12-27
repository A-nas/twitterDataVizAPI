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

    public function getTopwordsAction(Request $request){ // will return only hashtags
        $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
        try{
            $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
            $command = new MongoDB\Driver\Command( [
                ['$project' => ['words' => ['$split' => ['$teweet',' '] ] ] ],
                ['$unwind' => '$words'],
                ['$match' => ['words' => '/^#/']],
                ['$group' => ['_id' => ['word' => '$words'],'total_amount' => ['$sum' => 1] ] ],
                ['$sort' => [ 'total_amount' => -1 ] ]
                ]);
            //$cursor = $manager->executeCommand('paperman.course', $command);
            //return new JsonResponse( json_encode( $cursor->toArray() ) );
        }catch (Exception $e) {
            echo 'Exception reçue : ',  $e->getMessage(), "\n";
        }

        /*$query = array(
                     array('$project' => array('words' => array('$split' => ["$teweet", " "]))),
                     array('$unwind' => "$words"),
                     array('$match' => array('words' => '/^#/')),
                     array('$group' => array('_id' => array('word' => 'words'), total_amount => array('$sum' => 1))),
                     array('$sort' => array('total_amount' => -1)));*/
    
    }
}

?>