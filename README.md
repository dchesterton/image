CSD Photo
=========

Supported image types:
   - JPEG
   - TIFF
   - PNG
   - RAW FORMATS
   	- CR2, NEF, etc.

Supported image meta types:
   - XMP
   - IPTC
   - EXIF

// USE CASE #1, modifying existing meta

```php
$image = Image::fromFile($filename);

$xmp = $image->getXmp();
$xmp->setHeadline('A test headline');
$xmp->setCaption('Caption');

$image->save();
```

### Standalone XMP

#### Generating standalone XMP

```php
$xmp = new Xmp;
$xmp->setHeadline('A headline')
...

$data = $xmp->getXml();
```

#### Modifying standalone XMP

```php
$xmp = new Xmp($data); // or Xmp::fromFile($filename)
$xmp->setHeadline('A headline');

$data = $xmp->getXml();
```

### Setting/replacing XMP in image

```php
$xmp = new Xmp;
$xmp->setHeadline('A headline');
...

$image = Image::fromFile($filename);
$image->setXmp($xmp);

$image->save() // or $image->getBytes()
```

### Loading specific image type

When file type is known, you can load the file type directly.

```php
$jpeg = JPEG::fromFile($filename);
```

### Modify from raw bytes

You can also instantiate objects from the raw bytes

```php
$data = ...

#jpeg = new JPEG($data);
$jpeg->getXmp()->setHeadline('Test headline');

$data = $jpeg->getBytes();
```

### Aggregate metadata

When just want a piece of meta data and don't care whether it's from XMP/IPTC or even EXIF, you can use the aggregate meta object.

```php
$image = Image::fromFile($filename);
$headline = $image->getAggregateMeta()->getHeadline();
```

You can even modify meta data on an aggregate level

```php
$image = Image::fromFile($filename);
$image->getAggregateMeta()->setHeadline('Headline');

$image->save();
```

This would set the headline in both XMP and IPTC.

#### Get GPS data

$image = ...
$gps = $image->getAggregateMeta()->getGPS(); // checks EXIF and XMP
// or $gps = $image->getExif()->getGPS();

$lat = $gps->getLatitude();
