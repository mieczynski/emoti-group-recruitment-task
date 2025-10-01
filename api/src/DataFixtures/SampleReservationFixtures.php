<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Enum\RoomType as RoomTypeEnum;
use App\Entity\RoomType as RoomTypeEntity;
use App\Entity\Reservation;
use App\Entity\ReservationDate;
use App\Entity\VacancyCalendar;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class SampleReservationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $samples = [
            [RoomTypeEnum::STANDARD, 'Alice Johnson', 'alice@example.com', 2, new \DateTimeImmutable('today +2 days'), UserFixtures::USER_DEFAULT],
            [RoomTypeEnum::DELUXE,   'Bob Smith',     'bob@example.com',   3, new \DateTimeImmutable('today +5 days'), UserFixtures::USER_ADMIN],
        ];

        foreach ($samples as [$type, $guest, $email, $nights, $start, $userRef]) {
            /** @var RoomTypeEntity $rt */
            $rt   = $this->getReference(RoomTypeFixtures::refKey($type), RoomTypeEntity::class);
            /** @var User $user */
            $user = $this->getReference($userRef, User::class);

            $end = $start->modify(sprintf('+%d days', $nights));
            $reservation = new Reservation($rt, $start, $end, $guest, $email);
            $reservation->setUser($user);

            $total = '0.00';
            for ($d = $start; $d < $end; $d = $d->modify('+1 day')) {
                /** @var VacancyCalendar|null $vc */
                $vc = $manager->getRepository(VacancyCalendar::class)->findOneBy([
                    'roomType' => $rt,
                    'date'     => $d,
                ]);
                if (!$vc || $vc->getCapacityAvailable() < 1) {
                    continue;
                }

                $vc->setCapacityAvailable($vc->getCapacityAvailable() - 1);
                $price = (string)($vc->getPrice() ?? '0.00');

                $line = new ReservationDate($d, $price);
                $reservation->addReservationDate($line);
                $manager->persist($line);

                $total = bcadd($total, $price, 2);
            }

            if (\count($reservation->getReservationDates()) > 0) {
                $reservation->setTotalPrice($total);
                $manager->persist($reservation);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class, VacancyCalendarFixtures::class];
    }
}
