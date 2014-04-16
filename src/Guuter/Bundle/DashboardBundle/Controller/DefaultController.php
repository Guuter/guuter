<?php

namespace Guuter\Bundle\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction()
    {
        $peopleManager = $this->get('guuter.node.people.manager');
        $connectionManager = $this->get('guuter.node.connection.manager');

        $people = $peopleManager
            ->initPeople($this->getUser()->getGraphId())
        ;

        // $peopleTo = $peopleManager
        //     ->initPeople(4)
        // ;
        // $connectionManager->addConnection($people, $peopleTo, 'friends');

        $connections = $connectionManager->getConnections($people);

        // foreach ($connections as $connection) {
        //     echo $connection->getEndNode();
        // }
        // die;
        return [
            'people' => $people,
            'connections' => $connections,
        ];
    }
}
