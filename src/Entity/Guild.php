<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GuildRepository")
 */
class Guild
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $tag;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="guilds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @Gedmo\Slug(fields={"tag", "name"})
     * @ORM\Column(length=150, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $gid;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GuildLog", mappedBy="guild")
     * @ORM\OrderBy({"lid" = "DESC"})
     */
    private $guildLogs;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\GuildMember", mappedBy="guild", cascade={"persist", "remove"})
     */
    private $guildMembers;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\GuildStash", mappedBy="guild", cascade={"persist", "remove"})
     */
    private $guildStash;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\GuildTreasury", mappedBy="guild", cascade={"persist", "remove"})
     */
    private $guildTreasury;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $display_stash;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $display_treasury;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $display_members;

    /**
     * @ORM\Column(type="boolean")
     */
    private $display_in_directory;

    public function __construct()
    {
        $this->guildLogs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getGid(): ?string
    {
        return $this->gid;
    }

    public function setGid(string $gid): self
    {
        $this->gid = $gid;

        return $this;
    }

    public function getSlug()
    {
      return $this->slug;
    }

    /**
     * @return Collection|GuildLog[]
     */
    public function getGuildLogs(): Collection
    {
        return $this->guildLogs;
    }

    public function addGuildLog(GuildLog $guildLog): self
    {
        if (!$this->guildLogs->contains($guildLog)) {
            $this->guildLogs[] = $guildLog;
            $guildLog->setGuild($this);
        }

        return $this;
    }

    public function removeGuildLog(GuildLog $guildLog): self
    {
        if ($this->guildLogs->contains($guildLog)) {
            $this->guildLogs->removeElement($guildLog);
            // set the owning side to null (unless already changed)
            if ($guildLog->getGuild() === $this) {
                $guildLog->setGuild(null);
            }
        }

        return $this;
    }

    public function getGuildMembers(): ?GuildMember
    {
        return $this->guildMembers;
    }

    public function setGuildMembers(GuildMember $guildMembers): self
    {
        $this->guildMembers = $guildMembers;

        // set the owning side of the relation if necessary
        if ($this !== $guildMembers->getGuild()) {
            $guildMembers->setGuild($this);
        }

        return $this;
    }

    public function getGuildStash(): ?GuildStash
    {
        return $this->guildStash;
    }

    public function setGuildStash(GuildStash $guildStash): self
    {
        $this->guildStash = $guildStash;

        // set the owning side of the relation if necessary
        if ($this !== $guildStash->getGuild()) {
            $guildStash->setGuild($this);
        }

        return $this;
    }

    public function getGuildTreasury(): ?GuildTreasury
    {
        return $this->guildTreasury;
    }

    public function setGuildTreasury(GuildTreasury $guildTreasury): self
    {
        $this->guildTreasury = $guildTreasury;

        // set the owning side of the relation if necessary
        if ($this !== $guildTreasury->getGuild()) {
            $guildTreasury->setGuild($this);
        }

        return $this;
    }

    public function getDisplayStash(): ?string
    {
        return $this->display_stash;
    }

    public function setDisplayStash(string $display_stash): self
    {
        $this->display_stash = $display_stash;

        return $this;
    }

    public function getDisplayTreasury(): ?string
    {
        return $this->display_treasury;
    }

    public function setDisplayTreasury(string $display_treasury): self
    {
        $this->display_treasury = $display_treasury;

        return $this;
    }

    public function getDisplayMembers(): ?string
    {
        return $this->display_members;
    }

    public function setDisplayMembers(string $display_members): self
    {
        $this->display_members = $display_members;

        return $this;
    }

    public function getDisplayInDirectory(): ?bool
    {
        return $this->display_in_directory;
    }

    public function setDisplayInDirectory(bool $display_in_directory): self
    {
        $this->display_in_directory = $display_in_directory;

        return $this;
    }
}
