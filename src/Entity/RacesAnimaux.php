<?php

namespace App\Entity;

use App\Repository\RacesAnimauxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RacesAnimauxRepository::class)]
class RacesAnimaux
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
   

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'race', targetEntity: Animaux::class)]
    private Collection $animaux;

    public function __construct()
    {
        $this->animaux = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, Animaux>
     */
    public function getAnimaux(): Collection
    {
        return $this->animaux;
    }

    public function addAnimaux(Animaux $animaux): self
    {
        if (!$this->animaux->contains($animaux)) {
            $this->animaux->add($animaux);
            $animaux->setRace($this);
        }

        return $this;
    }

    public function removeAnimaux(Animaux $animaux): self
    {
        if ($this->animaux->removeElement($animaux)) {
            // set the owning side to null (unless already changed)
            if ($animaux->getRace() === $this) {
                $animaux->setRace(null);
            }
        }
        return $this;
    }
}
