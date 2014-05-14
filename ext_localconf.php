<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
$signalSlotDispatcher->connect(
		'TYPO3\\CMS\\Impexp\\Utility\\ImportExportUtility',
		'AfterImportExportInitialisation',
		'Avency\\ForceUidOnEmptyInstance\\DistributionUtility',
		'modifyImporter'
);

?>