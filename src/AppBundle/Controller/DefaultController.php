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
        $maxResults = 12;
        $route_param_page = array();
        $route_param_search_query = array();
        $search_query = null;
        $placeholder = "Find a site...";
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
        $start = 1;
        $end = 1;
        if ($total_pages > 1) {
            $start = 1;
            $end = $total_pages;
            if ($total_pages > 5 && $page > 3) {
                $end = $page + 2 < $total_pages ? $page + 2 : $total_pages;
                $start = $end - 4 > 1 ? $end - 4 : 1;
            } elseif ($page > 5) {
                $end = 5;
            }
        }
        return $this->render('sites/index.html.twig', array(
                    'sites' => $sites,
                    'total_pages' => $total_pages,
                    'start' => $start,
                    'end' => $end,
                    'page' => $page,
                    'form' => $form->createView(),
                    'route_param_page' => $route_param_page,
                    'route_param_search_query' => $route_param_search_query,
                    'search_query' => $search_query,
                    'placeholder' => $placeholder
        ));
    }

    /**
     * @Rest\View()
     * @Rest\Get("/sites/list-for-map" , name="sites_list_for_map", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function sitesListForMapAction(Request $request) {
        $site = new Site();
        $em = $this->getDoctrine()->getManager();
        $json = array();
        $sites = $em->getRepository('AppBundle:Site')->getAll(null, null, null);
        $view = View::create(['sites' => $sites]);
        $view->setFormat('json');
        return $view;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Delete("/sites/{id}", name="site_delete", options={ "method_prefix" = false, "expose" = true  })
     */
    public function removeSiteAction(Site $site) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        if (empty($site)) {
            return new JsonResponse(['message' => 'Site not found'], Response::HTTP_NOT_FOUND);
        }
        $repositorySite = $this->getDoctrine()->getManager()->getRepository('AppBundle:Site');
        if ($site) {
            if ($site->getSiteType() == 1) {
                $nodalSite = $site->getNodalSite();
                $nodalSite->removeEndSite($site);
                $repositorySite->updateSite($nodalSite);
            } else {
                $endSites = $site->getEndSites();
                foreach ($endSites as $endSite) {
                    $endSite->setNodalSite(null);
                    $repositorySite->updateSite($endSite);
                }
            }
            $repositorySite->deleteSite($site);
            $view = View::create(["message" => 'Site successfully deleted']);
            $view->setFormat('json');
            return $view;
            //return new JsonResponse(["message" => 'Site supprimée avec succès'], Response::HTTP_OK);
        } else {
            return new JsonResponse(["message" => 'Site not found'], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Rest\View()
     * @Rest\Get("/sites/{id}" , name="site_get_one", options={ "method_prefix" = false, "expose" = true })
     */
    public function getSiteByIdAction(Site $site) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        if (empty($site)) {
            return new JsonResponse(['message' => 'Site not found'], Response::HTTP_NOT_FOUND);
        }
        $form = $this->createForm('AppBundle\Form\SiteType', $site, array('method' => 'PUT'));
        $site_details = $this->renderView('sites/show.html.twig', array(
            'site' => $site,
            'form' => $form->createView()
        ));
        $view = View::create(['site_details' => $site_details]);
        $view->setFormat('json');
        return $view;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/sites/{id}/assign-node" , name="assign_node_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getAssignNodeAction(Site $site) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        if (empty($site)) {
            return new JsonResponse(['message' => 'Site not found'], Response::HTTP_NOT_FOUND);
        }
        $nodeList = $this->getSitesNearBy($site);
        $assign_node_form = $this->renderView('sites/assign_node.html.twig', array(
            'site' => $site,
            'nodeList' => $nodeList
        ));
        $view = View::create(['assign_node_form' => $assign_node_form]);
        $view->setFormat('json');
        return $view;
    }

    /**
     * @Rest\View()
     * @Rest\Put("/sites/{id}/assign-node" , name="assign_node_put", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function putAssignNodeAction(Request $request, Site $site) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        if (empty($site)) {
            return new JsonResponse(['message' => 'Site not found'], Response::HTTP_NOT_FOUND);
        }
        $repositorySite = $this->getDoctrine()->getManager()->getRepository('AppBundle:Site');
        $nodalSite = new Site();
        if ($request->isMethod("PUT")) {
            $node = intval($request->get("node"));
            $nodalSite = $repositorySite->find($node);
            $site = $this->assignAzimutFrequenceAndPolarization($site, $nodalSite);
            $site->setNodalSite($nodalSite);
            $nodalSite->addEndSite($site);

            $nodalSite = $repositorySite->updateSite($nodalSite);
            $site = $repositorySite->updateSite($site);

            $view = View::create(["message" => 'Node successfully assigned',]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["message" => 'An error occured during node assigning!'], Response::HTTP_NOT_FOUND);
        }
    }

    //get list of three nearby site of a site reference
    public function getSitesNearBy(Site $siteRef) {
        $repositorySite = $this->getDoctrine()->getManager()->getRepository('AppBundle:Site');
        $listeSiteNodaux = $repositorySite->findBy(array("siteType" => 2, "status" => 1));
        if (count($listeSiteNodaux) >= 3) {
            $dist_tmp1 = $this->Distance($siteRef, $listeSiteNodaux[0]);
            $dist_tmp2 = $this->Distance($siteRef, $listeSiteNodaux[1]);
            $dist_tmp3 = $this->Distance($siteRef, $listeSiteNodaux[2]);

            if ($dist_tmp1 < $dist_tmp2) {
                if ($dist_tmp1 < $dist_tmp3) {
                    if ($dist_tmp2 < $dist_tmp3) {
                        $d1 = $dist_tmp1;
                        $j1 = 0;
                        $d2 = $dist_tmp2;
                        $j2 = 1;
                        $d3 = $dist_tmp3;
                        $j3 = 2;
                    } else {
                        $d1 = $dist_tmp1;
                        $j1 = 0;
                        $d2 = $dist_tmp3;
                        $j2 = 2;
                        $d3 = $dist_tmp2;
                        $j3 = 1;
                    }
                } else {
                    $d1 = $dist_tmp3;
                    $j1 = 2;
                    $d2 = $dist_tmp1;
                    $j2 = 0;
                    $d3 = $dist_tmp2;
                    $j3 = 1;
                }
            } else {
                if ($dist_tmp1 > $dist_tmp3) {
                    if ($dist_tmp2 > $dist_tmp3) {
                        $d1 = $dist_tmp3;
                        $j1 = 2;
                        $d2 = $dist_tmp2;
                        $j2 = 1;
                        $d3 = $dist_tmp1;
                        $j3 = 0;
                    } else {
                        $d1 = $dist_tmp2;
                        $j1 = 1;
                        $d2 = $dist_tmp3;
                        $j2 = 2;
                        $d3 = $dist_tmp1;
                        $j3 = 0;
                    }
                } else {
                    $d1 = $dist_tmp2;
                    $j1 = 1;
                    $d2 = $dist_tmp1;
                    $j2 = 0;
                    $d3 = $dist_tmp3;
                    $j3 = 2;
                }
            }
            if (count($listeSiteNodaux) > 3) {
                for ($i = 3; $i < count($listeSiteNodaux); $i++) {
                    $d = $this->Distance($siteRef, $listeSiteNodaux[0]);
                    $j = $i;
                    if ($d < $d1) {
                        $d3 = $d2;
                        $d2 = $d1;
                        $d1 = $d;
                        $j3 = $j2;
                        $j2 = $j1;
                        $j1 = $j;
                    } else {
                        if ($d < $d2) {
                            $d3 = $d2;
                            $d2 = $d;
                            $j3 = $j2;
                            $j2 = $j;
                        } else {
                            if ($d < $d3) {
                                $d3 = $d;
                                $j3 = $j;
                            }
                        }
                    }
                }
            }
            $return_array = array(array("dist" => round($d1 / 1000, 3), "site" => $listeSiteNodaux[$j1]), array("dist" => round($d2 / 1000, 3), "site" => $listeSiteNodaux[$j2]), array("dist" => round($d3 / 1000, 3), "site" => $listeSiteNodaux[$j3]));
        } else {
            $return_array = array();
            foreach ($listeSiteNodaux as $nodalSite) {
                $return_array[] = array("dist" => round($this->Distance($siteRef, $nodalSite) / 1000, 3), "site" => $nodalSite);
            }
        }
        return $return_array;
    }

    //calculate distance between two points with lat and long
    public function Distance(Site $siteA, Site $siteB) {
        $R = 6378000; //Rayon de la terre en mètre
        $lat_a = deg2rad($siteA->getLatitude());
        $long_a = deg2rad($siteA->getLongitude());
        $lat_b = deg2rad($siteB->getLatitude());
        $long_b = deg2rad($siteB->getLongitude());

        return $R * acos(sin($lat_a) * sin($lat_b) + cos($long_b - $long_a) * cos($lat_b) * cos($lat_a));
    }

    //calculate azimut between two points with lat and long
    public function azimut(Site $site1, Site $site2) {
        $X1 = deg2rad($site1->getLatitude());
        $X2 = deg2rad($site2->getLatitude());
        $Y = deg2rad($site1->getLongitude() - $site2->getLongitude());
        $dX = cos($X1) * sin($X2) - sin($X1) * cos($X2) * cos($Y);
        $dY = cos($X2) * sin($Y);
        return round(rad2deg(atan2($dX, $dY)), 2);
    }

    //Assign Azimut, Frequence and Polarization to the end site
    public function assignAzimutFrequenceAndPolarization(Site $endSite, Site $nodalSite) {
        $otherEndSites = $nodalSite->getEndSites();
        if ($otherEndSites->contains($endSite)) {
            $otherEndSites->removeElement($endSite);
        }
        $azimut = $this->azimut($endSite, $nodalSite);
        $endSite->setAzimut($azimut);
        if (count($otherEndSites) == 0) {
            $endSite->setFrequenceNumber("F1");
            $endSite->setPolarisation("V");
        } else {
            $azimutDifferencesF1 = array();
            $azimutDifferencesF2 = array();
            $azimutDifferencesF3 = array();
            $azimutDifferencesF4 = array();
            //Calcule des tableaux des différences d'azimut entre le nouveau end site et les autres end site du nodal site.
            //regroupés par les 4 fréquences
            foreach ($otherEndSites as $otherEndSite) {
                if ($otherEndSite->getFrequenceNumber() == "F1") {
                    $azimutDifferencesF1[] = abs($otherEndSite->setAzimut() - $azimut);
                }
                if ($otherEndSite->getFrequenceNumber() == "F2") {
                    $azimutDifferencesF2[] = abs($otherEndSite->setAzimut() - $azimut);
                }
                if ($otherEndSite->getFrequenceNumber() == "F3") {
                    $azimutDifferencesF3[] = abs($otherEndSite->setAzimut() - $azimut);
                }
                if ($otherEndSite->getFrequenceNumber() == "F4") {
                    $azimutDifferencesF4[] = abs($otherEndSite->setAzimut() - $azimut);
                }
            }
            $azimutDifferences = array($azimutDifferencesF1, $azimutDifferencesF2, $azimutDifferencesF3, $azimutDifferencesF4);
            $i = 0;
            while ($i < 4) {
                $azimutDifferencesFreq = $azimutDifferences[$i];
                $freq = "F" . $i + 1;
                //Utilisation du tableaux des sites de frequence Fi
                if (count($azimutDifferencesF1) > 0) {
                    $nDiffAzimutInf45 = 0;
                    $nDiffAzimutIn45_90 = 0;
                    $nDiffAzimutSup90 = 0;
                    $endSite->setFrequenceNumber($freq);
                    $endSite->setPolarisation("V");
                    foreach ($azimutDifferencesFreq as $azimutDiff) {
                        if ($azimutDiff < 45) {
                            $nDiffAzimutInf45++;
                        } elseif ($azimutDiff >= 45 && $azimutDiff <= 90) {
                            $nDiffAzimutIn45_90++;
                        } elseif ($azimutDiff > 90) {
                            $nDiffAzimutSup90++;
                        }
                        if ($nDiffAzimutInf45 == 1) {
                            break;
                        }
                        if ($nDiffAzimutIn45_90 >= 2) {
                            break;
                        }
                    }
                    if ($nDiffAzimutIn45_90 == 1) {
                        $endSite->setFrequenceNumber($freq);
                        $endSite->setPolarisation("H");
                        return $endSite;
                    } elseif ($nDiffAzimutSup90 == count($azimutDifferencesFreq)) {
                        $endSite->setFrequenceNumber($freq);
                        $endSite->setPolarisation("V");
                        return $endSite;
                    }
                }
                $i++;
            }
        }
        return $endSite;
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
                return new JsonResponse(["success" => false, 'message' => 'A site with this t-number already exist'], Response::HTTP_BAD_REQUEST);
            }
            //***************gestion des equipements du site ************************** */
            $equipments = $site->getEquipments();
            foreach ($equipments as $equipment) {
                $equipment->setSite($site);
            }
            $site->setSiteType(intval($request->get('site-type')));

            $site = $repositorySite->saveSite($site);

            $view = View::create(["message" => 'Site successfully added']);
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
            return new JsonResponse(['message' => 'Site not found'], Response::HTTP_NOT_FOUND);
        }

        foreach ($site->getEquipments() as $equipment) {
            $originalEquipments->add($equipment);
        }
        $oldSiteType = $site->getSiteType();

        $form = $this->createForm('AppBundle\Form\SiteType', $site, array('method' => 'PUT'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $siteUnique = $repositorySite->findOneBy(array('tNumber' => $site->getTNumber(), 'status' => 1));
            if ($siteUnique && $siteUnique->getId() != $site->getId()) {
                return new JsonResponse(["success" => false, 'message' => 'A site with this t-number already exist'], Response::HTTP_BAD_REQUEST);
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

            if ($site->getSiteType() !== $oldSiteType) {
                if ($oldSiteType == 1) {
                    $nodalSite = $site->getNodalSite();
                    $nodalSite->removeEndSite($site);
                    $repositorySite->updateSite($nodalSite);
                    $site->setNodalSite(null);
                } else {
                    $endSites = $site->getEndSites();
                    foreach ($endSites as $endSite) {
                        $endSite->setNodalSite(null);
                        $site->removeEndSite($endSite);
                        $repositorySite->updateSite($endSite);
                    }
                }
            }
            $site = $repositorySite->updateSite($site);

            $view = View::create(["message" => 'Site successfully modified']);
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
