<?php

namespace Guuter\Bundle\UserBundle\Node;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Everyman\Neo4j\Relationship;
use Guuter\Bundle\Neo4jBundle\Exception\RelationshipAlreadyExistException;
use Guuter\Bundle\UserBundle\Node\People;
use Guuter\Bundle\UserBundle\Node\PeopleManager;
use Guuter\Bundle\UserBundle\Event\PeopleAddConnectionEvent;
use Guuter\Bundle\UserBundle\Event\PeopleAddShareEvent;
use Guuter\Bundle\UserBundle\Event\PeopleAddSubscriptionEvent;

class ConnectionManager
{
    const CONNECTION = 'CONNECTION';
    const SUBSCRIPTION = 'SUBSCRIPTION';
    const SHARE = 'SHARE';

    protected $peopleManager;
    protected $dispatcher;

    public function __construct(PeopleManager $peopleManager, EventDispatcherInterface $dispatcher)
    {
        $this->peopleManager = $peopleManager;
        $this->dispatcher = $dispatcher;
    }

    public function getClient()
    {
        return $this->peopleManager->getClient();
    }

    public function initPeople($id)
    {
        $people = $this->peopleManager->initPeople($id);
        if ($people->isNew()) {
            throw new \Exception("People not found");
        }

        return $people;
    }

    private function addPeopleUniqueRelationship(People $peopleFrom, People $peopleTo, $type, $propertyName, $propertyValue)
    {
        if ($peopleFrom->getId() == $peopleTo->getId()) {
            throw new \Exception("Can't create relation to itself");
        }

        $peopleFrom->getRelationships($type, Relationship::DirectionOut);

        $relationships = $peopleFrom->getRelationships(array($type), Relationship::DirectionOut);
        foreach ($relationships as $relationship) {
            if ($relationship->getEndNode()->getId() == $peopleTo->getId() && $relationship->getProperty($propertyName) == $propertyValue) {
                throw new RelationshipAlreadyExistException(sprintf('People %d already has a "%s" with %s "%s" relationship outgoing with People %d', $peopleFrom->getId(), $type, $propertyName, $propertyValue, $peopleTo->getId()));
            }
        }

        $peopleFrom
            ->relateTo($peopleTo, $type)
            ->setProperty($propertyName, $propertyValue)
            ->setProperty('created_at', time())
            ->save()
        ;
    }

    private function getRelationships(People $people, $type)
    {
        return $people->getRelationships($type, Relationship::DirectionOut);
    }

    private function getRelationshipsByProperty(People $people, $type, $propertyName)
    {
        $relationships = array();

        $allRelationships = $this->getRelationships($people, $type);

        foreach ($allRelationships as $relationship) {
            $relationships[$relationship->getProperty($propertyName)] = $relationship;
        }

        return $relationships;
    }

    private function getRelationshipsForProperty(People $people, $type, $name, $propertyName, $propertyValue)
    {
        $relationships = $this->getRelationshipsByProperty($people, $type, $propertyName);

        if (!array_key_exists($propertyValue, $relationships)) {
            return array();
        }

        return $relationships[$propertyValue];
    }

    public function getConnections(People $people)
    {
        return $this->getRelationships($people, ConnectionManager::CONNECTION);
    }

    public function getConnectionsForName(People $people, $name)
    {
        return $this->getRelationshipsForProperty($people, ConnectionManager::CONNECTION, 'name', $name);
    }

    public function getConnectionsByName(People $people)
    {
        return $this->getRelationshipsByProperty($people, ConnectionManager::CONNECTION, 'name');
    }

    public function addConnection(People $peopleFrom, People $peopleTo, $name)
    {
        $this->addPeopleUniqueRelationship($peopleFrom, $peopleTo, ConnectionManager::CONNECTION, 'name', $name);

        // $event = new PeopleAddConnectionEvent($peopleFrom, $peopleTo, $name);
        // $this->dispatcher->dispatch('gutter.people.connection', $event);

        return $this;
    }

    public function getSubscriptions(People $people)
    {
        return $this->getRelationships($people, ConnectionManager::SUBSCRIPTION);
    }

    public function getSubscriptionsForTag(People $people, $tag)
    {
        return $this->getRelationshipsForProperty($people, ConnectionManager::SUBSCRIPTION, 'tag', $tag);
    }

    public function getSubscriptionsByTag(People $people)
    {
        return $this->getRelationshipsByProperty($people, ConnectionManager::SUBSCRIPTION, 'tag');
    }

    public function addSubscription(People $peopleFrom, People $peopleTo, $tag)
    {
        $this->addPeopleUniqueRelationship($peopleFrom, $peopleTo, ConnectionManager::SUBSCRIPTION, 'tag', $tag);

        // $event = new PeopleAddSubscriptionEvent($peopleFrom, $peopleTo, $name);
        // $this->dispatcher->dispatch('gutter.people.subscription', $event);

        return $this;
    }

