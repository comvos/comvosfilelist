# Using comvosfilelist as mm\_dam\_filelist replacement #

One goals of this extension is to replace  mm\_dam\_filelist in existing TYPO3 
installations. So after developing the first version it has been time to
check if it is possible to really replace existing plugins without even touching 
them. 

The short answer is ***YES***, it is possible.

From version 1.0.2 comvosfilelist comes with a basic compatibility with  
mm\_dam\_filelist. This compatibility could and should be developed further because until
now it only supports few features of mm\_dam\_filelist.

## Features working ##

- replaced plugin and prefixed "Old" to plugin name
- secure filenames/files
- flexform persists an can be used

mm\_dam\_filelist-flexform settings working:

- category 
- entries per page

## Features worth migrating ##

mm\_dam\_filelist had some features from which we don't think that they are neccessary in comvosfilelist.
Furthermore many things can be done in templates as we use twig. However there are some neat features 
we would like to see in future versions of comvosfilelist. If you have any additions 
or want to support development by coding or sponsoring a feature, please let us know by 
[email](info@comvos.de) or by opening an [issue/pull request on github](https://github.com/comvos/comvosfilelist/issues).

- searching
- setting list order in flexform and/or frontend

Right now there's no timeline for implementing these or any other features. 
Nonetheless you are invited to give feedback, express wishes or as mentioned above 
support us in some way.
