<?php

namespace Guuter\Bundle\Neo4jBundle\Node;

use Everyman\Neo4j\Node as BaseNode;

class Node extends BaseNode
{
    public function isNew()
    {
        return is_empty($this->getId());
    }
}
