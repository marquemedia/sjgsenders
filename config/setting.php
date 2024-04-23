<?php

return [
    "google" => [
        'g_client_id'     => "####",
        'g_client_secret' => "###",
        'g_client_status' => "#####",
    ],

    "gateway_credentials" => [
        "sms" => [
            "default_gateway_id" => 1,
            
            "101NEXMO" => [
                "api_key"    => "####",
                "api_secret" => "####",
                "sender_id"  => 1
            ],
            "102TWILIO" => [
                "account_sid" => "##rd5d##",
                "auth_token"  => "####",
                "from_number" => "####",
                "sender_id"   => "####"
            ],
            "103MESSAGE_BIRD" => [
                "access_key" => "####",
                "sender_id"  => "####",
            ],
            "104TEXT_MAGIC" => [
                "api_key"             => "####",
                "text_magic_username" => "#####",
                "sender_id"           => "####"
            ],
            "105CLICKA_TELL" => [
                "clickatell_api_key" => "####",
                "sender_id"          => "####"
            ],
            "106INFOBIP" => [
                "infobip_base_url" => "####",
                "infobip_api_key"  => "####",
                "sender_id"        => "####"
            ],
            "107SMS_BROADCAST" => [
                "sms_broadcast_username" => "####",
                "sms_broadcast_password" => "####",
            ],
        ],
   
        "email" => [
            'smtp' => [
				'host'       => 'smtp.mailtrap.io',
				'driver'     => 'SMTP',
				'port'       => '2525',
				'encryption' => [

                   'Standard encryption (TLS)' => 'tls',
                   'Secure encryption (SSL)'   => 'ssl',
                   'PowerMTA Server'           => 'pwmta',
                   'STARTTLS'                  => 'starttls',
                   'None or No SSL'            => 'none',
                ],
				'username'   => 'Username',
				'password'   => 'Password',
                ]
			,
			'sendgrid'=>[
				'secret_key' => 'Api Secret Key',
			]
			,
			'aws' =>[
				'profile'      => 'Ses Profile',
				'version'      => 'Ses Version',
				'region'       => 'Ses Region',
				'sender_email' => 'Ses Sender Email ',
			]
			,
			'mailjet' => [
                'secret_key' => 'Api Secret Key',
                'api_key' => 'Api Public Key'
            ]
			,
			'mailgun' => [
				'secret_key'      => 'Api Secret Key',
				'verified_domain' => 'Verified Domain'
			],
        ]
    ],

    "recaptcha" => [
        'recaptcha_key'    => "####",
        'recaptcha_secret' => "###",
        'recaptcha_status' => "2",
    ],
    
    "webhook" => [
        'callback_url' => "####",
        'verify_token' => "###",
    ],

    "whatsapp_business_credentials" => [

        'default' => [

            'version'           => "v19.0",
        ],
        'required' => [

            'user_access_token'            => "###",
            'phone_number_id'              => "###",
            'whatsapp_business_account_id' => "###",
        ],
        'optional' => [

            'business_id'       => "###",
        ]
        
    ]
];

