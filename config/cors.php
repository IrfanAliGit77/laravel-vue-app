<?php
return [
  'paths' => ['*'],
  'allowed_methods' => ['*'],
  'allowed_origins' => ['http://localhost:8000'], // Ganti dengan port aplikasi Anda
  'allowed_headers' => ['*'],
  'exposed_headers' => [],
  'max_age' => 0,
  'supports_credentials' => true,
];

