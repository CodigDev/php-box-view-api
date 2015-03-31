Box View API PHP SDK
================
**Unofficial PHP SDK for the [Box View API].**


*Created by [Romain Bruckert]

- - -

Documentation
-------------
For general API documentaion review the [Box View API Documentation](https://developers.box.com/view).


# Getting started

Include via composer or just get the repository files. You neex BoxApi and BoxDocument classes that work together.

```
...
"require": {
	"romainbruckert/php-box-view-api": "dev-master"
}
...
```


# General usage

After you have included the BoxApi.php and the BoxDocument classes in you PHP file, you can just set everything up and do...

```
require('lib/BoxApi.php');
require('lib/BoxDocument.php');

	$config = array('api_key' => 'blablablah');
	
	$document = new BoxDocument($config);
	
	$document->setName("Hello world");
	// ... and other manipulations here !
	
	// show every error that happened from every manipulation above
	print_r($document->getMessages()); // empty if no errors ! :-)
```

# Upload a document

Depending on if you set document URL or local path on the server, the class will choose the best upload method. If both are set, the URL upload method will be prefered (its quicker but does not handle very large file sizes).

```
$config = array('api_key' => 'blablablah');

$document = new BoxDocument($config);

	$document->setUrl('http://whatever.com/my-file.pdf');
	$document->setPath('/home/var/mywebsite/public/my-file.pdf');
