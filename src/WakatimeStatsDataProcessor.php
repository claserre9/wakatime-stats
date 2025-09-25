<?php

declare(strict_types=1);

namespace Claserre9\WakatimeStats;

use Exception;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Output\BufferedOutput;

class WakatimeStatsDataProcessor
{
	private $wakatimeData;
	private $output;

	public function __construct($wakatimeData)
	{
		$this->wakatimeData = $wakatimeData;
		$this->output = new BufferedOutput();
	}

	/**
	 * @throws Exception
	 */
	public function checkData()
	{
		if (!$this->wakatimeData['is_up_to_date']) {
			throw new Exception("Wakatime data is not up to date yet. Please wait a few minutes.");
		}
	}

	/**
	 * @throws Exception
	 */
	public function generateStats(): string
	{
		$this->checkData();
		$categories = [
			'languages' => 'Programming Languages',
			'editors' => 'Editors',
			'operating_systems' => 'Operating Systems',
		];

		$tableStyle = $_SERVER['INPUT_TABLE_STYLE'] ?? $_ENV['TABLE_STYLE'] ?? 'default';
		if (!in_array($tableStyle, ["default", "box", "box-double"])) {
			$tableStyle = 'default';
		}

		$maxLanguages = (int)$_SERVER['INPUT_MAX_LANGUAGES'] ?? $_ENV['MAX_LANGUAGES'] ?? 5;
		if (!is_numeric($maxLanguages) || $maxLanguages < 1) {
			$maxLanguages = 5;
		}

		foreach ($categories as $dataKey => $categoryTitle) {
			$table = new Table($this->output);
			$table->setStyle($tableStyle);
			$stats = $this->wakatimeData[$dataKey];
			$table->setHeaderTitle($this->getHeaderTitle($categoryTitle));
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
			$this->output->writeln('');
			$this->output->writeln('');
		}

		return $this->formatStats();
	}

	private function getHeaderTitle($categoryTitle): string
	{
		return "{$this->wakatimeData['range']} Stats for $categoryTitle";
	}

	private function formatStats(): string
	{
		$resultsStats = trim($this->output->fetch());
		return "### Wakatime Stats\n```\n$resultsStats\n```";
	}
}

