<?php
declare(strict_types=1);

use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth;

require_once dirname(__DIR__) . '/vendor/autoload.php';

function firebaseAuth(): Auth
{
    $projectId = trim(
        (string) getenv('FIREBASE_PROJECT_ID')
    );

    $clientEmail = trim(
        (string) getenv('FIREBASE_CLIENT_EMAIL')
    );

    $privateKey = (string) getenv(
        'FIREBASE_PRIVATE_KEY'
    );

    if ($projectId === '') {
        throw new RuntimeException(
            'FIREBASE_PROJECT_ID não está configurada.'
        );
    }

    if ($clientEmail === '') {
        throw new RuntimeException(
            'FIREBASE_CLIENT_EMAIL não está configurada.'
        );
    }

    if ($privateKey === '') {
        throw new RuntimeException(
            'FIREBASE_PRIVATE_KEY não está configurada.'
        );
    }

    /*
     * A Vercel pode armazenar a chave
     * com \n literal.
     *
     * Converte para quebras de linha reais.
     */
    $privateKey = str_replace(
        [
            '\\r\\n',
            '\\n',
            '\\r'
        ],
        [
            "\r\n",
            "\n",
            "\r"
        ],
        $privateKey
    );

    /*
     * Remove aspas externas caso tenham
     * sido copiadas para a variável.
     */
    $privateKey = trim(
        $privateKey,
        " \t\n\r\0\x0B\"'"
    );

    if (
        !str_contains(
            $privateKey,
            'BEGIN PRIVATE KEY'
        )
    ) {
        throw new RuntimeException(
            'FIREBASE_PRIVATE_KEY não possui um formato válido.'
        );
    }

    $serviceAccount = [
        'type' => 'service_account',
        'project_id' => $projectId,
        'client_email' => $clientEmail,
        'private_key' => $privateKey
    ];

    $factory = (new Factory())
        ->withServiceAccount($serviceAccount)
        ->withProjectId($projectId);

    return $factory->createAuth();
}