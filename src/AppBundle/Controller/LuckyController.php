<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class LuckyController extends Controller
{
    /**
     * @Route("/lucky/number/{count}")
     */
    public function numberAction($count)
    {
        for($i=0;$i < $count;$i++){
            $number[] = rand(0,100);    
        }

        $numbers = implode(', ', $number);
        
        return $this->render('lucky/number.html.twig',array(
                                                                'numbers' => $numbers,
                                                                'count'   => $count
                                                    ));
    }

    /**
     * @Route("/api/lucky/number")
     */
    public function apiNumberAction()
    {
        $number = array(
                        'number' => rand(0, 100)
                        );
        
        return new JsonResponse($number);
    }
}
