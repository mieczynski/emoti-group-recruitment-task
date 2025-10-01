<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'reservation_dates')]
#[ORM\Index(columns: ['date'])]
#[ORM\UniqueConstraint(columns: ['reservation_id', 'date'])]
class ReservationDate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Reservation::class, inversedBy: 'reservationDates')]
    #[ORM\JoinColumn(name: 'reservation_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Reservation $reservation = null;

    #[ORM\Column(type: 'date_immutable')]
    private \DateTimeImmutable $date;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $price = '0.00';

    public function __construct(\DateTimeImmutable $date, string $price)
    {
        $this->date = $date;
        $this->price = $price;
    }

    public function getId(): ?int { return $this->id; }

    public function getReservation(): ?Reservation { return $this->reservation; }
    public function setReservation(?Reservation $r): self { $this->reservation = $r; return $this; }

    public function getDate(): \DateTimeImmutable { return $this->date; }
    public function setDate(\DateTimeImmutable $d): self { $this->date = $d; return $this; }

    public function getPrice(): string { return $this->price; }
    public function setPrice(string $p): self { $this->price = $p; return $this; }
}
