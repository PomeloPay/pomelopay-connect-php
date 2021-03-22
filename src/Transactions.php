<?php

namespace PomeloPayConnect;

use PomeloPayConnect\Crypt\Signature;
use PomeloPayConnect\Model\Transaction;

class Transactions
{
    const ENDPOINT = 'transactions';

    /**
     * @var Client
     */
    private $client;

    /**
     * Payments constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $json
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(array $json)
    {
        $transaction = (new Transaction())->fromArray($json);
        if(array_key_exists('validForHours', $json)) {
            $json['expires'] = $transaction->getExpires();
            unset($json['validForHours']);
        }
        $json['signature'] = (new Signature($transaction, $this->client->getApiKey()))->sign();
        return $this->client->post(self::ENDPOINT, $json);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get(string $id)
    {
        return $this->client->get(self::ENDPOINT.'/'.$id);
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function list(array $params)
    {
        return $this->client->get(self::ENDPOINT, $params);
    }
}
