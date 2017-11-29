<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TwitterController extends Controller
{
	// careful ! any route anotation added here will be ignored
    public function getWordsAction($type, Request $request)
    {
        // replace this example code with whatever you need
        return new JsonResponse('{ "hello" : "word"}');
    }
}

?>