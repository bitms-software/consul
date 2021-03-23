<?php


namespace Bitms\Component\Consul\Request\Agent;


use Bitms\Component\Consul\CacheAdapter;
use Bitms\Component\Consul\Exception\ConsulException;
use Bitms\Component\Consul\Request\HttpClient;
use Bitms\Component\Consul\Model\Service as SM;

class Service
{
    private const HEALTH_SERVICE_PATH = '/v1/agent/health/service/name/%s';
    private const REGISTER_SERVICE_PATH = '/v1/agent/service/register';
    private const DEREGISTER_SERVICE_PATH = '/v1/agent/service/deregister/%s';

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

    /**
     * @param string $serviceName
     * @param bool $updateCache
     * @return string
     * @throws ConsulException
     */
    public function getAddress(string $serviceName, bool $updateCache = false):string
    {
        $cache = new CacheAdapter;

        /**
         * Clear key from APCu
         */
        if($updateCache == true) {
            $cache->delete($serviceName);
        }

        $result = $cache->get($serviceName);

        if($result == false) {

            $config = $this->get($serviceName);

            $item = [];

            foreach ($config as $i => $service) {
                if ($service['AggregatedStatus'] == 'passing') {
                    $item[] = (array)$service['Service'];
                }
            }
            $item = $item[array_rand($item)];

            $protocol = (in_array($item['Port'], ['443, 8443'])) ? 'https' : 'http';

            $result = sprintf('%s://%s:%s', $protocol, $item['Address'], $item['Port']);

            if(filter_var($result, FILTER_VALIDATE_URL) == true) {
                $cache->add($serviceName, $result, 0, true);
            }
        }

        return $result;
    }

    public function get(string $serviceName):array
    {
        $path = sprintf(self::HEALTH_SERVICE_PATH, $serviceName);

        $result = $this->client->request('GET', $path);
        if(empty($result)) {
            throw new ConsulException(sprintf('Service %s not found to Consul API', $serviceName), 500);
        }
        return $result;
    }


    public function add(SM $service)
    {

        $config = [
            'Id' => $service->getId(),
            'Name' => $service->getName(),
            'Tags' => $service->getTags(),
            'Address' => $service->getAddress(),
            'Port' => $service->getPort(),
            'Checks' => $service->getChecks()
        ];

        $response = $this->client->request('PUT', self::REGISTER_SERVICE_PATH, $config);

        return $response;
//        $parameters = array_merge($config, $service);
    }

    public function drop(string $serviceName)
    {

    }
}