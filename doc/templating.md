# Templating with twig and comvosfilelist #

As template engine comvosfilelist is using twig instead of fluid or markertemplates. 
Feature richness, documentation and stability are the main reasons for that. The 
twig documentaion can be found on the [project homepage](http://twig.sensiolabs.org/).

Comvosfilelist extends twig with some special template filters and functions, which
are explained in this document.

## General TYPO3 features ##

There are basic TYPO3-Features needed to be bridged into twig templates. They 
result in the following filters and functions:

**Functions:**

- typolink
- previewImage
- includeStylesheet
- includeJavascript
- overwritePageTitle
- overwritePageDescription

**Filters:**

- t3trans
- t3webpath

### Functions ###

#### typolink ####

Generates a TYPO3 page url using the *cObject->typolink* method. 

**Example:**

In this example a link is generated using the variable *singlePageId* defined in 
typoscript. Besides some static parameters, the filename is added as a parameter.
The filename is first translated into a relative path by the filter *t3webpath* and 
encrypted by the filter *t3crypt*.

`typolink(conf.singlePageId,{'additionalParams':'&tx_comvosfilelist_pi1[action]=show&tx_comvosfilelist_pi1[file]=' ~ file|t3webpath|t3crypt }) %}`

#### previewImage ####

Generates a thumbnail using *t3lib_stdGraphic->imageMagickConvert* method.

The first parameter is the absolute path of the desired file.

The second parameter is an array containing the following main options:

- maxW - maximum width
- maxH - maximum height
- height - definite height
- width - definite width
- folder - subfolder to typo3temp to be used, important if you want to use security features
- params - to be passed to *imageMagickConvert*


**Example:**

`previewImage(file.realpath,{ 'folder' : 'comvosfilelist/' ~ pageid,'maxW': 150 })`

Notes about the example:

- using `'comvosfilelist/' ~ pageid` as relative folder lets the extension 
determine access rights on demand.
- file variables within the twig templates are objects of type 
[SplFileInfo](http://php.net/manual/en/class.splfileinfo.php). So you have some
nice helpers.

#### includeStylesheet and includeJavascript ####

Including style sheets and javascript in extensions the old TYPO3 way issnt nice as
you have to do this in what's next to a controller: the pi-class.

Moving this into the template, you can now add your prefered stylesheet easily 
from within you view.

**Example:**

`{{ includeStylesheet('/typo3conf/ext/comvosfilelist/templates/css/comvosfilelist-default.css') }}`

`{{ includeJavascript('/fileadmin/templates/comvosfilelist/default/js/comvosfilelist.js') }}`

**Notice:** from version 1.0.3 on you can use paths relative to your template like 'css/comvosfilelist-default.css'


#### overwritePageTitle and overwritePageDescription ####

When it comes to lists with single views changing the page title and meta description 
is a commonly needed feature. It's another feature you might preferably do in your 
own template with your own kind of logic. With these to template functions this is 
very easy, as they overwrite the TSFE->page propertys.

**Example:**

`{{ overwritePageTitle(file.meta.title ~ ' - ' ~ tsfe.page.title) }}`

`{{ overwritePageDescription(file.meta.description) }}`

Notes about this example:

- file.meta is only available when using DAM
- The page title is set to the file's DAM title and page title separated by "-"

### Filters ###

#### t3trans ###

This filter lets you use the plugins translations by applying it to the default 
text and passing the translation key to the filter.

**Example:**

`<a href="{{ file|t3webpath }}">{{ 'download now'|t3trans('download') }}</a>`


#### t3webpath ####

Translates an absolute filename to a path relative to the TYPO3 document root.

**Example:**

`<a href="{{ file|t3webpath }}">{{ 'download'|t3trans('download') }}</a>`

## Filelist specific features ##

To keep things well encapsulated and clean there's a second twig extension for 
features only needed for this extension. Until now the twig extension only 
contains encryption funcionallity to hide filenames and generate secure link.


Two filters are added additionally:

### t3securefile ###

The **t3securefile** filter generates a link matching the secure filelink pattern
described in the [installation documentation](../README.md)

**Example:**

`<img src="{{ thumb|t3securefile }}" alt="{{ file.meta.title }}" title="{{ file.meta.title }}">`


### t3crypt ###

The **t3crypt** filter encrypts the filename when passed to the single view "action".

**Example:**

`typolink(conf.singlePageId,{'additionalParams':'&tx_comvosfilelist_pi1[action]=show&tx_comvosfilelist_pi1[file]=' ~ file|t3webpath|t3crypt }) %}`


----------

----------

The TYPO3 extension „comvosfilelist“ is being developed by the web agency comvos online medien GmbH.


As a TYPO3 and Magento agency, we specialize in creating shop solutions using Magento, as well as webpages using the CMS system TYPO3.

[http://www.comvos.de/](http://www.comvos.de/)

----------


Die Magento Extension "comvosfilelist" wird von der Werbeagentur comvos online medien GmbH entwickelt .

Als [TYPO3 Agentur](http://www.typo3-integration.de/typo3-agentur.html) und [Magento Agentur](http://www.comvos.de/magento-agentur.html "Magento Agentur") sind wir speziealisiert auf Shop Lösungen mit Magento sowie Webseiten mit dem CMS System TYPO3.

[http://www.comvos.de/](http://www.comvos.de/)