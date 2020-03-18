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
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="binary", unique=true)
     */
    protected $id;


    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=40, nullable=false)
     */
    private $uuid;

    /**
     * @var \StaticPage
     *
     * @ORM\ManyToOne(targetEntity="StaticPage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="static_page_id", referencedColumnName="id")
     * })
     */
    protected $static_page_id;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
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
     * @var string|null
     *
     * @ORM\Column(name="menu_position", type="integer", length=1, nullable=true, options={"default"="0"})
     */
    private $menu_position;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true, options={"default":"CURRENT_TIMESTAMP"})
     */
    private $created_at;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true, options={"default":"CURRENT_TIMESTAMP"})
     */
    private $updated_at;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true, options={"default"=NULL})
     */
    private $deleted_at;


    public function __construct()
    {
        $this->created_at = new \DateTime('now');
        $this->updated_at = new \DateTime('now');
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setTimestamps()
    {
        $this->updated_at = new \DateTime('now');
        if ($this->created_at == null) {
            $this->created_at = new \DateTime('now');
        }
    }

    public function setId($uuid)
    {
        $this->id = pack("H*", str_replace('-', '', $uuid));
    }

    public function getId()
    {
        return $this->id;
    }

    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
        $this->setId($uuid);
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

    public function setUser($user)
    {
        $this->user_uuid = $user->getUuid();
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


    /**
     * @return integer|null
     */
    public function getMenuPosition(): ?int
    {
        return $this->menu_position;
    }

    /**
     * @param integer|null $menu_position
     */
    public function setMenuPosition(?int $menu_position): void
    {
        $this->menu_position = $menu_position;
    }


}
