<?php

namespace Invertus\dpdBaltics\Service\Export;

interface ExportableInterface
{
    /**
     * Get data to export
     *
     * @return array
     */
    public function getRows();

    /**
     * Get export file name
     *
     * @return string
     */
    public function getFileName();

    /**
     * Get array of headers
     *
     * @return array|string
     */
    public function getHeaders();

    /**
     * checks if has errors
     * @return bool
     */
    public function hasErrors();

    /**
     * gets array of errors
     * @return array
     */
    public function getErrors();
}
