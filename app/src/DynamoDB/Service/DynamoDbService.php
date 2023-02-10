<?php

namespace App\DynamoDB\Service;

use App\DynamoDB\Service\ClientFactory as DynamoDbClientFactory;
use Aws\DynamoDb\DynamoDbClient;
use Aws\Result;
use InvalidArgumentException;
use League\Csv\Reader;

class DynamoDbService
{
    protected DynamoDbClient $client;

    public function __construct()
    {
        $this->client = DynamoDbClientFactory::instance();
    }

    /**
     * @param string $filepath
     * @return mixed
     * @deprecated UNTESTED/UNUSED
     */
    public function loadFile(string $filepath): mixed
    {
        if (!file_exists($filepath)) throw new InvalidArgumentException('File ' . $filepath . ' not found.');

        $pathinfo = pathinfo($filepath);

        if ($pathinfo['extension'] === 'json') {
            $data = $this->loadJsonFile($filepath);
        }

        if ($pathinfo['extension'] === 'csv') {
            $data = $this->loadCsvFile($filepath);
        }

        return $data;
    }

    /**
     * @param string $filepath
     * @return array
     * * @deprecated unused/untested
     */
    public function loadJsonFile(string $filepath): array
    {
        if (!file_exists($filepath)) throw new InvalidArgumentException('File ' . $filepath . ' not found.');
        return json_decode(file_get_contents($filepath), true);
    }

    /**
     * @param string $filepath
     * @return \Iterator
     * @throws \League\Csv\Exception
     * @throws \League\Csv\UnavailableStream
     * @deprecated unused/untested
     */
    public function loadCsvFile(string $filepath): \Iterator
    {
        $csv = Reader::createFromPath($filepath);
        $csv->setHeaderOffset(0);
        return $csv->getRecords();
    }

    /**
     * @param array $config
     * @return \Aws\Result
     */
    public function createTable(array $config)
    {
        return $this->client->createTable($config);
    }

    /**
     * @param string $name
     * @return \Aws\Result
     */
    public function dropTable(string $name)
    {
        return $this->client->deleteTable([
            'TableName' => $name,
        ]);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function tableExists(string $name): bool
    {
        $res = $this->client->listTables();
        return in_array($name, $res->get('TableNames'));
    }

    /**
     * @param string $tableName
     * @param array $item
     * @return \Aws\Result
     */
    public function putItem(string $tableName, array $item)
    {
        $data = [
            'TableName' => $tableName,
            'Item' => $item,
        ];

        return $this->client->putItem($data);
    }

    public function getItem(string $tableName, string $keyName, string $keyValue)
    {
        $data = [
            'TableName' => $tableName,
            'Key' => $this->createStringKey($keyName, $keyValue),
        ];

        return $this->client->getItem($data);
    }

    /**
     * @param string $tableName
     * @return \Aws\Result
     */
    public function scan(string $tableName)
    {
        return $this->client->scan([
            'TableName' => $tableName,
        ]);
    }

    /**
     * @param string $tableName - name of the table
     * @param array $objectKey - the Identifier of the document being changed (e.g. clientname or clientid)
     * @param string $itemKey - the key of the key-value being changed, e.g. REACT_APP_SEARCH_URL
     * @param array $itemValue - array of value type and value, i.e. [ 'S' => 'a string']
     * @return Result
     *
     * Super basic find and replace on a document
     */
    public function updateItemValue(string $tableName, array $objectKey, string $itemKey, array $itemValue): Result
    {
        $payload = [
            'TableName' => $tableName,
            'Key' => $objectKey,
            'UpdateExpression' => "SET #itemName = :itemValue",
            'ExpressionAttributeNames' => [
                "#itemName" => $itemKey,
            ],
            'ExpressionAttributeValues' => [
                ":itemValue" => $itemValue,
            ],
        ];

        return $this->client
            ->updateItem($payload);
    }

    public function createStringKey(string $keyName, string $keyValue): array
    {
        return [
            $keyName => [
                'S' => $keyValue,
            ],
        ];
    }
}
