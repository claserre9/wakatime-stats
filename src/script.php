<?php
//require 'vendor/autoload.php';
//
//use GuzzleHttp\Client;
//use GuzzleHttp\Exception\GuzzleException;
//use Symfony\Component\Console\Helper\Table;
//use Symfony\Component\Console\Output\BufferedOutput;
//
//
//$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
//$dotenv->safeLoad();
//
//
//$repositoryInfo = $_SERVER['GITHUB_REPOSITORY'] ?? $_ENV['GITHUB_REPOSITORY'];
//
//if(!$repositoryInfo) die("Repository info not provided");
//
//list($githubUsername, $repositoryName) = explode('/', $repositoryInfo);
//
//if(!$githubUsername || !$repositoryName) die("Repository info is incomplete");
//
//if($githubUsername !== $repositoryName) die("Repository is not the special one required. Must have <username>/<username>");
//
//$client = new Client([
//    'base_uri' => 'https://wakatime.com/api/v1/users/' . $_ENV['WAKATIME_USER_ID'] . '/',
//    'headers' => ['Authorization' => 'Bearer ' . $_ENV['WAKATIME_API_KEY']]
//]);
//
//try {
//    $output = new BufferedOutput();
//    $tableEditors = new Table($output);
//    $tableLanguages = new Table($output);
//    $tableOS = new Table($output);
//
//    $responses = GuzzleHttp\Promise\Utils::unwrap([
//        $client->getAsync('stats'),
//        $client->getAsync("https://api.github.com/repos/$githubUsername/$githubUsername/contents/README.md", [
//            'headers' => [
//                'Accept' => 'application/vnd.github+json',
//                'X-GitHub-Api-Version' => '2022-11-28',
//                'Authorization' => 'Bearer ' . $_ENV['GITHUB_TOKEN'],
//            ]
//        ]),
//    ]);
//
//    $wakatimeResponse = $responses[0];
//    $githubResponse = $responses[1];
//    $wakatimeData = json_decode($wakatimeResponse->getBody()->getContents(), true);
//
//    $wakatimeResults = $wakatimeData["data"];
//    $editorsStats = $wakatimeResults['editors'];
//    $languagesStats = $wakatimeResults['languages'];
//    $osStats = $wakatimeResults['operating_systems'];
//
//    $tableEditors->setHeaders(['Editor', 'Total Hour']);
//    foreach ($editorsStats as $editorsStat) {
//        $tableEditors->addRow([$editorsStat["name"], $editorsStat["text"]]);
//    }
//
//    // Top five languages
//    $tableLanguages->setHeaders(['Language', 'Total Hour']);
//    foreach ($languagesStats as $index => $languagesStat) {
//        $tableLanguages->addRow([$languagesStat["name"], $languagesStat["text"]]);
//        if ($index === 4) break;
//    }
//
//    $tableOS->setHeaders(["Operating System", "Total Hour"]);
//    foreach ($osStats as $osStat) {
//        $tableOS->addRow([$osStat["name"], $osStat["text"]]);
//    }
//
//    $tableOS->render();
//    $tableEditors->render();
//    $tableLanguages->render();
//
//    $tableOfContent = $output->fetch();
//
//    $tableOfContent = <<<STATS
//### Wakatime Stats
//```
//$tableOfContent
//```
//STATS;
//
//
//    $githubResult = json_decode($githubResponse->getBody()->getContents(), true);
//    $client = new Client(); // Reuse the client for subsequent requests
//    $contentResponse = $client->get($githubResult['download_url']);
//    $existingContent = $contentResponse->getBody()->getContents();
//
//    // Find the position of the comment [//]: # (wakatime-stats)
//    $wakatimeStatsPos = strpos($existingContent, '[//]: # (wakatime-stats)');
//
//    // If the comment is found, insert the tableOfContent below it
//    if ($wakatimeStatsPos !== false) {
//        $endMarkerPos = strpos($existingContent, '[//]: # (end-wakatime-stats)');
//
//        if ($endMarkerPos !== false) {
//            $newContent = substr($existingContent, 0, $wakatimeStatsPos) . "[//]: # (wakatime-stats)\n\n" . $tableOfContent . "\n\n[//]: # (end-wakatime-stats)\n\n" . substr($existingContent, $endMarkerPos + strlen('[//]: # (end-wakatime-stats)'));
//        } else {
//            echo "Failed to find the end marker [//]: # (end-wakatime-stats).";
//            exit; // Exit the script
//        }
//    } else {
//        echo "Comment marker [//]: # (wakatime-stats) not found in README.";
//        exit; // Exit the script
//    }
//
//    // Prepare the updated content to be sent to GitHub
//    $updatedContent = base64_encode($newContent);
//    $updateData = [
//        'message' => 'Update README with statistics',
//        'content' => $updatedContent,
//        'sha' => $githubResult['sha'], // SHA of the existing content
//    ];
//
//    // Make a PUT request to update the README file on GitHub
//    $updateResponse = $client->request('PUT', $githubResult['url'], [
//        'headers' => [
//            'Authorization' => 'Bearer ' . $_ENV['GITHUB_TOKEN'],
//        ],
//        'json' => $updateData,
//    ]);
//
//    if ($updateResponse->getStatusCode() === 200) {
//        echo "README file has been updated successfully on GitHub.";
//    } else {
//        echo "Failed to update README on GitHub. Status code: " . $updateResponse->getStatusCode();
//    }
//} catch (GuzzleException | Throwable $e) {
//    // Handle exceptions here
//    echo "An error occurred: " . $e->getMessage();
//}
