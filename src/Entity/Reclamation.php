<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $intitule = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $corps = null;

    #[ORM\Column(length: 50)]
    private ?string $priorite = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\OneToMany(mappedBy: 'reclamation', targetEntity: PiecesReclamation::class)]
    private Collection $piecesReclamations;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $file = null;

    #[ORM\ManyToOne(inversedBy: 'reclamations')]
    private ?User $usager_id = null;

    public function __construct()
    {
        $this->piecesReclamations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): self
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getCorps(): ?string
    {
        return $this->corps;
    }

    public function getExcerptCorps(): ?string
    {
        if (strlen($this->corps) < 50) {
            return $this->corps;
        }
        return substr($this->corps, 0, 50) . "...";
    }

    public function setCorps(string $corps): self
    {
        $this->corps = $corps;

        return $this;
    }

    public function getPriorite(): ?string
    {
        return $this->priorite;
    }

    public function setPriorite(string $priorite): self
    {
        $this->priorite = $priorite;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection<int, PiecesReclamation>
     */
    public function getPiecesReclamations(): Collection
    {
        return $this->piecesReclamations;
    }

    public function addPiecesReclamation(PiecesReclamation $piecesReclamation): self
    {
        if (!$this->piecesReclamations->contains($piecesReclamation)) {
            $this->piecesReclamations->add($piecesReclamation);
            $piecesReclamation->setReclamation($this);
        }

        return $this;
    }

    public function removePiecesReclamation(PiecesReclamation $piecesReclamation): self
    {
        if ($this->piecesReclamations->removeElement($piecesReclamation)) {
            // set the owning side to null (unless already changed)
            if ($piecesReclamation->getReclamation() === $this) {
                $piecesReclamation->setReclamation(null);
            }
        }

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getUsagerId(): ?User
    {
        return $this->usager_id;
    }

    public function setUsagerId(?User $usager_id): self
    {
        $this->usager_id = $usager_id;

        return $this;
    }
}
