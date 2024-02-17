<?php

use App\Models\User;

return [

    /*
    |--------------------------------------------------------------------------
    | Spark Path
    |--------------------------------------------------------------------------
    |
    | This configuration option determines the URI at which the Spark billing
    | portal is available. You are free to change this URI to a value that
    | you prefer. You shall link to this location from your application.
    |
    */

    'path' => 'billing',

    /*
    |--------------------------------------------------------------------------
    | Spark Middleware
    |--------------------------------------------------------------------------
    |
    | These are the middleware that requests to the Spark billing portal must
    | pass through before being accepted. Typically, the default list that
    | is defined below should be suitable for most Laravel applications.
    |
    */

    'middleware' => ['web', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    |
    | These configuration values allow you to customize the branding of the
    | billing portal, including the primary color and the logo that will
    | be displayed within the billing portal. This logo value must be
    | the absolute path to an SVG logo within the local filesystem.
    |
    */

    'brand' =>  [
        'logo' => realpath(__DIR__ . '/../public/images/color-logo.svg'),
        'color' => '#EA1F88',
    ],

    /*
    |--------------------------------------------------------------------------
    | Proration Behavior
    |--------------------------------------------------------------------------
    |
    | This value determines if charges are prorated when making adjustments
    | to a plan such as incrementing or decrementing the quantity of the
    | plan. This also determines proration behavior if changing plans.
    |
    */

    'prorates' => true,

    /*
    |--------------------------------------------------------------------------
    | Spark Date Format
    |--------------------------------------------------------------------------
    |
    | This date format will be utilized by Spark to format dates in various
    | locations within the billing portal, such as while showing invoice
    | dates. You should customize the format based on your own locale.
    |
    */

    'date_format' => 'F j, Y',

    /*
    |--------------------------------------------------------------------------
    | Spark Billables
    |--------------------------------------------------------------------------
    |
    | Below you may define billable entities supported by your Spark driven
    | application. The Paddle edition of Spark currently only supports a
    | single billable model entity (team, user, etc.) per application.
    |
    | In addition to defining your billable entity, you may also define its
    | plans and the plan's features, including a short description of it
    | as well as a "bullet point" listing of its distinctive features.
    |
    */

    'billables' => [

        'user' => [
            'model' => User::class,

            'trial_days' => 5,

            'default_interval' => 'monthly',

            'plans' => [
                [
                    'name' => 'Starter',
                    'short_description' => '150 units per month with 6% discount.',
                    'monthly_id' => 'price_1OkuuHEjLWGu0g9vE6T2ndmw',
                    //  'monthly_id' => 'price_1ObvxSEjLWGu0g9vIezEDGTW', <-- test mode
                    //  'yearly_id' => env('SPARK_STANDARD_YEARLY_PLAN', 'pri_1001'),
                    'features' => [
                        '150 units per month',
                        'Up to 2 users',
                        'Access to all tools',
                        '~70.000 words OR',
                        '~115 AI images OR',
                        '~25 hours of audio transcription OR',
                        '~10.500 words of text to audio',
                        'Email support',
                    ],
                    'archived' => false,
                ],
                [
                    'name' => 'Pro',
                    'short_description' => '500 units per month with 10% discount. The best deal!',
                    'monthly_id' => 'price_1OkuwREjLWGu0g9vsfuDkHqW',
                    //  'monthly_id' => 'price_1Obw79EjLWGu0g9vrEaHEpxm', <-- test mode
                    //  'yearly_id' => env('SPARK_STANDARD_YEARLY_PLAN', 'pri_1001'),
                    'features' => [
                        '500 units per month',
                        'Up to 10 users',
                        'Access to all tools',
                        '~240.000 words OR',
                        '~390 AI images OR',
                        '~85 hours of audio transcription OR',
                        '~35.500 words of text to audio',
                        '24/7 support'
                    ],
                    'archived' => false,
                ],
                [
                    'name' => 'Enterprise',
                    'short_description' => '1000 units per month with 15% discount.',
                    'monthly_id' => 'price_1OkuyIEjLWGu0g9vrmSDMGzN',
                    // 'monthly_id' => 'price_1OePJYEjLWGu0g9vVkAQ0ZW6', <-- test mode
                    //  'yearly_id' => env('SPARK_STANDARD_YEARLY_PLAN', 'pri_1001'),
                    'features' => [
                        '1000 units per month',
                        'Up to 30 users',
                        'Access to all tools',
                        '~480.000 words OR',
                        '~780 AI images OR',
                        '~170 hours of audio transcription OR',
                        '~71.000 words of text to audio',
                        '24/7 support'
                    ],
                    'archived' => false,
                ],
            ],

        ],

    ],
];
