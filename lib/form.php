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
	public $options = [];
	public $items = [];
	public $buttons = [];
	private $f3;

	public function __construct()
	{
		$this->f3 = \Base::instance();

		$this->options = [
			'group' => 'data',
			'prefix' => '',
			'type' => 'horizontal',
			'action' => '',
			'charset' => $this->f3->get('ENCODING'),
			'enctype' => 'multipart/form-data',
			'method' => 'post',
			'target' => '_self',
		];

		// Allow overwriting the default values.
		if ($this->f3->exists('FORM'))
			$this->options = array_merge($this->options, $this->f3->get('FORM'));
	}

	public function setOptions($options = [])
	{
		$this->options = array_merge($this->options, $options);
	}

	public function button($button)
	{
		return $this->buttons[] = $button;
	}

	public function element($item)
	{
		// No text? use the name as a text key then!
		if (empty($item['text']))
			$item['text'] = $this->f3->get($this->options['prefix'] . $item['name']);

		// Give it a chance to use a full text string.
		$item['desc']  = !empty($item['desc']) ? $item['desc'] : '';

		$item['id'] = 'form_'. $item['name'];

		// Normalize element.
		$item['html'] = str_replace([
			'{name}',
			'{id}',
			'{class}',
			'{extra}',
		], [
			'name="'. ($this->options['group'] ? $this->options['group'] .'['. $item['name'] .']' : $item['name']) .'"',
			'id="'. $item['id'] .'"',
			(!empty($item['class']) ? $item['class'] : ''),
			(!empty($item['extra']) ? $item['extra'] : ''),
		], $item['html']);

		return $this->items[$item['name']] = $item;
	}

	public function addTextArea($item = [])
	{
		// Kinda needs this...
		if (empty($item) || empty($item['name']))
			return;

		$item['type'] = 'textarea';
		$item['value'] = empty($item['value']) ? '' : $item['value'];
		$rows = 'rows="'. (!empty($item['rows']) ? $item['rows'] : 5) .'"';

		$item['html'] = '<'. $item['type'] .'  '. $rows .' class="form-control {class}" {name} {id} {extra}>'. $item['value'] .'</'. $item['type'] .'>';

		return $this->element($item);
	}

	public function addHtml($item = [])
	{
		// Kinda needs this...
		if (empty($item) || empty($item['name']))
			return;

		$item['type'] = 'html';

		return $this->element($item);
	}

	public function addCaptcha($item = [])
	{
		// Kinda needs this...
		if (empty($item) || empty($item['name']))
			return;

		$item['type'] = 'captcha';

		$item['html'] = '<input type="'. $item['type'] .'" {name} {id} class="form-control {class}" value="'. $item['value'] .'" {extra}>';

		return $this->element($item);
	}

	public function addHiddenField($name, $value)
	{
		$item['type'] = 'hidden';
		$item['name'] = $name;
		$item['html'] = '<input type="'. $item['type'] .'" {name} {id} value="'. $value .'" />';
		return $this->element($item);
	}

	public function addText($item = [])
	{
		// Kinda needs this...
		if (empty($item) || empty($item['name']))
			return;

		$item['type'] = 'text';

		$item['html'] = '<input type="'. $item['type'] .'" {name} {id} class="form-control {class}" value="'. $item['value'] .'" {extra}>';

		return $this->element($item);
	}

	public function addPassword($item = [])
	{
		// Kinda needs this...
		if (empty($item) || empty($item['name']))
			return;

		$item['type'] = 'password';

		$item['html'] = '<input type="'. $item['type'] .'" {name} {id} class="form-control {class}" value="'. $item['value'] .'" {extra}>';

		return $this->element($item);
	}

	public function addCheck($item = [])
	{
		// Kinda needs this...
		if (empty($item) || empty($item['name']))
			return;

		$item['type'] = 'checkbox';
		$item['checked'] = empty($item['checked']) ? '' : 'checked="checked"';

		$item['html'] = '<input type="hidden" {name}  value="0" /><input type="'. $item['type'] .'" {name} {id} value="1" '. $item['checked'] .' class="{class}" {extra}>';

		return $this->element($item);
	}

	public function addRadio($item = [])
	{
		// Kinda needs this...
		if (empty($item) || empty($item['name']))
			return;

		$item['type'] = 'radio';
		$item['checked'] = empty($item['checked']) ? '' : 'checked="checked"';
		$item['disabled'] = !empty($item['disabled']) ? 'disabled' : '';
		$item['value'] = !empty($item['value']) ? $item['value'] : '';
		$item['inline'] = !empty($item['inline']);

		$item['html'] = '<input type="hidden" {name} value="0" /><input type="'. $item['type'] .'" {name} {id} value="'. $item['value'] .'" '. $item['checked'] .' class="{class}" {extra}>';

		return $this->element($item);
	}

	public function addRadios($item = [])
	{
		// Kinda needs this...
		if (empty($item) || empty($item['name']))
			return;

		$item['type'] = 'radios';
		$item['html'] = '';

		return $this->element($item);
	}

	public function addButton($item = [])
	{
		$button = [
			'type' => 'input',
			'class' => 'btn-default',
			'text' => '',
			'extra' => '',
		];

		return $this->button(array_merge($button, $item));
	}

	public function addList($item = [])
	{
		$item['html'] = '<select {name} {id} class="{class}">';

		foreach ($item['options'] as $value => $name) {
			$item['html'] .= '<option value="' . $value . '">' . $name . '</option>';
		}
		$item['html'] .= '</select>';

		return $this->element($item);
	}

	public function build($customVar = '')
	{
		$this->f3->set(($customVar ?: '_form'), [
			'options' => $this->options,
			'items' => $this->items,
			'buttons' => $this->buttons,
		]);
	}

	public function getCounter()
	{
		return count($this->items);
	}

	public function getitems($id = 0)
	{
		return !empty($id) ? $this->items[$id] : $this->items;
	}

	public function modifyElement($id = 0, $data = array())
	{
		if (empty($id) || empty($data) || empty($this->items[$id]))
			return false;

		$this->items[$id] = $data;
	}
}
