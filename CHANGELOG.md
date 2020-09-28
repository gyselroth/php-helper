gyselroth Helper Library
========================

Version 0.3.00 - Not released yet
---------------------------------
* Extract HtmlPurifier filters into separate dependency
* Remove wrapper methods from HelperString redirecting to HelperPreg 

Version 0.2.21 - Released 2020/09/28
------------------------------------
* Remove composer branch alias

Version 0.2.20 - Released 2020/09/28
------------------------------------
* Add method: HelperArray::sortAssociative()
* Add method: HelperDate::renderIso8601Date()
* Add method: HelperDate::toZendDate()
* Add method: HelperServerClient::addToSessionArray()
* Add method: HelperServerClient::getRequestScheme()
* Add method: HelperString::compressHtml()
* Add method: HelperString::isBase64encodedString()
* Add method: HelperString::isHexadecimalHash()
* Add method: HelperString::lowerFirstAndLast()
* Add ZF1 reflection method: getPhpDocDescriptionOfControllerAction()
* Add ZF1 reflection method: getRequestParamsOfControllerAction()
* Add ZF1 reflection method: getSourceCodeOfControllerAction()
* Declare and use types more strict
* Extract and use loggerWrapper from external package instead of built-in

Version 0.2.19 - Released 2020/04/02
------------------------------------
* Add method: HelperTimerange::rangeStartsBeforeAndEndsInAllowedRange()
* Add method: HelperTimerange::rangeStartsInAndEndsAfterAllowedRange()

Version 0.2.18 - Released 2020/03/13
------------------------------------
* Extend HelperHtml::getCleanedHtml() w/ new option: $allowBase64images

Version 0.2.17 - Released 2020/02/17
------------------------------------
* Extend translate method with the parameter $escapeHtmlEntities

Version 0.2.16 - Released 2020/02/10
------------------------------------
* Add method validateStringImproved (improved version of validateString)

Version 0.2.15 - Released 2020/02/05
------------------------------------
* Update dependency: use smalot/pdfparser 0.14.0

Version 0.2.14 - Released 2020/02/04
------------------------------------
* HelperDate::getDateFromUnixTimestamp: Correct too narrow $format argument type declaration 
* Cleanup: Remove unused imports and private fields, reduce redundant code

Version 0.2.12, 0.2.13 - Released 2020/01/31
--------------------------------------------
* 0.2.13: Improve HelperImage: Second-check by detecting correct MIME in case of inapplicable given one
* 0.2.13: Add method: HelperImage::ensureCorrectImageFileExtension()
* 0.2.12: Add error logging 
* 0.2.12: Correct pathinfo/extension resolving in file- and image-helpers 

Version 0.2.10, 0.2.11 - Released 2020/01/30
--------------------------------------------
* 0.2.11: Bugfix - HelperImage::SaveThumbnail: Corrected file extension extraction
* 0.2.10: Language-level / upwards-compatibility: Update left-over use of deprecated curly brackets array access

Version 0.2.8, 0.2.9 - Released 2020/01/29
------------------------------------------
* 0.2.9: Improve HelperHtml::getCleanedHtml added option to escape double quotes
* 0.2.8: Improve PHP 7.4 compatibility (used vendor package zf1-future)

Version 0.2.5, 0.2.6, 0.2.7 - Released 2020/01/28
-------------------------------------------------
* 0.2.7: HelperNumeric::intImplode: Update deprecated implode()-arguments order  
* 0.2.6: Changed ZF1 dependency (required for Zend_Date handling): switched to "shardj/zf1-future"  
* 0.2.5: Improve thumbnail generation: Support more formats (bmp, gif, jpeg, png)

Version 0.2.4 - Released 2020/01/27
-----------------------------------
* Correct too narrow return type signals (added forgotten |null)
* Collect more sanitization methods into HelperSanitize 
* Mark HelperArray::isIterable() deprecated - Use PSL isIterable() instead
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
* Add unit-test: Test PSR-4 autoload of interfaces

Version 0.2.0 - Released 2019/11/19
--------------------------------
* Use interface for non-private constants w/ multiple use
* Performance: Use more strict types and rel. identity comparisons, use more opcode caching
* Add overlooked php-ext dependency declarations in composer.json
* Add various helper methods and classes: HelperImage, HelperMongoDB, HelperTimerange  
* Add CONTRIBUTING.md
 
Version 0.1.3 - Released 2019/07/10
-----------------------------------
* Change return value to given array instead an empty array in HelperArray::castSubColumn()

Version 0.1.2 - Released 2019/07/09 
-----------------------------------
* Add HelperArray::castSubColumn()

Version 0.1.1 - Released 2019/04/03 
-----------------------------------
* Add HelperFile::getMimeType; added "ext-fileinfo" to package requirements
* Change HelperArray::intImplode() argument type declaration: Allow all data-types 
* Change HelperArray::intExplode() argument type declaration: Added possible null
* Extract @todo comments into github issues

Version 0.1.0 - Released 2018/05/31 
-----------------------------------
* Initial Release
