<?php

/**
 * @package F3 gravatar
 * @version 1.0
 * @author Jessica González <suki@missallsunday.com>
 * @copyright Copyright (c) 2017, Jessica González
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3
 */

class Gravatar extends \Prefab
{
	public $options = [
		's' => 80,
		'd' => 'identicon',
		'r' => 'g',
	];
	public $config = [];
	public $email = '';
	public $url = 'https://www.gravatar.com/avatar/';

	function __construct()
	{
		$f3=\Base::instance();

		$this->config = $f3->get('GRAVATAR');

		// Allow overwriting the default values.
		$this->options = $f3->exists('GRAVATAR.options') ? array_merge($this->options, $f3->get('GRAVATAR.options')) : $this->options;

		// Perhaps a different url?
		$this->url = $f3->exists('GRAVATAR.url') ? $f3->get('GRAVATAR.url') : $this->url;

		// Set the route.
		$f3->route(array('GET /gravatar/@email'), [$this,'route'], ($f3->exists('GRAVATAR.cache') ? $f3->get('GRAVATAR.cache') : 86400));
	}

	function get($email = '', $options = [])
	{
		$this->_options = array_merge($this->_options, $options);

		return $this->setUrl($email);
	}

	function setUrl($email = '')
	{
		return $this->url . md5(strtolower(trim($this->email))) .'?'.  http_build_query($this->options);
	}

	function route($f3, $args)
	{
		$email = isset($args['email']) ? $f3->clean($args['email']) : '';

		$gravatar = \Audit::instance()->email($email, ($f3->exists('GRAVATAR.mx') ?: false)) ? \Web::instance()->request($this->setUrl($email)) : [];

		if (!empty($gravatar) && $gravatar['headers'][0] == 'HTTP/1.1 200 OK')
		{
			header($gravatar['headers'][3]);
			echo $f3->read($gravatar['body']);
		}

		// No? then build a generic identicon.
		else
		{
			$img = new \Image();
			$img->identicon(\Web::instance()->slug($email));
			$img->render('jpeg',NULL,90);
			unset($img);
		}
	}
}
