<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Helper;

use Exception;

/**
 * Contains static methods to manipulate strings.
 *
 * @author Guillaume Lajarige <lajarige.guillaume@free.fr>
 */
class StringHelper {

    /**
     * Format $sValue to camel case. If $bTrim is false, then spaces and other characters like '_' / '-' / etc will be preserved.
     *
     * @param string $sValue String to format
     * @param boolean $bTrim Remove none alpha-numeric characters
     * @return string
     */
    public static function toCamelCase($sValue, $bTrim = true) {
        $sFormatString = $sValue;
        if (!$bTrim) {
            $sFormatString = ucwords(strtolower($sValue));
        } else {
            // TODO : Not implemented
            throw new Exception('StringHelper : toCamelCase with $bTrim = true not implemented yet!');
        }

        return $sFormatString;
    }

}

?>