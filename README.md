# WordPress External Image Fetcher
WordPress plugin to fetch images that do not exist locally from a remote site (usually live site).

## Why?
I frequently create local copies of live website to work on.  

They frequently have a large number of images in the media library that would often not be practical to download, but are important to be able to effectively work on the site.

This plugin allows you to specify the url of the live site, and then, if the image is not found locally, the url will be modified to the same image on the live site.

## Limitations
It's pretty dumb, if things change on the live site, images are deleted etc then they won't be found.

THere's probably a bit of a performance hit as every image is first checked to see if it exists locally first.  A future extension might be to save a list of images that do not exist locally to avoid the repeated file exists checks.  I've not found it to be an issue thus far though.

## Usage
1. Install Plugin
2. Activate Plugin
3. Go to `Settings` > `External Image Fixer` and enter the Live site URL (with no trailing slash) and hit `Save Changes`

And that's it.  Images not present locally will now be served from the external site.
