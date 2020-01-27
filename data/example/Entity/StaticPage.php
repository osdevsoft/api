<?php

namespace App\NexinEs\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * StaticPage
 *
 * @ORM\Table(name="static_page")
 * @ORM\Entity
 */
class StaticPage
{

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="uuid", type="string", length=255, nullable=false)
     */
    private $uuid;
//
//    /**
//     * @var string|null
//     *
//     * @ORM\Column(name="parent_uuid", type="text", length=255, nullable=true, options={"default"="NULL"})
//
//     */
//    private $parent_uuid;

    /**
     * @var \StaticPage
     *
     * @ORM\ManyToOne(targetEntity="StaticPage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="static_page_uuid", referencedColumnName="uuid")
     * })
     */
    protected $static_page_uuid;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_uuid", referencedColumnName="uuid")
     * })
     */
    private $user_uuid;


    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="text", length=255, nullable=true, options={"default"="NULL"})
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="seo_name", type="text", length=255, nullable=true, options={"default"="NULL"})
     */
    private $seo_name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="content", type="text", length=16777215, nullable=true, options={"default"="NULL"})
     */
    private $content;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default":"CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true, options={"default":"CURRENT_TIMESTAMP"})
     */
    private $updatedAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true, options={"default"=NULL})
     */
    private $deletedAt;


    public function __construct()
    {
        $this->createdAt = date('Y-m-d H:i:s');
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setTimestamps()
    {
        $this->updatedAt = new \DateTime('now');
        if ($this->createdAt == null) {
            $this->createdAt = new \DateTime('now');
        }
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getUser()
    {
        return $this->user_uuid;
    }

    public function getUserUuid()
    {
        return $this->user_uuid;
    }

    public function setUserUuid($user_uuid)
    {
        $this->user_uuid = $user_uuid;
    }

    public function getStaticPage()
    {
        return $this;
    }

    public function getStaticPageUuid()
    {
        return $this->static_page_uuid;
    }

    public function setStaticPage($static_page_uuid)
    {
        $this->static_page_uuid = $static_page_uuid;
    }

    public function setStaticPageUuid($static_page_uuid)
    {
        $this->static_page_uuid = $static_page_uuid;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getSeoName(): ?string
    {
        return $this->seo_name;
    }

    /**
     * @param string|null $seo_name
     */
    public function setSeoName(?string $seo_name): void
    {
        $this->seo_name = $seo_name;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }
}
