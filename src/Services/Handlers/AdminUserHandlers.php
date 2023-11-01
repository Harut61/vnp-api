<?php
namespace App\Services\Handlers;

use App\Entity\AdminRoles;
use App\Entity\AdminUser;
use App\Entity\Audit\AuditAdminUserLogin;
use App\Enums\UserRoleEnum;
use App\Repository\AdminUserRepository;
use App\Services\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminUserHandlers
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var AdminUserRepository $repo */
    public $repo;

    protected $adminRolesHandlers;

    /** @var EmailService  $emailService */
    protected $emailService;

    /** @var TranslatorInterface $translator */
    protected $translator;

    /** @var UserPasswordEncoderInterface $encoder */
    protected $encoder;

    /**
     * AdminUserHandlers constructor.
     * @param EntityManagerInterface $entityManager
     * @param AdminRolesHandlers $adminRolesHandlers
     * @param EmailService $emailService
     * @param TranslatorInterface $translator
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(EntityManagerInterface $entityManager, AdminRolesHandlers $adminRolesHandlers, EmailService $emailService, TranslatorInterface $translator, UserPasswordEncoderInterface $encoder)
    {
        $this->repo = $entityManager->getRepository(AdminUser::class);
        $this->adminRolesHandlers = $adminRolesHandlers;
        $this->emailService = $emailService;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->encoder = $encoder;
    }

    /**
     * @param AdminUser $adminUser
     * @return AdminUser
     */
    public function setDefaultRole(AdminUser $adminUser)
    {
        /** @var AdminRoles $userRoles */
        $userRoles = $this->adminRolesHandlers->repo->findOneBy(["code"=> UserRoleEnum::ROLE_SUB_USER]);
        return $adminUser->addAdminRoles($userRoles);
    }

    /**
     * @param AdminUser $adminUser
     * @return int
     */
    public function sendEmailVerification(AdminUser $adminUser)
    {
        return $this->emailService->sendEmail("emails/validation_account.html.twig",
            $adminUser->email,
            [
            'token' => $adminUser->emailVerificationToken,
            'user' => $adminUser
            ] , $this->translator->trans('email.invitation.subject'));
    }

    public function setEmailVerificationToken(AdminUser $user)
    {
        if(getenv("APP_ENV") == "test"){
            $token = "123456789";
        } else {
            $token =  hash('SHA512', $user->email . time());
        }
        
        $user->emailVerificationToken = $token;
        $user->enabled = false;
        return $user;
    }

    /**
     * @param AdminUser $adminUser
     * @return int
     */
    public function sendResetPassWordEmail(AdminUser $adminUser)
    {
        return $this->emailService->sendEmail("emails/reset_password.html.twig",
            $adminUser->email,
            [
            'user' => $adminUser
            ], $this->translator->trans('email.reset_password.subject'));
    }

    /**
     * @param AdminUser $adminUser
     * @return int
     */
    public function sendResetEmailEmail(AdminUser $adminUser)
    {
        return $this->emailService->sendEmail("emails/reset_email.html.twig",
            $adminUser->email,
            [
                'user' => $adminUser
            ], $this->translator->trans('email.reset_email.subject'));
    }

    public function setResetOTP(AdminUser $user)
    {
        if(getenv("APP_ENV") == "test"){
            $otp = "1234";
        } else {
            $digits = 4;
            $otp =  rand(pow(10, $digits-1), pow(10, $digits)-1);
        }

        $user->otp = $otp;
        $user->enabled = false;
        return $user;
    }

    /**
     * @param $email
     * @return bool
     */
    public function checkUserExistByEmail($email)
    {
        $userExist = $this->repo->loadUserByemail($email);
        return (!empty($userExist)) ? true : false ;
    }

    /**
     * @param $password
     * @param AdminUser $adminUser
     * @return AdminUser
     */
    public function updatePassword($password, AdminUser $adminUser)
    {
        $adminUser->updatePassword($this->encoder->encodePassword($adminUser, $password));
        $adminUser->enabled = true;
        return $adminUser;
    }

    /**
     * @param AdminUser $adminUser
     * @return AdminUser
     */
    public function save(AdminUser $adminUser)
    {
        $this->entityManager->persist($adminUser);
        $this->entityManager->flush();
        return $adminUser;
    }

    /**
     * @param $type
     * @param $action
     * @param $ip
     * @param AdminUser $adminUser
     */
    public function insertAudit($type, $action, $ip , AdminUser $adminUser)
    {
        $auditAdminUsersLogin = new AuditAdminUserLogin();
        $auditAdminUsersLogin->userId = $adminUser->getId();
        $auditAdminUsersLogin->type = $type;
        $auditAdminUsersLogin->action = $action;
        $auditAdminUsersLogin->ip = $ip;

        $this->entityManager->persist($auditAdminUsersLogin);
        $this->entityManager->flush();

    }
}