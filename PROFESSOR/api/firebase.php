<?php
declare(strict_types=1);

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;

require_once __DIR__ . '/../vendor/autoload.php';

function firebaseAuth(): Auth
{
    $projectId = getenv('FIREBASE_PROJECT_ID');
    $clientEmail = getenv('FIREBASE_CLIENT_EMAIL');
    $privateKey = getenv('FIREBASE_PRIVATE_KEY');

    if (!$projectId) {
        throw new RuntimeException(
            'FIREBASE_PROJECT_ID não configurado.'
        );
    }

    if (!$clientEmail) {
        throw new RuntimeException(
            'FIREBASE_CLIENT_EMAIL não configurado.'
        );
    }

    if (!$privateKey) {
        throw new RuntimeException(
            'FIREBASE_PRIVATE_KEY não configurado.'
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
        'client_email' => $clientEmail,
        'private_key' => $privateKey
    ];

    $factory = new Factory();

    $factory = $factory->withServiceAccount(
        $serviceAccount
    );

    return $factory->createAuth();
}
?>