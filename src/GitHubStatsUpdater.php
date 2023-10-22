<?php

namespace Claserre9\WakatimeStats;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GitHubStatsUpdater
{
    private Client $githubClient;
    private string $githubToken;

    public function __construct($githubToken)
    {
        $this->githubClient = new Client();
        $this->githubToken = $githubToken;
    }

    /**
     * @throws GuzzleException
     */
    public function updateReadme($githubOwner, $githubRepo, $tableOfContent)
    {
        $githubResponse = $this->githubClient->get("https://api.github.com/repos/$githubOwner/$githubRepo/contents/README.md", [
            'headers' => [
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
                'Authorization' => 'Bearer ' . $this->githubToken,
            ]
        ]);

        $githubResult = json_decode($githubResponse->getBody()->getContents(), true);

        // Find the position of the comment [//]: # (wakatime-stats)
        $existingContent = base64_decode($githubResult['content']);
        $wakatimeStatsPos = strpos($existingContent, '[//]: # (wakatime-stats)');

        if ($wakatimeStatsPos !== false) {
            $endMarkerPos = strpos($existingContent, '[//]: # (end-wakatime-stats)');
            if ($endMarkerPos !== false) {
                $newContent = substr($existingContent, 0, $wakatimeStatsPos) . "[//]: # (wakatime-stats)\n\n" . $tableOfContent . "\n\n[//]: # (end-wakatime-stats)\n\n" . substr($existingContent, $endMarkerPos + strlen('[//]: # (end-wakatime-stats)'));
            } else {
                echo "Failed to find the end marker [//]: # (end-wakatime-stats).";
                exit(1);
            }
        } else {
            echo "Comment marker [//]: # (wakatime-stats) not found in README.";
            exit(1);
        }

        $updatedContent = base64_encode($newContent);
        $updateData = [
            'message' => 'Update README with statistics',
            'content' => $updatedContent,
            'sha' => $githubResult['sha'],
        ];

        $updateResponse = $this->githubClient->request('PUT', $githubResult['url'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->githubToken,
            ],
            'json' => $updateData,
        ]);

        if ($updateResponse->getStatusCode() === 200) {
            echo "README file has been updated successfully on GitHub.";
            exit(0);
        } else {
            echo "Failed to update README on GitHub. Status code: " . $updateResponse->getStatusCode();
            exit(1);
        }
    }
}