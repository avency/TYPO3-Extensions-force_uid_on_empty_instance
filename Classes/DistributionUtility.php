<?php
namespace Avency\ForceUidOnEmptyInstance;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Avency DEV Team <info at avency dot de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class DistributionUtility
 */
class DistributionUtility {

	/**
	 * Array of patterns of tables name that are allowed to have records in
	 *
	 * @var array
	 */
	protected $ignoreTablePatterns = array(
		'/^be_users$/',
		'/^be_sessions$/',
		'/^cf_/',
		'/^static_/',
		'/^sys_domain$/',
		'/^sys_file_storage$/',
		'/^sys_registry$/',
		'/^tx_extensionmanager_domain_model_repository$/',
	);

	/**
	 * Modifies the import object to force all uids, if required database tables are empty
	 *
	 * @param \TYPO3\CMS\Impexp\ImportExport $import
	 */
	public function modifyImporter(\TYPO3\CMS\Impexp\ImportExport $import) {
		if ($this->isEmptyDatabase()) {
			$import->force_all_UIDS = TRUE;
		}
	}

	/**
	 * Checks if all tables with the exception of matching ignoreTablePatterns are empty
	 *
	 * @return bool
	 */
	protected function isEmptyDatabase() {
		$isEmpty = FALSE;
		$tables = $GLOBALS['TYPO3_DB']->admin_get_tables();

		foreach(array_keys($tables) as $tableName) {
			foreach($this->ignoreTablePatterns as $ignoreTablePattern) {
				if (preg_match($ignoreTablePattern, $tableName)) {
					continue 2;
				}
			}
			$res = $GLOBALS['TYPO3_DB']->sql_query('SELECT COUNT(*) as records FROM ' . $tableName . ' WHERE 1=1');
			if ($res !== FALSE) {
				// reset for next table
				$isEmpty = FALSE;
				$countRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				$GLOBALS['TYPO3_DB']->sql_free_result($res);
				if ($countRow['records'] > 0) {
					// records found, break and return
					break;
				}

				// okay, no records
				$isEmpty = TRUE;
			}
		}

		return $isEmpty;
	}

}
