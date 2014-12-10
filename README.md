csd-photo
=========

Image types
   - JPEG
   - TIFF
   - PNG
   - RAW FORMATS
   	- CR2, NEF, etc.

Image meta types
   - XMP
   - IPTC
   - EXIF

// USE CASE #1, modifying existing meta

$image = Image::fromFile($filename);

$xmp = $image->getXmp();
$xmp->setHeadline('A test headline');
$xmp->setCaption('Caption');

$image->save();


// USE CASE #2, generating standalone XMP

$xmp = new Xmp;
$xmp->setHeadline('A headline')
...

$data = $xmp->getXml();


// USE CASE #2.1, modifying standalone XMP

$xmp = new Xmp($data); // or Xmp::fromFile($filename)
$xmp->setHeadline('A headline');

$data = $xmp->getXml();


// USE CASE #3, setting own meta

$xmp = new Xmp;
$xmp->setHeadline('A headline');
...

$image = Image::fromFile($filename);
$image->setXmp($xmp);

$image->save() // or $image->getBytes()



// USE CASE #4, loading specific image type

$jpeg = JPEG::fromFile($filename);


// USE CASE #5, modify raw bytes, possibly stored in DB or ImageMagick or whatever...

$data = ...

#jpeg = new JPEG($data);
$jpeg->getXmp()->setHeadline('Test headline');

$data = $jpeg->getBytes();


// USE CASE #6, just want a piece of meta data, don't care whether it's from XMP/IPTC or even EXIF

$image = Image::fromFile($filename);
$headline = $image->getAggregateMeta()->getHeadline();

// USE CASE #7, set a piece of meta data across all

$image = Image::fromFile($filename);
$image->getAggregateMeta()->setHeadline($headline);

$image->save(); // or $image->getBytes()


// USE CASE #7, get GPS data

$image = ...
$gps = $image->getAggregateMeta()->getGPS(); // checks EXIF and XMP
// or $gps = $image->getExif()->getGPS();

$lat = $gps->getLatitude();



