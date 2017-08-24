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
	private $_cursor;

	public function __construct()
	{
		$this->f3 = \Base::instance();

		$this->options = [
			'file' => '../.env',
			'default' => null,
		];

		// Allow overwriting the default values.
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
				return; // Show some error or throw an exception, dunno.

			// Good old fopen to the rescue.
			$handle = fopen($file, "r");
			if ($handle)
			{
			    while (($line = fgets($handle)) !== false)
			    	$this->parse($line);

			    fclose($handle);
			}


	}

	protected function parse($line)
	{

	}

	protected function _setVar($name, $value = null)
	{

	}
}
