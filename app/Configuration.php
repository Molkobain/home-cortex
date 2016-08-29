<?php

namespace Molkobain\Silex\App;

/**
 * Note : This is not used yet.
 */
class Configuration {

    static protected $aParams = array(
    );

    static public function getAll() {
        return static::$aParams;
    }

    static public function get($sParamName) {
        if (array_key_exists($sParamName, static::$aParams)) {
            return static::$aParams[$sParamName];
        } else {
            throw new Exception('Unknown configuration parameter "' . $sParamName . '"');
        }
    }

}

?>
