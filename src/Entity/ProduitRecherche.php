<?php

namespace App\Entity;

use App\Repository\ProduitRechercheRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRechercheRepository::class)]
class ProduitRecherche
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $libelle = null;

    #[ORM\Column(nullable: true)]
    private ?float $prixMini = null;

    #[ORM\Column(nullable: true)]
    private ?float $prixMaxi = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getPrixMini(): ?float
    {
        return $this->prixMini;
    }

    public function setPrixMini(?float $prixMini): self
    {
        $this->prixMini = $prixMini;

        return $this;
    }

    public function getPrixMaxi(): ?float
    {
        return $this->prixMaxi;
    }

    public function setPrixMaxi(?float $prixMaxi): self
    {
        $this->prixMaxi = $prixMaxi;

        return $this;
    }
}
