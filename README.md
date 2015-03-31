Box View API PHP SDK
================
**Unofficial PHP SDK for the [Box View API].**


*Created by [Romain Bruckert]

- - -

Documentation
-------------
For general API documentaion review the [Box View API Documentation](https://developers.box.com/view).


## Getting started

Include via composer or just get the repository files. You neex BoxApi and BoxDocument classes that work together.

```
"require": {
	"romainbruckert/php-box-view-api": "dev-master"
}
...
```

## General usage

After you have included the BoxApi.php and the BoxDocument classes in you PHP file, you can just set everything up and do...

```
require('lib/BoxApi.php');
require('lib/BoxDocument.php');

	$config = array('api_key' => 'blablablah');
	$document = new BoxDocument($config);
	
	$document->setName("Hello world");
	// ... and other manipulations here !
	
	// show every error that happened from every manipulation above (empty if none)
	print_r($document->getMessages());
```

## Upload a document

Depending on if you set document URL or local path on the server, the class will choose the best upload method. If both are set, the URL upload method will be prefered (quicker from my experience but does not handle very large file sizes).

```
$config = array('api_key' => 'blablablah');
$document = new BoxDocument($config);

	$document->setUrl('http://whatever.com/my-file.pdf');
	$document->setPath('/home/var/whatever.com/public/my-file.pdf');
```

## Download document assets

Downloads original document in PDF or ZIP assets. Errors ares logges in $document->getMessages() if something unexpected happened.

```
$config = array('api_key' => 'blablablah');
$document = new BoxDocument($config);

	// You need an existing id
	$document->setId('ghj5m8bn...jeb8h86h');

	$zipContents = $document->assets('zip');

	if($zipContents)
	{
		// fell free to save or extract the zip on your server...
		file_put_contents('/home/var/whatever.com/public/yeaaap/assets.zip', $zipContents);
	}

```
