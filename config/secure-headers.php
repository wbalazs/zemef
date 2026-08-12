<?php

return [
    'chrome_extension_id' => env('CHROME_EXTENSION_ID', 'iehdohnplfknjjjlfknggfdmljfnnaei'),

    'csp' => [
        'default-src' => [
            "'self'",
            'http://zemef.demo',
            'http://localhost:*',
            'http://127.0.0.1:*',
        ],
        
        'script-src' => [
            "'self'",
            "'unsafe-eval'",
            "'unsafe-inline'", // MUST be here for extension
            "'wasm-unsafe-eval'",
            "'inline-speculation-rules'",
            "'sha256-95/LHTlnDoZRMv94YfMSYVQdiMBvhE/7VQTp70jxKgY='", // Add the hash from error
            'http://localhost:*',
            'http://127.0.0.1:*',
            'chrome-extension://iehdohnplfknjjjlfknggfdmljfnnaei',
            'chrome-extension://e174024a-32ac-466e-a371-a82bb219e635',
            'chrome-extension://bnjjngeaknajbdcgpfkgnonkmififhfo',
            'https://cdn.jsdelivr.net',
            'https://printjs-4de6.kxcdn.com',
        ],
        
        'style-src' => [
            "'self'",
            "'unsafe-inline'",
            'https://cdn.jsdelivr.net',
            'https://fonts.googleapis.com',
            'https://maxcdn.bootstrapcdn.com',
            'https://printjs-4de6.kxcdn.com',
        ],
        
        'img-src' => [
            "'self'",
            'data:',
            'blob:',
            'http://zemef.demo',
            'https:',
        ],
        
        'font-src' => [
            "'self'",
            'data:',
            'https://cdn.jsdelivr.net',
            'https://fonts.gstatic.com',
            'https://maxcdn.bootstrapcdn.com',
        ],
        
        'connect-src' => [
            "'self'",
            'http://zemef.demo',
            'http://localhost:*',
            'http://127.0.0.1:*',
            'https://cdn.jsdelivr.net',
            'ws://localhost:*',
            'ws://127.0.0.1:*',
        ],
        
        'frame-src' => [
            "'self'",
        ],
        
        'object-src' => [
            "'none'",
        ],
        
        'base-uri' => [
            "'self'",
        ],
        
        'form-action' => [
            "'self'",
        ],
        
        'worker-src' => [
            "'self'",
            'blob:',
        ],
    ],

    'headers' => [
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-XSS-Protection' => '1; mode=block',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Permissions-Policy' => 'geolocation=(), microphone=(), camera=(), payment=(), serial=(self)',
    ],
];