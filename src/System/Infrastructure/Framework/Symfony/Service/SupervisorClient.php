<?php

namespace Marvin\System\Infrastructure\Framework\Symfony\Service;
use Marvin\System\Domain\Exception\SupervisorConnectionError;
use Marvin\System\Domain\Exception\SupervisorXmlRpcFault;
use Marvin\System\Domain\Service\SupervisorClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class SupervisorClient implements SupervisorClientInterface
{
    public function __construct(
        #[Autowire(env: 'SUPERVISOR_URL')]
        private string $url,
        #[Autowire(env: 'SUPERVISOR_USER')]
        private string $username,
        #[Autowire(env: 'SUPERVISOR_PASS')]
        private string $password,
    ) {}

    private function call(string $method, array $params = []): mixed
    {
        $body = xmlrpc_encode_request($method, $params);
        $ch = curl_init($this->url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => ['Content-Type: text/xml'],
            CURLOPT_USERPWD => "{$this->username}:{$this->password}",
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new SupervisorConnectionError('Erreur cURL Supervisor: ' . $err);
        }

        curl_close($ch);

        $decoded = xmlrpc_decode($response);
        if (is_array($decoded) && xmlrpc_is_fault($decoded)) {
            throw new SupervisorXmlRpcFault("Fault Supervisor: " . $decoded['faultString']);
        }
        return $decoded;
    }

    public function listProcesses(): array
    {
        return $this->call('supervisor.getAllProcessInfo');
    }

    public function start(string $name): mixed
    {
        return $this->call('supervisor.startProcess', [$name]);
    }

    public function stop(string $name): mixed
    {
        return $this->call('supervisor.stopProcess', [$name]);
    }

    public function restart(string $name): void
    {
        $this->stop($name);
        sleep(2);
        $this->start($name);
    }

    public function reloadConfig(): array
    {
        return $this->call('supervisor.reloadConfig');
    }

    public function version(): string
    {
        return $this->call('supervisor.getSupervisorVersion');
    }

    public function getState(): array
    {
        return $this->call('supervisor.getState');
    }
}
