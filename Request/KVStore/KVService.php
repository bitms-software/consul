<?php


namespace Bitms\Component\Consul\Request\KVStore;

use Bitms\Component\Consul\Exception\ConsulException;
use Bitms\Component\Consul\Model\KVStore;
use Bitms\Component\Consul\Request\HttpClient;

class KVService
{
    private HttpClient $client;

    /**
     * KVService constructor.
     * @param HttpClient|null $client
     */
    public function __construct(HttpClient|null $client = null)
    {
        if(is_null($client))  {
            $this->client = new HttpClient;
        }
    }

    public function setClient(HttpClient $client)
    {
        $this->client = $client;
    }

    public function get(string $key = ''):KVStore
    {
        $path = sprintf('/v1/kv/%s', $key);

        $response = $this->client->request('GET', $path);

        $em = new KVStore;

        if(!empty($response)) {
            $kv = reset($response);
            $em->setKey($kv['Key']);
            $em->setValue(base64_decode($kv['Value']));
        }

        return $em;
    }

    public function set(string $key, array $configuration):KVStore
    {
        $path = sprintf('/v1/kv/%s', $key);

        $response = $this->client->request('PUT', $path, $configuration);

        if($response != true) {
            throw new ConsulException('Error create configuration', 500);
        }

        return $this->get($key);
    }
}