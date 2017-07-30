<?php

namespace AppBundle\Controller;

use AppBundle\DI\RouterFinder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        $s = 'fghj';
        // replace this example code with whatever you need

        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ));
    }


    public function findAction(Request $request){
        $start = 'A';
        $end = 'D';
        /** @var RouterFinder $routerFinderService */
        $routerFinderService = $this->get('router_finder');

        $fountRoutes = $routerFinderService->getRoute($start,$end);

        return $this->render('find/find.html.twig',['routs'=>$fountRoutes]);
    }
}
