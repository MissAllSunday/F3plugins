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

	public function loadString($string = "", $returnValues = true)
	{
		// Need something to work with.
		if (empty($string))
			return false;

		$line = strtok($string, $this->options['separator']);

		while ($line !== false)
			$this->parse($line);

		return $this->_buffer;
	}

	protected function parse($line)
	{
		$line = trim($line);

		// No empty lines. No comments. No invalid variables
		if (empty($line) || $this->_isComment($line))
			return;

		list($variable, $value) = array_map('trim',explode('=', $line));

		// Be good
		$value = $this->sanitizeValue($value);
		$variable = $this->sanitizeVariable($variable);

		$this->_buffer[$variable] = $value;

		return $this->_buffer[$variable];
	}

	protected function sanitizeVariable($variable)
	{
		// Is it a valid PHP var?
		if (!$this->_isInvalid($variable))
			return null;

		return $variable;
	}

	protected function sanitizeValue($value)
	{
		// Remove comments after var definition
		$value = $this->_rstrstr($value, '#');

		// String?
		if ($this->_findQuote($value))
			$value = str_replace($value[0], '', $value);

		// No? then no spaces!
		else
			$value = preg_replace('/\s+/', '', $value);

		return $value;
	}

	protected function _isComment($line)
	{
		// Should we be using {} or []? the world will never know!
		return (bool) isset($line[0]) && $line[0] === '#';
	}

	protected function _findQuote($value)
	{
		return isset($value[0]) && ($value[0] === '"' || $value[0] === '\'');
	}

	protected function _isInvalid($variable)
	{
		// Line starts with a number
		if (ctype_digit($variable[0]))
			return false;

		// Needs to pass the regex from PHP manuals
		if (!preg_match('/^[a-zA-Z\x7f-\xff][a-zA-Z0-9\x7f-\xff]*/', $line))
			return false;

		return true;
	}

	protected function _rstrstr($haystack,$needle)
	{
		return substr($haystack, 0,strpos($haystack, $needle));
	}

	protected function _setVar($name, $value = null)
	{
		$_ENV[$name] = $value;
		$_SERVER[$name] = $value;

		if (function_exists('putenv'))
			putenv("$name=$value");
	}
}
