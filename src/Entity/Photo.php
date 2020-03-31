<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Collection as Category;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhotoRepository")
 */
class Photo
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
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Collection", inversedBy="photos")
     */
    private $collection;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="photos")
     */
    private $user;

    /**
     * @ORM\Column(type="text")
     */
    private $image_name;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $longitude;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Favorite", mappedBy="photo")
     */
    private $favorites;

    public function __construct()
    {
        $this->favorites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getCollection(): ?Category
    {
        return $this->collection;
    }

    public function setCollection(Category $collection): self
    {
        $this->collection = $collection;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->image_name;
    }

    public function setImageName(string $image_name): self
    {
        $this->image_name = $image_name;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return Collection|Favorite[]
     */
    public function getFavorites(): Collection
    {
        return $this->favoritess;
    }

    public function addFavorite(Favorite $favorite): self
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites[] = $favorite;
            $favorite->setPhoto($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): self
    {
        if ($this->favorites->contains($favorite)) {
            $this->favorites->removeElement($favorite);
            // set the owning side to null (unless already changed)
            if ($favorite->getPhoto() === $this) {
                $favorite->setPhoto(null);
            }
        }

        return $this;
    }
}
