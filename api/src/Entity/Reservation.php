<?php
declare(strict_types=1);

namespace App\Entity;

use App\Enum\ReservationStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'reservations')]
#[ORM\Index(columns: ['room_type_id'])]
#[ORM\Index(columns: ['start_date'])]
#[ORM\Index(columns: ['end_date'])]
#[ORM\Index(columns: ['status'])]
#[ORM\Index(columns: ['email'])]
#[ORM\HasLifecycleCallbacks]
class Reservation
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['reservation:read'])]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: RoomType::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'room_type_id', referencedColumnName: 'id', nullable: false, onDelete: 'RESTRICT')]
    private RoomType $roomType;

    #[ORM\Column(type: 'date_immutable', name: 'start_date')]
    #[Groups(['reservation:read'])]
    private \DateTimeImmutable $startDate;

    #[ORM\Column(type: 'date_immutable', name: 'end_date')]
    #[Groups(['reservation:read'])]
    private \DateTimeImmutable $endDate;

    #[ORM\Column(length: 120, name: 'guest_name')]
    #[Groups(['reservation:read'])]
    private string $guestName;

    #[ORM\Column(length: 180)]
    #[Groups(['reservation:read'])]
    private string $email;

    #[ORM\Column(type: 'string', enumType: ReservationStatus::class, length: 16)]
    #[Groups(['reservation:read'])]
    private ReservationStatus $status = ReservationStatus::BOOKED;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, name: 'total_price')]
    #[Groups(['reservation:read'])]
    private string $totalPrice = '0.00';

    #[ORM\Column(type: 'datetime_immutable', name: 'created_at')]
    #[Groups(['reservation:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', name: 'cancelled_at', nullable: true)]
    #[Groups(['reservation:read'])]
    private ?\DateTimeImmutable $cancelledAt = null;

    /** @var Collection<int, ReservationDate> */
    #[ORM\OneToMany(mappedBy: 'reservation', targetEntity: ReservationDate::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $reservationDates;

    public function __construct(
        RoomType $roomType,
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $endDate,
        string $guestName,
        string $email
    ) {
        $this->id = Uuid::v4(); // ✅ generate a real UUID
        $this->roomType = $roomType;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->guestName = $guestName;
        $this->email = $email;
        $this->createdAt = new \DateTimeImmutable();
        $this->reservationDates = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function ensureId(): void
    {
        // extra safety in case someone constructs without calling the normal ctor
        if (!isset($this->id)) {
            $this->id = Uuid::v4();
        }
    }

    public function getId(): Uuid { return $this->id; }

    public function getRoomType(): RoomType { return $this->roomType; }
    public function setRoomType(RoomType $rt): self { $this->roomType = $rt; return $this; }

    public function getStartDate(): \DateTimeImmutable { return $this->startDate; }
    public function setStartDate(\DateTimeImmutable $d): self { $this->startDate = $d; return $this; }

    public function getEndDate(): \DateTimeImmutable { return $this->endDate; }
    public function setEndDate(\DateTimeImmutable $d): self { $this->endDate = $d; return $this; }

    public function getGuestName(): string { return $this->guestName; }
    public function setGuestName(string $n): self { $this->guestName = $n; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $e): self { $this->email = $e; return $this; }

    public function getStatus(): ReservationStatus { return $this->status; }
    public function setStatus(ReservationStatus $s): self { $this->status = $s; return $this; }

    public function getTotalPrice(): string { return $this->totalPrice; }
    public function setTotalPrice(string $p): self { $this->totalPrice = $p; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getCancelledAt(): ?\DateTimeImmutable { return $this->cancelledAt; }
    public function setCancelledAt(?\DateTimeImmutable $d): self { $this->cancelledAt = $d; return $this; }

    /** @return Collection<int, ReservationDate> */
    public function getReservationDates(): Collection { return $this->reservationDates; }

    public function addReservationDate(ReservationDate $rd): self
    {
        if (!$this->reservationDates->contains($rd)) {
            $this->reservationDates->add($rd);
            $rd->setReservation($this);
        }
        return $this;
    }

    public function removeReservationDate(ReservationDate $rd): self
    {
        if ($this->reservationDates->removeElement($rd) && $rd->getReservation() === $this) {
            $rd->setReservation(null);
        }
        return $this;
    }
}
