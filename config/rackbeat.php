<?php

return [
    'domain'               => env( 'RACKBEAT_DOMAIN', 'app.rackbeat.com' ),
    'endpoint'             => env( 'RACKBEAT_ENDPOINT', 'https://app.rackbeat.com/api/' ),
    'integration_endpoint' => env( 'RACKBEAT_INTEGRATION_ENDPOINT', 'https://app.rackbeat.com/integration/' )
];