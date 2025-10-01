<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Enum\RoomType as RoomTypeEnum;
use App\Entity\RoomType as RoomTypeEntity;
use App\Entity\VacancyCalendar;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class VacancyCalendarFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $today     = new \DateTimeImmutable('today');
        $end       = $today->modify('+120 days');

        $setup = [
            [RoomTypeEnum::STANDARD, 5, '100.00'],
            [RoomTypeEnum::DELUXE,   3, '160.00'],
            [RoomTypeEnum::SUITE,    2, '240.00'],
        ];

        foreach ($setup as [$type, $capacity, $basePrice]) {
            /** @var RoomTypeEntity $rt */
            $rt = $this->getReference(RoomTypeFixtures::refKey($type), RoomTypeEntity::class);

            for ($d = $today; $d < $end; $d = $d->modify('+1 day')) {
                $isWeekend = (int)$d->format('N') >= 6;
                $price = $isWeekend
                    ? number_format((float)$basePrice * 1.2, 2, '.', '')
                    : $basePrice;

                $vc = new VacancyCalendar($rt, $d, $capacity, $capacity, $price);
                $manager->persist($vc);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [RoomTypeFixtures::class];
    }
}
