
# Laravel Identity Documents

  

[![Latest Version on Packagist][ico-version]][link-packagist]

[![Total Downloads][ico-downloads]][link-downloads]

[![StyleCI][ico-styleci]][link-styleci]

  

For general questions and suggestions join gitter:

  

[![Join the chat at https://gitter.im/werk365/identitydocuments](https://badges.gitter.im/werk365/identitydocuments.svg)](https://gitter.im/werk365/identitydocuments?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

  

Package that allows you to handle documents like passports and other documents that contain a Machine Readable Zone (MRZ). This package allows you to process images of documents to find the MRZ, parse the MRZ, parse the Visual Inspection Zone (VIZ) and also to find and return a crop of the passport picture (using face detection).

  
  

## Installation

  

Via Composer

  

``` bash

$ composer require werk365/identitydocuments

```

  

Publish config (optional)

``` bash

$ php artisan vendor:publish --provider="Werk365\IdentityDocuments\IdentityDocumentsServiceProvider"

```

## Configuration

### Services

The first important thing to know about the package is that you can use any OCR and or Face Detection API that you want. This package is not doing any of those itself.

#### Google Vision Service

Included with the package is a `Google` service class that will be loaded for both OCR and Face Detection by default. If you wish to use the Google service, no further configuration is required besides providing your credentials. To do this, make a service account and download the JSON key file. Then convert the JSON to a PHP array so it can be used as a normal Laravel config file. Your config file would have to be called `google_key.php`, be placed in the config folder and look like this:

```php
return [
"type" => "service_account",
"project_id" => "",
"private_key_id" => "",
"private_key" => "",
"client_email" => "",
"client_id" => "",
"auth_uri" => "",
"token_uri" => "",
"auth_provider_x509_cert_url" => "",
"client_x509_cert_url" => "",
];
```
#### Creating Custom Services
If you want to use any other API for OCR and/or Face Detection, you can make your own service, or take a look at our list of available services not included in the main package (WIP).

Making a service is relatively easy, if you want to make a service that does the OCR, all you have to do is create a class that implements `Werk365\IdentityDocuments\Interfaces\OCR`. Similarly, there is also a `Werk365\IdentityDocuments\Interfaces\FaceDetection` interface. To make creating custom services even easier you can use the following command:
```bash
$ php artisan id:service <name> <type>
```
Where `name` is the `ClassName` of the service you wish to create, and `type` is either `OCR`, `FaceDetection` or `Both`. This will create a new (empty) service for you in your `App\Services` namespace implementing the `OCR`, `FaceDetection` or both interfaces.

#### Using Custom Services
The usage section will go over how to use functionality provided by this package, but if you wish to use the static `IdentityDocument:all()` method, you will have to publish the package config and set the service values there. Default values are:
```php
use Werk365\IdentityDocuments\Services\Google;

return [
'ocrService' => Google::class,
'faceDetectionService' => Google::class,
];
```
  

## Usage

Create a new Identity Document with a maximum of 2 images (optional) in this example we'll use a POST request that includes 2 images on our example controller.
```php
use Illuminate\Http\Request;
use Werk365\IdentityDocuments\IdentityDocument;

class ExampleController {
	public function id(Request $request){
		$document = new IdentityDocument($request->front, $request->back);
	}
}
```

There are now a few things we can do with this newly created Identity Document. First of all finding and returning the MRZ:
```php
$mrz = $document->getMrz();
```

We can then also get a parsed version of the MRZ by using
```php
$parsed = $document->getParsedMrz();
```

As the MRZ only allows for A-Z and 0-9 characters, anyone with accents in their name would not get a correct first or last name from the MRZ. To (attempt to) find the correct first and last name on the VIZ part of the document, use:
```php
$viz = $document->getViz();
```
This will return an array containing both the found first and last names as well as a confidence score. The confidence score is a number between 0 and 1 and shows the similarity between the MRZ and VIZ version of the name. Please not that results can differ based on your system's iconv() implementation.

To get the passport picture from the document use:
```php
$face = $document->getFace()
```
This returns an `Intervention\Image\Image`

  

## Change log

  

Please see the [changelog](changelog.md) for more information on what has changed recently.

  

## Contributing

  

Please see [contributing.md](contributing.md) for details and a todolist.

  

## Security

  

If you discover any security related issues, please email <hergen.dillema@gmail.com> instead of using the issue tracker.

  

## Credits

  

-  [Hergen Dillema][link-author]

-  [All Contributors][link-contributors]

  

## License

  

. Please see the [license file](license.md) for more information.

  

[ico-version]: https://img.shields.io/packagist/v/werk365/identitydocuments.svg?style=flat-square

[ico-downloads]: https://img.shields.io/packagist/dt/werk365/identitydocuments.svg?style=flat-square

[ico-travis]: https://img.shields.io/travis/werk365/identitydocuments/master.svg?style=flat-square

[ico-styleci]: https://styleci.io/repos/281089912/shield

  

[link-packagist]: https://packagist.org/packages/werk365/identitydocuments

[link-downloads]: https://packagist.org/packages/werk365/identitydocuments

[link-travis]: https://travis-ci.org/werk365/identitydocuments

[link-styleci]: https://styleci.io/repos/281089912

[link-author]: https://github.com/HergenD

[link-contributors]: ../../contributors