    public function getShares(People $people)
    {
        return $this->getRelationships($people, ConnectionManager::SHARE);
    }

    public function getSharesForTag(People $people, $tag)
    {
        return $this->getRelationshipsForProperty($people, ConnectionManager::SHARE, 'tag', $tag);
    }

    public function getSharesByTag(People $people)
    {
        return $this->getRelationshipsByProperty($people, ConnectionManager::SHARE, 'tag');
    }

    public function addShare(People $peopleFrom, People $peopleTo, $tag)
    {
        $this->addPeopleUniqueRelationship($peopleFrom, $peopleTo, ConnectionManager::SHARE, 'tag', $tag);

        // $event = new PeopleAddShareEvent($peopleFrom, $peopleTo, $name);
        // $this->dispatcher->dispatch('gutter.people.share', $event);

        return $this;
    }

    // public function askFriendship(User $userFrom, User $userTo)
    // {
    //     $time = time();

    //     $relationships = $userFrom->getRelationships(array('ASK_FRIENDSHIP'), Relationship::DirectionOut);
    //     foreach ($relationships as $relationship) {
    //         if ($relationship->getEndNode()->getId() == $userTo->getId()) {
    //             throw new \Exception(sprintf('User %d already has a "ASK_FRIENDSHIP" relationship outgoing with User %d', $userFrom->getId(), $userTo->getId()));
    //         }
    //     }

    //     $relationships = $userFrom->getRelationships(array('ASK_FRIENDSHIP'), Relationship::DirectionIn);
    //     foreach ($relationships as $relationship) {
    //         if ($relationship->getStartNode()->getId() == $userTo->getId()) {
    //             throw new \Exception(sprintf('User %d already has a "ASK_FRIENDSHIP" relationship ingoing with User %d', $userFrom->getId(), $userTo->getId()));
    //         }
    //     }

    //     $relationships = $userFrom->getRelationships(array('FRIENDSHIP'), Relationship::DirectionOut);
    //     foreach ($relationships as $relationship) {
    //         if ($relationship->getStartNode()->getId() == $userTo->getId()) {
    //             throw new \Exception(sprintf('User %d already has a "FRIENDSHIP" relationship with User %d', $userFrom->getId(), $userTo->getId()));
    //         }
    //     }

    //     $userFrom
    //             ->relateTo($userTo, 'ASK_FRIENDSHIP')
    //             ->setProperty('created_at', $time)
    //             ->save()
    //         ;
    // }

    // public function getFriendships(User $user)
    // {
    //     return $user->getRelationships('FRIENDSHIP', Relationship::DirectionOut);
    // }

    // public function getFriendshipUsers(User $user)
    // {
    //     $traversal = new Traversal($this->client);
    //     $traversal->addRelationship('FRIENDSHIP', Relationship::DirectionOut)
    //         // ->setPruneEvaluator(Traversal::PruneNone)
    //         // ->setReturnFilter(Traversal::ReturnAll)
    //         ->setMaxDepth(2)
    //     ;

    //     return $traversal->getResults($user, Traversal::ReturnTypeNode);
    // }

    // public function createFriendship(User $userFrom, User $userTo)
    // {
    //     $time = time();

    //     $relationships = $userFrom->getRelationships(array('FRIENDSHIP'), Relationship::DirectionAll);
    //     foreach ($relationships as $relationship) {
    //         if ($relationship->getStartNode()->getId() == $userTo->getId()) {
    //             throw new \Exception(sprintf('User %d already has a "FRIENDSHIP" relationship with User %d', $userFrom->getId(), $userTo->getId()));
    //         }
    //     }

    //     $found = false;
    //     $relationships = $userFrom->getRelationships(array('ASK_FRIENDSHIP'), Relationship::DirectionIn);
    //     foreach ($relationships as $relationship) {
    //         if ($relationship->getStartNode()->getId() == $userTo->getId()) {
    //             $found = true;
    //         }
    //     }
    //     if ($found !== true) {
    //         throw new \Exception(sprintf('User %d must have a "ASK_FRIENDSHIP" relationship ingoing with User %d', $userFrom->getId(), $userTo->getId()));
    //     }

    //     $transaction = $this->client->beginTransaction();

    //     try {
    //         $userFrom
    //             ->relateTo($userTo, 'FRIENDSHIP')
    //             ->setProperty('created_at', $time)
    //             ->setProperty('disclosure', 'public')
    //             ->save()
    //         ;
    //         $userTo
    //             ->relateTo($userFrom, 'FRIENDSHIP')
    //             ->setProperty('created_at', $time)
    //             ->setProperty('disclosure', 'public')
    //             ->save()
    //         ;

    //         $transaction->commit();
    //     } catch (\Exception $e) {
    //         $transaction->rollback();
    //     }
    // }
}
