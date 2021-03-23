<?php


namespace Bitms\Component\Consul;


use Bitms\Component\Consul\Exception\ConsulException;
use Bitms\Component\Consul\Helper\Configuration;
use Bitms\Component\Consul\Helper\DefaultCheckService;
use Bitms\Component\Consul\Model\KVStore;
use Bitms\Component\Consul\Request\Agent\Service;
use Bitms\Component\Consul\Request\HttpClient;
use Bitms\Component\Consul\Request\KVStore\KVService;

class ConsulAdapter
{
    private string $dirname;

    private string $address;


    /**
     * @param string $dirname
     */
    public function setDirname(string $dirname):void
    {
        $this->dirname = $dirname;
    }

    /**
     * @return string
     */
    public function getDirname():string
    {
        return $this->dirname;
    }

    public function setAddress(string $address)
    {
        $this->address = $address;
    }

    public function getAddress(): string
    {
        return $this->address ?? "";
    }

    public function getConfig():Configuration
    {
        $config = new Configuration;
        if(!empty($this->getDirname())) {
            $config->setDirname($this->getDirname());
        }

        return $config;
    }

    public function getClient():HttpClient
    {
        $client = new HttpClient;

        if(!empty($this->getAddress())) {
            $client->setAddress($this->getAddress());
        }

        return $client;
    }

    /**
     * @return Model\KVStore
     * @throws Exception\ConsulException
     */
    public function getLocalConfig():KVStore
    {
        $config = $this->getConfig();
        $keyword = $config->get();

        return $this->KVStore()->get($keyword);
    }



    public function KVStore():KVService
    {
        $service = new KVService;
        $service->setClient($this->getClient());

        return $service;
    }

    /**
     * @return array
     * @throws ConsulException
     */
    public function addService(): array
    {
        $sm = new DefaultCheckService;
        $config = self::getLocalConfig()->getValue();
        $config = json_decode($config, true);
        if(json_last_error() != 0) {
            throw new ConsulException('Error JSON parse Consul config', 500);
        }
        $sm->setEnv($config);


        $service = $this->Service()->add($sm->getServiceCheck());


        return [$service];
    }

    public function Service():Service
    {
        $service = new Service;
        $service->setClient($this->getClient());

        return $service;
    }

}