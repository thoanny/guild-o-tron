<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GuildTagRepository")
 */
class GuildTag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $en;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $de;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $es;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Guild", mappedBy="guild_tags")
     */
    private $guilds;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uid;

    public function __construct()
    {
        $this->guilds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFr(): ?string
    {
        return $this->fr;
    }

    public function setFr(string $fr): self
    {
        $this->fr = $fr;

        return $this;
    }

    public function getEn(): ?string
    {
        return $this->en;
    }

    public function setEn(string $en): self
    {
        $this->en = $en;

        return $this;
    }

    public function getDe(): ?string
    {
        return $this->de;
    }

    public function setDe(string $de): self
    {
        $this->de = $de;

        return $this;
    }

    public function getEs(): ?string
    {
        return $this->es;
    }

    public function setEs(string $es): self
    {
        $this->es = $es;

        return $this;
    }

    /**
     * @return Collection|Guild[]
     */
    public function getGuilds(): Collection
    {
        return $this->guilds;
    }

    public function addGuild(Guild $guild): self
    {
        if (!$this->guilds->contains($guild)) {
            $this->guilds[] = $guild;
            $guild->addGuildTag($this);
        }

        return $this;
    }

    public function removeGuild(Guild $guild): self
    {
        if ($this->guilds->contains($guild)) {
            $this->guilds->removeElement($guild);
            $guild->removeGuildTag($this);
        }

        return $this;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(string $uid): self
    {
        $this->uid = $uid;

        return $this;
    }
}
