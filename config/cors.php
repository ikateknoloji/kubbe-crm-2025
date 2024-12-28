<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Bu yapılandırma public bir API için ayarlanmıştır. Tüm yöntemlere,
    | başlıklara ve kaynaklara izin verir.
    |
    */

    'paths' => ['*'], 

    'allowed_methods' => ['*'], 

    'allowed_origins' => ['*'], 

    'allowed_origins_patterns' => [], 

    'allowed_headers' => ['*'], 

    'exposed_headers' => ['*'], 

    'max_age' => 0, 

    'supports_credentials' => false, 

];
