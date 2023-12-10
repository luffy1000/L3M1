<?php

namespace App\Entity;

use App\Repository\ReservationsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\CreatedAtTrait;

#[ORM\Entity(repositoryClass: ReservationsRepository::class)]
class Reservations

{   use CreatedAtTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable:false)]
    private ?string $reference = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\ManyToMany(targetEntity: Users::class, inversedBy: 'reservations')]
    private Collection $users;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $stripeSessionId = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $paypalOrderId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $method = null;

    #[ORM\Column]
    private ?bool $isPaid = null;

    #[ORM\Column]
    private ?bool $isAssign = null;

    
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
        $this->price = 30;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(Users $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(Users $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function getStripeSessionId(): ?string
    {
        return $this->stripeSessionId;
    }

    public function setStripeSessionId(string $stripeSessionId): static
    {
        $this->stripeSessionId = $stripeSessionId;

        return $this;
    }

    public function getPaypalOrderId(): ?string
    {
        return $this->paypalOrderId;
    }

    public function setPaypalOrderId(string $paypalOrderId): static
    {
        $this->paypalOrderId = $paypalOrderId;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(?string $method): static
    {
        $this->method = $method;

        return $this;
    }

    public function isIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): static
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function isIsAssign(): ?bool
    {
        return $this->isAssign;
    }

    public function setIsAssign(bool $isAssign): static
    {
        $this->isAssign = $isAssign;

        return $this;
    }

   
}
