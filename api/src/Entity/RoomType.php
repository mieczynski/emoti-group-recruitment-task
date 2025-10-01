<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'room_types')]
#[ORM\Index(columns: ['name'])]
#[ORM\Index(columns: ['code'])]
#[ORM\HasLifecycleCallbacks]
class RoomType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private string $name;

    #[ORM\Column(length: 32, unique: true, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    /** @var Collection<int, VacancyCalendar> */
    #[ORM\OneToMany(mappedBy: 'roomType', targetEntity: VacancyCalendar::class, orphanRemoval: true)]
    private Collection $vacancyCalendars;

    /** @var Collection<int, Reservation> */
    #[ORM\OneToMany(mappedBy: 'roomType', targetEntity: Reservation::class)]
    private Collection $reservations;

    public function __construct(string $name, ?string $code = null)
    {
        $this->name = $name;
        $this->code = $code;
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->vacancyCalendars = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }

    #[ORM\PreUpdate]
    public function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; $this->touch(); return $this; }

    public function getCode(): ?string { return $this->code; }
    public function setCode(?string $code): self { $this->code = $code; $this->touch(); return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    /** @return Collection<int, VacancyCalendar> */
    public function getVacancyCalendars(): Collection { return $this->vacancyCalendars; }

    public function addVacancyCalendar(VacancyCalendar $vc): self
    {
        if (!$this->vacancyCalendars->contains($vc)) {
            $this->vacancyCalendars->add($vc);
            $vc->setRoomType($this);
        }
        return $this;
    }

    public function removeVacancyCalendar(VacancyCalendar $vc): self
    {
        if ($this->vacancyCalendars->removeElement($vc) && $vc->getRoomType() === $this) {
            $vc->setRoomType(null);
        }
        return $this;
    }

    /** @return Collection<int, Reservation> */
    public function getReservations(): Collection { return $this->reservations; }
}
