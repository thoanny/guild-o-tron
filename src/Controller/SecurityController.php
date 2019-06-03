<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountFormType;
use App\Utils\Gw2Api;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
      throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    /**
     * @Route("/forgotten-password", name="app_forgotten_password")
     */
    public function forgottenPassword(
        Request $request,
        UserPasswordEncoderInterface $encoder,
        \Swift_Mailer $mailer,
        TokenGeneratorInterface $tokenGenerator
    ): Response
    {

        if ($request->isMethod('POST')) {

            $email = $request->request->get('email');

            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneByEmail($email);

            if ($user === null) {
              // $this->addFlash('danger', 'Email Inconnu');
              return $this->redirectToRoute('homepage');
            }

            $token = $tokenGenerator->generateToken();

            try{
                $user->setResetToken($token);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('homepage');
            }

            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            $message = (new \Swift_Message('Forgot password'))
                ->setFrom('hello@anthony-destenay.fr')
                ->setTo($user->getEmail())
                ->setBody(
                    "Reset your password: " . $url,
                    'text/html'
                );

            $mailer->send($message);

            $this->addFlash('success', 'flash.forgottenpassword.email.sent');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('security/forgotten_password.html.twig');
    }

    /**
     * @Route("/reset-password/{token}", name="app_reset_password")
     */
    public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
    {

        if ($request->isMethod('POST')) {
            $entityManager = $this->getDoctrine()->getManager();

            $user = $entityManager->getRepository(User::class)->findOneByResetToken($token);
            /* @var $user User */

            if ($user === null) {
                $this->addFlash('danger', 'flash.forgottenpassword.token.notfound');
                return $this->redirectToRoute('homepage');
            }

            $user->setResetToken(null);
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $entityManager->flush();

            $this->addFlash('success', 'flash.forgottenpassword.password.updated');

            return $this->redirectToRoute('app_login');
        }else {

            return $this->render('security/reset_password.html.twig', ['token' => $token]);
        }

    }

    /**
     * @Route("/account", name="app_account")
     * @IsGranted("ROLE_USER")
     */

    public function account(Request $request) {

      $user = $this->getUser();
      $form = $this->createForm(AccountFormType::class, $user);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {

        $api = new Gw2Api();
        $account = $api->get('/account', $form->get('api_key')->getData());

        if(!$account) {
          $this->addFlash('danger', 'flash.api.nodata');
          return $this->redirectToRoute('app_account');
        }

        $entityManager = $this->getDoctrine()->getManager();
        // $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_account');
      }

      return $this->render('security/account.html.twig', [
          'form' => $form->createView(),
      ]);
    }

}
