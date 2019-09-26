gyselroth Helper Library
========================

Version 0.1.4 - Not released yet
--------------------------------
* Used interface for non-private constants w/ multiple use
* Performance: Use more strict types, use more opcode caching
* Added overlooked php-ext dependency declarations in composer.json
* Added various helper methods and classes: HelperImage, HelperMongoDB  
* Added CONTRIBUTING.md
 
Version 0.1.3 - Released 2019/07/10
-----------------------------------
* Changed return value to given array instead an empty array in HelperArray::castSubColumn()


Version 0.1.2 - Released 2019/07/09 
-----------------------------------
* Added HelperArray::castSubColumn()


Version 0.1.1 - Released 2019/04/03 
-----------------------------------
* Added HelperFile::getMimeType; added "ext-fileinfo" to package requirements
* Change HelperArray::intImplode() argument type declaration: Allow all data-types 
* Changed HelperArray::intExplode() argument type declaration: Added possible null
* Extracted @todo comments into github issues


Version 0.1.0 - Released 2018/05/31 
-----------------------------------
* Initial Release
