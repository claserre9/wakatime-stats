<?php

declare(strict_types=1);

namespace Claserre9\WakatimeStats;

use Exception;
use GuzzleHttp\Exception\GuzzleException;

class StatsUpdateService
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Orchestrates the end-to-end update: fetch -> process -> update README.
     *
     * @throws GuzzleException
     * @throws Exception
     */
    public function run(): void
    {
        $fetcher = new WakatimeDataFetcher($this->config->getWakatimeUserId(), $this->config->getWakatimeApiKey());
        $data = $fetcher->fetchStats($this->config->getTimeRange());

        $processor = new WakatimeStatsDataProcessor($data);
        $stats = $processor->generateStats();

        $updater = new GitHubStatsUpdater($this->config->getGithubToken());
        $updater->updateReadme($this->config->getRepoOwner(), $this->config->getRepoName(), $stats);
    }
}
