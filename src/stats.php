<?php

require  __DIR__ . '/../vendor/autoload.php';

use Claserre9\WakatimeStats\GitHubStatsUpdater;
use Claserre9\WakatimeStats\WakatimeDataFetcher;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Output\BufferedOutput;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();


$wakatimeUserId = $_ENV['WAKATIME_USER_ID'] ?? $_SERVER['WAKATIME_USER_ID'] ?? $_SERVER['INPUT_WAKATIME_USER_ID'] ?? '';
$wakatimeApiKey = $_ENV['WAKATIME_API_KEY'] ?? $_SERVER['WAKATIME_API_KEY'] ?? $_SERVER['INPUT_WAKATIME_API_KEY'] ?? '';
$wakatimeRange = $_ENV['INPUT_WAKATIME_RANGE'] ?? 'all_time';
$githubToken =  $_ENV['GH_TOKEN'] ?? $_SERVER['GH_TOKEN'] ?? $_SERVER['INPUT_GH_TOKEN'] ?? '';


if (!$wakatimeUserId || !$wakatimeApiKey){
    echo("Wakatime infos not provided");
    exit(1);
}
if (!$githubToken){
    echo("GitHub token not provided");
    exit(1);
}

$repositoryInfo = $_SERVER['GITHUB_REPOSITORY'] ?? $_ENV['GH_REPOSITORY'] ?? $_SERVER['INPUT_GITHUB_REPOSITORY'] ?? '';

if (!$repositoryInfo){
    echo("Repository info not provided");
    exit(1);
}

list($githubUsername, $githubRepositoryName) = explode('/', $repositoryInfo);

if (!$githubUsername || !$githubRepositoryName){
    echo("Repository info is incomplete");
    exit(1);
}

if ($githubUsername !== $githubRepositoryName){
    echo("Repository is not the special one required. Must have <username>/<username>");
    exit(1);
}


$wakatimeDataFetcher = new WakatimeDataFetcher($wakatimeUserId, $wakatimeApiKey);
try {
    $wakatimeData = $wakatimeDataFetcher->fetchStats();
    $readableRange = $wakatimeDataFetcher->getReadableRange();

    if (!$wakatimeData['is_up_to_date']) {
        exit("Wakatime data is not up to date yet. Please wait a few minutes.");
    }

} catch (GuzzleException $e) {
    echo $e->getMessage()."\n";
    exit(1);
}

$output = new BufferedOutput();


$tableLanguages = new Table($output);
$languagesStats = $wakatimeData['languages'];
$tableLanguages->setHeaderTitle("$readableRange Stats (Top Five)");
$tableLanguages->setHeaders(['Languages', 'Total Hours']);
foreach ($languagesStats as $index => $languagesStat) {
    $tableLanguages->addRow(
        [
            $languagesStat["name"],
            new TableCell(
                $languagesStat["text"],
                ['style' => new TableCellStyle(['align' => 'center',])]
            )
        ]);
    if ($index === 4) break;
}

$tableLanguages->setColumnWidth(0, 15);
$tableLanguages->setColumnWidth(1, 30);
$tableLanguages->setStyle('box');
$tableLanguages->render();

$allTimeStats = trim($output->fetch());

$statsResult = "
### Wakatime Stats
```
{$allTimeStats}
```";


$githubStatsUpdater = new GitHubStatsUpdater($githubToken);

try {
    $githubStatsUpdater->updateReadme($githubUsername, $githubRepositoryName, $statsResult);
} catch (GuzzleException $e) {
    echo $e->getMessage()."\n";
    exit(1);
}