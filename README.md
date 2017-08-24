### Gravatar
A very simple gravatar implementation.

Simplest use is as follows:
```php
\Gravatar::instance()->get('email');
```
This will return a formatted url using the default gravatar options.

The class also provides a generic F3 route named gravatar which accepts an email param:

```
GET /gravatar/@email
```

If no email param is provided or gravatar returns an error, the class will generate a generic identicon via \Image()->identicon();

To enable the route, put a call to \Gravatar::instance(); on your index.php file before the call to $f3->run(); or wherever you prefer.

You can overwrite the default options by setting a F3 hive var:

```php
	$f3->set('GRAVATAR', [
		'options' => [
			's' => 80,
			'd' => 'identicon',
			'r' => 'g',
		],
		'cache' => 86400,
		'url' => 'https://www.gravatar.com/avatar/',
		'mx' => false,
	]);
```

- options An array of gravatar options, gravatar support the following params:
  - s size
  - d default gravatar type
  - r gravatar rating
- cache If the GET /gravatar/ route is been used, this setting is used to set a F3 routing cache. Defaults to one day.
- url The url used to request a gravatar.
- mx boolean, defines the second param in \Audit::instance()->email() which checks for the email mx records.

You can also overwrite the default options on a per call basis, Gravatar::get() has a second argument which is an array of gravatar options:

```php
\Gravatar::instance()->get('email', [
	's' => 64, // size
	'd' => 'monster', // Default image
	'r' => 'pg', // rating
]);
```

### Form
A form generator. This plugin allow you to quickly build forms, the simplest use would be:

```php
$form = \Form::instance();
	$form->setOptions([
		'action' => 'signup',
	]);

	$form->addText([
		'name' => 'text',
		'value' => '',
		'text' => 'a full text description',
	]);

	$form->addButton([
		'text' => 'submit',
	]);

	$form->build();
```

By default the form has the following options:

```php
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
```
- group If set, the class will group all your fields into a POST array named after it.
- prefix All fields require a text key, however, if one isn't provided the class will look for a dictionary file var using the $f3 PREFIX and the field name:
 $element['text'] = $f3->get( $f3->get('PREFIX') . $this->options . $element['name']);
 - type bootstraps form types, horizontal or inline.
 - action An $f3 route where the POST data will be sent, needs to be an already registered route.
 - charset Defaults to $this->f3->get('ENCODING').
 - method post the method to sent the form as.
 - target the form target

Those options can be replaced by a $f3 hive var named FORM.

The only required option is the action field. This option needs to be set everytime you want to use the form generator using the method setOptions();

All created fields requires a name option, this is used to identify the field across the form and the resulting POST data.

Currently the form can create the following fields:

- text:
```php
	$form->addText([
		'name' => 'text',
		'value' => '',
		'text' => 'a full text description',
	]);
```
- textarea
```php
	$form->addTextArea([
		'name' => 'text',
		'value' => '',
		'text' => 'a full text description',
		'rows' => 5
	]);
```
- captcha This option uses $f3 own captcha generator. the field assumes you already have a ready to work captcha route.
```php
	$form->addCaptcha([
		'name' => 'text',
		'url' => '',
		'text' => 'a full text description',
	]);
```
- hidden field  A simple way to add hidden fields, the method accepts two strings, name and value.
```php
	$form->addHiddenField($name, $value);
```
- html  A generic way to inject direct HTML elements to the form. This field requires a special html key which contains the actual HTML you want to add
```php
$form->addHtml([
	'name' => 'text',
	'html' => 'some HTML here!',
	'text' => 'a full text description',
]);
```

All elements also accept the following params:
- extra a simple way to inject extra content to the fields, useful for adding data attribute data or to disable fields via the "disabled" bootstrap option
- class Used to add extra classes to the field
- desc Used to give each field a more detailed description.

To add a submit button call the addButton method:
```php
$form->addButton([
	'text' => 'submit',
]);
```
This method accepts a unique text param which is the text the button will display.

Once all the fields has been created, call the build method() to generate an $f3 hive var.

Include the form.html template file whenever you want the form to appear and thats it! enjoy your form.
