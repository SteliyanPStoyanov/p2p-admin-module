<?php

return [
    'name' => 'Communication',
    'test_emails' => [
        'sps@stikcredit.bg',
        'ava@stikcredit.bg',
    ],
    'test_phones' => [
        [
            'client_id' => 2,
            'phone' => '0888 888 888'
        ],
        [
            'client_id' => 2,
            'phone' => '0777 777 777'
        ],
    ],
    'gender' => [
        'male',
        'female',
        'common'
    ],
    'emailDefaultVariables' => [
        'layout_contacts_link' => env('APP_URL') . '/offices/gr-sofiya',
        'layout_contacts_link_title' => 'Контакти',
        'layout_about_us_link' => env('APP_URL') . '/about-us',
        'layout_about_us_link_title' => 'За нас',
        'firmFacebookLink' => 'https://www.facebook.com/afranga/',
        'layout_facebook_link' => 'https://www.facebook.com/afranga/',
        'layout_facebook_link_title' => 'Facebook',
        'layout_twitter_link' => 'https://twitter.com/afranga1',
        'layout_twitter_link_title' => 'Twitter',
        'layout_home_link_title' => 'Начало',

        'logo' => env('APP_URL') . '/images/afranga-logo-transparent1.png',
        'firmName' => 'Afranga',
        'firmPhone' => '070010514',
        'firmEmail' => 'info@afranga.com',
        'firmWebSite' => env('APP_URL'),
        'loginPage' => env('APP_URL') . '/login',
        'unsubscribeLink' => env('APP_URL'),
        'btnGet' => env('APP_URL'),
        'verification_link' => env('APP_URL') . '/login',
        'siteImgUrl' => env('APP_URL') . '/',
        'siteLogo' => env('APP_URL') . '/images/afranga-logo-transparent1.png',
        'siteUrl' => env('APP_URL') . '/',
        'restorePasswordUrl' => env('APP_URL') . '/forgot-password',
    ],
];
