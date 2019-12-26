<?php

return [

    'directory' => [
        'path' => env('DATASOURCE_PATH', 'DataSources')
    ],

    'provider' => [
        'file' => env('PROVIDER_NAME', 'RepositoryServiceProvider')
    ],
];
