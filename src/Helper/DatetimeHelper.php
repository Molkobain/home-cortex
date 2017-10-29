<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Helper;

use DateInterval;
use DateTime;
use DateTimeZone;

/**
 * DatetimeHelper
 *
 * @author Guillaume Lajarige <lajarige.guillaume@free.fr>
 */
class DatetimeHelper {

    /**
     * Converts a timestamp into a DateTime object
     *
     * @param integer $iTimestamp The timestamp to convert into a DateTime
     * @param boolean $bIsDelta Is the timestamp a real datetime or just a delta to add to another dattime ?
     * @param boolean $bToLocaleTimezone Should the timestamp be localized to the server timezone ?
     * @return \DateTime
     */
    public static function makeDatetimeFromTimestamp($iTimestamp, $bIsDelta = false, $sTimezoneToApply = null) {
        // Creating datetime as of now
        $oNewDatetime = new DateTime();

        // Applying timezone
        if ($sTimezoneToApply !== null) {
            $oNewDatetime->setTimezone(new DateTimeZone($sTimezoneToApply));
        }

        if ($bIsDelta) {
            // Moving datetime back to midnight if timestamp is a delta
            $oNewDatetime->setTimestamp(strtotime('midnight', time()));
            // Converting timestamp to dateinterval
            $oTodayDateInterval = new DateInterval('PT' . $iTimestamp . 'S');
            // Adding it to the datetime
            $oNewDatetime->add($oTodayDateInterval);
        } else {
            $oNewDatetime->setTimestamp($iTimestamp);
        }

        return $oNewDatetime;
    }

}

?>