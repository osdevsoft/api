<?php

namespace App\NexinEs\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Osds\Auth\Domain\Entity\Auth;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User extends Auth
{

    /**
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="binary", unique=true)
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="StaticPage", mappedBy="user_id")
     **/
    public $static_pages;


    public function __construct()
    {
        $this->static_pages = new ArrayCollection();
        parent::__construct();
    }

    public function getStaticPage()
    {
        return $this->static_pages;
    }

}
