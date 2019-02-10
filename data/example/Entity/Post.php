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
    }

    public function getAuthor()
    {
        return $this->authorUuid;
    }

    public function getComment()
    {
        return $this->comments;
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
}
