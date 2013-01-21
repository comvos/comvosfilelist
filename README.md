# comvosfilelist, a TYPO3 filelist extension with dam support and focus on security #

When it comes to filelists in TYPO3 we at comvos were
unhappy with the existing solutions concerning security,
feature-richness and quality of code.


For example mm_dam_filelist was/is pretty powerfull but lacks good transparent
security policy and codebase istn't that pretty. There are many others like
fefilebrowser with good intentions but big disadvantages. Comvosfilelist shall
be an alternative. Right now it does not have a lot of features, but it should
be a good, clean and secure start for a feature-rich filelist.


## Features ##

- security layer for fileaccess
- fileaccess is determined by page access rights (transparent and easy)
- secure thumbnails
- easy templating using twig
- usable with or without DAM
- observe directories with reports module to check security regularly


## Future and Development ##

We are very motivated in supporting and improving this extension continuously. So please contact us in case of any bugs or feature requests. Using Github to make this process transparent and documented would be nice.

## About the extension-structure ##

Developing this extension we decided to go a new/different way, not using extbase or old pi-base extension structure. Even though extension is stable, it's also an experiment in the matter of structuring the code and the components used.

### Why we did't use extbase ###

After developing several extensions in extbase we experienced several disadvantages by doing so.

- Unfixed bugs in extbase stayed unfixed for a long time
- Performance pretty bad data-rich, complex systems even though optimizations have been made
- pretty far behind latest flow releases (what about Doctrine?)
- fluid has some very rough edges

### Why doctrine and twig ###

Instead of extbase we decided to use symfony2 components using composer to build the extension. We only use Doctrine DBAL to ease database access ( keep complexity and dependency low ). Twig is very powerful, has excellent documentation, and is easy to use and extend.

The controller functionality of the extension is provided by an oldschool pi-class bringing the components together.

The idea is: Extbase as well as 100% TYPO3-pi-base way might bring a lot of migration problems when updating to a new major release based on TYPO3 Flow. Building the extension with as little external dependencies as possible using state of the art components might be a better way.


Furthermore it's an experiment about if this could be done in reasonable time and if it pays out in the end. Until now everything is looking pretty promising.

## Installation ##

When fetching the extension from github remember to install dependencies via [composer](http://getcomposer.org/)!

### 1. secure your files ###

First thing you should do is think about is security.


Most importent Question is:

***Which folders shall have restricted fileaccess?***


As soon as you know this you have to protect the folders with your http-server, so the server returns a 403 response when trying to access a file in the directory.

***You also have to protect "typo3temp/comvosfilelist"***


### 2. Setup your webserver for streaming ###

By default comvosfilelist doesn't use the real filename when linking files or thumbnails. Generated URLs have a defined structure that must be rewriten internally to enable TYPO3 / comvosfilelist to handle the file-streaming.

Pattern is:

`/comvosfilelist/<pageid>/<encryptedfilename>/<cHash>`

With apache rewrite configuration looks like this:

`RewriteRule ^/comvosfilelist/(.*)/(.*)/(.*) /index.php?id=$1&tx_comvosfilelist_pi1[action]=stream&tx_comvosfilelist_pi1[file]=$2&cHash=$3 [L]`


### 3. Installing the extension ###

After installing the extension by extension manager, you have to make some important settings.

1. The encryption key for the server-side filename encryption, to hide real filenames.
2. A comma-seperated list containing all folders, that have to be protected (used by the reports-module to check directoryprotection)
3. The hostname that should be used by the statusreport to check directoryprotection. (remember to change when migrating from development enviroments)


In the reports section of your TYPO3-Installation there should now be a section providing information about your protected folders and the "fileadmin"-folder which should be reachable any time. If "fileadmin"-folder is reported unreachable you might have entered a wrong hostname in the extension configuration.

## Configuration ##

If everything worked out fine during the installation. You should now be able to add the new contentelement "Extended Filelist" to your pages.

Within the plugin's flexform you have few configuration options.

### 1. Use DAM ?###

Wether or not to use DAM-Information. can also be set by typoscript setup:

`useDAM = false`

### 2. Template ###

The template to be used to render the filelelist. There are two templates(listview and singleview) that have to be present in one folder, which ist configured in typoscript-setup.

The default folder is defined in:

`templateFolders.default`

You can add more by defining other keys than *default*. For example:

`templateFolders.MyUniqueTemplateName `

To make them selectable in the flexform you have to add some lines to the **Page-TS** like:

`TCEFORM.tt_content.pi_flexform.comvosfilelist_pi1.sDEF.template {

  # Add templates to flexform dropdown
  addItems {    
    MyUniqueTemplateName = My template's descriptive title
  }

}`


### Entries per page ###

Defines how many files are shown on each page, you can leave this empty to use the default typoscript setup:

`entriesPerPage = 30`

### Filesource - DAM-Category or Directory ###

Finally you have to configure the filesource for the plugin. It can be either a DAM-Category or a directory. If you select a DAM-Category it's your responsability all files in the category are within protected folders.

### Fileaccess ###

As the plugin streams the file to the client, setting the page access rights to the right feuser-group grants access to the listet files. ***If you dont't protect your page/plugin, your files are not protected either.***

----------

----------
Die Magento Extension "comvosfilelist" wird von der Werbeagentur comvos online medien GmbH entwickelt .

Als [TYPO3 Agentur](http://www.typo3-integration.de/typo3-agentur.html) und [Magento Agentur](http://www.comvos.de/magento-agentur.html "Magento Agentur") sind wir speziealisiert auf Shop Lösungen mit Magento sowie Webseiten mit dem CMS System TYPO3.

[http://www.comvos.de/](http://www.comvos.de/)