<?php
namespace App\Services\Handlers;

use App\Entity\AdminRoles;
use App\Entity\Audit\AuditEndUserLogin;
use App\Entity\EndUser;
use App\Entity\Audit\AuditAdminUserLogin;
use App\Entity\RegistrationLog;
use App\Enums\UserRoleEnum;
use App\Repository\AdminUserRepository;
use App\Repository\EndUserRepository;
use App\Security\Provider\EndUserProvider;
use App\Services\EmailService;
use App\Util\Apple\AppleAuth;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class EndUserHandlers
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var EndUserRepository $repo */
    public $repo;


    /** @var EmailService  $emailService */
    protected $emailService;

    /** @var TranslatorInterface $translator */
    protected $translator;

    /** @var UserPasswordEncoderInterface $encoder */
    protected $encoder;

    /** @var JWTTokenManagerInterface $JWTManager */
    protected $JWTManager;

    /** @var RefreshTokenManagerInterface $refreshTokenManager */
    protected $refreshTokenManager;

    /** @var EndUserProvider $endUserProvider */
    protected $endUserProvider;

    /** @var KernelInterface $kernel */
    protected $kernel;

    /** @var  ManagerRegistry $doctrine */
    protected $doctrine;

    /**
     * EndUserHandlers constructor.
     * @param ContainerInterface $container
     * @param EmailService $emailService
     * @param TranslatorInterface $translator
     * @param UserPasswordEncoderInterface $encoder
     * @param JWTTokenManagerInterface $JWTManager
     * @param RefreshTokenManagerInterface $refreshTokenManager
     * @param EndUserProvider $endUserProvider
     * @param KernelInterface $kernel
     */
    public function __construct(ContainerInterface $container,
                                EmailService $emailService,
                                TranslatorInterface $translator,
                                UserPasswordEncoderInterface $encoder,
                                JWTTokenManagerInterface $JWTManager,
                                RefreshTokenManagerInterface $refreshTokenManager,
                                EndUserProvider $endUserProvider,
                                KernelInterface $kernel
    )
    {
        $this->doctrine = $container->get('doctrine');
        $this->entityManager = $this->doctrine->getManager();
        $this->repo = $this->entityManager->getRepository(EndUser::class);
        $this->emailService = $emailService;
        $this->translator = $translator;
        $this->encoder = $encoder;
        $this->JWTManager = $JWTManager;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->endUserProvider = $endUserProvider;
        $this->kernel = $kernel;
    }


    /**
     * @param EndUser $endUser
     * @return int
     */
    public function sendEmailVerification(EndUser $endUser)
    {
        return $this->emailService->sendEmail("emails/endUser/register_email_verification.html.twig",
            $endUser->email,
            [
            'token' => $endUser->emailVerificationToken,
            'user' => $endUser
            ] , $this->translator->trans('email.end_user_register.subject'));
    }

    public function setEmailVerificationToken(EndUser $user)
    {
        if(getenv("APP_ENV") == "test"){
            $otp = "1234";
        } else {
            $digits = 4;
            $otp =  rand(pow(10, $digits-1), pow(10, $digits)-1);
        }

        $user->emailVerificationToken = $otp;
        $user->enabled = false;
        return $user;
    }

    /**
     * @param EndUser $endUser
     * @return int
     */
    public function sendResetPassWordEmail(EndUser $endUser)
    {
        return $this->emailService->sendEmail("emails/reset_password.html.twig",
            $endUser->email,
            [
            'user' => $endUser
            ], $this->translator->trans('email.reset_password.subject'));
    }


    /**
     * @param EndUser $endUser
     * @return int
     */
    public function sendResetEmailEmail(EndUser $endUser, $newEmail)
    {
        return $this->emailService->sendEmail("emails/reset_email.html.twig",
            $newEmail,
            [
                'user' => $endUser
            ], $this->translator->trans('email.reset_password.subject'));
    }

    /**
     * @param EndUser $endUser
     * @return int
     */
    public function sendRegistrationConfirmEmail(EndUser $endUser)
    {
        return $this->emailService->sendEmail("emails/endUser/registeration_confirm.html.twig",
            $endUser->email,
            [
            'user' => $endUser
            ], $this->translator->trans('email.end_user_registeration_confirm.subject'));
    }

    public function setResetOTP(EndUser $user)
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
     * @param $email
     * @return bool
     */
    public function checkUserExistByChosenName($chosenName)
    {
        $userExist = $this->repo->loadUserByChosenName($chosenName);
        return (!empty($userExist)) ? true : false ;
    }

    /**
     * @param $email
     * @return EndUser|null
     */
    public function getByEmail($email)
    {
        return $this->repo->loadUserByemail($email);
    }

    /**
     * @param $password
     * @param EndUser $endUser
     * @return EndUser
     */
    public function updatePassword($password, EndUser $endUser)
    {
        $password = $this->generatePassword($password, $endUser);
        $endUser->updatePassword($password);
        $endUser->enabled = true;
        return $endUser;
    }

    /**
     * @param $password
     * @param EndUser $endUser
     * @return string
     */
    public function generatePassword($password, EndUser $endUser) {
        return $this->encoder->encodePassword($endUser, $password);
    }
    /**
     * @param EndUser $endUser
     * @return EndUser
     */
    public function save(EndUser $endUser)
    {
        $this->entityManager->persist($endUser);
        $this->entityManager->flush();
        return $endUser;
    }

    /**
     * @param $email
     * @param $ip
     * @param $postParams
     * @param $type
     * @return RegistrationLog
     */
    public function initRegistrationLog($email, $ip, $postParams, $type) {
        $registrationLog = new RegistrationLog();
        $registrationLog->email = $email;
        $registrationLog->ipAddress =$ip;
        $registrationLog->registrationType = $type;
        $registrationLog->postParams = $postParams;
        return $registrationLog;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param RegistrationLog $registrationLog
     * @return RegistrationLog
     */
    public function saveRegistrationLog( EntityManagerInterface $entityManager, RegistrationLog $registrationLog)
    {

        $entityManager->persist($registrationLog);
        $entityManager->flush();
        return $registrationLog;
    }

    /**
     * @param $type
     * @param $action
     * @param $ip
     * @param EndUser $endUser
     */
    public function insertAudit($type, $action, $ip , EndUser $endUser)
    {
        $auditAdminUsersLogin = new AuditEndUserLogin();
        $auditAdminUsersLogin->userId = $endUser->getId();
        $auditAdminUsersLogin->type = $type;
        $auditAdminUsersLogin->action = $action;
        $auditAdminUsersLogin->ip = $ip;

        $this->entityManager->persist($auditAdminUsersLogin);
        $this->entityManager->flush();

    }

    /**
     * @param EndUser $endUser
     * @return array
     */
    public function generateToken(EndUser $endUser){
        $valid = new \DateTime('now');
        $valid->add(new \DateInterval('P3D'));

        $refreshToken = $this->refreshTokenManager->create();
        $refreshToken->setUsername($endUser->getUsername());
        $refreshToken->setRefreshToken();
        $refreshToken->setValid($valid);

        $this->refreshTokenManager->save($refreshToken);
        return [
            'token' =>  $this->JWTManager->create($endUser),
            'refresh_token' => $refreshToken->getRefreshToken(),
            'data' => [
                'roles' => $endUser->getRoles()
            ]
        ];
    }

    /**
     * @param $platform
     * @param $token
     * @return array|bool
     */
    public function loginWithGoogle($platform , $token)
    {
        $googleClientId =  getenv("GOOGLE_CLIENT_ID_WEB");

//        if($platform == "android") {
//            $googleClientId =  getenv("GOOGLE_CLIENT_ID_ANDROID");
//        }

        if ($platform == "ios") {
            $googleClientId =  getenv("GOOGLE_CLIENT_ID_IOS");
        }

        $client = new \Google_Client(['client_id' => $googleClientId]);  // Specify the CLIENT_ID of the app that accesses the backend

        $payload = $client->verifyIdToken($token);

        if ($payload) {
            $userId = $payload['sub'];
            $email = $payload['email'];
            $picture = $payload['picture'];
            $endUser = $this->endUserProvider->fetchUserByGoogleToken($userId, $email);
            $newUser = false;
            if(!$endUser) {
                $newUser = true;
                $endUser = new EndUser();
                $endUser->email = $email;
                $endUser->username = $email;
                $endUser->plainPassword = "";
                $endUser->googleId = $userId;
                $endUser->profilePicUrl = $picture;
                $endUser->enabled = true;
                $this->save($endUser);
                $this->sendRegistrationConfirmEmail($endUser);
            }
            return [
                "newUser" => $newUser,
                "endUser" => $endUser,
                ]
            ;
        }

        return false;
    }

    /**
     * @param $platform
     * @param $token
     * @return array|bool
     */
    public function loginWithApple($platform , $token)
    {

            $configData = [
                "client_id" => getenv("APPLE_CLIENT_KEY"),
                "team_id"   => getenv("APPLE_TEAM_ID"),
                "key_id"    => getenv("APPLE_KEY"),
                "key"       => $this->kernel->getProjectDir().'/APPLE_AUTHKEY.p8', //path where is your p8 key example if your key is in storage
                "code"      => $token
            ];

            $appleAuth = new AppleAuth($configData);

            $jwt = $appleAuth->getJwtSigned();

            // Refresh Token and get user Data
            $userData = $appleAuth->getUserData();
            $user = $userData["user"];


        if ($user) {
            $userId = $user->sub;
            $email = $user->email;
            /** @var EndUser $endUser */
            $endUser = $this->endUserProvider->fetchUserByAppleToken($userId);
            $newUser = false;
            $applePrivateEmail = false;

            // List of not allowed domains
            $notAllowed = [
                'privaterelay.appleid.com'
            ];

            // Make sure the address is valid
            if (filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                // Separate string by @ characters (there should be only one)
                $parts = explode('@', $email);
                // Remove and return the last part, which should be the domain
                $domain = array_pop($parts);
                // Check if the domain is in our list
                if ( in_array($domain, $notAllowed))
                {
                    $newUser = true;
                    $applePrivateEmail = true;
                }
            }

            if(!$endUser) {
                $newUser = true;
                $endUser = new EndUser();
                if($applePrivateEmail) {
                    $endUser->applePrivateEmail = $email;
                    $endUser->isApplePrivateEmail = true;
                }
                $endUser->email = $email;
                $endUser->username = $email;
                $endUser->plainPassword = "";
                $endUser->appleId = $userId;
                $endUser->enabled = true;
                $this->save($endUser);
                $this->sendRegistrationConfirmEmail($endUser);
            } else {
                // Separate string by @ characters (there should be only one)
                $parts = explode('@', $endUser->email);

                // Remove and return the last part, which should be the domain
                $domain = array_pop($parts);

                // Check if the domain is in our list
                if ( !in_array($domain, $notAllowed))
                {
                    $newUser = false;
                }
            }

            return [
                "newUser" => $newUser,
                "endUser" => $endUser,
                ]
            ;
        }

        return false;
    }
}
