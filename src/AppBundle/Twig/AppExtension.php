<?php

namespace AppBundle\Twig;

/**
 * Description of AppExtension
 *
 * @author Eric TONYE
 */
class AppExtension extends \Twig_Extension {
    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('site_type', function($value) {
                        switch ($value) {
                            case 1:
                                return "End";
                            case 2:
                                return "Nodal";
                        }
                    }),
        );
    }

    public function getName() {
        return 'app_extension';
    }

}
