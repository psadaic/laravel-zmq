<?php

namespace Pelim\LaravelZmq\Connector;

/**
 * Class ZmqConnector
 * @package Pelim\LaravelZmq\Connector
 */
class ZmqPublish extends ZmqConnector
{
    /**
     * ZmqPublish constructor.
     * @param string $connection
     */
    public function __construct($connection = 'publish')
    {
        parent::__construct($connection);
    }

    /**
     * Connect to the socket for publishing.
     * @param null $index
     * @return \ZMQSocket
     * @throws \ZMQSocketException
     */
    public function connect($index = null)
    {
        if(isset($index))
            $this->connection = 'publish.'.$index;

        $parse_dsn = parse_url($this->dsn());
        if(is_array($parse_dsn)){
            $testConnection = @fsockopen($parse_dsn['host'], $parse_dsn['port']);
            if(!is_resource($testConnection)){
                return null;
            }
            fclose($testConnection);
        }

        $context = new \ZMQContext();
        $socket_method = \Config::get(sprintf('zmq.connections.%s.method', $this->connection), \ZMQ::SOCKET_PUB);
        $socket = $context->getSocket($socket_method);
        $socket->connect($this->dsn());

        usleep(500);

        return $socket;
    }
}
