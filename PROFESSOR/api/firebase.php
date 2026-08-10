<?php
declare(strict_types=1);

use Kreait\Firebase\Factory;

require_once dirname(__DIR__) . '/vendor/autoload.php';

function firebaseAuth()
{
    $projectId = getenv('FIREBASE_PROJECT_ID');
    $clientEmail = getenv('FIREBASE_CLIENT_EMAIL');
    $privateKey = getenv('FIREBASE_PRIVATE_KEY');

    if (!$projectId) {
        throw new RuntimeException(
            'FIREBASE_PROJECT_ID não está configurada.'
        );
    }

    if (!$clientEmail) {
        throw new RuntimeException(
            'FIREBASE_CLIENT_EMAIL não está configurada.'
        );
    }

    if (!$privateKey) {
        throw new RuntimeException(
            'FIREBASE_PRIVATE_KEY não está configurada.'
        );
    }

    $privateKey = str_replace(
        ['\\n', '\\r\\n'],
        ["\n", "\r\n"],
        $privateKey
    );

    $serviceAccount = [
        'type' => 'service_account',
        'project_id' => $projectId,
        'private_key_id' => '',
        'private_key' => $privateKey,
        'client_email' => $clientEmail,
        'client_id' => '',
        'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
        'token_uri' => 'https://oauth2.googleapis.com/token',
        'auth_provider_x509_cert_url' =>
            'https://www.googleapis.com/oauth2/v1/certs',
        'client_x509_cert_url' => ''
    ];

    return (new Factory())
        ->withServiceAccount($serviceAccount)
        ->createAuth();
}