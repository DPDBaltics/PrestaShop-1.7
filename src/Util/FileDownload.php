<?php

namespace Invertus\dpdBaltics\Util;

use Configuration;
use DPDBaltics;
use Invertus\dpdBaltics\Config\Config;

class FileDownload
{

    /**
     * @var DPDBaltics
     */
    private $module;

    public function __construct(DPDBaltics $module)
    {
        $this->module = $module;
    }

    public function dumpFile($content, $name, $format)
    {
        $extension = strtolower($format);
        $file = sprintf('%s.%s', $name, $extension);
        $file = htmlentities($file, ENT_QUOTES | ENT_HTML401, 'UTF-8');

        $tmpFolder = $this->module->getLocalPath().'tmp/';

        file_put_contents($tmpFolder.$file, $content);

        if (ob_get_level() && ob_get_length() > 0) {
            ob_clean();
        }

        if (file_exists($tmpFolder.$file)) {
            if(ob_get_length() > 0) {
                ob_clean();
            }

            header('Content-Description: File Transfer');
            header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0, max-age=1');
            header('Pragma: public');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            header('Content-Type: application/'.$extension);
            header('Content-Transfer-Encoding: Binary');
            header('Content-Length:'.filesize($tmpFolder.$file));
            header('Accept-Ranges: bytes');

            switch (Configuration::get(Config::LABEL_PRINT_OPTION)) {
                case 'browser_print':
                    header('Content-Disposition: inline; filename='.$file);
                    break;
                case 'download':
                default:
                    header('Content-Disposition: attachment; filename='.$file);
                    break;
            }

            readfile($tmpFolder.$file);
            unlink($tmpFolder.$file);
        }
    }
}
