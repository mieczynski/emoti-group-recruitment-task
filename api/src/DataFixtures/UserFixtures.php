<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixtures extends Fixture
{
    public const USER_DEFAULT = 'user_default';
    public const USER_ADMIN   = 'user_admin';

    public function __construct(private readonly UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $u = (new User())
            ->setEmail('user@example.com')
            ->setRoles(['ROLE_USER']);
        $u->setPassword($this->hasher->hashPassword($u, 'password'));
        $manager->persist($u);
        $this->addReference(self::USER_DEFAULT, $u);

        $admin = (new User())
            ->setEmail('admin@example.com')
            ->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'password'));
        $manager->persist($admin);
        $this->addReference(self::USER_ADMIN, $admin);

        $manager->flush();
    }
}
