<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GuildStashRepository")
 * @ORM\HasLifecycleCallbacks
 */
class GuildStash
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     */
    private $stash = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $checksum;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Guild", inversedBy="guildStash", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $guild;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStash(): ?array
    {
        return $this->stash;
    }

    public function setStash(array $stash): self
    {
        $this->stash = $stash;

        return $this;
    }

    public function getChecksum(): ?string
    {
        return $this->checksum;
    }

    public function setChecksum(string $checksum): self
    {
        $this->checksum = $checksum;

        return $this;
    }

    public function getGuild(): ?Guild
    {
        return $this->guild;
    }

    public function setGuild(Guild $guild): self
    {
        $this->guild = $guild;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Gets triggered only on insert
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->updated_at = new \DateTime("now");
    }

    /**
     * Gets triggered every time on update
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updated_at = new \DateTime("now");
    }
}
