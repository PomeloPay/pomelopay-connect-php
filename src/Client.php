<?php

namespace PomeloPayConnect;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;

class Client
{
    const API_VERSION = '2.0';
    const APP_VERSION = 'pomelo-php-v2.1.2';
    const SIGN_METHOD = 'sha1';
    const SANDBOX_ENDPOINT = 'https://api.dev.pomelopay.com/public/';
    const PRODUCTION_ENDPOINT = 'https://api.pomelopay.com/public/';

    /**
     * @var \GuzzleHttp\Client
     */
    private $httpClient;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $appId;

    /**
     * @var string
     */
    private $mode;

    /**
     * @var array<mixed, mixed>|null
     */
    private $clientOptions;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var Transactions
     */
    public $transactions;


    /**
     * Client constructor.
     * @param string $apiKey
     * @param string $appId
     * @param string $mode
     * @param array<mixed, mixed> $clientOptions
     */
    public function __construct(string $apiKey, string $appId, $mode = 'production', array $clientOptions = [])
    { 
        $this->apiKey = $apiKey;
        $this->appId = $appId;
        $this->mode = $mode;
        $this->baseUrl = isset($clientOptions['baseUrl']) ? $clientOptions['baseUrl'] : ($mode === 'production' ? self::PRODUCTION_ENDPOINT : self::SANDBOX_ENDPOINT);
        if (isset($clientOptions['baseUrl'])) unset($clientOptions['baseUrl']);
        $this->clientOptions = $clientOptions;

        $this->initiateHttpClient();

        $this->transactions = new Transactions($this);
    }

    /**
     * @param GuzzleClient $client
     * @return void
     */
    public function setClient(GuzzleClient $client)
    {
        $this->httpClient = $client;
    }

    /**
     * Initiates the HttpClient with required headers
     * @return void
     */
    private function initiateHttpClient()
    {
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' =>  $this->apiKey,
            ]
        ];

        $this->httpClient = new GuzzleClient(array_replace_recursive($this->clientOptions, $options));
    }

    /**
     * @return string
     */
    private function buildBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param Response $response
     * @return mixed
     */
    private function handleResponse(Response $response)
    {
        $stream = Utils::streamFor($response->getBody());
        $data = json_decode($stream);

        return $data;
    }

    /**
     * @param string $endpoint
     * @param array<mixed, mixed> $json
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post($endpoint, $json)
    {
        $json['apiVersion'] = self::API_VERSION;
        $json['appVersion'] = self::APP_VERSION;
        $json['signMethod'] = self::SIGN_METHOD;

        $response = $this->httpClient->request('POST', $this->buildBaseUrl().$endpoint, ['json' => $json]);
        return $this->handleResponse($response);
    }

    /**
     * @param string $endpoint
     * @param array<mixed, mixed> $pagination
     * @return mixed
     */
    public function get(string $endpoint, array $pagination = [])
    {
        $response = $this->httpClient->request(
            'GET',
            $this->applyPagination($this->buildBaseUrl().$endpoint, $pagination)
        );

        return $this->handleResponse($response);
    }

    /**
     * @param string $url
     * @param array<mixed, mixed> $pagination
     * @return string
     */
    private function applyPagination(string $url, array $pagination)
    {
        if (count($pagination)) {
            return $url.'?'.http_build_query($this->cleanPagination($pagination));
        }

        return $url;
    }

    /**
     * @param array<mixed, mixed> $pagination
     * @return array<mixed, mixed>
     */
    private function cleanPagination(array $pagination)
    {
        $allowed = [
            'page',
        ];

        return array_intersect_key($pagination, array_flip($allowed));
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }
}
