<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Doctrine\RefreshTokenManager;
use App\Entity\EndUser;
use App\Entity\RegistrationLog;
use App\Enums\EndUserRegistrationTypeEnum;
use App\Enums\UserStatusEnum;
use App\Security\Provider\EndUserProvider;
use App\Services\Handlers\EndUserHandlers;
use App\Util\AwsSqsUtil;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Google\Exception;
use function GuzzleHttp\Psr7\get_message_body_summary;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Services\Vne\PreferencesService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Psr\Log\LoggerInterface;

/**
 * @Route("/end_user")
 */
class EndUserAuthController extends AbstractController
{
    private $logger;

    /**
     * EndUserAuth constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @param JWTTokenManagerInterface $JWTManager
     * @param LoggerInterface $logger
     * @param UserInterface $user
     * @return JsonResponse
     * @Route("/authentication_token", name="end_user_authentication_token")
     */
    public function authentication(Request $request, JWTTokenManagerInterface $JWTManager, UserInterface $user)
    {
        /** @var EndUser $user */
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        if(!$user->enabled){
            return new JsonResponse(['message' => "verify your email"], 400);
        }

        return new JsonResponse(['accessToken' => $JWTManager->create($user)], 200);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     * @Route("/exist", name="end_user_exist")
     */
    public function userExist(Request $request, EntityManagerInterface $entityManager)
    {
        $params = json_decode($request->getContent(), true);
        if (!array_key_exists("email", $params)) {
            return $this->json(["message" => "email Required"], 422);
        }
        $email = (\array_key_exists('email', $params)) ? $params['email'] : null;
        $conn = $entityManager->getConnection();
        $sql = "select id from end_users where email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $params["email"]);
        $stmt->execute();

        $userExist = ($stmt->rowCount() > 0) ? true : false;

        if(!empty($userExist)){
            /** @var EndUser $user */
            $user = $entityManager->getRepository(EndUser::class)->findOneBy(['email' => $email]);
            if(empty($user->password)) {
                if(!empty($user->googleId)){
                    return new JsonResponse(['message' => sprintf("User Already Exist With googleId. Set Password if you want"), 'SocialLogin' => 'googleId', 'password' => false], 400);
                }
                if(!empty($user->appleId)){
                    return new JsonResponse(['message' => sprintf("User Already Exist With appleId. Set Password if you want"), 'SocialLogin' => 'appleId', 'password' => false], 400);
                }
            }
            else{
                if(!empty($user->googleId)){
                    return new JsonResponse(['message' => sprintf("User Already Exist With googleId."), 'SocialLogin' => 'googleId', 'password' => true], 400);
                }
                if(!empty($user->appleId)){
                    return new JsonResponse(['message' => sprintf("User Already Exist With appleId."), 'SocialLogin' => 'appleId', 'password' => true], 400);
                }
            }
        }

        return new JsonResponse(['exist' => $userExist], 200);
    }

    /**
     * @Route("/auth/google/{platform}", name="api_google_authentication_token")
     * @param $platform
     * @param Request $request
     * @param EndUserHandlers $endUserHandlers
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function authWithGoogle($platform, Request $request,
                                   EndUserHandlers $endUserHandlers, EntityManagerInterface $entityManager
    )
    {
        $params = json_decode($request->getContent(), true);
        if (!array_key_exists("token", $params)) {
            return $this->json(["message" => "token Required"], 422);
        }

        try{
            $responseArray = $endUserHandlers->loginWithGoogle($platform, $params["token"]);
        } catch (\Exception $exception) {
            $registrationLog = $endUserHandlers->initRegistrationLog("", $request->getClientIp(), $params, EndUserRegistrationTypeEnum::GOOGLE );
            $registrationLog->status = "Failed";
            $registrationLog->message = $exception->getMessage();
//            $entityManager->persist($registrationLog);
//            $entityManager->flush();
            return $this->json(["message" =>  $exception->getMessage()], 422);
        }

        if ($responseArray) {
            /** @var EndUser $endUser */
            $endUser = $responseArray["endUser"];

            $response = $endUserHandlers->generateToken($endUser);
            $registrationLog = $endUserHandlers->initRegistrationLog($endUser->email, $request->getClientIp(), $params, EndUserRegistrationTypeEnum::GOOGLE );
            $registrationLog->status = "Success";
//            $entityManager->persist($registrationLog);
//            $entityManager->flush();
            $response["newUser"] = $responseArray["newUser"];
            return new JsonResponse($response, 200);
        }

