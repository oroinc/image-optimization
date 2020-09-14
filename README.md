# OroImageOptimizationBundle

OroImageOptimizationBundle enables an administrator to control the quality of images using the UI, thereby reducing the size of images in storage.

## Configure Processors

### Libraries

OroImageOptimizationBundle supports the following libraries:
 * [pngquant](https://pngquant.org/) - utility for lossy compression of PNG images.
 * [jpegoptim](https://github.com/tjko/jpegoptim) - utility to optimize/compress JPEG files.

For proper work, you need libraries whose versions correspond to the following:
 * pngquant >= 2.5.0
 * jpegoptim >= 1.4.0

### Configuration for Setup

To configure the package, you need to add the following options to the parameters.yml:

``` yaml
  liip_imagine.pngquant.binary: /usr/bin/pngquant
  liip_imagine.jpegoptim.binary: /usr/bin/jpegoptim
```

> * Processors are external libraries, so they need to be installed separately.
> * If the configuration specifies the incorrect paths to the libraries, their versions do not match or libraries are not installed, the system will work without image processing, and these settings will not be available and will not be displayed in the system configuration.
> * If the configuration is not specified explicitly, the system will try to find libraries automatically and will log errors if the library is not found.

### UI Configuration

 * **JPEG Resize Quality (%)** - values from 30 to 100, the higher the value, the better the image quality.
 * **PNG Resize Quality (%)** - ‘Preserve quality’ and ‘Minimize file size’. Indicates how much you want to reduce the image quality.

Resources
---------

  * [OroCommerce, OroCRM and OroPlatform Documentation](https://doc.oroinc.com)
  * [Liip imagine](https://github.com/liip/LiipImagineBundle)
  * [pngquant](https://pngquant.org)
  * [jpegoptim](https://github.com/tjko/jpegoptim)
