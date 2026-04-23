<?php

return [
    'api_base_url' => env('ZATCA_API_BASE_URL', ''),
    'compliance_csid' => env('ZATCA_COMPLIANCE_CSID', ''),
    'compliance_secret' => env('ZATCA_COMPLIANCE_SECRET', ''),
    'production_csid' => env('ZATCA_PRODUCTION_CSID', ''),
    'production_secret' => env('ZATCA_PRODUCTION_SECRET', ''),
    'private_key' => env('ZATCA_PRIVATE_KEY', ''),
    'certificate' => env('ZATCA_CERTIFICATE', ''),
    'environment' => env('ZATCA_ENVIRONMENT', 'sandbox'),
];
