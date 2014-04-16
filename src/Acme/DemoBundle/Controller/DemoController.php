<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Acme\DemoBundle\Form\ContactType;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Guuter\Domain\User\Node\User;
use Guuter\Domain\User\Manager as UserManager;
use Everyman\Neo4j\Relationship;
use Everyman\Neo4j\Traversal;

class DemoController extends Controller
{
    /**
     * @Route("/", name="_demo")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/hello/{name}", name="_demo_hello")
     * @Template()
     */
    public function helloAction($name)
    {
        $client = $this->get('guuter.neo4j.client');

        $userManager = new UserManager($client);

        $userOne = $userManager->initUser(13);
        $userTwo = $userManager->initUser(1);
        // $userTwo = $userManager->initUser(1);

        // $userManager->askFriendship($userOne, $userTwo);
        // $userManager->createFriendship($userOne, $userTwo);

        // $queryString = "MATCH (n)<-[:FRIENDSHIP]-(x) RETURN x";
        // $query = new \Everyman\Neo4j\Cypher\Query($client, $queryString);
        // $result = $query->getResultSet();

        $traversal = new \Everyman\Neo4j\Traversal($client);
        $traversal->addRelationship('FRIENDSHIP', Relationship::DirectionOut)
            // ->setPruneEvaluator(Traversal::PruneNone)
            // ->setReturnFilter(Traversal::ReturnAll)
            ->setMaxDepth(2)
        ;

        $users = $traversal->getResults($userOne, Traversal::ReturnTypeNode);

        // var_dump($users);
        print_r($userOne->getId());

        print_r('---');
        foreach ($users as $user) {
            echo '<pre>';
            print_r($user->getId());
            echo '</pre>';
        }
        die;
        $arthur = $client->makeNode();
        // $arthur
        //     ->setProperty('name', 'Erik')
        //     ->setProperty('mood', 'majesaispas')
        //     ->setProperty('home', 'Paris')
        //     ->save()
        // ;

        $arthur = $client->getNode(1);
        $fabien = $client->getNode(2);

        $fabien
            ->relateTo($arthur, 'ASK_FRIENDSHIP')
            ->setProperty('created_at', time())
            ->save()
        ;

        $name = $arthur->getId();

        return array('name' => $name);
    }

    /**
     * @Route("/contact", name="_demo_contact")
     * @Template()
     */
    public function contactAction(Request $request)
    {
        $form = $this->createForm(new ContactType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $mailer = $this->get('mailer');

            // .. setup a message and send it
            // http://symfony.com/doc/current/cookbook/email.html

            $request->getSession()->getFlashBag()->set('notice', 'Message sent!');

            return new RedirectResponse($this->generateUrl('_demo'));
        }

        return array('form' => $form->createView());
    }
}
