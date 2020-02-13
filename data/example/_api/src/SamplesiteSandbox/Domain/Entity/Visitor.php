<?php

namespace App\SamplesiteSandbox\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Visitor
 *
 * @ORM\Table(name="visitor")
 * @ORM\Entity
 */
class Visitor
{
    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=255, nullable=false)
     * @ORM\Id
     */
    private $uuid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $name = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $email = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $password = 'NULL';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="string", nullable=true, options={"default"="current_timestamp()"})
     */
    private $createdAt = 'current_timestamp()';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="string", nullable=true, options={"default"="current_timestamp()"})
     */
    private $updatedAt = 'current_timestamp()';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="deleted_at", type="string", nullable=true, options={"default"="NULL"})
     */
    private $deletedAt = 'NULL';

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="visitorUuid")
     **/
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->createdAt = date('Y-m-d H:i:s');
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getComment()
    {
        return $this->comments;
    }
}
