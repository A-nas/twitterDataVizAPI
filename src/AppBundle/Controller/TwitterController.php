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
	 
    public $type = array('Hashtag'=> '#','Mention'=>'@');

    public function getWordsAction(Request $request){

    	$manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
        $query = new \MongoDB\Driver\Query(array('tweetDate' => '4 nov. 2014'), array('projection' => [ 'teweet' => 1 , 'tweetDate' => 1 ]));
        $cursor = $manager->executeQuery('paperman.course', $query);
        return new JsonResponse( json_encode( $cursor->toArray() ) );
    }

    // remove backslashs and "'s" notation
    public function getTopwordsAction(Request $request,$by){

        if(!array_key_exists($by, $this->type)){
            $error = array("Success" => "False","Anmalie" =>"parametre non pris en charge");
            http_response_code(500);
            return new JsonResponse( json_encode( $error ) );
        }
        $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
        $command = new \MongoDB\Driver\Command([
                'aggregate' => 'course',
                'pipeline' => [
                    ['$project' => ['words' => ['$split' => ['$teweet',' '] ] ] ],
                    ['$unwind' => '$words'],
                    ['$match' => ['words' => new \MongoDB\BSON\Regex('^'.$this->type[$by])]],
                    ['$group' => ['_id' => ['word' => '$words'],'total_amount' => ['$sum' => 1] ] ],
                    ['$sort' => [ 'total_amount' => -1 ] ],
                    ['$limit' => 10]
                ],
                'cursor' => new \stdClass,]);
        $cursor = $manager->executeCommand('paperman', $command);
        return new JsonResponse( json_encode( $cursor->toArray() ) );
    }

    public function getTweetbydayAction(Request $request){

        $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
        $command = new \MongoDB\Driver\Command([
                'aggregate' => 'course',
                'pipeline' => [
                    ['$group' => ['_id' => '$tweetDate' , 'total_amount' => ['$sum' => 1] ] ],
                    //['$sort' => [ 'total_amount' => -1 ] ] //sort by date when date is formated
                ],
                'cursor' => new \stdClass,]);
        $cursor = $manager->executeCommand('paperman', $command);
        return new JsonResponse( json_encode( $cursor->toArray() ) );
    }

    //max per month&year ?
    public function getToptweetsAction(Request $request){

        $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
        //$query = new \MongoDB\Driver\Query([], [['$limit' => 10 ] , '$sort' => [ 'favorite' => -1 ]]);
        
        $command = new \MongoDB\Driver\Command([
                'aggregate' => 'course',
                'pipeline' => [
                    ['$project' => ['tweet' => '$teweet', 'favorite' => '$favorite'] ],
                    ['$sort' => [ 'favorite' => -1 ] ],
                    ['$limit' => 10],
                ],
                'cursor' => new \stdClass,]);   
        $cursor = $manager->executeCommand('paperman', $command);
        return new JsonResponse( json_encode( $cursor->toArray() ) );
    }

    //ne marche pas
    public function gettopfavoriteAction(Request $request){

        $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
        $query = new \MongoDB\Driver\Query(['tweetDate' => '4 nov. 2014'], [
            'projection' => [ 'teweet' => 1 , 'favorite' => 1 ],
            '$sort' => ['favorite' => -1],
            '$limit' => 10,
        ]);
        $cursor = $manager->executeQuery('paperman.course', $query);
        return new JsonResponse( json_encode( $cursor->toArray() ) );
    }
}

?>