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
        'best_reply_awarded' => 50,
        'reply_favorited' => 5,
    ],

];
