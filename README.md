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
