<?php

return [
    
    "routes" => [

        "gateway_user.gateway.whatsapp"               => "Oops! Your current Plan doesn't allow you to add Whatsapp Devices.",
        "gateway_user.gateway.whatsapp.qrcode"        => "Oops! Your current Plan doesn't allow you to add Whatsapp Devices.",
        "gateway_user.gateway.whatsapp.device.status" => "Oops! Your current Plan doesn't allow you to add Whatsapp Devices.",
        "campaign_user.campaign.whatsapp"             => "Oops! Your current Plan doesn't allow you to maintain Whatsapp Campaign.",
        "message_user.whatsapp"                       => "Oops! Your current Plan doesn't allow you to maintain Whatsapp Campaign.",
        

        "gateway_user.gateway.sms"          => "Oops! Your current Plan doesn't allow you to add SMS Gateways.",
        "campaign_user.campaign.sms"        => "Oops! Your current Plan doesn't allow you to maintain SMS Campaign.",
        "message_user.sms"                  => "Oops! Your current Plan doesn't allow you to use SMS Message.",

        "gateway_user.mail.gateway"         => "Oops! Your current Plan doesn't allow you to add Email Gateways.",
        "campaign_user.campaign.email"      => "Oops! Your current Plan doesn't allow you to maintain Email Campaign.",
        "message_user.manage.email"         => "Oops! Your current Plan doesn't allow you to use Email Message.",

        "gateway_sendmethod.android"        => "Oops! Your current Plan doesn't allow you to add Android Gateway.",
        "settings_user.default.sms.gateway" => "Oops! Your current Plan doesn't allow you to send messages with an Android gateway.",

    ],

    "types" => [
        "whatsapp",
        "sms",
        "mail",
        "android"
    ],

    "gateway_access" => [
        'user_create',
        'admin_access'
    ],

    "pricing_plan" => [

        "sms" => [
            "is_allowed" => true,
            "gateway_limit" => 0,
            "allowed_gateways" => [

            ],
            "credits" => "sms_credit",
            "android" => [
                "is_allowed"    => true,
                "gateway_limit" => 0
            ]
        ],
        "email" => [
            "is_allowed" => true,
            "gateway_limit" => 0,
            "allowed_gateways" => [

            ],
            "credits" => "email_credit"
        ],
        "whatsapp" => [
            "is_allowed" => true,
            "gateway_limit" => 0,
            "credits" => "whatsapp_credit",
        ],
    ]
];
