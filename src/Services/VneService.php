<?php

namespace App\Services;

use Endpoints\Events\EventsClient;
use Endpoints\Events\PingRequest;
use Grpc\ChannelCredentials;
use Grpc\GrpcException;
use Symfony\Component\DependencyInjection\ContainerInterface;


class VneService
{
    /** @var  $client EventsClient */
    protected $client;

    /** @var  $container ContainerInterface */
    protected $container;

    public $service;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getClient()
    {
        $channel_credentials = ChannelCredentials::createSsl(null);

        $client = new EventsClient("vne-core.dev.ivnews.com:443",  [ 'credentials' => $channel_credentials]);

    }

    public function loadService($service)
    {
        $this->service = $this->container->get('App\Services\Vne\\'.$service);
    }




}