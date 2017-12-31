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
    public $topby = array('favorits' => 'favorite', 'retweets' => 'retweet', 'comments' => 'comment');

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

    //max per month&year ? *** must go to stack
    public function getToptweetsAction(Request $request,$by){

        if(!array_key_exists($by, $this->topby)){
            $error = array("Success" => "False","Anmalie" =>"parametre non pris en charge");
            http_response_code(500);
            return new JsonResponse( json_encode( $error ) );
        }
        $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
        //$query = new \MongoDB\Driver\Query([], [['$limit' => 10 ] , '$sort' => [ 'favorite' => -1 ]]);
        
        $command = new \MongoDB\Driver\Command([
                'aggregate' => 'course',
                'pipeline' => [ // quand je change le parametre de favorite je recois un autre resultas
                    ['$project' => ['tweet' => '$teweet', $this->topby[$by] => '$'.$this->topby[$by]] ],
                    ['$sort' => [ $this->topby[$by] => -1 ] ],
                    ['$limit' => 10],
                ],
                'cursor' => new \stdClass,]);   
        $cursor = $manager->executeCommand('paperman', $command);
        return new JsonResponse( json_encode( $cursor->toArray() ) );
    }

    // deprecated
    public function getToptweetssAction(Request $request){

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

    //works !! must add parameter to get top of all numeric values
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


    //favorite per date (must use MapReduce)
    public function getToptweetperdayAction(Request $request){

        $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
        $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
        $command = new \MongoDB\Driver\Command([
                'aggregate' => 'yay',
                'pipeline' => [
                    [
                        '$project' => [
                            'tweet' => '$teweet' ,
                            'dates' => ['$dateToString' => [ 'format' => '%Y-%m-%d', 'date' => '$tweetDate' ]],
                            'Favorite' => '$favorite'
                                      ]
                    ],
                    ['$group' => 
                    ['_id' => [
                        'date' => '$dates'
                              ],
                        'FavoriteSum' => ['$sum' => '$Favorite'] ] ],
                    ['$sort' => [ 'tweetDate' => -1 ] ] //sort by date when date is formated
                ],
                'cursor' => new \stdClass,]);
        $cursor = $manager->executeCommand('paperman', $command);
        return new JsonResponse( json_encode( $cursor->toArray() ) );
    }

    // top words (use filter)
    public function getTopfiltredwordsAction(Request $request){
        $filter = array('to','as', 'the', 'a', 's', 'in', 'on', '.', ',', 'is', 'and', 'of', 'for'); // upper case included
        $manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
        $command = new \MongoDB\Driver\Command([
                'aggregate' => 'course',
                'pipeline' => [
                    ['$project' => ['words' => ['$split' => ['$teweet',' '] ] ] ],
                    ['$unwind' => '$words'],
                    ['$match' => [ 'words' => ['$nin' => $filter ]]],
                    //['$match' => ['words' => new \MongoDB\BSON\Regex('^'.$this->type[$by])]],
                    /*['$filter' => [ 
                        'input' => ['the','in'],
                        'as' => 'exc',
                        'cond' => ['words' => [ '$nin' => '$$num' ]] ]],*/
                    // add all properties
                    ['$group' => ['_id' => ['word' => '$words'],'total_amount' => ['$sum' => 1] ] ],
                    ['$sort' => [ 'total_amount' => -1 ] ],
                    ['$limit' => 10]
                ],
                'cursor' => new \stdClass,]);
        $cursor = $manager->executeCommand('paperman', $command);
        return new JsonResponse( json_encode( $cursor->toArray() ) );
    }
}

?>