<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NationaliteRepository")
 * @ApiResource()
 */
class Nationalite
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
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Auteur", mappedBy="Nationalite")
     */
    private $auteurs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Auteur", mappedBy="Nationalites")
     */
    private $auteursObj;

    public function __construct()
    {
        $this->auteurs = new ArrayCollection();
        $this->auteursObj = new ArrayCollection();
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
     * @return Collection|Auteur[]
     */
    public function getAuteurs(): Collection
    {
        return $this->auteurs;
    }

    /**
     * @return Collection|Auteur[]
     */
    public function getAuteursObj(): Collection
    {
        return $this->auteursObj;
    }

    public function addAuteursObj(Auteur $auteursObj): self
    {
        if (!$this->auteursObj->contains($auteursObj)) {
            $this->auteursObj[] = $auteursObj;
            $auteursObj->setNationalites($this);
        }

        return $this;
    }

    public function removeAuteursObj(Auteur $auteursObj): self
    {
        if ($this->auteursObj->contains($auteursObj)) {
            $this->auteursObj->removeElement($auteursObj);
            // set the owning side to null (unless already changed)
            if ($auteursObj->getNationalites() === $this) {
                $auteursObj->setNationalites(null);
            }
        }

        return $this;
    }
}
