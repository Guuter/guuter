<?php

namespace Guuter\Bundle\UserBundle\Node;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Everyman\Neo4j\Relationship;
use Guuter\Bundle\UserBundle\Node\People;
use Guuter\Bundle\UserBundle\Node\PeopleManager;
use Guuter\Bundle\UserBundle\Node\PersonalAttribute;

class PersonalAttributeManager
{
    const CHANGE = 'CHANGE';
    const REMOVE = 'REMOVE';

    protected $peopleManager;
    protected $dispatcher;
    protected $personalAttributeIds;

    public function __construct(PeopleManager $peopleManager, EventDispatcherInterface $dispatcher)
    {
        $this->peopleManager = $peopleManager;
        $this->dispatcher = $dispatcher;
    }

    public function setPersonalAttributeIds(array $personalAttributeIds = array())
    {
        $this->personalAttributeIds = $personalAttributeIds;
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

    public function getPersonalAttribute($identifier)
    {
        if (!array_key_exists($identifier, $this->personalAttributeIds)) {
            throw new \Exception("Personal Attribute not found in configuration");
        }

        $this->getClient()->setNodeFactory(array('Guuter\Bundle\UserBundle\Node\PersonalAttribute', 'create'));

        $personalAttribute = $this->getClient()->getNode($this->personalAttributeIds[$identifier]);

        if ($personalAttribute->getIdentifier() != $identifier) {
            throw new \Exception("Personal Attribute identifier in configuration not match the node one");
        }

        return $personalAttribute;
    }

    private function createRelationship(People $people, PersonalAttribute $personalAttribute, $type)
    {
        return $people
            ->relateTo($personalAttribute, $type)
            ->setProperty('created_at', time())
            ->setProperty('disclosure', 'public')
        ;
    }

    private function changePersonalAttribute(People $people, $identifier, $value)
    {
        $transaction = $this->getClient()->beginTransaction();

        try {
            $people->setProperty('personalAttribute_' . $identifier, $value);
            $people->save();

            // $personalAttribute = $this->getPersonalAttribute($identifier);

            // $this
            //     ->createRelationship($people, $personalAttribute, self::CHANGE)
            //     ->setProperty('value', $value)
            //     ->save()
            // ;

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
        }

        // $this->dispatchChange($people, $type, $value);

        return $this;
    }

    public function changeEmail(People $people, $email)
    {
        return $this->changePersonalAttribute($people, 'email', $email);
    }

    public function changeFirstname(People $people, $firstname)
    {
        return $this->changePersonalAttribute($people, 'firstname', $firstname);
    }

    public function changeLastname(People $people, $lastname)
    {
        return $this->changePersonalAttribute($people, 'lastname', $lastname);
    }

    public function changeWeight(People $people, $weight)
    {
        return $this->changePersonalAttribute($people, 'weight', $weight);
    }
}
