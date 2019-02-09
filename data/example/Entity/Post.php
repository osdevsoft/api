<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Post
 *
 * @ORM\Table(name="post", indexes={@ORM\Index(name="post_user_idx", columns={"user_uuid"})})
 * @ORM\Entity
 */
class Post
{

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=255, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $uuid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true, options={"default"="NULL"})
     */
    private $title = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="content", type="text", length=16777215, nullable=true, options={"default"="NULL"})
     */
    private $content = 'NULL';

    /**
     * @var \string|null
     *
     * @ORM\Column(name="created_at", type="string", nullable=true, options={"default"="current_timestamp()"})
     */
    private $createdAt = 'current_timestamp()';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true, options={"default"="current_timestamp()"})
     */
    private $updatedAt = 'current_timestamp()';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true, options={"default"="NULL"})
     */
    private $deletedAt = 'NULL';

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_uuid", referencedColumnName="uuid")
     * })
     */
    private $user;


    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="postUuid")
     **/
    private $comments;


    public function __construct() {
        $this->comments = new ArrayCollection();
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getComment()
    {
        return $this->comments;
    }
}
