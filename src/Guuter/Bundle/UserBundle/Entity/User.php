<?php

namespace Guuter\Bundle\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", unique=true)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\Column(name="graph_id", type="integer", unique=true)
     */
    protected $graphId;

    public function setGraphId($id)
    {
        $this->graphId = $id;
    }

    public function getGraphId()
    {
        return $this->graphId;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        $this->username = $email;
    }

    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;
        $this->usernameCanonical = $emailCanonical;
    }
}
