<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    private ?string $prix = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\ManyToMany(targetEntity: Recette::class, mappedBy: 'produits')]
    private Collection $recettes;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $cru = null;

    #[ORM\Column]
    private ?bool $cuit = null;

    #[ORM\Column]
    private ?bool $bio = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $debutDisponibilite = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $finDisponibilite = null;

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

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function __construct()
    {
        $this->dateCreation = new \DateTime('now');
        $this->recettes = new ArrayCollection();
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, Recette>
     */
    public function getRecettes(): Collection
    {
        return $this->recettes;
    }

    public function addRecette(Recette $recette): self
    {
        if (!$this->recettes->contains($recette)) {
            $this->recettes->add($recette);
            $recette->addProduit($this);
        }

        return $this;
    }

    public function removeRecette(Recette $recette): self
    {
        if ($this->recettes->removeElement($recette)) {
            $recette->removeProduit($this);
        }

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

    public function isCru(): ?bool
    {
        return $this->cru;
    }

    public function setCru(bool $cru): self
    {
        $this->cru = $cru;

        return $this;
    }

    public function isCuit(): ?bool
    {
        return $this->cuit;
    }

    public function setCuit(bool $cuit): self
    {
        $this->cuit = $cuit;

        return $this;
    }

    public function isBio(): ?bool
    {
        return $this->bio;
    }

    public function setBio(bool $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getDebutDisponibilite(): ?\DateTimeInterface
    {
        return $this->debutDisponibilite;
    }

    public function setDebutDisponibilite(?\DateTimeInterface $debutDisponibilite): self
    {
        $this->debutDisponibilite = $debutDisponibilite;

        return $this;
    }

    public function getFinDisponibilite(): ?\DateTimeInterface
    {
        return $this->finDisponibilite;
    }

    public function setFinDisponibilite(?\DateTimeInterface $finDisponibilite): self
    {
        $this->finDisponibilite = $finDisponibilite;

        return $this;
    }

}
