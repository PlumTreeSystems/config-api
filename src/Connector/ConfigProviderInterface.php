<?php

namespace ConfigAPI\Connector;

interface ConfigProviderInterface
{
    public function getRanks(): array;

    public function getBonuses(): array;

    public function getParameters(): array;

    public function getMetrics(): array;

    public function getConfig(): array;

    public function getMilestones(): array;

    public function getFlags(): array;

    public function getWarmup(): array;
}
