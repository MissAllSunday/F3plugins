<?php

/**
 * @package F3 form
 * @version 1.0
 * @author Jessica González <suki@missallsunday.com>
 * @copyright Copyright (c) 2017, Jessica González
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3
 */

class Form extends \Prefab
{
	public $options = [
		'group' => 'data',
		'prefix' => '',
	];
	public $items = [];
	protected $_counter = 0;

	function __construct()
	{
		$this->f3 = \Base::instance();

		// Allow overwriting the default values.
		$this->options = $f3->exists('FORM') ? array_merge($this->options, $f3->get('FORM');
	}

	function setOptions($options = array())
		{
			$this->options = array_merge($this->_options, $options);
		}

	function addElement($item)
	{
		// No text? use the name as a text key then!
		if (empty($item['text']))
			$item['text'] = $this->f3->get($this->options['prefix'] . $item['name']);

		// Give it a chance to use a full text string.
		if (empty($item['desc']))
			$item['desc']  = $this->f3->get($this->options['prefix'] . $item['name'] .'_desc');

		// Normalize element.
		$item['html'] = str_replace([
			'{name}',
			'{id}',
			'{class}'
		], [
			'name="'. ($this->options['group'] ? $this->options['name'] .'['. $item['name'] .']' : $item['name']) .'"',
			'id="form_'. $item['name'] .'"',
			(!empty($item['class']) ? $item['class'] : ''),
		], $item['html']);

		$this->items[++$this->_counter] = $item;
	}

	function getitems($id = 0)
	{
		return !empty($id) ? $this->items[$id] : $this->items;
	}

	function modifyElement($id = 0, $data = array())
	{
		if (empty($id) || empty($data) || empty($this->items[$id]))
			return false;

		$this->items[$id] = $data;
	}

	function addTextArea($item = array())
	{
		// Kinda needs this...
		if (empty($item) || empty($item['name']))
			return;

		$item['type'] = 'textarea';
		$item['value'] = empty($item['value']) ? '' : $item['value'];
		$rows = 'rows="'. (!empty($item['rows']) ? $item['size']['rows'] : 5) .'"';

		$item['html'] = '<'. $item['type'] .'  '. $rows .' class="form-control {class}" {name} {id}>'. $item['value'] .'</'. $item['type'] .'>';

		return $this->addElement($item);
	}

	function addCheck($item = array())
	{
		// Kinda needs this...
		if (empty($param) || empty($param['name']))
			return;

		$param['type'] = 'checkbox';
		$param['checked'] = empty($param['checked']) ? '' : 'checked="checked"';
		$param['disabled'] = !empty($param['disabled']) ? 'disabled' : '';

		$param['html'] = '<input type="'. $param['type'] .'" {name} {id} value="1" '. $param['checked'] .' class="{class}">';

		return $this->addElement($param);
	}
}
