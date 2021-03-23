<?php

namespace Bitms\Component\Consul\Request;

use Bitms\Component\Consul\Exception\ConsulException;

class HttpClient
{
    private const CONSUL_PROTOCOL = 'http';

    private const CONSUL_HOST = '172.17.0.1';

    private const CONSUL_PORT = '8500';

    /**
     * @var string
     */
    private string $address;

    /**
     * @param string $address
     */
    public function setAddress(string $address):void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getAddress():string
    {
        if(empty($this->address)) {
            $this->address = sprintf(
                '%s://%s:%s',
                self::CONSUL_PROTOCOL, self::CONSUL_HOST, self::CONSUL_PORT
            );
        }

        return $this->address;
    }

    public function get(string $path)
    {
        return $this->request('GET', $path);
    }


    public function request(string $method, string $path = '/', array $parameters = [], array $headers = [])
    {
        $uri = $this->getAddress() . $path;

        if(filter_var($uri, FILTER_VALIDATE_URL) == false) {
            throw new ConsulException('URI Address is not valid', 500);
        }

        /**
         * Initialize a HTTP-Client session
         *
         * @see https://www.php.net/manual/en/function.curl-init.php
         */
        $client = \curl_init();

        /**
         * Set an option for a cURL transfer
         *
         * @see https://www.php.net/manual/en/function.curl-setopt.php
         */
        \curl_setopt($client, CURLOPT_URL, $uri);
        \curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($client, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        switch($method) {
            case 'PUT':
                if(is_array($parameters)) {
                    $parameters = json_encode($parameters, JSON_PRETTY_PRINT);
                }

                \curl_setopt($client, CURLOPT_CUSTOMREQUEST, "PUT");
                \curl_setopt($client, CURLOPT_POSTFIELDS, $parameters);
                break;
            case 'POST':
                if(is_array($parameters)) {
                    $parameters = json_encode($parameters);
                }
                \curl_setopt( $client, CURLOPT_POSTFIELDS, $parameters);
                break;
            case 'GET':
            default:
                \curl_setopt($client, CURLOPT_HTTPGET, true);
                break;
        }

        /**
         * Request
         *
         * @see https://www.php.net/manual/en/function.curl-exec.php
         */
        $result = \curl_exec($client);

        /**
         * Check connection error
         */
        if(\curl_errno($client) == 7) {
            throw new ConsulException(\curl_error($client), 500);
        }

        /**
         * Close a HTTP-Client session
         *
         * @see https://www.php.net/manual/en/function.curl-close.php
         */
        \curl_close($client);

        if(!empty($result)) {
            $result = json_decode($result, true);
            if (json_last_error() != JSON_ERROR_NONE) {
                throw new ConsulException('Error response json decode', 500);
            }
        }

        return $result;
    }

}