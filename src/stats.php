<?php

require  __DIR__ . '/../vendor/autoload.php';

use Claserre9\WakatimeStats\GitHubStatsUpdater;
use Claserre9\WakatimeStats\WakatimeDataFetcher;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Output\BufferedOutput;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$allowedTableStyle = ['default', 'box', 'compact', 'box-double', 'borderless'];

$wakatimeUserId = $_ENV['WAKATIME_USER_ID'] ?? $_SERVER['WAKATIME_USER_ID'] ?? $_SERVER['INPUT_WAKATIME_USER_ID'] ?? '';
$wakatimeApiKey = $_ENV['WAKATIME_API_KEY'] ?? $_SERVER['WAKATIME_API_KEY'] ?? $_SERVER['INPUT_WAKATIME_API_KEY'] ?? '';
$wakatimeRange = $_ENV['INPUT_WAKATIME_RANGE'] ?? 'all_time';
$githubToken =  $_ENV['GH_TOKEN'] ?? $_SERVER['GH_TOKEN'] ?? $_SERVER['INPUT_GH_TOKEN'] ?? '';
$tableStyle = $_ENV['TABLE_STYLE'] ?? $_SERVER['TABLE_STYLE'] ?? $_SERVER['INPUT_TABLE_STYLE'] ?? 'default';
$wakatimeTimeRange = $_ENV['WAKATIME_TIME_RANGE'] ?? $_SERVER['WAKATIME_TIME_RANGE'] ?? $_SERVER['INPUT_WAKATIME_TIME_RANGE'] ?? 'all_time';


if(!in_array(strtolower($tableStyle) , $allowedTableStyle)){
    $tableStyle = 'default';
}


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
    $wakatimeData = $wakatimeDataFetcher->fetchStats($wakatimeTimeRange);
    $readableRange = $wakatimeDataFetcher->getReadableRange();

    if (!$wakatimeData['is_up_to_date']) {
        exit("Wakatime data is not up to date yet. Please wait a few minutes.");
    }

} catch (GuzzleException $e) {
    echo $e->getMessage()."\n";
    exit(1);
}



$output = new BufferedOutput();

$categories = [
    'languages' => 'Programming Languages',
    'editors' => 'Editors',
    'operating_systems' => 'Operating Systems',
];

// Find the maximum number of rows among all categories
$maxRows = 0;
foreach ($categories as $dataKey => $categoryTitle) {
    $stats = $wakatimeData[$dataKey];
    $maxRows = max($maxRows, count($stats));
}

foreach ($categories as $dataKey => $categoryTitle) {
    $table = new Table($output);
    $stats = $wakatimeData[$dataKey];
    $table->setHeaderTitle("$readableRange Stats for $categoryTitle");
    $table->setHeaders([$categoryTitle, 'Total Hours']);

    foreach ($stats as $index => $stat) {
        $table->addRow([
            $stat["name"],
            new TableCell(
                $stat["text"],
                ['style' => new TableCellStyle(['align' => 'center'])]
            )
        ]);
    }

    // Pad the table with empty rows if necessary
    $rowCount = count($stats);
    for ($i = $rowCount; $i < $maxRows; $i++) {
        $table->addRow(['', '']);
    }

    $table->setColumnWidth(0, 15);
    $table->setColumnWidth(1, 30);
    $table->render();

    $output->writeln(''); // Add a new line after rendering the table
}

$resultsStats = trim($output->fetch());



$statsResult = "
### Wakatime Stats
```
{$resultsStats}
```";


$githubStatsUpdater = new GitHubStatsUpdater($githubToken);

try {
    $githubStatsUpdater->updateReadme($githubUsername, $githubRepositoryName, $statsResult);
} catch (GuzzleException $e) {
    echo $e->getMessage()."\n";
    exit(1);
}
