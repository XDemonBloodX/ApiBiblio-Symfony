<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdherentRepository")
 * @ApiResource()
 * @UniqueEntity(
 *  fields={"mail"},
 *  message = "Le mail est deja existant : {{ value }}, veuillez saisir un mail different..."
 * )
 */
class Adherent implements UserInterface
{
    const ROLE_ADMIN = "ROLE_ADMIN";
    const ROLE_MANAGER = "ROLE_MANAGER";
    const ROLE_USER = "ROLE_USER";
    const DEFAUT_ROLE = "ROLE_USER";


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codeCommune;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Pret", mappedBy="adherent")
     */
    private $prets;

    /**
     * @ORM\Column(type="array", length=255)
     */
    private $roles;

    public function __construct()
    {
        $this->prets = new ArrayCollection();
        $myRole[]=[self::DEFAUT_ROLE];
        $this->roles=$myRole;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getCodeCommune(): ?string
    {
        return $this->codeCommune;
    }

    public function setCodeCommune(?string $codeCommune): self
    {
        $this->codeCommune = $codeCommune;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection|Pret[]
     */
    public function getPrets(): Collection
    {
        return $this->prets;
    }

    public function addPret(Pret $pret): self
    {
        if (!$this->prets->contains($pret)) {
            $this->prets[] = $pret;
            $pret->setAdherent($this);
        }

        return $this;
    }

    public function removePret(Pret $pret): self
    {
        if ($this->prets->contains($pret)) {
            $this->prets->removeElement($pret);
            // set the owning side to null (unless already changed)
            if ($pret->getAdherent() === $this) {
                $pret->setAdherent(null);
            }
        }

        return $this;
    }
    public function eraseCredentials(){}

    public function getSalt() {
        return null;
    }

    public function getUsername() {
        return $this->getMail();
    }

    public function getRoles() :array
    {
        return $this->roles;
    }

    /**
     * Affecte les roles de l'utilité
     */
    public function setRoles(array $roles) :self
    {
        $this->roles=$roles;
        return $this;
    }
}
