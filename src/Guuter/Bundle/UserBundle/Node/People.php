<?php

namespace Guuter\Bundle\UserBundle\Node;

use Guuter\Bundle\Neo4jBundle\Node\Node;

class People extends Node
{
    public static function create($client, $properties)
    {
        $node = new self($client);
        $node->setProperties($properties);

        return $node;
    }

    public function __toString()
    {
        return (string) $this->getPersonalAttribute('email');
    }

    public function getPersonalAttribute($personalAttribute)
    {
        return $this->getProperty(sprintf('personalAttribute_%s', $personalAttribute));
    }

    // public function changeEmail($email)
    // {
    //     $this->setProperty('email', $email);
    // }

    // public function changeLastname($lastname)
    // {
    //     $this->setProperty('lastname', $lastname);
    // }

    // public function changeFirstname($firstname)
    // {
    //     $this->setProperty('firstname', $firstname);
    // }

    // public function getFriendships()
    // {
    //     return $this->getRelationships('FRIENDSHIP');
    // }

    // public function getUnfriendships()
    // {
    //     return $this->getRelationships('IS_NO_MORE_FRIEND_WITH');
    // }
}
