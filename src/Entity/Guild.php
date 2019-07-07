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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facebook;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $twitter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $youtube;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $discord;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $twitch;

    /**
     * @ORM\Column(type="integer")
     */
    private $level;

    /**
     * @ORM\Column(type="integer")
     */
    private $capacity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $emblem;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $checksum;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $introduction;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $chart;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $notary;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $tavern;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $mine;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $workshop;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $market;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $arena;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $war_room;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="guild")
     */
    private $events;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\GuildTag", inversedBy="guilds")
     */
    private $guild_tags;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\GuildActivity", inversedBy="guilds")
     */
    private $guild_activities;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uid;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $discord_guild_id;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $discord_events_notifications_channel;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GuildRaid", mappedBy="guild")
     */
    private $guildRaids;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GuildDecoration", mappedBy="Guild")
     */
    private $guildDecorations;

    public function __construct()
    {
        $this->guildLogs = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->guild_tags = new ArrayCollection();
        $this->guild_activities = new ArrayCollection();
        $this->guildRaids = new ArrayCollection();
        $this->guildDecorations = new ArrayCollection();
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

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getYoutube(): ?string
    {
        return $this->youtube;
    }

    public function setYoutube(?string $youtube): self
    {
        $this->youtube = $youtube;

        return $this;
    }

    public function getDiscord(): ?string
    {
        return $this->discord;
    }

    public function setDiscord(?string $discord): self
    {
        $this->discord = $discord;

        return $this;
    }

    public function getTwitch(): ?string
    {
        return $this->twitch;
    }

    public function setTwitch(?string $twitch): self
    {
        $this->twitch = $twitch;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getEmblem(): ?string
    {
        return $this->emblem;
    }

    public function setEmblem(string $emblem): self
    {
        $this->emblem = $emblem;

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

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(?string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getChart(): ?string
    {
        return $this->chart;
    }

    public function setChart(?string $chart): self
    {
        $this->chart = $chart;

        return $this;
    }

    public function getNotary(): ?int
    {
        return $this->notary;
    }

    public function setNotary(int $notary): self
    {
        $this->notary = $notary;

        return $this;
    }

    public function getTavern(): ?int
    {
        return $this->tavern;
    }

    public function setTavern(int $tavern): self
    {
        $this->tavern = $tavern;

        return $this;
    }

    public function getMine(): ?int
    {
        return $this->mine;
    }

    public function setMine(int $mine): self
    {
        $this->mine = $mine;

        return $this;
    }

    public function getWorkshop(): ?int
    {
        return $this->workshop;
    }

    public function setWorkshop(int $workshop): self
    {
        $this->workshop = $workshop;

        return $this;
    }

    public function getMarket(): ?int
    {
        return $this->market;
    }

    public function setMarket(int $market): self
    {
        $this->market = $market;

        return $this;
    }

    public function getArena(): ?int
    {
        return $this->arena;
    }

    public function setArena(int $arena): self
    {
        $this->arena = $arena;

        return $this;
    }

    public function getWarRoom(): ?int
    {
        return $this->war_room;
    }

    public function setWarRoom(int $war_room): self
    {
        $this->war_room = $war_room;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setGuild($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            // set the owning side to null (unless already changed)
            if ($event->getGuild() === $this) {
                $event->setGuild(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GuildTag[]
     */
    public function getGuildTags(): Collection
    {
        return $this->guild_tags;
    }

    public function addGuildTag(GuildTag $guildTag): self
    {
        if (!$this->guild_tags->contains($guildTag)) {
            $this->guild_tags[] = $guildTag;
        }

        return $this;
    }

    public function removeGuildTag(GuildTag $guildTag): self
    {
        if ($this->guild_tags->contains($guildTag)) {
            $this->guild_tags->removeElement($guildTag);
        }

        return $this;
    }

    /**
     * @return Collection|GuildActivity[]
     */
    public function getGuildActivities(): Collection
    {
        return $this->guild_activities;
    }

    public function addGuildActivity(GuildActivity $guildActivity): self
    {
        if (!$this->guild_activities->contains($guildActivity)) {
            $this->guild_activities[] = $guildActivity;
        }

        return $this;
    }

    public function removeGuildActivity(GuildActivity $guildActivity): self
    {
        if ($this->guild_activities->contains($guildActivity)) {
            $this->guild_activities->removeElement($guildActivity);
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

    public function getDiscordGuildId(): ?int
    {
        return $this->discord_guild_id;
    }

    public function setDiscordGuildId(?int $discord_guild_id): self
    {
        $this->discord_guild_id = $discord_guild_id;

        return $this;
    }

    public function getDiscordEventsNotificationsChannel(): ?int
    {
        return $this->discord_events_notifications_channel;
    }

    public function setDiscordEventsNotificationsChannel(?int $discord_events_notifications_channel): self
    {
        $this->discord_events_notifications_channel = $discord_events_notifications_channel;

        return $this;
    }

    /**
     * @return Collection|GuildRaid[]
     */
    public function getGuildRaids(): Collection
    {
        return $this->guildRaids;
    }

    public function addGuildRaid(GuildRaid $guildRaid): self
    {
        if (!$this->guildRaids->contains($guildRaid)) {
            $this->guildRaids[] = $guildRaid;
            $guildRaid->setGuild($this);
        }

        return $this;
    }

    public function removeGuildRaid(GuildRaid $guildRaid): self
    {
        if ($this->guildRaids->contains($guildRaid)) {
            $this->guildRaids->removeElement($guildRaid);
            // set the owning side to null (unless already changed)
            if ($guildRaid->getGuild() === $this) {
                $guildRaid->setGuild(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GuildDecoration[]
     */
    public function getGuildDecorations(): Collection
    {
        return $this->guildDecorations;
    }

    public function addGuildDecoration(GuildDecoration $guildDecoration): self
    {
        if (!$this->guildDecorations->contains($guildDecoration)) {
            $this->guildDecorations[] = $guildDecoration;
            $guildDecoration->setGuild($this);
        }

        return $this;
    }

    public function removeGuildDecoration(GuildDecoration $guildDecoration): self
    {
        if ($this->guildDecorations->contains($guildDecoration)) {
            $this->guildDecorations->removeElement($guildDecoration);
            // set the owning side to null (unless already changed)
            if ($guildDecoration->getGuild() === $this) {
                $guildDecoration->setGuild(null);
            }
        }

        return $this;
    }

}
