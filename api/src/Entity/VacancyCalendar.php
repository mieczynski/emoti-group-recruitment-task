<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'vacancy_calendar')]
#[ORM\Index(columns: ['date'])]
#[ORM\Index(columns: ['room_type_id'])]
#[ORM\UniqueConstraint(columns: ['date', 'room_type_id'])]
class VacancyCalendar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: RoomType::class, inversedBy: 'vacancyCalendars')]
    #[ORM\JoinColumn(name: 'room_type_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?RoomType $roomType = null;

    #[ORM\Column(type: 'date_immutable')]
    private \DateTimeImmutable $date;

    #[ORM\Column(name: 'capacity_total')]
    private int $capacityTotal = 0;

    #[ORM\Column(name: 'capacity_available')]
    private int $capacityAvailable = 0;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $price = null;

    public function __construct(RoomType $roomType, \DateTimeImmutable $date, int $capacityTotal, int $capacityAvailable, ?string $price = null)
    {
        $this->roomType = $roomType;
        $this->date = $date;
        $this->capacityTotal = $capacityTotal;
        $this->capacityAvailable = $capacityAvailable;
        $this->price = $price;
    }

    public function getId(): ?int { return $this->id; }

    public function getRoomType(): ?RoomType { return $this->roomType; }
    public function setRoomType(?RoomType $roomType): self { $this->roomType = $roomType; return $this; }

    public function getDate(): \DateTimeImmutable { return $this->date; }
    public function setDate(\DateTimeImmutable $date): self { $this->date = $date; return $this; }

    public function getCapacityTotal(): int { return $this->capacityTotal; }
    public function setCapacityTotal(int $v): self { $this->capacityTotal = $v; return $this; }

    public function getCapacityAvailable(): int { return $this->capacityAvailable; }
    public function setCapacityAvailable(int $v): self { $this->capacityAvailable = $v; return $this; }

    public function getPrice(): ?string { return $this->price; }
    public function setPrice(?string $price): self { $this->price = $price; return $this; }
}
