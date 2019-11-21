gyselroth Helper Library
========================

Version 0.2.1 - Released 2019/21/19
-----------------------------------
* Added unit-test: Test PSR-4 autoload of interfaces

Version 0.2.0 - Released 2019/11/19
--------------------------------
* Used interface for non-private constants w/ multiple use
* Performance: Use more strict types and rel. identity comparisons, use more opcode caching
* Added overlooked php-ext dependency declarations in composer.json
* Added various helper methods and classes: HelperImage, HelperMongoDB, HelperTimerange  
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
