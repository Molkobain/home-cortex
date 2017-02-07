<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Helper\Calendar;

use Exception;
use Google_Client;
use Google_Service_Calendar;

/**
 * GoogleCalendarAPIHelper is based on https://developers.google.com/google-apps/calendar/overview APIs
 * It allows to retrieve informations on a Google user account's calendars
 *
 * More informations on https://developers.google.com/google-apps/calendar/overview
 *
 * @author Guillaume Lajarige <lajarige.guillaume@free.fr>
 */
class GoogleCalendarAPIHelper
{
    const APPLICATION_NAME = 'Web Application';
    const CREDENTIALS_PATH = APP_BASE_DIR . '/app/config/google_api-calendar_credentials.json';
    const CLIENT_SECRET_PATH = APP_BASE_DIR . '/app/config/google_api-client_secret.json';
    const SCOPES = [Google_Service_Calendar::CALENDAR_READONLY];
    const ENUM_ACCESS_TYPE_OFFLINE = 'offline';

    /**
     * Returns an array of events for the next 3 days
     * @param array $aCalendarIds
     * @return array
     */
    public static function getNextEvents($aCalendarIds = [])
    {
        $aEvents = [];

        // Setting default calendar id
        if(empty($aCalendarIds))
        {
            $aCalendarIds[] = static::findPrimaryCalendarId();
        }

        // Getting the API client and construct the service object.
        $oClient = static::getClient();
        $oService = new Google_Service_Calendar($oClient);

        // Getting current date
        $iTimestampToday = strtotime('today');

        // Retrieving events for the next 2 days on the user's calendars.
        foreach($aCalendarIds as $sCalendarId) {
            // Calendar options
            $oCalendar = $oService->calendarList->get($sCalendarId);

            // Calendar list events options
            $sDateMin = date('c');
            $sDateMax = date('Y-m-d\T23:59:59+01:00', strtotime('tomorrow + 6 day'));
            $aOptParams = [
                'maxResults' => 10,
                'orderBy' => 'startTime',
                'singleEvents' => TRUE,
                'timeMin' => $sDateMin,
                'timeMax' => $sDateMax,
            ];
            $oCalendarResults = $oService->events->listEvents($sCalendarId, $aOptParams);

            // Parsing results
            foreach ($oCalendarResults->getItems() as $oEvent) {
                // Period data
                if ($oEvent->start->date !== null) {
                    $sPeriodType = 'days';
                    $sPeriodStart = $oEvent->start->date;
                    $sPeriodEnd = $oEvent->end->date;

                    $sEventDay = $oEvent->start->date;
                }
                else{
                    $sPeriodType = 'hours';
                    $sPeriodStart = $oEvent->start->dateTime;
                    $sPeriodEnd = $oEvent->end->dateTime;

                    $sEventDay = $oEvent->start->dateTime;
                }

                // Color data
                if($oEvent->colorId !== null)
                {
                    $sColorId = $oEvent->colorId;
                }
                elseif ($oCalendar->colorId !== null)
                {
                    $sColorId = $oCalendar->colorId;
                }
                else
                {
                    $sColorId = null;
                }

                // Calendar data
                $sCalendarName = ( ($oCalendar->primary !== true) && ($oCalendarResults->summary !== 'Contacts')) ? $oCalendarResults->summary : null;

                // Checking in how many days the event is
                $iSecondsBetweenTodayAndEvent = strtotime($sEventDay) - $iTimestampToday;
                $iDaysBetweenTodayAndEvent = floor($iSecondsBetweenTodayAndEvent / 86400);

                // Adding event to results
                $aEvents[$iDaysBetweenTodayAndEvent][] = [
                    'title' => $oEvent->summary,
                    'color_code' => static::convertColorIdToHex($sColorId),
                    'calendar_name' => $sCalendarName,
                    'private' => ($oEvent->visibility === 'private'),
                    'period' => [
                        'type' => $sPeriodType,
                        'start' => $sPeriodStart,
                        'end' => $sPeriodEnd,
                    ],
                ];
            }
        }

        // Sorting event days
        ksort($aEvents);
        // Sorting events within days
        foreach($aEvents as $iKey => $aDayEvents) {
            uasort($aEvents[$iKey], function ($a, $b) {
                return $a['period']['start'] > $b['period']['start'];
            });
        }

        return $aEvents;
    }

    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    public static function getClient()
    {
        $oClient = new Google_Client();
        $oClient->setApplicationName(static::APPLICATION_NAME);
        $oClient->setScopes(static::SCOPES);
        $oClient->setAuthConfig(static::CLIENT_SECRET_PATH);
        $oClient->setAccessType(static::ENUM_ACCESS_TYPE_OFFLINE);

        // Load authorized credentials from a file
        $sCredentialsPath = static::CREDENTIALS_PATH;
        if(!file_exists($sCredentialsPath))
        {
            throw new Exception('GoogleCalendarAPI: New credentials needed, run the command !');

            // Check folloing code to know how to proceed
            /*// Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

            // Store the credentials to disk.
            if(!file_exists(dirname($credentialsPath))) {
                mkdir(dirname($credentialsPath), 0700, true);
            }
            file_put_contents($credentialsPath, json_encode($accessToken));
            printf("Credentials saved to %s\n", $credentialsPath);*/
        }
        $aAccessToken = json_decode(file_get_contents($sCredentialsPath), true);
        $oClient->setAccessToken($aAccessToken);

        // Refresh the token if it's expired.
        if ($oClient->isAccessTokenExpired()) {
            $oClient->fetchAccessTokenWithRefreshToken($oClient->getRefreshToken());
            file_put_contents($sCredentialsPath, json_encode($oClient->getAccessToken()));
        }

        return $oClient;
    }

    /**
     * @param string $sColorId
     * @return string
     */
    protected static function convertColorIdToHex($sColorId)
    {
        $sColorCode = null;

        $aColorCodeMap = [
            '1' => '#ac725e',
            '2' => '#d06b64',
            '3' => '#f83a22',
            '4' => '#fa573c',
            '5' => '#ff7537',
            '6' => '#ffad46',
            '7' => '#42d692',
            '8' => '#16a765',
            '9' => '#7bd148',
            '10' => '#b3dc6c',
            '11' => '#fbe983',
            '12' => '#fad165',
            '13' => '#92e1c0',
            '14' => '#9fe1e7',
            //'15' => '#9fc6e7', We don't need this one as it is the default color
            '16' => '#4986e7',
            '17' => '#9a9cff',
            '18' => '#b99aff',
            '19' => '#c2c2c2',
            '20' => '#cabdbf',
            '21' => '#cca6ac',
            '22' => '#f691b2',
            '23' => '#cd74e6',
            '24' => '#a47ae2',
        ];

        if(array_key_exists($sColorId, $aColorCodeMap))
        {
            $sColorCode = $aColorCodeMap[$sColorId];
        }

        return $sColorCode;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    protected static function findPrimaryCalendarId()
    {
        // Getting the API client and construct the service object.
        $oClient = static::getClient();
        $oService = new Google_Service_Calendar($oClient);

        // Iterating over calendar until we find the primary
        foreach($oService->calendarList->listCalendarList()->getItems() as $oCalendar)
        {
            if($oCalendar->primary === true)
            {
                return $oCalendar->id;
            }
        }

        throw new Exception('GoogleCalendarApiHelper: Could not find primary calendar');
    }
}