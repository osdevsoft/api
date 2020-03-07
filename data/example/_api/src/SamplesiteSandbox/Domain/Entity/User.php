<?php

namespace App\SamplesiteSandbox\Domain\Entity;

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
     * @ORM\OneToMany(targetEntity="StaticPage", mappedBy="user_uuid")
     **/
    public $static_pages;

    public function getStaticPage()
    {
        return $this->static_pages;
    }

}
