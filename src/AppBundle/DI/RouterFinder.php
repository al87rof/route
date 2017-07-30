<?php

namespace AppBundle\DI;

use AppBundle\Entity\Route;
use Doctrine\ORM\EntityManager;

class RouterFinder
{
    /** @var EntityManager */
    private $entityManager;

    private $startPointRouts = null;
    private $endPointRouts = null;
    private $foundRouts = [];
    private $routeFound = false;
    private $routeId = 0;
    private $glb = 0;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getRoute($start, $end)
    {
        $this->buildRoute($start, $end);
        return $this->getRouterInfo();
    }


    private function buildRoute($start, $end, $parent = false)
    {
        $dataRouts = $this->initPoints($start);
        if ($dataRouts) {
            /** @var Route $route */
            foreach ($dataRouts as $key => $route) {
                if ($route->getEnd() == $end) {
                    if($parent){
                        $this->foundRouts[$this->routeId]['route'][$parent->getId()] = $parent;
                    }
                    $this->foundRouts[$this->routeId]['route'][$route->getId()] = $route;
                    $this->foundRouts[$this->routeId]['found'] = true;
                    $this->routeId++;
                } else {
                    if($parent == false){
                        $this->foundRouts[$this->routeId]['route'][$route->getId()] = $route;
                    }else{
                        $this->foundRouts[$this->routeId]['route'][$parent->getId()] = $parent;
                    }
                    $this->buildRoute($route->getEnd(), $end, $route);
                }
            }
        }else{
            $this->routeId++;
        }
    }


    private function initPoints($start)
    {
        /** @var Route $startRote */
        $data = $this->entityManager->getRepository("AppBundle:Route")->findBy(['start' => $start]);
        return $data;
    }

    private function getRouterInfo(){
        $foundRouteInfo = [];
        foreach ($this->foundRouts as $row){
            if(isset($row['found'])){
                $foundRouteInfo[] = new RouterInfo($row['route']);
            }
        }
        return $foundRouteInfo;
    }
}

class RouterInfo{


    private $incomeData;
    private $pointsRoute;
    private $totalTime;
    private $totalDistance;

    public function __construct($data)
    {
        $this->incomeData = $data;
        $this->init();
    }

    private function init()
    {
        sort($this->incomeData);
        foreach ($this->incomeData as $key => $rout) {
            $this->pointsRoute .= $rout->getStart().'->';
            if($key == count($this->incomeData)-1){
                $this->pointsRoute .= $rout->getEnd();
            }
            $this->totalDistance = $this->totalDistance + $rout->getDistance();
            $this->totalTime = $this->totalTime + $rout->getTime();
        }
    }

    public function getPointsRoute(){
        return $this->pointsRoute;
    }

    public function getTotalDistance(){
        return $this->totalDistance;
    }

    public function getTotalTime(){
        return $this->totalTime;
    }

}
