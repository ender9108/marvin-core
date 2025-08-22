<?php

namespace EnderLab\DddCqrsBundle\Infrastructure\RabbitMq;

use AMQPChannel;
use AMQPChannelException;
use AMQPConnection;
use AMQPConnectionException;
use AMQPExchange;
use AMQPExchangeException;
use AMQPQueue;
use AMQPQueueException;
use InvalidArgumentException;

class DomainEventRabbitMqConfigurator
{
    private AMQPChannel $channel;
    private array $queues = [];

    /**
     * @throws AMQPConnectionException
     */
    public function __construct(
        private readonly string $dsn
    ) {
        $this->initChannelFromDsn();
    }

    public function setQueues(array $queues): void
    {
        $this->queues = $queues;
    }

    /**
     * @throws AMQPConnectionException
     */
    private function initChannelFromDsn(): void
    {
        $url = parse_url($this->dsn);
        if (!$url) {
            throw new InvalidArgumentException("Invalid DSN: {$this->dsn}");
        }

        $host = $url['host'] ?? 'localhost';
        $port = isset($url['port']) ? (int)$url['port'] : 5672;
        $user = $url['user'] ?? 'guest';
        $password = $url['pass'] ?? 'guest';
        $vhost = isset($url['path']) ? ltrim($url['path'], '/') : '/';
        $vhost = urldecode($vhost);

        $connection = new AMQPConnection([
            'host' => $host,
            'port' => $port,
            'login' => $user,
            'password' => $password,
            'vhost' => $vhost,
        ]);
        $connection->connect();
        $this->channel = new AMQPChannel($connection);
    }

    /**
     * @throws AMQPQueueException
     * @throws AMQPExchangeException
     * @throws AMQPConnectionException
     * @throws AMQPChannelException
     */
    public function configure(): void
    {
        $exchangeName = 'domain.event';

        // Déclaration de l’exchange durable
        $exchange = new AMQPExchange($this->channel);
        $exchange->setName($exchangeName);
        $exchange->setType(AMQP_EX_TYPE_TOPIC);
        $exchange->setFlags(AMQP_DURABLE);
        $exchange->declareExchange();

        // Déclaration des queues + bindings
        foreach ($this->queues as $queueName => $data) {
            $queue = new AMQPQueue($this->channel);
            $queue->setName($queueName);
            $queue->setFlags(AMQP_DURABLE);
            $queue->declareQueue();

            foreach ($data['binding_keys'] as $bindingKey) {
                $queue->bind($exchangeName, $bindingKey);
            }
        }
    }
}
