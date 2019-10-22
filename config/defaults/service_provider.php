<?php

return [
    // Service Provider Data that we are deploying
    // Identifier of the SP entity  (must be a URI)
    'entityId' => '',
    // Specifies info about where and how the <AuthnResponse> message MUST be
    // returned to the requester, in this case our SP.
    'assertionConsumerService' => [
        // URL Location where the <Response> from the IdP will be returned
        'url' => '',
        // SAML protocol binding to be used when returning the <Response>
        // message.  Onelogin Toolkit supports for this endpoint the
        // HTTP-POST binding only
        'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'
    ],
    // If you need to specify requested attributes, set a
    // attributeConsumingService. nameFormat, attributeValue and
    // friendlyName can be omitted. Otherwise remove this section.
    "attributeConsumingService" => [
        "serviceName" => "SP test",
        "serviceDescription" => "Test Service",
        "requestedAttributes" => [
            [
                "name" => "",
                "isRequired" => false,
                "nameFormat" => "",
                "friendlyName" => "",
                "attributeValue" => ""
            ]
        ]
    ],
    // Specifies info about where and how the <Logout Response> message MUST be
    // returned to the requester, in this case our SP.
    'singleLogoutService' => [
        // URL Location where the <Response> from the IdP will be returned
        'url' => '',
        // SAML protocol binding to be used when returning the <Response>
        // message.  Onelogin Toolkit supports for this endpoint the
        // HTTP-Redirect binding only
        'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'
    ],
    // Specifies constraints on the name identifier to be used to
    // represent the requested subject.
    // Take a look on lib/Saml2/Constants.php to see the NameIdFormat supported
    'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',

    // Usually x509cert and privateKey of the SP are provided by files placed at
    // the certs folder. But we can also provide them with the following parameters
    'x509cert' => '',
    'privateKey' => ''

    /*
     * Key rollover
     * If you plan to update the SP x509cert and privateKey
     * you can define here the new x509cert and it will be
     * published on the SP metadata so Identity Providers can
     * read them and get ready for rollover.
     */
    // 'x509certNew' => '',
];
