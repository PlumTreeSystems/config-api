<?php

namespace ConfigAPI\Connector;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class ExternalConfigProvider implements ConfigProviderInterface
{
    private Client $httpClient;
    private string $configApiHost;
    private string $configApiSecret;

    private array $ranks = [];
    private array $metrics = [];
    private array $bonuses = [];
    private array $parameters = [];
    private array $config = [];
    private array $milestones = [];
    private array $flags = [];
    private array $warmup = [];

    public function __construct(string $configApiHost, string $configApiSecret)
    {
        $this->httpClient = new Client();
        $this->configApiHost = $configApiHost;
        $this->configApiSecret = $configApiSecret;
    }

    private function get(string $path): ResponseInterface
    {
        return $this->httpClient->get("http://$this->configApiHost/api/$path", [
            'headers' => [
                'Authorization' => "Bearer $this->configApiSecret"
            ]
        ]);
    }

    public function getRanks(): array
    {
        if ($this->ranks) {
            return $this->ranks;
        }
        $response = $this->get('ranks');
        if ($response->getStatusCode() === 200) {
            $ranks = json_decode((string)$response->getBody(), true)['ranks'] ?? [];
            return ($this->ranks = array_column($ranks, null, 'shortLabel'));
        }
        return [];
    }

    public function getBonuses(): array
    {
        if ($this->bonuses) {
            return $this->bonuses;
        }
        $response = $this->get('bonuses');
        if ($response->getStatusCode() === 200) {
            return $this->bonuses = json_decode((string)$response->getBody(), true)['bonuses'] ?? [];
        }
        return [];
    }

    public function getMetrics(): array
    {
        if ($this->metrics) {
            return $this->metrics;
        }
        $response = $this->get('metrics');
        if ($response->getStatusCode() === 200) {
            $metrics = json_decode((string)$response->getBody(), true)['metrics'] ?? [];
            return ($this->metrics = array_column($metrics, null, 'key'));
        }
        return [];
    }

    public function getParameters(): array
    {
        if ($this->parameters) {
            return $this->parameters;
        }
        $response = $this->get('parameters');
        if ($response->getStatusCode() === 200) {
            return $this->parameters = json_decode((string)$response->getBody(), true)['parameters'] ?? [];
        }
        return [];
    }

    public function getConfig(): array
    {
        if ($this->config) {
            return $this->config;
        }
        $response = $this->get('config');
        if ($response->getStatusCode() === 200) {
            return $this->config = json_decode((string)$response->getBody(), true)['config'] ?? [];
        }
        return [];
    }

    public function getMilestones(): array
    {
        if ($this->milestones) {
            return $this->milestones;
        }
        $response = $this->get('milestones');
        if ($response->getStatusCode() === 200) {
            return $this->milestones = array_column(
                json_decode((string)$response->getBody(), true)['milestones'] ?? [],
                null,
                'key'
            );
        }
        return [];
    }

    public function getFlags(): array
    {
        if ($this->flags) {
            return $this->flags;
        }
        $response = $this->get('flags');
        if ($response->getStatusCode() === 200) {
            return $this->flags = json_decode((string)$response->getBody(), true)['flags'] ?? [];
        }
        return [];
    }

    public function getWarmup(): array
    {
        if ($this->warmup) {
            return $this->warmup;
        }
        $response = $this->get('warmup');
        if ($response->getStatusCode() === 200) {
            $warmup = json_decode((string)$response->getBody(), true)['warmup'] ?? [];
            if (isset($warmup['items'])) {
                $items = [];
                foreach ($warmup['items'] as $item) {
                    if (isset($item['metric'])) {
                        $items[$item['metric']] = $item;
                    }
                }
                $warmup['items'] = $items;
            }
            return $this->warmup = $warmup;
        }
    }
}
