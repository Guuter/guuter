<?php

namespace Guuter\Bundle\UserBundle\Node;

use Guuter\Bundle\Neo4jBundle\Node\Node;

class PersonalAttribute extends Node
{
    public static function create($client, $properties)
    {
        $node = new self($client);
        $node->setProperties($properties);

        return $node;
    }

    public function getIdentifier()
    {
        return $this->getProperty('identifier');
    }

    public function getName()
    {
        return $this->getProperty('name');
    }
}
