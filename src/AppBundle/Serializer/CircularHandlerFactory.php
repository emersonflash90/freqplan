<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Serializer;

/**
 * Description of CircularHandlerFactory
 *
 * @author Eric TONYE
 */
class CircularHandlerFactory {
    /**
     * @return \Closure
     */
    public static function getId()
    {
        return function ($object) {
            return $object->getId();
        };
    }
}
