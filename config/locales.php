<?php

return [
    'session_key' => 'locale',

    /**
     * The locale that is required for primary content fields (e.g. blog posts, questionnaire titles).
     * Other locales are optional/nullable.
     */
    'primary' => 'nl',

    'supported' => [
        'nl' => 'NL',
        'en' => 'EN',
        'de' => 'DE',
    ],

    'host_defaults' => [
        'hermesresults.com' => 'en',
        'www.hermesresults.com' => 'en',
        'hermesresults.nl' => 'nl',
        'www.hermesresults.nl' => 'nl',
        'hermesresults.eu' => 'de',
        'www.hermesresults.eu' => 'de',
    ],
];
