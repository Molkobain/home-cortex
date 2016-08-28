<?php

namespace Molkobain\Silex\App;

class Configuration {

    static protected $aParams = array(
    );

    static public function Get($sParamName) {
        if (array_key_exists($sParamName, static::$aParams)) {
            return static::$aParams[$sParamName];
        } else {
            throw new Exception('Unknown configuration parameter "' . $sParamName . '"');
        }
    }

}

?>
