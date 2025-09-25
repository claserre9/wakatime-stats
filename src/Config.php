<?php

declare(strict_types=1);

namespace Claserre9\WakatimeStats;

use InvalidArgumentException;

class Config
{
    private string $githubToken;
    private string $wakatimeUserId;
    private string $wakatimeApiKey;
    private string $timeRange;
    private string $tableStyle;
    private int $maxLanguages;
    private string $repoOwner;
    private string $repoName;

    private const VALID_RANGES = ['last_7_days', 'last_30_days', 'last_6_months', 'last_year', 'all_time'];
    private const VALID_TABLE_STYLES = ['default', 'box', 'box-double'];

    public static function fromEnv(): self
    {
        // Inputs from GitHub Actions are exposed as INPUT_* env vars
        $githubToken = self::getFirstNonEmpty([
            'INPUT_GH_TOKEN',
            'GH_TOKEN',
        ]);
        $wakatimeUserId = self::getFirstNonEmpty(['INPUT_WAKATIME_USER_ID', 'WAKATIME_USER_ID']);
        $wakatimeApiKey = self::getFirstNonEmpty(['INPUT_WAKATIME_API_KEY', 'WAKATIME_API_KEY']);

        $timeRange = self::getFirstNonEmpty(['INPUT_WAKATIME_TIME_RANGE', 'WAKATIME_TIME_RANGE']) ?? 'all_time';
        if (!in_array($timeRange, self::VALID_RANGES, true)) {
            $timeRange = 'all_time';
        }

        $tableStyle = self::getFirstNonEmpty(['INPUT_TABLE_STYLE', 'TABLE_STYLE']) ?? 'default';
        if (!in_array($tableStyle, self::VALID_TABLE_STYLES, true)) {
            $tableStyle = 'default';
        }

        $maxLanguagesRaw = self::getFirstNonEmpty(['INPUT_MAX_LANGUAGES', 'MAX_LANGUAGES']) ?? '5';
        $maxLanguages = (int)trim((string)$maxLanguagesRaw);
        if ($maxLanguages < 1) {
            $maxLanguages = 5;
        }

        $repo = self::getFirstNonEmpty(['GITHUB_REPOSITORY', 'GH_REPOSITORY']);
        if ($repo === null || strpos($repo, '/') === false) {
            throw new InvalidArgumentException('Invalid repository info. Expected format "<owner>/<repo>".');
        }
        [$owner, $name] = explode('/', $repo, 2);
        if ($owner === '' || $name === '') {
            throw new InvalidArgumentException('Invalid repository info. Expected format "<owner>/<repo>".');
        }

        return new self(
            $githubToken ?? '',
            $wakatimeUserId ?? '',
            $wakatimeApiKey ?? '',
            $timeRange,
            $tableStyle,
            $maxLanguages,
            $owner,
            $name
        );
    }

    public function __construct(
        string $githubToken,
        string $wakatimeUserId,
        string $wakatimeApiKey,
        string $timeRange,
        string $tableStyle,
        int $maxLanguages,
        string $repoOwner,
        string $repoName
    ) {
        if ($githubToken === '' || $wakatimeUserId === '' || $wakatimeApiKey === '') {
            throw new InvalidArgumentException('Missing required environment variables.');
        }
        $this->githubToken = $githubToken;
        $this->wakatimeUserId = $wakatimeUserId;
        $this->wakatimeApiKey = $wakatimeApiKey;
        $this->timeRange = $timeRange;
        $this->tableStyle = $tableStyle;
        $this->maxLanguages = $maxLanguages;
        $this->repoOwner = $repoOwner;
        $this->repoName = $repoName;
    }

    public function getGithubToken(): string { return $this->githubToken; }
    public function getWakatimeUserId(): string { return $this->wakatimeUserId; }
    public function getWakatimeApiKey(): string { return $this->wakatimeApiKey; }
    public function getTimeRange(): string { return $this->timeRange; }
    public function getTableStyle(): string { return $this->tableStyle; }
    public function getMaxLanguages(): int { return $this->maxLanguages; }
    public function getRepoOwner(): string { return $this->repoOwner; }
    public function getRepoName(): string { return $this->repoName; }

    /**
     * For compatibility with WakatimeStatsDataProcessor which reads from INPUT_*.
     */
    public function exportInputsToServer(): void
    {
        $_SERVER['INPUT_TABLE_STYLE'] = $this->tableStyle;
        $_SERVER['INPUT_MAX_LANGUAGES'] = (string)$this->maxLanguages;
        $_SERVER['INPUT_WAKATIME_TIME_RANGE'] = $this->timeRange;
        $_SERVER['GITHUB_REPOSITORY'] = $this->repoOwner . '/' . $this->repoName;
        $_SERVER['INPUT_GH_TOKEN'] = $this->githubToken;
        $_SERVER['INPUT_WAKATIME_USER_ID'] = $this->wakatimeUserId;
        $_SERVER['INPUT_WAKATIME_API_KEY'] = $this->wakatimeApiKey;
    }

    private static function getFirstNonEmpty(array $keys): ?string
    {
        foreach ($keys as $key) {
            $val = $_SERVER[$key] ?? $_ENV[$key] ?? null;
            if ($val !== null && trim((string)$val) !== '') {
                return (string)$val;
            }
        }
        return null;
    }
}
