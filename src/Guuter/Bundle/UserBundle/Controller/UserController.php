<?php

namespace Guuter\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class UserController extends Controller
{
    /**
     * @Template()
     */
    public function asideAction()
    {
        $peopleManager = $this->get('guuter.node.people.manager');

        $people = $peopleManager
            ->initPeople($this->getUser()->getGraphId())
        ;

        return [
            'people' => $people,
        ];
    }
}
