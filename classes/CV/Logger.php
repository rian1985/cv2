<?php
namespace CV;

use Slim\LogWriter;
use Slim\Log;

class Logger extends LogWriter
{
	
	private static $levelString = array(
				Log::DEBUG => 'DEBUG',
				Log::ERROR => 'ERROR',
				Log::FATAL => 'FATAL',
				Log::INFO => 'INFO ',
				Log::WARN => 'WARN '
			);
	
	private $outputFilePath;
	
	public function __construct($outputFilePath)
	{
		$this->outputFilePath = $outputFilePath;
	}
	
	public function write($message, $level)
	{
		$level = self::$levelString[$level];
		$message = implode(PHP_EOL . str_repeat(' ', 8), explode(PHP_EOL, $message));
		error_log("[$level] $message", 3, $this->outputFilePath);
	}
	
}