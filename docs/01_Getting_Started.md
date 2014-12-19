# Getting Started

The recommended way of installing the client is via [Composer](http://getcomposer.org/). Run the following command to
add the library to your composer.json file.

    composer require dchesterton/image

## Supported file types

Currently the library supports JPEG, ....

## Opening an image

### From a file

The easiest way to open a file is using the `CSD\Image\Image::fromFile` method, which will guess the correct file type
class from the file's extension.

    use CSD\Image\Image;

    $image = Image::fromFile('yourfile.jpg');
    ...

If you know the file format in advance or if the file name does not have an extension, you can use one of the specific
file type classes in the same way:


    use CSD\Image\Type\JPEG;
    use CSD\Image\Type\PNG;

    $jpeg = JPEG::fromFile('yourfile.jpg');
    $png = PNG::fromFile('yourfile.png');
    ...

File type classes exist for all [supported file types](#Header2).

### From a string

Sometimes you will have a file as a binary string, e.g. if you've stored it in a database. You can pass the file as the
first argument to any of the file type classes.

    use CSD\Image\Type\JPEG;

    $data = get_my_image(); // fetched from database etc.
    $image = new JPEG($data);
    ...