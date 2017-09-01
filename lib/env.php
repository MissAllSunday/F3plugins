<?php

/**
 * @package F3 env
 * @version 1.0
 * @author Jessica González <suki@missallsunday.com>
 * @copyright Copyright (c) 2017, Jessica González
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3
 */

class Env extends \Prefab
{
	private $f3;
	public $options = [];
	private $_buffer = [];

	public function __construct()
	{
		$this->f3 = \Base::instance();

		$this->options = [
			'file' => '../.env',
			'separator' => PHP_EOL,
		];

		// Allow overwriting the default values
		if ($this->f3->exists('env_options'))
			$this->options = array_merge($this->options, $this->f3->get('env_options'));
	}

	public function load($files = "")
	{
		// Work with arrays
		$files = (array) $files;

		// No files? use the default one
		$files = $files ?: $this->options['file'];

		// Some light checks
		foreach ($files as $file)
		{
			if (is_dir($file) || !is_readable($file))
				return; // Show some error or throw an exception, dunno

			// Good old fopen to the rescue.
			$handle = fopen($file, "r");

			// To the infinity and beyond... NOT!
			if ($handle)
				while (($line = fgets($handle)) !== false)
					$this->parse($line);

			fclose($handle);
		}
	}

	public function loadString($string = "")
	{
		// Need something to work with.
		if (empty($string))
			return false;

		$line = strtok($string, $this->options['separator']);

		while ($line !== false)
			$this->parse($line);
	}

	protected function parse($line)
	{
		$line = trim($line);

		// No empty lines. No comments. No invalid variables
		if (empty($line) || $this->_isComment($line) || $this->_isInvalid($line))
			return;

		list($variable, $value) = array_map('trim',explode('=', $line));

		// Remove comments after var definition
		$value = $this->_rstrstr($value, '#');

	}

	protected function _isComment($line)
	{
		return (bool) isset($line[0]) && $line[0] === '#';
	}

	protected function _isInvalid($line)
	{
		// Line starts with a number
		if (ctype_digit($line[0]))
			return false;

		// Needs to pass the regex from PHP manuals
		if (!preg_match('/^[a-zA-Z\x7f-\xff][a-zA-Z0-9\x7f-\xff]*/', $line))
			return false;
	}

	protected function _setVar($name, $value = null)
	{

	}

	protected function _rstrstr($haystack,$needle)
	{
		return substr($haystack, 0,strpos($haystack, $needle));
	}

	protected function sanitizeVariable($variable)
	{
		// Do something here

		return $variable;
	}

	protected function sanitizeValue($value)
	{
		$value = $this->_rstrstr($value, '#');

		return $value;
	}
}
