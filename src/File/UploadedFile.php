<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    INVERTUS, UAB www.invertus.eu <support@invertus.eu>
 * @copyright Copyright (c) permanent, INVERTUS, UAB
 * @license   Addons PrestaShop license limitation
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of INVERTUS, UAB
 */

namespace Invertus\dpdBaltics\File;

use Module;
use RuntimeException;

/**
 * Class DPDUploadedFile
 */
class UploadedFile
{
    const FILE_NAME = 'UploadedFile';

    /**
     * @var array|string[]
     */
    private $allowedMimeTypes;

    /**
     * @var string
     */
    private $extension;

    /**
     * @var string|null
     */
    private $fileName = null;

    /**
     * @var string|null
     */
    private $tmpName = null;

    /**
     * DPDUploadedFile constructor.
     *
     * @param string $uploadedFilename
     * @param string $extension
     * @param array|string[] $allowedMimeTypes Allowed mime types for uploaded file
     */
    public function __construct($uploadedFilename, $extension, array $allowedMimeTypes)
    {
        $this->allowedMimeTypes = $allowedMimeTypes;
        $this->extension = $extension;

        $this->validate($uploadedFilename);
    }

    /**
     * Get uploaded file temporary name
     *
     * @return string|null
     */
    public function getTmpName()
    {
        return $this->tmpName;
    }

    /**
     * Get uploaded file name
     *
     * @return null|string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Validate uploaded file
     *
     * @param string $uploadedFilename
     */
    private function validate($uploadedFilename)
    {
        $module = Module::getInstanceByName('dpdbaltics');

        if (!isset($_FILES[$uploadedFilename]) || $_FILES[$uploadedFilename]['error'] == 4) {
            throw new RuntimeException($module->l('Upload file was not found', self::FILE_NAME));
        }

        $file = $_FILES[$uploadedFilename];
        $tmpName = $file['tmp_name'];

        if (empty($tmpName)) {
            throw new RuntimeException(
                $module->l('Error occured while importing file. Please check file and try again.', self::FILE_NAME)
            );
        }

        if (function_exists('finfo_open')) {
            $mime = defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
            $fileInfo = finfo_open($mime);
            $mimeType = finfo_file($fileInfo, $tmpName);
            finfo_close($fileInfo);
        } else {
            $mimeType = $file['type'];
        }

        if (0 >= $file['size'] ||
            !in_array($mimeType, $this->allowedMimeTypes) ||
            $file['error'] ||
            !preg_match('/.'.$this->extension.'/', $file['name'])
        ) {
            throw new RuntimeException($module->l('Invalid upload file selected', self::FILE_NAME));
        }

        $this->fileName = $file['name'];
        $this->tmpName = $tmpName;
    }
}
