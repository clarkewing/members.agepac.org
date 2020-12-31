<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tooltip Message
    |--------------------------------------------------------------------------
    |
    | Text that appears in the tooltip when the cursor hover the bubble, before
    | the popup opens.
    |
    */

    'tooltip' => 'Donnez votre avis',

    /*
    |--------------------------------------------------------------------------
    | Popup Title
    |--------------------------------------------------------------------------
    |
    | This is the text that will appear below the logo in the feedback popup
    |
    */

    'title' => 'Aide-nous à améliorer notre site web',

    /*
    |--------------------------------------------------------------------------
    | Success Message
    |--------------------------------------------------------------------------
    |
    | This message will be displayed if the feedback message is correctly sent.
    |
    */

    'success' => 'Merci pour ton avis !',

    /*
    |--------------------------------------------------------------------------
    | Placeholder
    |--------------------------------------------------------------------------
    |
    | This text will appear as the placeholder of the textarea in which the
    | the user will type his feedback.
    |
    */

    'placeholder' => 'Saisis tes commentaires ici...',

    /*
    |--------------------------------------------------------------------------
    | Button Label
    |--------------------------------------------------------------------------
    |
    | Text of the confirmation button to send the feedback.
    |
    */

    'button' => 'Envoyer mes commentaires',

    /*
    |--------------------------------------------------------------------------
    | Feedback Texts
    |--------------------------------------------------------------------------
    |
    | Must match the feedbacks array from the config file
    |
    */
    'feedbacks' => [
        'like' => [
            'title' => 'J’aime quelque chose',
            'label' => 'Qu’est ce que tu as aimé ?',
        ],
        'dislike' => [
            'title' => 'Je n’aime pas quelque chose',
            'label' => 'Qu’est-ce que tu n’aimes pas?',
        ],
        'suggestion' => [
            'title' => 'J’ai une suggestion',
            'label' => 'Quelle est ta suggestion ?',
        ],
         'bug' => [
             'title' => 'J’ai trouvé un bug',
             'label' => 'Explique ce qui s’est passé',
         ],
    ],
];