        $registrationLog = $endUserHandlers->initRegistrationLog("", $request->getClientIp(), $params, EndUserRegistrationTypeEnum::GOOGLE );
        $registrationLog->status = "Failed";
        $registrationLog->message = "InValid Token";
//        $entityManager->persist($registrationLog);
//        $entityManager->flush();
        return $this->json(["message" => "InValid Token"], 422);
    }

    /**
     * @Route("/auth/apple/{platform}", name="api_apple_authentication_token")
     * @param $platform
     * @param Request $request
     * @param EndUserHandlers $endUserHandlers
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function authWithApple($platform, Request $request,
                                   EndUserHandlers $endUserHandlers, EntityManagerInterface $entityManager
    )
    {
        $params = json_decode($request->getContent(), true);
        if (!array_key_exists("token", $params)) {
            return $this->json(["message" => "token Required"], 422);
        }

        try{
            $responseArray = $endUserHandlers->loginWithApple($platform, $params["token"]);
        }catch (\Exception $exception) {
            $registrationLog = $endUserHandlers->initRegistrationLog("", $request->getClientIp(), $params, EndUserRegistrationTypeEnum::APPLE );
            $registrationLog->status = "Failed";
            $registrationLog->message = $exception->getMessage();
//            $entityManager->persist($registrationLog);
//            $entityManager->flush();
            return new JsonResponse([ "message" => $exception->getMessage() ], 422);
        }

        if ($responseArray) {
            $response = $endUserHandlers->generateToken($responseArray["endUser"]);
            $response["newUser"] = $responseArray["newUser"];
            return new JsonResponse($response, 200);
        }

        return $this->json(["message" => "InValid Token"], 422);
    }
    /**
     * @Route("/register/apple", name="api_apple_register_end_user")
     *
     * @param Request $request
     * @param EndUserHandlers $endUserHandlers
     * @return JsonResponse
     */
    public function signUpWithApple(Request $request,
                                  EndUserHandlers $endUserHandlers )
    {
        // get Data From Request
        $data = json_decode($request->getContent(), true);
        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        // check email and contactInfo value exist in json
        $email = (\array_key_exists('email', $data)) ? $data['email'] : null;
        $chosenName = (\array_key_exists('chosenName', $data)) ? $data['chosenName'] : "";
        if (empty($chosenName) || empty($email)) {
            return new JsonResponse(['message' => sprintf('email or chosenName  is empty')], 400);
        }

        // AdminUser Email Exist Or not
        $userExist = $endUserHandlers->checkUserExistByEmail($email);
        if (!empty($userExist)) {
            return new JsonResponse(['message' => sprintf("User Already Exist With email!")], 400);
        }
        /** @var EndUser $user */
        $user = $this->getUser();
        $user->email = $email;
        $user->username = $email;
        $user->chosenName = $chosenName;
        $endUserHandlers->save($user);

        $endUserHandlers->sendEmailVerification($user);
        $endUserHandlers->insertAudit("create", "User Created", $request->getClientIp(), $user);

        return new JsonResponse(['message' => sprintf('User %s successfully registered', $user->getUsername())], 201);
    }

    /**
     * @Route("/register", methods="POST", name="end_user_register")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param EndUserHandlers $endUserHandlers
     * @param EntityManagerInterface $entityManager
     * @return Response|JsonResponse
     */
    public function register(Request $request, ValidatorInterface $validator, EndUserHandlers $endUserHandlers, EntityManagerInterface $entityManager, AwsSqsUtil $awsSqsUtil)
    {
        // get Data From Request
        $data = json_decode($request->getContent(), true);
        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        // check email and contactInfo value exist in json
        $email = (\array_key_exists('email', $data)) ? $data['email'] : null;
        $chosenName = (\array_key_exists('chosenName', $data)) ? $data['chosenName'] : "";
        $password = (\array_key_exists('password', $data)) ? $data['password'] : "";
        $newsMarket= (\array_key_exists('newsMarket', $data)) ? $data['newsMarket'] : [];

        // AdminUser Email Exist Or not
        $userExist = $endUserHandlers->checkUserExistByEmail($email);

        $registrationLog = $endUserHandlers->initRegistrationLog($email, $request->getClientIp(), $data, EndUserRegistrationTypeEnum::EMAIL );

        if(!empty($userExist) && empty($password)){
            /** @var EndUser $user */
            $user = $entityManager->getRepository(EndUser::class)->findOneBy(['email' => $email]);
            if(!empty($user->googleId)){
                return new JsonResponse(['message' => sprintf("User Already Exist With googleId. Set Password if you want"), 'SocialLogin' => 'googleId'], 400);
            }
            if(!empty($user->appleId)){
                return new JsonResponse(['message' => sprintf("User Already Exist With appleId. Set Password if you want"), 'SocialLogin' => 'appleId'], 400);
            }
        }

        if (empty($chosenName) || empty($password) || empty($email) || empty($newsMarket)) {
            $registrationLog->status = "Failed";
            $registrationLog->message = "email or chosenName or password or newsMarket is empty";
//            $entityManager->persist($registrationLog);
//            $entityManager->flush();
            return new JsonResponse(['message' => sprintf('email or chosenName or password or newsMarket is empty')], 400);
        }

        if (!empty($userExist)) {
            $registrationLog->status = "Failed";
            $registrationLog->message = "User Already Exist With email!";
            /** @var EndUser $user */
            $user = $entityManager->getRepository(EndUser::class)->findOneBy(['email' => $email]);
            if($user->enabled == false){
                return new JsonResponse(['message' => sprintf("User Already Exist With email, please activate your account")], 400);
            }
//            $entityManager->persist($registrationLog);
//            $entityManager->flush();
            return new JsonResponse(['message' => sprintf("User Already Exist With email!")], 400);
        }
        $endUser = new EndUser();
        $endUser->setUsername($email);
        $endUser->setEmail($email);
        $endUser->chosenName = $chosenName;
        $endUser->plainPassword = $password;
        $endUser->setSocialName($chosenName);
        $endUser->newsMarketList = $newsMarket;
        
        try{
            $errors = $validator->validate($endUser, ['groups' => 'registration']);
        }
        catch (ValidationException $e){
            $errors = $e->getMessage();
        }

        if (!empty($errors)) {
            $registrationLog->status = "Failed";
            $registrationLog->message = $errors;
//            $entityManager->persist($registrationLog);
//            $entityManager->flush();
            return new JsonResponse(['message' => $errors], 400);
        }

        $endUser->password = $endUserHandlers->generatePassword($password, $endUser);
        $endUser = $endUserHandlers->setEmailVerificationToken($endUser);

        $endUser = $endUserHandlers->save($endUser);
        $endUserHandlers->sendEmailVerification($endUser);

        $endUserHandlers->insertAudit("create", "User Created", $request->getClientIp(), $endUser);

        $registrationLog->status = "Success";

//        $entityManager->persist($registrationLog);
//        $entityManager->flush();
        $queueUrl = $awsSqsUtil->getQueueUrl(getenv("SOURCE_VIDEO_DELETE_SQS_QUEUE_NAME"));
        $awsSqsUtil->sendMessage($queueUrl ,
            json_encode([
                "ipAddress"=>$request->getClientIp(),
                "email" => $email,
                "status" => $registrationLog->status,
                "postParams" => $data,
                "message" => $registrationLog->message,
                "registrationType" => EndUserRegistrationTypeEnum::EMAIL

            ])
        );

        return new JsonResponse(['message' => sprintf('User %s successfully created', $endUser->getUsername())], 201);
    }

    /**
     * @Route("/register/resend/otp", methods="POST")
     * @param Request $request
     * @param EndUserHandlers $endUserHandlers
     * @return Response|JsonResponse
     */
    public function resendRegistrationOtp(Request $request, EndUserHandlers $endUserHandlers)
    {
        // get Data From Request
        $data = json_decode($request->getContent(), true);
        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        // check email and contactInfo value exist in json
        $email = (\array_key_exists('email', $data)) ? $data['email'] : null;
        if ( empty($email)) {
            return new JsonResponse(['message' => sprintf('email is empty')], 400);
        }
        $endUser = $endUserHandlers->getByEmail($email);
        if($endUser->enabled) {
            return new JsonResponse(['message' => 'User Already Active, Please Login'], 422);
        }
        $endUserHandlers->sendEmailVerification($endUser);
        return new JsonResponse(['message' => 'OTP Sent on your email'], 200);
    }

    /**
     * @param Request $request
     * @param JWTTokenManagerInterface $JWTManager
     * @param EndUserHandlers $endUserHandlers
     * @return JsonResponse
     * @Route("/register/confirm", methods="POST", name="enduser_confirm_registration")
     */
    public function completeRegistration(Request $request, JWTTokenManagerInterface $JWTManager, EndUserHandlers $endUserHandlers)
    {

        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $otp = (\array_key_exists('otp', $data)) ? $data['otp'] : null;

        if (empty($otp)) {
            return new JsonResponse(['message' => sprintf('Invalid OTP')], 400);
        }

        /** @var EndUser $endUser */
        $endUser = $endUserHandlers->repo->findOneBy(["emailVerificationToken" => $otp]);

        if (empty($endUser)) {
            return new JsonResponse(['message' => sprintf('Invalid OTP')], 400);
        }

        $endUser->emailVerificationToken = "";
        $endUser->enabled = true;
        $endUser->blocked = false;
        $endUser->userStatus = UserStatusEnum::ACTIVE;

        $endUserHandlers->save($endUser);

        $endUserHandlers->insertAudit("update", "Register Completed", $request->getClientIp(), $endUser);

        $response = $endUserHandlers->generateToken($endUser);
        $endUserHandlers->sendRegistrationConfirmEmail($endUser);
        return new JsonResponse($response, 200);
    }


    /**
     * @param Request $request
     * @param JWTTokenManagerInterface $JWTManager
     * @param EndUserHandlers $endUserHandlers
     * @return JsonResponse
     * @Route("/sign_up_status/update", methods="POST", name="sign_up_status_update")
     */
    public function signUpStatusUpdate($id, Request $request, JWTTokenManagerInterface $JWTManager, EndUserHandlers $endUserHandlers)
    {
        $endUser = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $signUpStatus = (\array_key_exists('signUpStatus', $data)) ? $data['signUpStatus'] : null;

        if (empty($signUpStatus)) {
            return new JsonResponse(['message' => sprintf('signUpStatus is empty')], 400);
        }



        $endUser->signUpStatus = $signUpStatus;
        $endUserHandlers->save($endUser);

        $endUserHandlers->insertAudit("update", "signUpStatus update Successfully", $request->getClientIp(), $endUser);

        return new JsonResponse(['message' => sprintf('signUpStatus update Successfully')], 200);
    }

    /**
     * @param Request $request
     * @param EndUserHandlers $endUserHandlers
     * @return JsonResponse
     * @Route("/reset/password", methods="POST", name="end_user_reset_password")
     */
    public function resetPassword(Request $request, EndUserHandlers $endUserHandlers)
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $email = (\array_key_exists('email', $data)) ? $data['email'] : null;
        if (empty($email)) {
            return new JsonResponse(['message' => sprintf('Invalid Request')], 400);
        }

        /** @var EndUser $endUser */
        $endUser = $endUserHandlers->repo->findOneBy(["email" => $email]);

        if(empty($endUser)){
            return new JsonResponse(['message' => sprintf('Invalid email')], 400);
        }

        $endUser = $endUserHandlers->setResetOTP($endUser);
        $endUserHandlers->sendResetPassWordEmail($endUser);
        $endUserHandlers->save($endUser);

        $endUserHandlers->insertAudit("update", "Reset Password Requested",$request->getClientIp(), $endUser);
        return new JsonResponse(['message' => "email sent"], 200);
    }

    /**
     * @param Request $request
     * @param EndUserHandlers $endUserHandlers
     * @return JsonResponse
     * @Route("/update/password", methods="POST", name="end_user_update_password")
     */
    public function updatePassword(Request $request, EndUserHandlers $endUserHandlers)
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $otp = (\array_key_exists('otp', $data)) ? $data['otp'] : null;
        $email = (\array_key_exists('email', $data)) ? $data['email'] : null;
        $password = (\array_key_exists('password', $data)) ? $data['password'] : null;

        if (empty($otp) && empty($password) && empty($email)) {
            return new JsonResponse(['message' => sprintf('Invalid Request')], 400);
        }

        /** @var EndUser $endUser */
        $endUser = $endUserHandlers->repo->findOneBy(["otp" => $otp, "email" => $email]);
        if(empty($endUser)){
            return new JsonResponse(['message' => sprintf('Invalid OTP')], 400);
        }

        $endUser = $endUserHandlers->updatePassword($password, $endUser);
        $endUser->otp = "";

        $endUserHandlers->save($endUser);

        if($endUser) {
            $endUserHandlers->insertAudit("update", "Password Updated",$request->getClientIp(), $endUser);
            // TODO send Reset Password confirm Email
            return $this->json(["message" => "password updated successfully!"]);
        } else{
            return new JsonResponse(['message' => sprintf('Request Failed')], 400);
        }
    }


    /**
     * @Route("/session/invalidate", methods="POST", name="end_user_session_invalidate")
     *
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param EndUserHandlers $endUserHandlers
     * @return JsonResponse
     */
    public function logOutFromDevice(Request $request, TokenStorageInterface $tokenStorage, EndUserHandlers $endUserHandlers)
    {
        /** @var EndUser $user */
        $user = $tokenStorage->getToken()->getUser();
        $params = json_decode($request->getContent(), true);
        $tokenParts = explode(".", $params["token"]);
//        $tokenHeader = base64_decode($tokenParts[0]);
        $tokenPayload = base64_decode($tokenParts[1]);

        $jwtPayload = json_decode($tokenPayload, true);
        $tokens = $user->tokens;
        if(array_key_exists($jwtPayload["token"], $tokens)){
            unset($tokens[$jwtPayload["token"]]);
            $user->tokens = $tokens;
            $endUserHandlers->save($user);
        }
        return new JsonResponse(["message" => "logout successfully!"], 200);
    }

    /**
     * @param Request $request
     * @param EndUserHandlers $endUserHandlers
     * @return JsonResponse
     * @Route("/change/password", methods="POST", name="change_password")
     */
    public function changePassword(Request $request, EndUserHandlers $endUserHandlers, JWTTokenManagerInterface $JWTManager)
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $password = (\array_key_exists('password', $data)) ? $data['password'] : "";

        if (empty($password)) {
            return new JsonResponse(['message' => sprintf('Password is not empty')], 400);
        }

        $password = (\array_key_exists('password', $data)) ? $data['password'] : null;
        /** @var EndUser $endUser */
        $endUser = $this->getUser();
        $endUser = $endUserHandlers->updatePassword($password, $endUser);
        $endUserHandlers->save($endUser);

        $endUserHandlers->insertAudit("update", "Password Update Completed", $request->getClientIp(), $endUser);

        return new JsonResponse(["message" => "Password Update Completed"], 200);
    }

    /**
     * @param Request $request
     * @param EndUserHandlers $endUserHandlers
     * @param PreferencesService $preferencesService
     * @return JsonResponse
     * @Route("/update/chosen/name", methods="POST", name="update_chosen_name")
     */
    public function updateChosenName(Request $request, EndUserHandlers $endUserHandlers, PreferencesService $preferencesService)
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $email = (\array_key_exists('email', $data)) ? $data['email'] : null;
        $chosenName = (\array_key_exists('chosenName', $data)) ? $data['chosenName'] : null;
        $newsMarket = (\array_key_exists('newsMarket', $data)) ? $data['newsMarket'] : [];
        $socialName = (\array_key_exists('socialName', $data)) ? $data['socialName'] : null;

        if (empty($chosenName) && empty($email)) {
            return new JsonResponse(['message' => sprintf('Invalid Request')], 400);
        }

        /** @var EndUser $endUser */
        $endUser = $endUserHandlers->repo->findOneBy(["email" => $email]);
        if(empty($endUser)){
            return new JsonResponse(['message' => sprintf('Invalid Email')], 400);
        }

        if ($socialName) {
            $socialNameExists = $endUserHandlers->repo->findOneBy(["socialName" => $socialName]);
            $existingUserEmail = null;
            if ($socialNameExists) {
                $existingUserEmail = $socialNameExists->email;
            }
            if (!$socialNameExists || $email === $existingUserEmail) {
                $endUser->socialName = $socialName;
            } else {
                return new JsonResponse(['message' => sprintf('Social Name already exists.')], 400);
            }
        }

        $endUser = $endUser->updateChosenName($chosenName);
        /** @var EndUser $endUser */
        $endUserId = $endUser->getId();

        if(!empty($newsMarket)){
            $endUser->newsMarketList = $newsMarket;
            try{
                $preferencesService->setNewsMarkets(
                    $endUserId,
                    $newsMarket
                );
            } catch (\Exception $exception) {
                $errors = $exception->getMessage();
                $this->logger->alert($errors);
            }

        }

        $endUserChosenName = $endUser->chosenName;
        $endUserNewsMarket = $endUser->newsMarketList;
        if (!empty($endUserChosenName) && !empty($endUserNewsMarket)) {
            $endUser->signUpStatus = true;
        }
        $endUserHandlers->save($endUser);
        $returnMessage = empty($newsMarket) ? "ChosenName updated successfully!" : "Data updated successfully!";
        if($endUser) {
            $endUserHandlers->insertAudit("update", "ChosenName Updated",$request->getClientIp(), $endUser);
            // TODO send Reset Password confirm Email
            return $this->json(["message" => $returnMessage]);
        } else{
            return new JsonResponse(['message' => sprintf('Request Failed')], 400);
        }
    }


    /**
     * @param Request $request
     * @param EndUserHandlers $endUserHandlers
     * @return JsonResponse
     * @Route("/change/email", methods="POST", name="end_user_change_email")
     */
    public function changeEmail(Request $request, EndUserHandlers $endUserHandlers, UserPasswordEncoderInterface $encoder)
    {

        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        /** @var EndUser $user */
        $user = $this->getUser();

        $password = (\array_key_exists('password', $data)) ? $data['password'] : null;

        if (empty($password)) {
            return new JsonResponse(['message' => sprintf('password is empty')], 400);
        }

        $adminPassword = $encoder->isPasswordValid($user, $password);

        if($adminPassword == false){
            return new JsonResponse(['message' => sprintf("Password is incorrect!")], 400);
        }

        $endUser = $endUserHandlers->setResetOTP($user);

        $endUserHandlers->sendResetEmailEmail($endUser, $endUser->email);
        $user->enabled = true;
        $endUserHandlers->save($endUser);

        $endUserHandlers->insertAudit("update", "Reset Email Requested",$request->getClientIp(), $endUser);
        return new JsonResponse(['message' => "email sent"], 200);
    }

    /**
     * @param Request $request
     * @param EndUserHandlers $endUserHandlers
     * @return JsonResponse
     * @Route("/change/email/verify", methods="POST", name="end_user_verify_email")
     */
    public function verifyEmail(Request $request, EndUserHandlers $endUserHandlers, UserPasswordEncoderInterface $encoder)
    {

        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        /** @var EndUser $user */
        $user = $this->getUser();

        $email = (\array_key_exists('email', $data)) ? $data['email'] : null;
        $otp = (\array_key_exists('otp', $data)) ? $data['otp'] : null;

        if (empty($email)) {
            return new JsonResponse(['message' => sprintf('email is empty')], 400);
        }
        if (empty($otp)) {
            return new JsonResponse(['message' => sprintf('otp is empty')], 400);
        }

        $userExist = $endUserHandlers->checkUserExistByEmail($email);

        if (!empty($userExist)) {
            return new JsonResponse(['message' => sprintf("User Already Exist With email!")], 400);
        }

        $endUser = $endUserHandlers->repo->findOneBy(["otp" => $otp]);
        if(empty($endUser)){
            return new JsonResponse(['message' => sprintf('Invalid OTP')], 400);
        }

        $endUser = $endUserHandlers->setResetOTP($user);

        $endUserHandlers->sendResetEmailEmail($endUser, $email);
        $user->enabled = true;
        $endUserHandlers->save($endUser);

        $endUserHandlers->insertAudit("update", "Reset Email Requested",$request->getClientIp(), $endUser);
        return new JsonResponse(['message' => "email sent"], 200);
    }

    /**
     * @param Request $request
     * @param EndUserHandlers $endUserHandlers
     * @return JsonResponse
     * @Route("/update/email", methods="POST", name="end_user_update_email")
     */
    public function updateEmail(Request $request, EndUserHandlers $endUserHandlers)
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $otp = (\array_key_exists('otp', $data)) ? $data['otp'] : null;
        $email = (\array_key_exists('email', $data)) ? $data['email'] : null;


        if (empty($otp) && empty($email)) {
            return new JsonResponse(['message' => sprintf('Invalid Request')], 400);
        }


        $endUser = $endUserHandlers->repo->findOneBy(["otp" => $otp]);
        if(empty($endUser)){
            return new JsonResponse(['message' => sprintf('Invalid OTP')], 400);
        }

        $endUser->email = $email;
        $endUser->otp = "";

        $endUserHandlers->save($endUser);

        if($endUser) {
            $endUserHandlers->insertAudit("update", "Email Updated",$request->getClientIp(), $endUser);
            // TODO send Reset Password confirm Email
            return $this->json(["message" => "email updated successfully!"]);
        } else{
            return new JsonResponse(['message' => sprintf('Request Failed')], 400);
        }
    }

    /**
     * @Route("/set/news_markets", name="set_news_markets")
     * @param Request $request
     * @return JsonResponse
     * @param PreferencesService $preferencesService
     * @param EndUserHandlers $endUserHandlers
     */
    public function setNewsMarkets(Request $request, PreferencesService $preferencesService, EndUserHandlers $endUserHandlers): Response
    {
        $body = json_decode($request->getContent());

        if (empty($body)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $pref = (array_key_exists('pref', $body)) ? $body->pref : null;
        /** @var EndUser $endUser */
        $endUser = $this->getUser();
        $endUser->newsMarketList = $pref;
        $endUserHandlers->save($endUser);
        $endUserId = $endUser->getId();
        
        if (empty($pref)) {
            return new JsonResponse(['message' => sprintf('News Market is not provided')], 400);
        }

        $response = $preferencesService->setNewsMarkets(
            $endUserId,
            $pref
        );
        return new JsonResponse(['message' => $response], 200);
    }
}
