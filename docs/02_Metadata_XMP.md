# XMP Metadata

The [Extensible Metadata Platform](http://en.wikipedia.org/wiki/Extensible_Metadata_Platform) (XMP) standard is a
modern, XML-based way of storing metadata in images supported by most image formats.

## Setting metadata

The class supports most common XMP metadata properties.

n.b. The class provides a [fluent interface](http://en.wikipedia.org/wiki/Fluent_interface#PHP) for setting multiple
properties consecutively.

```php
$xmp = new Xmp;

$xmp->setTitle('Title')
    ->setDescription('Description')
    ->set...;
```

### Custom elements

XMP is an XML-based format. If you have custom XMP elements which are not supported by the class, you can access the
underlying [`DomDocument`](http://php.net/manual/en/class.domdocument.php) object where you can modify the XML.

```php
$xmp = new Xmp;
$xmp->setTitle('title'); // add title using the class method

$dom = $xmp->getDom();
$dom->createElement(...); // add custom element

$string = $xmp->getString(); // will contain any custom elements you add
```

