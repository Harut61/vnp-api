<?php

namespace App\DataFixtures;

use App\Entity\AdminRoles;
use App\Entity\AdminUser;
use App\Enums\UserRoleEnum;

use App\Enums\UserStatusEnum;
use Composer\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\User;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container, UserPasswordEncoderInterface $encoder)
    {
        $this->container = $container;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $roles = UserRoleEnum::getConstants();
        $rolesInstances = [];

        foreach ($roles as $role){
            // add an Admin Role
            $adminRole = new AdminRoles();
            $adminRole->title = $role;
            $adminRole->code = $role;
            $manager->persist($adminRole);
            $rolesInstances[] = $adminRole;
        }

        // add an Admin User
        $email = 'adminuser@gmail.com';
        $user = new AdminUser();
        $user->setUsername($email);
        $user->setEmail($email);
        $password = $this->encoder->encodePassword($user, '123456');
        $user->updatePassword($password);
        $user->enabled = true;

        $user->addAdminRoles($rolesInstances[1]);
        $manager->persist($user);


        if(getenv("APP_ENV") === "dev"){
            $faker = \Faker\Factory::create();
            for($i =0 ; $i < 100 ; $i++){
                $user = new AdminUser();
                $email = $faker->companyEmail;
                $user->setUsername($email);
                $user->setEmail($email);
                $user->mobileNumber = $faker->phoneNumber;
                $user->fullName = "{$faker->firstName} {$faker->lastName}";
                $password = $this->encoder->encodePassword($user, '123456');
                $user->updatePassword($password);
                $user->enabled = true;

                $user->userStatus = $faker->randomElement([UserStatusEnum::PENDING, UserStatusEnum::ACTIVE, UserStatusEnum::BLOCKED]);

                if($user->userStatus == UserStatusEnum::BLOCKED) {
                    $user->blocked = true;
                }

                if($user->userStatus == UserStatusEnum::PENDING) {
                    $user->enabled = false;
                }

                // set role admin Sub User
                $user->addAdminRoles($rolesInstances[0]);
                $manager->persist($user);
            }
        }

        $manager->flush();
    }
}
