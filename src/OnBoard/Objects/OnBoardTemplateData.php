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

namespace Invertus\dpdBaltics\OnBoard\Objects;

class OnBoardTemplateData
{
    /**
     * @var string
     */
    private $containerClass;

    /**
     * @var string
     */
    private $heading;

    /**
     * @var array
     */
    private $paragraphObjectsArray = array();
    /**
     * @var array
     */
    private $fastMoveButtonObjectsArray = array();

    /**
     * @var array
     */
    private $buttonObjectsArray = array();
    /**
     * @var array
     */
    private $importedMainFilesDataArray = array();

    /**
     * @var string
     */
    private $manualConfigProgress;

    /**
     * @var OnBoardProgressBar
     */
    private $progressBarObj;

    public function setContainerClass($containerClass)
    {
        $this->containerClass = $containerClass;
    }

    public function setFastMoveButton($fastMoveButtonObj)
    {
        $this->fastMoveButtonObjectsArray[] = $fastMoveButtonObj;
    }

    public function setHeading($heading)
    {
        $this->heading = $heading;
    }

    public function setParagraph(OnBoardParagraph $paragraphObj)
    {
        $this->paragraphObjectsArray[] = $paragraphObj;
    }

    public function setButton(OnBoardButton $buttonObj)
    {
        $this->buttonObjectsArray[] = $buttonObj;
    }

    /**
     * @param array $importedMainFilesData
     */
    public function setImportedMainFilesData($importedMainFilesData)
    {
        $this->importedMainFilesDataArray = $importedMainFilesData;
    }

    /**
     * @param string $manualConfigProgress
     */
    public function setManualConfigProgress($manualConfigProgress)
    {
        $this->manualConfigProgress = $manualConfigProgress;
    }

    /**
     * @param OnBoardProgressBar
     */
    public function setProgressBarObj(OnBoardProgressBar $progressBarObj)
    {
        $this->progressBarObj = $progressBarObj;
    }

    public function getTemplateData()
    {
        return array(
            'containerClass' => $this->containerClass,
            'fastMoveButtons' => $this->fastMoveButtonObjectsArray,
            'heading' => $this->heading,
            'paragraphs' => $this->paragraphObjectsArray,
            'buttons' => $this->buttonObjectsArray,
            'importedMainFilesDataArray' => $this->importedMainFilesDataArray,
            'manualConfigProgress' => $this->manualConfigProgress,
            'progressBar' => $this->progressBarObj,
        );
    }
}
