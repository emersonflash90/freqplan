<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        // replace this example code with whatever you need
       return $this->redirectToRoute("map_home");
    }
    
    
    /**
     * @Route("/sites", name="sites_home")
     */
    public function sitesAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        // replace this example code with whatever you need
       return $this->render('sites/index.html.twig', array(
        ));
    }
    
    /**
     * @Route("/map", name="map_home")
     */
    public function mapAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        // replace this example code with whatever you need
       return $this->render('map/index.html.twig', array(
        ));
    }
    
    /**
     * @Route("/links", name="links_home")
     */
    public function linksAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        // replace this example code with whatever you need
       return $this->render('links/index.html.twig', array(
        ));
    }
    
    /**
     * @Route("/capex", name="capex_home")
     */
    public function capexAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        // replace this example code with whatever you need
       return $this->render('capex/index.html.twig', array(
        ));
    }
    
    /**
     * @Route("/opex", name="opex_home")
     */
    public function opexAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        // replace this example code with whatever you need
       return $this->render('opex/index.html.twig', array(
        ));
    }
    
    /**
     * @Route("/interferences", name="interferences_home")
     */
    public function interferencesAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        // replace this example code with whatever you need
       return $this->render('interferences/index.html.twig', array(
        ));
    }
    
    /**
     * @Rest\View()
     * @Rest\Get("/register-new-user", name="register_new_user_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function registerGetAction(Request $request) {
        $user = new User();
        $form = $this->createForm('AppBundle\Form\RegistrationType', $user);
        return $this->render('Registration/register.html.twig', array(
                    'form' => $form->createView(),
                    'user' => $user,
        ));
    }
    
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/register-new-user", name="register_new_user_post", options={ "method_prefix" = false, "expose" = true })
     */
    public function registerPostAction(Request $request)
    {   
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();
        $user = $userManager->createUser();
        $user->setEnabled(true);
        $form = $this->createForm('AppBundle\Form\RegistrationType', $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->addRole("ROLE_USER");
            if ($userManager->findUserByEmail($user->getEmail())) {
                $session->getFlashBag()->add('error', "Un utilisateur avec cette adresse email existe dejà!");
//                return $this->redirect($this->generateUrl('register_new_user'));
            }
            if ($userManager->findUserByUsername($user->getUsername())) {
                $session->getFlashBag()->add('error', "Un utilisateur avec ce nom d'utilisateur existe dejà!");
//                return $this->redirect($this->generateUrl('register_new_user'));
            }
            $userManager->updateUser($user);
//            $mail_service->sendWelcomeNewUserEmail($user);
            $session->getFlashBag()->add('success', "Votre compte a été créer avec succès.");
//            return $this->redirect($this->generateUrl('register_new_user'));
        } else {
            $session->getFlashBag()->add('error', "Le formulaire a été soumis avec des données erronées");
//            return $this->redirect($this->generateUrl('register_new_user'));
        }
        return $this->redirect($this->generateUrl('register_new_user'));
    }
}
