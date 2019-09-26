<?php

/**
 * Copyright (c) 2017-2019 gyselroth™  (http://www.gyselroth.net)
 *
 * @package \gyselroth\Helper
 * @author  gyselroth™  (http://www.gyselroth.com)
 * @link    http://www.gyselroth.com
 * @license Apache-2.0
 */

namespace Gyselroth\Helper\Interfaces;

interface ConstantsMimeTypesInterface
{
    public const MIME_TYPE_CSV  = 'text/csv';
    public const MIME_TYPE_HTML = 'text/html';
    public const MIME_TYPE_TEXT = 'text/plain';

    public const MIME_TYPE_IMAGE_GIF  = 'image/gif';
    public const MIME_TYPE_IMAGE_JPEG = 'image/jpeg';
    public const MIME_TYPE_IMAGE_PNG  = 'image/png';

    public const MIME_TYPE_JAVASCRIPT = 'text/javascript';

    public const MIME_TYPE_JSON = 'application/json';
    public const MIME_TYPE_PDF  = 'application/pdf';
    public const MIME_TYPE_XML  = 'application/xml';
    public const MIME_TYPE_ZIP  = 'application/zip';

    public const MIME_TYPE_MS_ACCESS     = 'application/msaccess';
    public const MIME_TYPE_MS_EXCEL      = 'application/msexcel';
    public const MIME_TYPE_MS_POWERPOINT = 'application/mspowerpoint';
    public const MIME_TYPE_MS_WORD       = 'application/msword';

    // DOCX
    public const MIME_TYPE_VND_OPEN_XML_WORD = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    // XLSX
    public const MIME_TYPE_VND_OPEN_XML_SPREADSHEET = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    public const MIME_TYPE_STREAM = 'application/octet-stream';
}
