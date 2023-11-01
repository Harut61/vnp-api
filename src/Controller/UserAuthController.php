<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\AdminUser;
use App\Entity\AdminRoles;
use App\Entity\EndUser;
use App\Enums\UserRoleEnum;
use App\Enums\UserStatusEnum;
use App\Repository\AdminUserRepository;
use App\Services\Handlers\AdminRolesHandlers;
use App\Services\Handlers\AdminUserHandlers;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserAuthController extends AbstractController
{
    /**
     * @param Request $request
     * @param JWTTokenManagerInterface $JWTManager
     * @param UserInterface $user
     * @return JsonResponse
     * @Route("/authentication_token", name="authentication_token")
     */
    public function authentication(Request $request, JWTTokenManagerInterface $JWTManager, UserInterface $user)
    {


        /** @var AdminUser $user */
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        if(!$user->enabled){
            return new JsonResponse(['message' => "verify your email"], 400);
        }

        /** @var EndUser $endUser */
        if (!$endUser) {
            throw $this->createNotFoundException('EndUser not found');
        }

        if(!$endUser->enabled){
            return new JsonResponse(['message' => "email is not verified"], 400);
        }

        return new JsonResponse(['accessToken' => $JWTManager->create($user)], 200);
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param AdminUserHandlers $adminUserHandlers
     * @param AdminRolesHandlers $adminRolesHandlers
     * @param \Swift_Mailer $mailer
     * @return JsonResponse|Response
     * @Route("/admin/register", methods="POST")
     */
    public function register(Request $request, ValidatorInterface $validator, AdminUserHandlers $adminUserHandlers, AdminRolesHandlers $adminRolesHandlers, \Swift_Mailer $mailer)
    {
        // get Data From Request
        $data = json_decode($request->getContent(), true);
        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        // check email and contactInfo value exist in json
        $email = (\array_key_exists('email', $data)) ? $data['email'] : null;
        $contactInfo = (\array_key_exists('contactInfo', $data)) ? $data['contactInfo'] : "";
        if (empty($contactInfo) || empty($email)) {
            return new JsonResponse(['message' => sprintf('email or Contact Info is empty')], 400);
        }

        // AdminUser Email Exist Or not
        $userExist = $adminUserHandlers->checkUserExistByEmail($email);
        if (!empty($userExist)) {
            return new JsonResponse(['message' => sprintf("User Already Exist With email!")], 400);
        }

        $user = new AdminUser();
        $user->setUsername($email);
        $user->setEmail($email);
        $user->contactInfo = $contactInfo;

        $errors = $validator->validate($user, ['groups' => 'registration']);

        if (!empty($errors)) {
            return new Response($errors);
        }

        $user = $adminUserHandlers->setDefaultRole($user);

        $user = $adminUserHandlers->setEmailVerificationToken($user);

        $user = $adminUserHandlers->save($user);
        $adminUserHandlers->sendEmailVerification($user);

        $adminUserHandlers->insertAudit("create", "User Created",$request->getClientIp(), $user);

        return new JsonResponse(['message' => sprintf('User %s successfully created', $user->getUsername())], 201);
    }

    /**
     * @param Request $request
     * @param JWTTokenManagerInterface $JWTManager
     * @param AdminUserHandlers $adminUserHandlers
     * @return JsonResponse
     * @Route("/admin/register/confirm", methods="POST", name="admin_confirm_registration")
     */
    public function completeRegistration(Request $request, JWTTokenManagerInterface $JWTManager, AdminUserHandlers $adminUserHandlers)
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $token = (\array_key_exists('token', $data)) ? $data['token'] : null;

        if (empty($token)) {
            return new JsonResponse(['message' => sprintf('Invalid Request')], 400);
        }

        /** @var AdminUser $adminUser */
        $adminUser = $adminUserHandlers->repo->findOneBy(["emailVerificationToken" => $token]);

        if(empty($adminUser)){
            return new JsonResponse(['message' => sprintf('Invalid Token')], 400);
        }


        $fullName = (\array_key_exists('fullName', $data)) ? $data['fullName'] : null;
        $password = (\array_key_exists('password', $data)) ? $data['password'] : "";


        if (empty($fullName)   || empty($token)  || empty($password)) {
            return new JsonResponse(['message' => sprintf('Password, fullName are not empty')], 400);
        }

        $password = (\array_key_exists('password', $data)) ? $data['password'] : null;
        $adminUser->emailVerificationToken = "";
        $adminUser->fullName = $fullName;
        $adminUser->enabled = true;
        $adminUser->blocked = false;
        $adminUser->userStatus = UserStatusEnum::ACTIVE;

        $adminUser = $adminUserHandlers->updatePassword($password, $adminUser);
        $adminUserHandlers->save($adminUser);

        $adminUserHandlers->insertAudit("update", "Register Completed",$request->getClientIp(), $adminUser);

        return new JsonResponse(['accessToken' => $JWTManager->create($adminUser)], 200);
    }

    /**
     * @param Request $request
     * @param AdminUserHandlers $adminUserHandlers
     * @return JsonResponse
     * @Route("/admin/reset/password", methods="POST", name="admin_reset_password")
     */
    public function resetPassword(Request $request, AdminUserHandlers $adminUserHandlers)
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $email = (\array_key_exists('email', $data)) ? $data['email'] : null;
        if (empty($email)) {
            return new JsonResponse(['message' => sprintf('Invalid Request')], 400);
        }

        /** @var AdminUser $adminUser */
        $adminUser = $adminUserHandlers->repo->findOneBy(["email" => $email]);

        if(empty($adminUser)){
            return new JsonResponse(['message' => sprintf('Invalid email')], 400);
        }

        $adminUser = $adminUserHandlers->setResetOTP($adminUser);
        $adminUserHandlers->sendResetPassWordEmail($adminUser);
        $adminUserHandlers->save($adminUser);

        $adminUserHandlers->insertAudit("update", "Reset Password Requested",$request->getClientIp(), $adminUser);
        return new JsonResponse(['message' => "email sent"], 200);
    }

    /**
     * @param Request $request
     * @param AdminUserHandlers $adminUserHandlers
     * @return JsonResponse
     * @Route("/admin/update/password", methods="POST", name="admin_update_password")
     */
    public function updatePassword(Request $request, AdminUserHandlers $adminUserHandlers)
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

        /** @var AdminUser $adminUser */
        $adminUser = $adminUserHandlers->repo->findOneBy(["otp" => $otp, "email" => $email]);
        if(empty($adminUser)){
            return new JsonResponse(['message' => sprintf('Invalid OTP')], 400);
        }

        $adminUser = $adminUserHandlers->updatePassword($password, $adminUser);
        $adminUser->otp = "";

        $adminUserHandlers->save($adminUser);

        if($adminUser) {
            $adminUserHandlers->insertAudit("update", "Password Updated",$request->getClientIp(), $adminUser);
            // TODO send Reset Password confirm Email
            return $this->json(["message" => "password updated successfully!"]);
        } else{
            return new JsonResponse(['message' => sprintf('Request Failed')], 400);
        }
    }

    /**
     * @Route("/session/invalidate", methods="POST", name="admin_user_session_invalidate")
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param AdminUserHandlers $adminUserHandlers
     * @return JsonResponse
     */
    public function logOutFromDevice(Request $request, TokenStorageInterface $tokenStorage, AdminUserHandlers $adminUserHandlers)
    {
        /** @var AdminUser $user */
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
            $adminUserHandlers->save($user);
        }
        return new JsonResponse(["message" => "logout successfully!"], 200);
    }

    /**
     * @param Request $request
     * @param AdminUserHandlers $adminUserHandlers
     * @return JsonResponse
     * @Route("/admin/change/email", methods="POST", name="admin_change_email")
     */
    public function changeEmail(Request $request, AdminUserHandlers $adminUserHandlers, UserPasswordEncoderInterface $encoder)
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        /** @var AdminUser $user */
        $user = $this->getUser();

        $email = (\array_key_exists('email', $data)) ? $data['email'] : null;
        $password = (\array_key_exists('password', $data)) ? $data['password'] : null;

        if (empty($email)) {
            return new JsonResponse(['message' => sprintf('email is empty')], 400);
        }
        if (empty($password)) {
            return new JsonResponse(['message' => sprintf('password is empty')], 400);
        }

        $userExist = $adminUserHandlers->checkUserExistByEmail($email);

        if (!empty($userExist)) {
            return new JsonResponse(['message' => sprintf("User Already Exist With email!")], 400);

        }

        $adminPassword = $encoder->isPasswordValid($user, $password);

        if($adminPassword == false){
            return new JsonResponse(['message' => sprintf("Password is incorrect!")], 400);
        }

        $adminUser = $adminUserHandlers->setResetOTP($user);

        $adminUserHandlers->sendResetEmailEmail($adminUser);
        $adminUserHandlers->save($adminUser);

        $adminUserHandlers->insertAudit("update", "Reset Email Requested",$request->getClientIp(), $adminUser);
        return new JsonResponse(['message' => "email sent"], 200);
    }

    /**
     * @param Request $request
     * @param AdminUserHandlers $adminUserHandlers
     * @return JsonResponse
     * @Route("/admin/update/email", methods="POST", name="admin_update_email")
     */
    public function updateEmail(Request $request, AdminUserHandlers $adminUserHandlers)
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

        /** @var AdminUser $adminUser */
        $adminUser = $adminUserHandlers->repo->findOneBy(["otp" => $otp]);
        if(empty($adminUser)){
            return new JsonResponse(['message' => sprintf('Invalid OTP')], 400);
        }

        $adminUser->email = $email;
        $adminUser->otp = "";

        $adminUserHandlers->save($adminUser);

        if($adminUser) {
            $adminUserHandlers->insertAudit("update", "Email Updated",$request->getClientIp(), $adminUser);
            // TODO send Reset Password confirm Email
            return $this->json(["message" => "email updated successfully!"]);
        } else{
            return new JsonResponse(['message' => sprintf('Request Failed')], 400);
        }
    }

    /**
     * @param Request $request
     * @param AdminUserHandlers $adminUserHandlers
     * @return JsonResponse|Response
     * @Route("/admin/re-invite", methods="POST")
     */
    public function reInvite(Request $request, AdminUserHandlers $adminUserHandlers)
    {
        // get Data From Request
        $data = json_decode($request->getContent(), true);
        if (empty($data)) {
            return new JsonResponse(['message' => sprintf('Empty Request')], 400);
        }

        $userId = $data['user_id'];

        /** @var AdminUser $adminUser */
        $subUser = $adminUserHandlers->repo->findOneBy(["id" => $userId]);
        $subUser->password = null;
        $subUser = $adminUserHandlers->setEmailVerificationToken($subUser);
        $adminUserHandlers->save($subUser);
        $adminUserHandlers->sendEmailVerification($subUser);

        return new JsonResponse(['message' => 'SubUser re-invited successfully'], 201);
    }


}
