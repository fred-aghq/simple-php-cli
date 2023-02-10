<?php

namespace App\DynamoDB\Service;

use App\Helper\App;
use Aws\Sdk;

class ClientFactory
{
    // Hold the class instance.
    private static \Aws\DynamoDb\DynamoDbClient|null $instance = null;

    // The constructor is private
    // to prevent initiation with outer code.
    private function __construct()
    {
        // The expensive process (e.g.,db connection) goes here.
    }

    // The object is created from within the class itself
    // only if the class has no instance.
    public static function instance(): \Aws\DynamoDb\DynamoDbClient
    {
        // Handy

        //$provider = CredentialProvider::env();
        //
        //$s3 = new Aws\S3\S3Client([
        //    'version'     => 'latest',
        //    'region'      => 'us-west-2',
        //    'credentials' => $provider
        //]);

        // @TODO: consider making this into a more abstract, AWS Client factory
        if (self::$instance == null) {
            $clientOptions = [
                'region' => getenv('AWS_REGION'),
                'version' => 'latest',
                'credentials' => [
                    'key' => getenv('AWS_ACCESS_KEY'),
                    'secret' => getenv('AWS_SECRET_KEY'),
                ],
            ];

            if (App::isDev() && $endpoint = getenv('AWS_DYNAMODB_ENDPOINT')) {
                $clientOptions['DynamoDb'] = [
                    'endpoint' => $endpoint,
                ];
            }

            $aws = new Sdk($clientOptions);
            self::$instance = $aws->createDynamoDb();
        }

        return self::$instance;
    }
}
