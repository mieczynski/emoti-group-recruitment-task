<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Enum\RoomType as RoomTypeEnum;
use App\Entity\RoomType as RoomTypeEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class RoomTypeFixtures extends Fixture
{
    public static function refKey(RoomTypeEnum $type): string
    {
        return 'roomtype_' . strtolower($type->name); // e.g. roomtype_standard
    }

    public function load(ObjectManager $manager): void
    {
        foreach (RoomTypeEnum::cases() as $type) {
            $rt = new RoomTypeEntity($type->label(), $type->value);
            $manager->persist($rt);
            $this->addReference(self::refKey($type), $rt);
        }

        $manager->flush();
    }
}
