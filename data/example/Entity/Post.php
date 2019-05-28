<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Post
 *
 * @ORM\Table(name="post", indexes={@ORM\Index(name="post_author_idx", columns={"author_uuid"})})
 * @ORM\Entity
 */
class Post
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="string", nullable=true, options={"default":"CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="string", nullable=true, options={"default":"CURRENT_TIMESTAMP"})
     */
    private $updatedAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="deleted_at", type="string", nullable=true, options={"default"="NULL"})
     */
    private $deletedAt;

    /**
     * @var \Author
     *
     * @ORM\ManyToOne(targetEntity="Author")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="author_uuid", referencedColumnName="uuid")
     * })
     */
    private $authorUuid;


    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="postUuid")
     **/
    private $comments;


    public function __construct() {
        $this->comments = new ArrayCollection();
        $this->createdAt = date('Y-m-d H:i:s');
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getAuthor()
    {
        return $this->authorUuid;
    }

    public function getComment()
    {
        return $this->comments;
    }

    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setContent($content)
    {

        $this->content = $content;
    }

    public function setAuthorUuid($authorUuid)
    {
        $this->authorUuid = $authorUuid;

    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setTimestamps() {
        $this->updatedAt = new \DateTime('now');
        if ($this->createdAt == null) {
           $this->createdAt = new \DateTime('now');
        }
    }

}
