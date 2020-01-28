gyselroth Helper Library
========================

Version 0.2.5 - Released 2020/01/28
-----------------------------------
* Improved thumbnail generation: Support more formats (bmp, gif, jpeg, png)


Version 0.2.4 - Released 2020/01/27
-----------------------------------
* Corrected too narrow return type signals (added forgotten |null)
* Collect more sanitization methods into HelperSanitize 
* Marked HelperArray::isIterable() deprecated - Use PSL isIterable() instead
* Method signatures cleanup: Remove unused arguments
* Clarify/make various type declarations and annotations more precise


Version 0.2.3 - Released 2020/01/07
-----------------------------------
* Correct formatting to follow PSR-2 more closely
* Performance: Root-namespace all PSL functions 
* More strict types: Type declarations and annotations in method signatures 
* Add phpstan config
* Add method: HelperDate::ensureSeparator() 
* Add method: HelperDate::getZendDateByDateString() 
* Add method: HelperImage::scaleJpegByData() 
* Add class: HelperSanitize
* Extract string rel. constants into interface: ConstantsEntitiesOfStrings


Version 0.2.2 - Released 2019/12/12
-----------------------------------
* Resolved #19: HelperServerClient::getClientIP() fails when called from CLI 
* Resolved #21: Add issue convention into CONTRIBUTING.md 


Version 0.2.1 - Released 2019/11/19
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
