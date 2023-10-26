<?php

require __DIR__.'/../vendor/autoload.php';

use Claserre9\WakatimeStats\GitHubStatsUpdater;
use Claserre9\WakatimeStats\WakatimeDataFetcher;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Output\BufferedOutput;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->safeLoad();

try {
    $githubToken = $_SERVER['INPUT_GH_TOKEN'] ?? $_ENV['GH_TOKEN'];
    $wakatimeUserId =  $_SERVER['INPUT_WAKATIME_USER_ID'] ??  $_ENV['WAKATIME_USER_ID'];
    $wakatimeApiKey = $_SERVER['INPUT_WAKATIME_API_KEY'] ?? $_ENV['WAKATIME_API_KEY'];
    $wakatimeTimeRange = $_SERVER['INPUT_WAKATIME_TIME_RANGE'] ?? $_ENV['WAKATIME_TIME_RANGE'] ?? 'all_time';
    $githubRepositoryInfo = $_SERVER['GITHUB_REPOSITORY'] ?? $_ENV['GH_REPOSITORY'];

    if (!$githubToken || !$wakatimeUserId || !$wakatimeApiKey || !$githubRepositoryInfo) {
        throw new Exception("Missing required environment variables.");
    }

    list($githubUsername, $githubRepositoryName) = explode('/', $githubRepositoryInfo);

    if (!$githubUsername || !$githubRepositoryName || $githubUsername !== $githubRepositoryName) {
        throw new Exception("Invalid repository info.");
    }

    $wakatimeDataFetcher = new WakatimeDataFetcher($wakatimeUserId, $wakatimeApiKey);
    $wakatimeData = $wakatimeDataFetcher->fetchStats($wakatimeTimeRange);

    if (!$wakatimeData['is_up_to_date']) {
        throw new Exception("Wakatime data is not up to date yet. Please wait a few minutes.");
    }

    $output = new BufferedOutput();
    $categories = [
        'languages' => 'Programming Languages',
        'editors' => 'Editors',
        'operating_systems' => 'Operating Systems',
    ];

    $tableStyle = $_SERVER['INPUT_TABLE_STYLE'] ?? $_ENV['TABLE_STYLE'] ?? 'default';
    if(!in_array($tableStyle, ["default", "box", "box-double"])) $tableStyle = 'default';


    $maxLanguages = (int) $_SERVER['INPUT_MAX_LANGUAGES'] ?? $_ENV['MAX_LANGUAGES'] ?? 5;
    if(!is_numeric($maxLanguages) || $maxLanguages < 1) $maxLanguages = 5;

    foreach ($categories as $dataKey => $categoryTitle) {
        $table = new Table($output);
        $table->setStyle($tableStyle);
        $stats = $wakatimeData[$dataKey];
        $table->setHeaderTitle("{$wakatimeDataFetcher->getReadableRange()} Stats for $categoryTitle");
        $table->setHeaders([$categoryTitle, 'Total Hours']);

        foreach ($stats as $index => $stat) {
            if ($dataKey == 'editors' && $stat["name"] == 'Unknown Editor') {
                continue;
            }

            $table->addRow([
                $stat["name"],
                new TableCell(
                    $stat["text"],
                    ['style' => new TableCellStyle(['align' => 'center'])]
                ),
            ]);


            if ($dataKey == 'languages' && $index === ($maxLanguages - 1)) {
                break;
            }
        }

        $table->setColumnWidth(0, 25);
        $table->setColumnWidth(1, 30);

        $table->render();
        $output->writeln('');
        $output->writeln('');
    }

    $resultsStats = trim($output->fetch());

    $statsResult = "### Wakatime Stats\n```\n$resultsStats\n```";

    $githubStatsUpdater = new GitHubStatsUpdater($githubToken);
    $githubStatsUpdater->updateReadme($githubUsername, $githubRepositoryName, $statsResult);
} catch (Exception | GuzzleException $e) {
    echo $e->getMessage() . "\n";
    exit(1);
}
