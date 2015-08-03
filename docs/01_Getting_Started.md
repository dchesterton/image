# Getting Started

The recommended way of installing the client is via [Composer](http://getcomposer.org/). Run the following command to
add the library to your composer.json file.

```
composer require dchesterton/image
```

## Supported image formats

Currently the library supports JPEG, ....

Each supported image format has its own class in the `CSD\Image\Format` namespace.

## Opening an image

### From a file

The easiest way to open a file is using the `CSD\Image\Image::fromFile` method, which will guess the correct file format
from the file's extension.

```php
use CSD\Image\Image;

$image = Image::fromFile('yourfile.jpg');
...
```

If you know the file format in advance or if the file name does not have an extension, you can use one of the specific
file format classes in the same way:

```php
use CSD\Image\Format\JPEG;
use CSD\Image\Format\PNG;

$jpeg = JPEG::fromFile('yourfile.jpg');
$png = PNG::fromFile('yourfile.png');
...
```

File format classes exist for all [supported file formats](#supported-file-formats).

### From a string

Sometimes you will have a file as a binary string, e.g. if you've stored it in a database. You can pass the file as the
first argument to any of the file type classes.

```php
use CSD\Image\Format\JPEG;

$data = get_my_image(); // fetched from database etc.
$image = JPEG::fromString($data);
...
```

### From a GD resource

You can add metadata to a GD resource using the library.

```
// example of creating an image with GD
$gd = imagecreate(100, 100);

$jpeg = JPEG::fromResource($gd);
```

### From a stream

If you already have a stream, e.g. an open file, you can pass the file too.

```
$file = fopen('...', 'r+');

$jpeg = JPEG::fromStream($file);
```
