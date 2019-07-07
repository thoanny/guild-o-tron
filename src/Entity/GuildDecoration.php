<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GuildDecorationRepository")
 */
class GuildDecoration
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Decoration")
     * @ORM\JoinColumn(nullable=false)
     */
    private $decoration;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Guild", inversedBy="guildDecorations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $guild;

    /**
     * @ORM\Column(type="json")
     */
    private $recipe = [];

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDecoration(): ?Decoration
    {
        return $this->decoration;
    }

    public function setDecoration(?Decoration $decoration): self
    {
        $this->decoration = $decoration;

        return $this;
    }

    public function getGuild(): ?Guild
    {
        return $this->Guild;
    }

    public function setGuild(?Guild $guild): self
    {
        $this->guild = $guild;

        return $this;
    }

    public function getRecipe(): ?array
    {
        return $this->recipe;
    }

    public function setRecipe(array $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
