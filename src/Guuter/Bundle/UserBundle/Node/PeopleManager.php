<?php

namespace Guuter\Bundle\UserBundle\Node;

use Everyman\Neo4j\Client;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PeopleManager
{
    protected $client;
    protected $dispatcher;

    public function __construct(Client $client, EventDispatcherInterface $dispatcher)
    {
        $this->client = $client;
        $this->dispatcher = $dispatcher;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function initPeople($id = null)
    {
        $this->client->setNodeFactory(array('Guuter\Bundle\UserBundle\Node\People', 'create'));

        $people = $id ? $this->client->getNode($id) : $this->client->makeNode()->save();

        if (!$people instanceof People) {
            throw new \Exception(sprintf("People with %d id not found", $id));
        }

        $label = $this->client->makeLabel('PEOPLE');
        $people->addLabels(array($label));

        return $people;
    }

    // private function createRelationship(People $people, $type)
    // {
    //     return $people
    //         ->relateTo($userFrom, 'FRIENDSHIP')
    //         ->setProperty('created_at', $time)
    //         ->setProperty('disclosure', 'public')
    //     ;
    // }

    // private function dispatchUpdate(People $people, $attribute, $oldValue)
    // {
    //     $event = new PeopleUpdateEvent($people, $attribute, $oldValue);
    //     $this->dispatcher->dispatch('gutter.people.update', $event);
    // }

    // public function updateEmail(People $people, $email)
    // {
    //     $oldValue = $people->getProperty('email');
    //     $people->setProperty('email', $email);
    //     $people->save();
    //     $this->dispatchUpdate($people, 'email', $oldValue);
    // }

    // public function updateLastname(People $people, $lastname)
    // {
    //     $oldValue = $people->getProperty('lastname');
    //     $people->setProperty('lastname', $lastname);
    //     $people->save();
    //     $this->dispatchUpdate($people, 'lastname', $oldValue);
    // }

    // public function updateFirstname(People $people, $firstname)
    // {
    //     $transaction = $this->client->beginTransaction();

    //     try {
    //         $people->setProperty('firstname', $firstname);
    //         $people->save();

    //         $rel = $this
    //             ->createRelationship($people, 'PERSONAL_ATTRIBUTE')
    //             // ->relateTo($userFrom, 'FRIENDSHIP')
    //             ->setProperty('created_at', $time)
    //             ->setProperty('disclosure', 'public')
    //             ->save()
    //         ;

    //         $transaction->commit();
    //     } catch (\Exception $e) {
    //         $transaction->rollback();
    //     }
    //     $oldValue = $people->getProperty('firstname');
    //     $people->setProperty('firstname', $firstname);
    //     $people->save();
    //     $this->dispatchUpdate($people, 'firstname', $oldValue);
    // }
}
