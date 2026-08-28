<?php

/**
 * Aucun config/cors.php n'existait — l'API tournait sur les défauts bruts
 * du framework, sans restriction explicite documentée. Risque atténué en
 * pratique car le frontend Next.js appelle cette API via un relais
 * serveur-à-serveur (app/api/backend/[...path]/route.ts côté frontend),
 * pas directement depuis le navigateur — mais une config explicite est
 * une meilleure pratique de défense en profondeur, utile aussi si un
 * appel direct navigateur→API est ajouté un jour (widget externe, app
 * mobile web, etc.).
 */

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://www.2i-online.com',
        'https://2i-online.com',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
