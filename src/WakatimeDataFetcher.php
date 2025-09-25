<?php

declare(strict_types=1);

namespace Claserre9\WakatimeStats;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class WakatimeDataFetcher
{
    private Client $client;

    protected string $range;

    private static array $statsRange = ['last_7_days',  'last_30_days', 'last_6_months', 'last_year', 'all_time'];

    public function __construct($wakatimeUserId, $wakatimeApiKey)
    {
        $this->client = new Client([
            'base_uri' => 'https://wakatime.com/api/v1/users/' . $wakatimeUserId . '/',
            'headers'  => ['Authorization' => 'Basic ' . base64_encode($wakatimeApiKey)],
        ]);
    }

    private function validateStatsRange($range)
    {
        if (!in_array($range, self::$statsRange, true)) {
            return 'all_time';
        }

        return $range;
    }

    /**
     * @throws GuzzleException
     */
    public function fetchStats($range = 'all_time')
    {
        $range = $this->validateStatsRange($range);

        $response     = $this->client->get("stats/$range");
        $wakatimeData = json_decode($response->getBody()->getContents(), true);
        return $wakatimeData['data'];
    }

    public function fetchProjectStats($range = 'all_time')
    {
        $range = $this->validateStatsRange($range);
    }

    public function fetchCommitStats($range = 'all_time')
    {
        $range = $this->validateStatsRange($range);
    }

    public function getReadableRange(): string
    {
        switch ($this->range) {
            case 'last_7_days':
                return 'Last 7 Days';
            case 'last_30_days':
                return 'Last 30 Days';
            case 'last_6_months':
                return 'Last 6 Months';
            case 'last_year':
                return 'Last Year';
            case 'all_time':
                return 'All Time';
        }
        return 'All Time';
    }

}
