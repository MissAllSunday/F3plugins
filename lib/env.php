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
	public function __construct()
	{
		$this->f3 = \Base::instance();

		$this->options = [
			'file' => '../',
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

		// Some light checks
		foreach ($files as $file)
		{
			if (is_dir($file) || !is_readable($file))
				return; // Show some error or throw an exception, dunno.
		}
	}

	protected function _setVar($name, $value = null)
	{

	}
}
