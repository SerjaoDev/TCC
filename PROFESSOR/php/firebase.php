<?php
declare(strict_types=1);

use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth;

require_once dirname(__DIR__) . '/vendor/autoload.php';

function firebaseAuth(): Auth
{
    $projectId =
        trim((string) getenv('FIREBASE_PROJECT_ID'));

    $clientEmail =
        trim((string) getenv('FIREBASE_CLIENT_EMAIL'));

    $privateKey =
        (string) getenv('FIREBASE_PRIVATE_KEY');

    if ($projectId === '') {
        throw new RuntimeException(
            'FIREBASE_PROJECT_ID não configurado.'
        );
    }

    if ($clientEmail === '') {
        throw new RuntimeException(
            'FIREBASE_CLIENT_EMAIL não configurado.'
        );
    }

    if ($privateKey === '') {
        throw new RuntimeException(
            'FIREBASE_PRIVATE_KEY não configurado.'
        );
    }

    $privateKey = str_replace(
        [
            '\\n',
            '\\r\\n'
        ],
        [
            "\n",
            "\r\n"
        ],
        $privateKey
    );

    $serviceAccount = [
        'type' =>
            'service_account',

        'project_id' =>
            $projectId,

        'client_email' =>
            $clientEmail,

        'private_key' =>
            $privateKey
    ];

    $factory =
        (new Factory)
            ->withServiceAccount(
                $serviceAccount
            )
            ->withProjectId(
                $projectId
            );

    return $factory->createAuth();
}