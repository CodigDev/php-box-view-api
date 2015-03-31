Box View API PHP SDK
================
**Unofficial PHP SDK for the [Box View API].**

* Integrates very easily with **Symfony 2** as vendor library.

* Not all of Box View API requests are supported for now. Here is the list: (upload, thumbnail, download Zip or Pdf assets, et document meta data and delete)

- - -

Documentation
-------------
For general API documentation review the [Box View API Documentation](https://developers.box.com/view).


## Getting started

Include via **composer** or just get the repository files. You need BoxApi and BoxDocument classes that work together.

```

	"require": {
		"romainbruckert/php-box-view-api": "1.0.*@dev"
	}
	...
```

## General usage

After you have included the **BoxApi.php** and the **BoxDocument.php** classes in you PHP file, you can just set everything up and do...

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

... or with as **Symfony 2** package...

```

	use RomainBruckert\BoxViewApi\BoxApi;
	use RomainBruckert\BoxViewApi\BoxDocument;

	$config = array('api_key' => 'blablablah');
	$document = new BoxDocument($config);
	// etc...
```

## Upload a document

Depending on if you set document URL or local path on the server, the class will choose the best upload method. If both are set, the URL upload method will be prefered (quicker from my experience but does not handle very large file sizes).

```

	$config = array('api_key' => 'blablablah');
	$document = new BoxDocument($config);

	// method 1
	$document->setUrl('http://whatever.com/my-file.pdf');

	// method 2
	$document->setPath('/home/var/whatever.com/public/my-file.pdf');
```

## Download document assets

Downloads original document in PDF or ZIP assets. Errors ares logges in $document->getMessages() if something unexpected happened.

```

	$config = array('api_key' => 'blablablah');
	$document = new BoxDocument($config);

	// You need an existing Box Api document id
	$document->setId('ghj5m8bn...jeb8h86h');

	$zipContents = $document->assets('zip');

	if($zipContents)
	{
		// fell free to save or extract the zip on your server...
		file_put_contents('/home/var/whatever.com/public/yeaaap/assets.zip', $zipContents);
	}
```

## Delete a document

Deletes the document from the Box View API.

```

	$config = array('api_key' => 'blablablah');
	$document = new BoxDocument($config);

	$success = $document->delete();
```


## Get thumbnail

Deletes the document from the Box View API.

```

	$config = array('api_key' => 'blablablah');
	$document = new BoxDocument($config);

	 // 16x16 output (the Box View APi has strange bugs with thumbs > 60 pixels from my experience)
	$image = $document->thumbnail(16, 16);
	
	// save your image on your server or somewhere
	// file_put_contents(...);
```
