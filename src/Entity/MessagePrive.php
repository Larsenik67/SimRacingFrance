<?php

namespace App\Entity;

use App\Repository\MessagePriveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessagePriveRepository::class)
 */
class MessagePrive
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $objet;

    /**
     * @ORM\Column(type="text")
     */
    private $contenu;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateTime;

    /**
     * @ORM\ManyToOne(targetEntity=user::class, inversedBy="messagePrives")
     * @ORM\JoinColumn(nullable=false)
     */
    private $destinataire;

    /**
     * @ORM\ManyToOne(targetEntity=user::class, inversedBy="messagePrives")
     * @ORM\JoinColumn(nullable=false)
     */
    private $expediteur;

    /**
     * @ORM\OneToMany(targetEntity=ReponsePrive::class, mappedBy="messagePrive")
     */
    private $reponsePrive;

    public function __construct()
    {
        $this->reponsePrives = new ArrayCollection();
        $this->reponsePrive = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(string $objet): self
    {
        $this->objet = $objet;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeInterface $dateTime): self
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getDestinataire(): ?user
    {
        return $this->destinataire;
    }

    public function setDestinataire(?user $destinataire): self
    {
        $this->destinataire = $destinataire;

        return $this;
    }

    public function getExpediteur(): ?user
    {
        return $this->expediteur;
    }

    public function setExpediteur(?user $expediteur): self
    {
        $this->expediteur = $expediteur;

        return $this;
    }

    /**
     * @return Collection<int, ReponsePrive>
     */
    public function getReponsePrive(): Collection
    {
        return $this->reponsePrive;
    }

    public function addReponsePrive(ReponsePrive $reponsePrive): self
    {
        if (!$this->reponsePrive->contains($reponsePrive)) {
            $this->reponsePrive[] = $reponsePrive;
            $reponsePrive->setMessagePrive($this);
        }

        return $this;
    }

    public function removeReponsePrive(ReponsePrive $reponsePrive): self
    {
        if ($this->reponsePrive->removeElement($reponsePrive)) {
            // set the owning side to null (unless already changed)
            if ($reponsePrive->getMessagePrive() === $this) {
                $reponsePrive->setMessagePrive(null);
            }
        }

        return $this;
    }
}
