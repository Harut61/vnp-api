<?php

namespace App\Command;

use App\Entity\AdminRoles;
use App\Entity\AdminUser;
use App\Entity\TranscodingProfile;
use App\Entity\TranscodingProfileOption;
use App\Enums\UserRoleEnum;
use App\Repository\AdminUserRepository;
use App\Services\PaasSetupService;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class IvnewsPaasSetupCommand extends Command
{
    protected static $defaultName = 'ivnews:paas-setup';

    private $container;
    private $entityManager;
    private $encoder;
    private $paasSetupService;

    public function __construct(ContainerInterface$container, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, PaasSetupService $paasSetupService)
    {
        parent::__construct();
        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
        $this->paasSetupService = $paasSetupService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Import Default Config Before Setup Environment');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // create Default Roles
        $res = $this->paasSetupService->createRoles();
        $this->printOutput($io, $res, 'Roles Successfully Imported!');

        $this->createUser($io);

        // create Transcoding Profile
        $res = $this->paasSetupService->createDefaultTranscodingProfile();
        $this->printOutput($io, $res, 'Transcoding Profile Successfully Imported!');

        // create default TimeZone
        $res = $this->paasSetupService->createDefaultTimeZone();
        $this->printOutput($io, $res, 'TimeZone Successfully Imported!');

        // create App Setting
        $res = $this->paasSetupService->createAppSettings();
        $this->printOutput($io, $res, 'App Setting Successfully Imported!');

        return 0;
    }

    /**
     * @param SymfonyStyle $io
     * @param $result
     * @param $importedMsg
     */
    private function printOutput(SymfonyStyle $io , $result, $importedMsg)
    {
        $io->writeln('');
        $io->writeln('');
        $io->writeln('########');
        $io->writeln('   ##');
        $io->writeln('########');

        foreach ($result as $re)
        {
            $io->writeln($re);
        }
        $io->success($importedMsg);
    }


    /**
     * @param SymfonyStyle $io
     * @return int
     */
    public function createUser(SymfonyStyle $io)
    {


        /** @var AdminUserRepository $userRepo */
        $userRepo = $this->entityManager->getRepository(AdminUser::class);
        $adminUser = $userRepo->findOneBy(["email" => "superadmin@ivnews.com"]);
        $email = 'superadmin@ivnews.com';
        $password = "ivnews@1162";

        if ($adminUser) {
            $io->writeln('######## User already exist #########');
            return 0;
        } else {
            // add an Admin User
            $adminUser = new AdminUser();
        }
        /** @var AdminRoles $adminRole */
        $adminRole = $this->entityManager->getRepository(AdminRoles::class)->findOneBy(["code"=> UserRoleEnum::ROLE_SUPER_ADMIN]);
        if (!$adminUser->hasRole($adminRole->code)) {
            $adminUser->addAdminRoles($adminRole);
        }

        $adminUser->setUsername($email);
        $adminUser->setEmail($email);
        $adminUser->fullName = "Super Admin";
        $passwordEncoded = $this->encoder->encodePassword($adminUser, $password);
        $adminUser->updatePassword($passwordEncoded);
        $adminUser->enabled = true;

        $this->entityManager->persist($adminUser);
        $this->entityManager->flush();


        $io->writeln('');
        $io->writeln('######## LOGIN CREDENTIALS #########');
        $io->writeln("email: $email");
        $io->writeln("password: $password");
        $io->writeln('######### LOGIN CREDENTIALS ########');
        $projectDir = $this->container->getParameter('kernel.project_dir');
        file_put_contents("$projectDir/.env", "");
        $io->success('User Sucessfully Imported!');
    }

  
}
