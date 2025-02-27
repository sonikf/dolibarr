<?php
/* Copyright (C) 2023	Laurent Destailleur		<eldy@users.sourceforge.net>
 * Copyright (C) 2024		MDW							<mdeweerd@users.noreply.github.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/debugbar/class/DataCollector/DolLogsCollector.php
 *	\brief      Class for debugbar collection
 *	\ingroup    debugbar
 */

use DebugBar\DataCollector\MessagesCollector;
use Psr\Log\LogLevel;

//use ReflectionClass;

/**
 * DolLogsCollector class
 */

class DolLogsCollector extends MessagesCollector
{
	/**
	 * @var string default logs file path
	 */
	protected $path;
	/**
	 * @var int number of lines to show
	 */
	protected $maxnboflines;

	/**
	 * @var int number of lines
	 */
	protected $nboflines;

	/**
	 * Constructor
	 *
	 * @param string $path     Path
	 * @param string $name     Name
	 */
	public function __construct($path = null, $name = 'logs')
	{
		parent::__construct($name);

		$this->nboflines = 0;
		$this->maxnboflines = getDolGlobalInt('DEBUGBAR_LOGS_LINES_NUMBER', 250); // High number slows seriously output

		$this->path = $path ?: $this->getLogsFile();
	}

	/**
	 *	Return widget settings
	 *
	 *  @return array<string,array{icon?:string,indicator?:string,widget?:string,tooltip?:string|array{html:string,class:string},map:string,default:string}>		Array

	 */
	public function getWidgets()
	{
		global $langs;

		$title = $langs->transnoentities('Logs');
		$name = $this->getName();

		return array(
			"$title" => array(
				"icon" => "list-alt",
				"widget" => "PhpDebugBar.Widgets.MessagesWidget",
				"map" => "$name.messages",
				"default" => "[]"
			),
			"$title:badge" => array(
				"map" => "$name.count",
				"default" => "null"
			)
		);
	}

	/**
	 *	Return collected data
	 *
	 *  @return array{count:int,messages:string[]}  Array of collected data
	 */
	public function collect()
	{
		global $conf;

		$uselogfile =  getDolGlobalInt('DEBUGBAR_USE_LOG_FILE');

		if ($uselogfile) {
			$this->getStorageLogs($this->path);
		} else {
			$log_levels = $this->getLevels();

			foreach ($conf->logbuffer as $line) {
				if ($this->nboflines >= $this->maxnboflines) {
					break;
				}
				foreach ($log_levels as $level_key => $level) {
					if (strpos(strtolower($line), strtolower($level_key)) == 20) {
						$this->nboflines++;
						// Use parent method to add the message
						$this->addMessage($line, $level, false);
					}
				}
			}
		}

		return parent::collect();
	}

	/**
	 * Get the path to the logs file
	 *
	 * @return string
	 */
	public function getLogsFile()
	{
		// default dolibarr log file
		$path = DOL_DATA_ROOT.'/dolibarr.log';
		return $path;
	}

	/**
	 * Get logs
	 *
	 * @param 	string 	$path     	Path
	 * @return 	void
	 */
	public function getStorageLogs($path)
	{
		if (!file_exists($path)) {
			return;
		}

		// Load the latest lines
		$file = implode("", $this->tailFile($path, $this->maxnboflines));

		foreach ($this->getLogs($file) as $log) {
			$this->addMessage($log['line'], $log['level'], false);
		}
	}

	/**
	 * Get latest file lines
	 *
	 * @param string       $file	File
	 * @param int          $lines	Lines
	 * @return string[]				Array
	 */
	protected function tailFile($file, $lines)
	{
		$handle = fopen($file, "r");
		$linecounter = $lines;
		$pos = -2;
		$beginning = false;
		$text = array();
		while ($linecounter > 0) {
			$t = " ";
			while ($t != "\n") {
				if (fseek($handle, $pos, SEEK_END) == -1) {
					$beginning = true;
					break;
				}
				$t = fgetc($handle);
				$pos--;
			}
			$linecounter--;
			if ($beginning) {
				rewind($handle);
			}
			$text[$lines - $linecounter - 1] = fgets($handle);
			if ($beginning) {
				break;
			}
		}
		fclose($handle);
		return array_reverse($text);
	}

	/**
	 * Search a string for log entries into the log file. Used when debug bar scan log file (very slow).
	 *
	 * @param  string  $file							File
	 * @return list<array{level:string,line:string}>	Lines of log entries
	 */
	public function getLogs($file)
	{
		$pattern = "/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}.*/";
		$log_levels = $this->getLevels();
		$matches = array();
		preg_match_all($pattern, $file, $matches);
		$log = array();
		foreach ($matches as $lines) {
			foreach ($lines as $line) {
				foreach ($log_levels as $level_key => $level) {
					if (strpos(strtolower($line), strtolower($level_key)) == 20) {
						$log[] = array('level' => $level, 'line' => $line);
					}
				}
			}
		}
		$log = array_reverse($log);
		return $log;
	}

	/**
	 * Get the log levels from psr/log.
	 *
	 * @return array<string,string>	Array of log level
	 */
	public function getLevels()
	{
		// @phan-suppress-next-line PhanRedefinedClassReference	 // Psr/LogLevel also provided by Sabre setup
		$class = new ReflectionClass(new LogLevel());
		$levels = $class->getConstants();
		$levels['ERR'] = 'error';
		$levels['WARN'] = 'warning';

		return $levels;
	}
}
