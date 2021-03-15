<?php

return [
    // If 'strict' is True, then the PHP Toolkit will reject unsigned
    // or unencrypted messages if it expects them to be signed or encrypted.
    // Also it will reject the messages if the SAML standard is not strictly
    // followed: Destination, NameId, Conditions ... are validated too.
    'strict' => true,

    // Enable debug mode (to print errors).
    'debug' => false,

    // Set a BaseURL to be used instead of try to guess
    // the BaseURL of the view that process the SAML Message.
    // Ex http://sp.example.com/
    //    http://example.com/sp/
    'baseurl' => null,

    'identity_providers' => [
        'my_idp_key' => [
            'sp' => [
                'entityId' => 'http://saml.test/sso/driver_slug/my_idp_key',
                // Specifies info about where and how the <AuthnResponse> message MUST be
                // returned to the requester, in this case our SP.
                'assertionConsumerService' => [
                    // URL Location where the <Response> from the IdP will be returned
                    'url' => 'http://saml.test/sso/saml2/acs'
                ],
                // Specifies info about where and how the <Logout Response> message MUST be
                // returned to the requester, in this case our SP.
                'singleLogoutService' => [
                    // URL Location where the <Response> from the IdP will be returned
                    'url' => 'http://saml.test/sso/saml2/sls'
                ],

                'x509cert' => '',

                'privateKey' => ''
            ],
            'idp' => [
                'entityId' => 'http://www.driver_slug.com/idp/',
                // SSO endpoint info of the IdP. (Authentication Request protocol)
                'singleSignOnService' => [
                    // URL Target of the IdP where the SP will send the Authentication Request Message
                    'url' =>
                        'https://localhost/sso/saml2/some_unique_app_id/sls'
                ],
                // SLO endpoint info of the IdP.
                'singleLogoutService' => [
                    // URL Location of the IdP where the SP will send the SLO Request
                    'url' => 'https://localhost/sso/saml2/some_unique_app_id/slo',
                    // URL location of the IdP where the SP will send the SLO Response (ResponseLocation)
                    // if not set, url for the SLO Request will be used
                    'responseUrl' => ''
                ],
                // Public x509 certificate of the IdP
                'x509cert' => 'TestKey'
            ]
        ]
    ]
];
