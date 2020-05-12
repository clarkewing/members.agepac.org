<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Administrators
    |--------------------------------------------------------------------------
    |
    | Users with these emails will have administrator privileges. This gives
    | them unrestricted access to control the app.
    |
    */

    'administrators' => [
        'john@example.com',
    ],

    /*
    |--------------------------------------------------------------------------
    | Reputation
    |--------------------------------------------------------------------------
    |
    | The points attributed to a user when completing the associated action.
    |
    */

    'reputation' => [
        'thread_published' => 10,
        'reply_posted' => 2,
        'best_post_awarded' => 50,
        'post_favorited' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Promotion courses
    |--------------------------------------------------------------------------
    |
    | The courses that users can sign up from.
    |
    */

    'courses' => [
        'EPL/S',
        'EPL/U',
        'EPL/P',
        'Cursus Prépa ATPL',
    ],

    /*
    |--------------------------------------------------------------------------
    | User Genders
    |--------------------------------------------------------------------------
    |
    | The genders users can select from.
    |
    */

    'genders' => [
        'M' => 'Homme', // M for Male
        'F' => 'Femme', // F for Female
        'O' => 'Autre', // O for Other
        'U' => 'Ne se déclare pas', // U for Unknown
    ],

];
