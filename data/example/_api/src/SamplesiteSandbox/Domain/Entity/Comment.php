<?php

namespace App\SamplesiteSandbox\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="comment", indexes={@ORM\Index(name="comment_visitor_idx", columns={"visitor_uuid"}), @ORM\Index(name="comment_post_idx", columns={"post_uuid"})})
 * @ORM\Entity
 */
class Comment
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
     * @ORM\Column(name="content", type="text", length=16777215, nullable=true, options={"default"="NULL"})
     */
    private $content = 'NULL';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="string", nullable=true, options={"default"="current_timestamp()"})
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="string", nullable=true, options={"default"="current_timestamp()"})
     */
    private $updatedAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="deleted_at", type="string", nullable=true, options={"default"="NULL"})
     */
    private $deletedAt;

    /**
     * @var \Post
     *
     * @ORM\ManyToOne(targetEntity="Post")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="post_uuid", referencedColumnName="uuid")
     * })
     */
    private $post_uuid;

    /**
     * @var \Visitor
     *
     * @ORM\ManyToOne(targetEntity="Visitor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="visitor_uuid", referencedColumnName="uuid")
     * })
     */
    private $visitorUuid;


    public function __construct()
    {
        $this->createdAt = date('Y-m-d H:i:s');
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getPost()
    {
        return $this->post_uuid;
    }

    public function getVisitor()
    {
        return $this->visitorUuid;
    }

    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    public function setContent($content)
    {

        $this->content = $content;
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
