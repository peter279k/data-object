<?php

/*
 * Data Object
 * https://github.com/ivopetkov/data-object
 * Copyright (c) 2016-2017 Ivo Petkov
 * Free to use under the MIT license.
 */

namespace IvoPetkov;

/**
 * 
 */
trait DataObjectToArrayTrait
{

    /**
     * Returns the object data converted as an array
     * 
     * @return array The object data converted as an array
     */
    public function toArray(): array
    {

        $toArray = function($object) use (&$toArray) {
            $result = [];
            $vars = get_object_vars($object);
            foreach ($vars as $name => $value) {
                if ($name !== 'internalDataObjectData') {
                    $reflectionProperty = new \ReflectionProperty($object, $name);
                    if ($reflectionProperty->isPublic()) {
                        $result[$name] = null;
                    }
                }
            }
            if (isset($object->internalDataObjectData)) {
                foreach ($object->internalDataObjectData as $name => $value) {
                    $result[substr($name, 1)] = null;
                }
            }
            ksort($result);
            foreach ($result as $name => $null) {
                $value = $object->$name;
                if (is_object($value)) {
                    if (method_exists($value, 'toArray')) {
                        $result[$name] = $value->toArray();
                    } else {
                        $propertyVars = $toArray($value);
                        foreach ($propertyVars as $propertyVarName => $propertyVarValue) {
                            if (is_object($propertyVarValue)) {
                                $propertyVars[$propertyVarName] = $toArray($propertyVarValue);
                            }
                        }
                        $result[$name] = $propertyVars;
                    }
                } else {
                    $result[$name] = $value;
                }
            }
            return $result;
        };
        return $toArray($this);
    }

}
