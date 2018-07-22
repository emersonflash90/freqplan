<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Site;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller {

    /**
     * @Rest\View()
     * @Rest\Get("/" , name="homepage", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function indexAction(Request $request) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        // replace this example code with whatever you need
        return $this->redirectToRoute("sites_home");
    }

    /**
     * @Rest\View()
     * @Rest\Get("/sites" , name="sites_home", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function sitesAction(Request $request) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $site = new Site();
        $em = $this->getDoctrine()->getManager();
        $page = 1;
        $maxResults = 8;
        $route_param_page = array();
        $route_param_search_query = array();
        $search_query = null;
        $placeholder = "Rechercher une site...";
        if ($request->get('page')) {
            $page = intval(htmlspecialchars(trim($request->get('page'))));
            $route_param_page['page'] = $page;
        }
        if ($request->get('search_query')) {
            $search_query = htmlspecialchars(trim($request->get('search_query')));
            $route_param_search_query['search_query'] = $search_query;
        }
        $start_from = ($page - 1) * $maxResults >= 0 ? ($page - 1) * $maxResults : 0;
        $total_pages = ceil(count($em->getRepository('AppBundle:Site')->getAllByString($search_query)) / $maxResults);
        $form = $this->createForm('AppBundle\Form\SiteType', $site);
        $sites = $em->getRepository('AppBundle:Site')->getAll($start_from, $maxResults, $search_query);
        // replace this example code with whatever you need
        return $this->render('sites/index.html.twig', array(
                    'sites' => $sites,
                    'total_pages' => $total_pages,
                    'page' => $page,
                    'form' => $form->createView(),
                    'route_param_page' => $route_param_page,
                    'route_param_search_query' => $route_param_search_query,
                    'search_query' => $search_query,
                    'placeholder' => $placeholder
        ));
    }
    
    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Delete("/sites/{id}", name="site_delete", options={ "method_prefix" = false, "expose" = true  })
     */
    public function removeSiteAction(Site $site) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $repositorySite = $this->getDoctrine()->getManager()->getRepository('AppBundle:Site');
        if ($site) {
            $repositorySite->deleteSite($site);
            $view = View::create(["message" => 'Site supprimée avec succès']);
            $view->setFormat('json');
            return $view;
            //return new JsonResponse(["message" => 'Site supprimée avec succès'], Response::HTTP_OK);
        } else {
            return new JsonResponse(["message" => 'Site introuvable'], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/sites", name="site_add", options={ "method_prefix" = false, "expose" = true })
     */
    public function postSiteAction(Request $request) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $site = new Site();
        $repositorySite = $this->getDoctrine()->getManager()->getRepository('AppBundle:Site');
        $form = $this->createForm('AppBundle\Form\SiteType', $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($repositorySite->findOneBy(array('tNumber' => $site->getTNumber(), 'status' => 1))) {
                return new JsonResponse(["success" => false, 'message' => 'Une site avec ce t-number existe dejà'], Response::HTTP_BAD_REQUEST);
            }
            //***************gestion des equipements du site ************************** */
            $equipments = $site->getEquipments();
            foreach ($equipments as $equipment) {
                $equipment->setSite($site);
            }
            $site->setSiteType(intval($request->get('site-type')));
            
            $site = $repositorySite->saveSite($site);
            
            $view = View::create(["message" => 'Site ajouté avec succès']);
            $view->setFormat('json');
            return $view;
        } else {
            $view = View::create($form);
            $view->setFormat('json');
            return $view;
        }
    }
    
    
    /**
     * @Rest\View()
     * @Rest\Put("/sites/{id}", name="site_update", options={ "method_prefix" = false, "expose" = true  })
     * @param Request $request
     */
    public function putSiteAction(Request $request, Site $site) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        return $this->updateSiteAction($request, $site);
    }

    public function updateSiteAction(Request $request, Site $site) {
        $repositorySite = $this->getDoctrine()->getManager()->getRepository('AppBundle:Site');
        $repositoryEquipment = $this->getDoctrine()->getManager()->getRepository('AppBundle:Equipment');
        $originalEquipments = new \Doctrine\Common\Collections\ArrayCollection();
        if (empty($site)) {
            return new JsonResponse(['message' => 'Site introuvable'], Response::HTTP_NOT_FOUND);
        }
        
        foreach ($site->getEquipments() as $equipment) {
            $originalEquipments->add($equipment);
        }
        

        $form = $this->createForm('AppBundle\Form\SiteType', $site, array('method' => 'PUT'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $siteUnique = $repositorySite->findOneBy(array('tNumber' => $site->getTNumber(), 'status' => 1));
            if ($siteUnique && $siteUnique->getId() != $site->getId()) {
                return new JsonResponse(["success" => false, 'message' => 'Une site avec ce t-number existe dejà'], Response::HTTP_BAD_REQUEST);
            }
            //***************gestion des abonnés de l'site ************************** */
            // remove the relationship between the Site and the Equipments
            foreach ($originalEquipments as $equipment) {
                if (false === $site->getEquipments()->contains($equipment)) {
                    // remove the site from the equipment
                    $site->getEquipments()->removeElement($equipment);
                    // if it was a many-to-one relationship, remove the relationship like this
                    $equipment->setSite(null);
                    $equipment->setStatus(0);
                    $repositoryEquipment->updateEquipment($equipment);
                }
            }
            $equipments = $site->getEquipments();
            foreach ($equipments as $equipment) {
                $equipment->setSite($site);
            }
            $site->setSiteType(intval($request->get('site-type')));
            
            $site = $repositorySite->updateSite($site);
            
            $view = View::create(["message" => 'Site modifié avec succès',]);
            $view->setFormat('json');
            return $view;
        } else {
            $edit_site_form = $this->renderView('sites/edit.html.twig', array('form' => $form->createView(), 'site' => $site));
            $view = View::create(['edit_site_form' => $edit_site_form]);
            $view->setFormat('json');
            return $view;
        }
    }
    
    
    
    
    

    /**
     * @Rest\View()
     * @Rest\Get("/map" , name="map_home", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function mapAction(Request $request) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        // replace this example code with whatever you need
        return $this->render('map/index.html.twig', array(
        ));
    }

    /**
     * @Rest\View()
     * @Rest\Get("/links" , name="links_home", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function linksAction(Request $request) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        // replace this example code with whatever you need
        return $this->render('links/index.html.twig', array(
        ));
    }

    /**
     * @Rest\View()
     * @Rest\Get("/capex" , name="capex_home", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function capexAction(Request $request) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        // replace this example code with whatever you need
        return $this->render('capex/index.html.twig', array(
        ));
    }

    /**
     * @Rest\View()
     * @Rest\Get("/opex" , name="opex_home", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function opexAction(Request $request) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        // replace this example code with whatever you need
        return $this->render('opex/index.html.twig', array(
        ));
    }

    /**
     * @Rest\View()
     * @Rest\Get("/interferences" , name="interferences_home", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function interferencesAction(Request $request) {
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
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('sites_home'));
        }
        $user = new User();
        $form = $this->createForm('AppBundle\Form\RegistrationType', $user);
        return $this->render('Registration/register.html.twig', array(
                    'form' => $form->createView(),
                    'user' => $user,
        ));
    }
    
    /**
     * @Rest\View()
     * @Rest\Get("/view-profile", name="view_profile", options={ "method_prefix" = false, "expose" = true })
     */
    public function viewProfileAction(Request $request) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('sites_home'));
        }
        return $this->render('Registration/profile.html.twig', array(
                    'user' => $this->getUser(),
        ));
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/register-new-user", name="register_new_user_post", options={ "method_prefix" = false, "expose" = true })
     */
    public function registerPostAction(Request $request) {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('sites_home'));
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
