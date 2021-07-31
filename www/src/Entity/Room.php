<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoomRepository::class)
 */
class Room
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=BookingRequest::class, mappedBy="room")
     */
    private $bookingRequests;

    public function __construct()
    {
        $this->bookingRequests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    /**
     * @return Collection|BookingRequest[]
     */
    public function getBookingRequests(): Collection
    {
        return $this->bookingRequests;
    }

    public function addBookingRequest(BookingRequest $bookingRequest): self
    {
        if (!$this->bookingRequests->contains($bookingRequest)) {
            $this->bookingRequests[] = $bookingRequest;
            $bookingRequest->setRoom($this);
        }

        return $this;
    }

    public function removeBookingRequest(BookingRequest $bookingRequest): self
    {
        if ($this->bookingRequests->removeElement($bookingRequest)) {
            // set the owning side to null (unless already changed)
            if ($bookingRequest->getRoom() === $this) {
                $bookingRequest->setRoom(null);
            }
        }

        return $this;
    }
}
