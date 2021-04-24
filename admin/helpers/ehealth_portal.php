<?php
/*--------------------------------------------------------------------------------------------------------|  www.vdm.io  |------/
    __      __       _     _____                 _                                  _     __  __      _   _               _
    \ \    / /      | |   |  __ \               | |                                | |   |  \/  |    | | | |             | |
     \ \  / /_ _ ___| |_  | |  | | _____   _____| | ___  _ __  _ __ ___   ___ _ __ | |_  | \  / | ___| |_| |__   ___   __| |
      \ \/ / _` / __| __| | |  | |/ _ \ \ / / _ \ |/ _ \| '_ \| '_ ` _ \ / _ \ '_ \| __| | |\/| |/ _ \ __| '_ \ / _ \ / _` |
       \  / (_| \__ \ |_  | |__| |  __/\ V /  __/ | (_) | |_) | | | | | |  __/ | | | |_  | |  | |  __/ |_| | | | (_) | (_| |
        \/ \__,_|___/\__| |_____/ \___| \_/ \___|_|\___/| .__/|_| |_| |_|\___|_| |_|\__| |_|  |_|\___|\__|_| |_|\___/ \__,_|
                                                        | |
                                                        |_|
/-------------------------------------------------------------------------------------------------------------------------------/

	@version		1.0.5
	@build			24th April, 2021
	@created		13th August, 2020
	@package		eHealth Portal
	@subpackage		ehealth_portal.php
	@author			Oh Martin <https://github.com/namibia/eHealth-Portal>
	@copyright		Copyright (C) 2020 Vast Development Method. All rights reserved.
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Portal for mobile health clinics

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Language;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Ehealth_portal component helper.
 */
abstract class Ehealth_portalHelper
{
	/**
	 * Composer Switch
	 * 
	 * @var      array
	 */
	protected static $composer = array();

	/**
	 * The Main Active Language
	 * 
	 * @var      string
	 */
	public static $langTag;

	/**
	 * Load the Composer Vendors
	 */
	public static function composerAutoload($target)
	{
		// insure we load the composer vendor only once
		if (!isset(self::$composer[$target]))
		{
			// get the function name
			$functionName = self::safeString('compose' . $target);
			// check if method exist
			if (method_exists(__CLASS__, $functionName))
			{
				return self::{$functionName}();
			}
			return false;
		}
		return self::$composer[$target];
	}

	/**
	 * Load the Component xml manifest.
	 */
	public static function manifest()
	{
		$manifestUrl = JPATH_ADMINISTRATOR."/components/com_ehealth_portal/ehealth_portal.xml";
		return simplexml_load_file($manifestUrl);
	}

	/**
	 * Joomla version object
	 */	
	protected static $JVersion;

	/**
	 * set/get Joomla version
	 */
	public static function jVersion()
	{
		// check if set
		if (!self::checkObject(self::$JVersion))
		{
			self::$JVersion = new JVersion();
		}
		return self::$JVersion;
	}

	/**
	 * Load the Contributors details.
	 */
	public static function getContributors()
	{
		// get params
		$params	= JComponentHelper::getParams('com_ehealth_portal');
		// start contributors array
		$contributors = array();
		// get all Contributors (max 20)
		$searchArray = range('0','20');
		foreach($searchArray as $nr)
 		{
			if ((NULL !== $params->get("showContributor".$nr)) && ($params->get("showContributor".$nr) == 1 || $params->get("showContributor".$nr) == 3))
			{
				// set link based of selected option
				if($params->get("useContributor".$nr) == 1)
         		{
					$link_front = '<a href="mailto:'.$params->get("emailContributor".$nr).'" target="_blank">';
					$link_back = '</a>';
				}
				elseif($params->get("useContributor".$nr) == 2)
				{
					$link_front = '<a href="'.$params->get("linkContributor".$nr).'" target="_blank">';
					$link_back = '</a>';
				}
				else
				{
					$link_front = '';
					$link_back = '';
				}
				$contributors[$nr]['title']	= self::htmlEscape($params->get("titleContributor".$nr));
				$contributors[$nr]['name']	= $link_front.self::htmlEscape($params->get("nameContributor".$nr)).$link_back;
			}
		}
		return $contributors;
	}

	/**
	 *	Can be used to build help urls.
	 **/
	public static function getHelpUrl($view)
	{
		return false;
	}

	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu)
	{
		// load user for access menus
		$user = JFactory::getUser();
		// load the submenus to sidebar
		JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_DASHBOARD'), 'index.php?option=com_ehealth_portal&view=ehealth_portal', $submenu === 'ehealth_portal');
		if ($user->authorise('payment.access', 'com_ehealth_portal') && $user->authorise('payment.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_PAYMENTS'), 'index.php?option=com_ehealth_portal&view=payments', $submenu === 'payments');
		}
		if ($user->authorise('general_medical_check_up.access', 'com_ehealth_portal') && $user->authorise('general_medical_check_up.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_GENERAL_MEDICAL_CHECK_UPS'), 'index.php?option=com_ehealth_portal&view=general_medical_check_ups', $submenu === 'general_medical_check_ups');
		}
		if ($user->authorise('antenatal_care.access', 'com_ehealth_portal') && $user->authorise('antenatal_care.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_ANTENATAL_CARES'), 'index.php?option=com_ehealth_portal&view=antenatal_cares', $submenu === 'antenatal_cares');
		}
		if ($user->authorise('immunisation.access', 'com_ehealth_portal') && $user->authorise('immunisation.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_IMMUNISATIONS'), 'index.php?option=com_ehealth_portal&view=immunisations', $submenu === 'immunisations');
		}
		if ($user->authorise('vmmc.access', 'com_ehealth_portal') && $user->authorise('vmmc.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_VMMCS'), 'index.php?option=com_ehealth_portal&view=vmmcs', $submenu === 'vmmcs');
		}
		if ($user->authorise('prostate_and_testicular_cancer.access', 'com_ehealth_portal') && $user->authorise('prostate_and_testicular_cancer.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_PROSTATE_AND_TESTICULAR_CANCERS'), 'index.php?option=com_ehealth_portal&view=prostate_and_testicular_cancers', $submenu === 'prostate_and_testicular_cancers');
		}
		if ($user->authorise('tuberculosis.access', 'com_ehealth_portal') && $user->authorise('tuberculosis.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_TUBERCULOSES'), 'index.php?option=com_ehealth_portal&view=tuberculoses', $submenu === 'tuberculoses');
		}
		if ($user->authorise('hiv_counseling_and_testing.access', 'com_ehealth_portal') && $user->authorise('hiv_counseling_and_testing.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_HIV_COUNSELINGS_AND_TESTINGS'), 'index.php?option=com_ehealth_portal&view=hiv_counselings_and_testings', $submenu === 'hiv_counselings_and_testings');
		}
		if ($user->authorise('family_planning.access', 'com_ehealth_portal') && $user->authorise('family_planning.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_FAMILY_PLANNINGS'), 'index.php?option=com_ehealth_portal&view=family_plannings', $submenu === 'family_plannings');
		}
		if ($user->authorise('health_education.access', 'com_ehealth_portal') && $user->authorise('health_education.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_HEALTH_EDUCATIONS'), 'index.php?option=com_ehealth_portal&view=health_educations', $submenu === 'health_educations');
		}
		if ($user->authorise('cervical_cancer.access', 'com_ehealth_portal') && $user->authorise('cervical_cancer.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_CERVICAL_CANCERS'), 'index.php?option=com_ehealth_portal&view=cervical_cancers', $submenu === 'cervical_cancers');
		}
		if ($user->authorise('breast_cancer.access', 'com_ehealth_portal') && $user->authorise('breast_cancer.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_BREAST_CANCERS'), 'index.php?option=com_ehealth_portal&view=breast_cancers', $submenu === 'breast_cancers');
		}
		if ($user->authorise('test.access', 'com_ehealth_portal') && $user->authorise('test.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_TESTS'), 'index.php?option=com_ehealth_portal&view=tests', $submenu === 'tests');
		}
		if ($user->authorise('immunisation_vaccine_type.access', 'com_ehealth_portal') && $user->authorise('immunisation_vaccine_type.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_IMMUNISATION_VACCINE_TYPES'), 'index.php?option=com_ehealth_portal&view=immunisation_vaccine_types', $submenu === 'immunisation_vaccine_types');
		}
		if ($user->authorise('unit.access', 'com_ehealth_portal') && $user->authorise('unit.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_UNITS'), 'index.php?option=com_ehealth_portal&view=units', $submenu === 'units');
		}
		if ($user->authorise('referral.access', 'com_ehealth_portal') && $user->authorise('referral.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_REFERRALS'), 'index.php?option=com_ehealth_portal&view=referrals', $submenu === 'referrals');
		}
		if ($user->authorise('medication.access', 'com_ehealth_portal') && $user->authorise('medication.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_MEDICATIONS'), 'index.php?option=com_ehealth_portal&view=medications', $submenu === 'medications');
		}
		if ($user->authorise('administration_part.access', 'com_ehealth_portal') && $user->authorise('administration_part.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_ADMINISTRATION_PARTS'), 'index.php?option=com_ehealth_portal&view=administration_parts', $submenu === 'administration_parts');
		}
		if ($user->authorise('strength.access', 'com_ehealth_portal') && $user->authorise('strength.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_STRENGTHS'), 'index.php?option=com_ehealth_portal&view=strengths', $submenu === 'strengths');
		}
		if ($user->authorise('patient_queue.access', 'com_ehealth_portal') && $user->authorise('patient_queue.submenu', 'com_ehealth_portal'))
		{
			JHtmlSidebar::addEntry(JText::_('COM_EHEALTH_PORTAL_SUBMENU_PATIENT_QUEUE'), 'index.php?option=com_ehealth_portal&view=patient_queue', $submenu === 'patient_queue');
		}
	}

	/**
	* Prepares the xml document
	*/
	public static function xls($rows, $fileName = null, $title = null, $subjectTab = null, $creator = 'Vast Development Method', $description = null, $category = null,$keywords = null, $modified = null)
	{
		// set the user
		$user = JFactory::getUser();
		// set fileName if not set
		if (!$fileName)
		{
			$fileName = 'exported_'.JFactory::getDate()->format('jS_F_Y');
		}
		// set modified if not set
		if (!$modified)
		{
			$modified = $user->name;
		}
		// set title if not set
		if (!$title)
		{
			$title = 'Book1';
		}
		// set tab name if not set
		if (!$subjectTab)
		{
			$subjectTab = 'Sheet1';
		}

		// make sure we have the composer classes loaded
		self::composerAutoload('phpspreadsheet');

		// Create new Spreadsheet object
		$spreadsheet = new Spreadsheet();

		// Set document properties
		$spreadsheet->getProperties()
			->setCreator($creator)
			->setCompany('Vast Development Method')
			->setLastModifiedBy($modified)
			->setTitle($title)
			->setSubject($subjectTab);
		// The file type
		$file_type = 'Xls';
		// set description
		if ($description)
		{
			$spreadsheet->getProperties()->setDescription($description);
		}
		// set keywords
		if ($keywords)
		{
			$spreadsheet->getProperties()->setKeywords($keywords);
		}
		// set category
		if ($category)
		{
			$spreadsheet->getProperties()->setCategory($category);
		}

		// Some styles
		$headerStyles = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '1171A3'),
				'size'  => 12,
				'name'  => 'Verdana'
		));
		$sideStyles = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => '444444'),
				'size'  => 11,
				'name'  => 'Verdana'
		));
		$normalStyles = array(
			'font'  => array(
				'color' => array('rgb' => '444444'),
				'size'  => 11,
				'name'  => 'Verdana'
		));

		// Add some data
		if (($size = self::checkArray($rows)) !== false)
		{
			$i = 1;

			// Based on data size we adapt the behaviour.
			$xls_mode = 1;
			if ($size > 3000)
			{
				$xls_mode = 3;
				$file_type = 'Csv';
			}
			elseif ($size > 2000)
			{
				$xls_mode = 2;
			}

			// Set active sheet and get it.
			$active_sheet = $spreadsheet->setActiveSheetIndex(0);
			foreach ($rows as $array)
			{
				$a = 'A';
				foreach ($array as $value)
				{
					$active_sheet->setCellValue($a.$i, $value);
					if ($xls_mode != 3)
					{
						if ($i == 1)
						{
							$active_sheet->getColumnDimension($a)->setAutoSize(true);
							$active_sheet->getStyle($a.$i)->applyFromArray($headerStyles);
							$active_sheet->getStyle($a.$i)->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
						}
						elseif ($a === 'A')
						{
							$active_sheet->getStyle($a.$i)->applyFromArray($sideStyles);
						}
						elseif ($xls_mode == 1)
						{
							$active_sheet->getStyle($a.$i)->applyFromArray($normalStyles);
						}
					}
					$a++;
				}
				$i++;
			}
		}
		else
		{
			return false;
		}

		// Rename worksheet
		$spreadsheet->getActiveSheet()->setTitle($subjectTab);

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$spreadsheet->setActiveSheetIndex(0);

		// Redirect output to a client's web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $fileName . '.' . strtolower($file_type) .'"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$writer = IOFactory::createWriter($spreadsheet, $file_type);
		$writer->save('php://output');
		jexit();
	}

	/**
	* Get CSV Headers
	*/
	public static function getFileHeaders($dataType)
	{
		// make sure we have the composer classes loaded
		self::composerAutoload('phpspreadsheet');
		// get session object
		$session = JFactory::getSession();
		$package = $session->get('package', null);
		$package = json_decode($package, true);
		// set the headers
		if(isset($package['dir']))
		{
			// only load first three rows
			$chunkFilter = new PhpOffice\PhpSpreadsheet\Reader\chunkReadFilter(2,1);
			// identify the file type
			$inputFileType = IOFactory::identify($package['dir']);
			// create the reader for this file type
			$excelReader = IOFactory::createReader($inputFileType);
			// load the limiting filter
			$excelReader->setReadFilter($chunkFilter);
			$excelReader->setReadDataOnly(true);
			// load the rows (only first three)
			$excelObj = $excelReader->load($package['dir']);
			$headers = array();
			foreach ($excelObj->getActiveSheet()->getRowIterator() as $row)
			{
				if($row->getRowIndex() == 1)
				{
					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false);
					foreach ($cellIterator as $cell)
					{
						if (!is_null($cell))
						{
							$headers[$cell->getColumn()] = $cell->getValue();
						}
					}
					$excelObj->disconnectWorksheets();
					unset($excelObj);
					break;
				}
			}
			return $headers;
		}
		return false;
	}

	/**
	* Load the Composer Vendor phpspreadsheet
	*/
	protected static function composephpspreadsheet()
	{
		// load the autoloader for phpspreadsheet
		require_once JPATH_SITE . '/libraries/phpspreadsheet/vendor/autoload.php';
		// do not load again
		self::$composer['phpspreadsheet'] = true;

		return  true;
	}

	/**
	 * Get a Variable 
	 *
	 * @param   string   $table        The table from which to get the variable
	 * @param   string   $where        The value where
	 * @param   string   $whereString  The target/field string where/name
	 * @param   string   $what         The return field
	 * @param   string   $operator     The operator between $whereString/field and $where/value
	 * @param   string   $main         The component in which the table is found
	 *
	 * @return  mix string/int/float
	 *
	 */
	public static function getVar($table, $where = null, $whereString = 'user', $what = 'id', $operator = '=', $main = 'ehealth_portal')
	{
		if(!$where)
		{
			$where = JFactory::getUser()->id;
		}
		// Get a db connection.
		$db = JFactory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array($what)));
		if (empty($table))
		{
			$query->from($db->quoteName('#__'.$main));
		}
		else
		{
			$query->from($db->quoteName('#__'.$main.'_'.$table));
		}
		if (is_numeric($where))
		{
			$query->where($db->quoteName($whereString) . ' '.$operator.' '.(int) $where);
		}
		elseif (is_string($where))
		{
			$query->where($db->quoteName($whereString) . ' '.$operator.' '. $db->quote((string)$where));
		}
		else
		{
			return false;
		}
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows())
		{
			return $db->loadResult();
		}
		return false;
	}

	/**
	 * Get array of variables
	 *
	 * @param   string   $table        The table from which to get the variables
	 * @param   string   $where        The value where
	 * @param   string   $whereString  The target/field string where/name
	 * @param   string   $what         The return field
	 * @param   string   $operator     The operator between $whereString/field and $where/value
	 * @param   string   $main         The component in which the table is found
	 * @param   bool     $unique       The switch to return a unique array
	 *
	 * @return  array
	 *
	 */
	public static function getVars($table, $where = null, $whereString = 'user', $what = 'id', $operator = 'IN', $main = 'ehealth_portal', $unique = true)
	{
		if(!$where)
		{
			$where = JFactory::getUser()->id;
		}

		if (!self::checkArray($where) && $where > 0)
		{
			$where = array($where);
		}

		if (self::checkArray($where))
		{
			// prep main <-- why? well if $main='' is empty then $table can be categories or users
			if (self::checkString($main))
			{
				$main = '_'.ltrim($main, '_');
			}
			// Get a db connection.
			$db = JFactory::getDbo();
			// Create a new query object.
			$query = $db->getQuery(true);

			$query->select($db->quoteName(array($what)));
			if (empty($table))
			{
				$query->from($db->quoteName('#__'.$main));
			}
			else
			{
				$query->from($db->quoteName('#_'.$main.'_'.$table));
			}
			// add strings to array search
			if ('IN_STRINGS' === $operator || 'NOT IN_STRINGS' === $operator)
			{
				$query->where($db->quoteName($whereString) . ' ' . str_replace('_STRINGS', '', $operator) . ' ("' . implode('","',$where) . '")');
			}
			else
			{
				$query->where($db->quoteName($whereString) . ' ' . $operator . ' (' . implode(',',$where) . ')');
			}
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows())
			{
				if ($unique)
				{
					return array_unique($db->loadColumn());
				}
				return $db->loadColumn();
			}
		}
		return false;
	}

	public static function jsonToString($value, $sperator = ", ", $table = null, $id = 'id', $name = 'name')
	{
		// do some table foot work
		$external = false;
		if (strpos($table, '#__') !== false)
		{
			$external = true;
			$table = str_replace('#__', '', $table);
		}
		// check if string is JSON
		$result = json_decode($value, true);
		if (json_last_error() === JSON_ERROR_NONE)
		{
			// is JSON
			if (self::checkArray($result))
			{
				if (self::checkString($table))
				{
					$names = array();
					foreach ($result as $val)
					{
						if ($external)
						{
							if ($_name = self::getVar(null, $val, $id, $name, '=', $table))
							{
								$names[] = $_name;
							}
						}
						else
						{
							if ($_name = self::getVar($table, $val, $id, $name))
							{
								$names[] = $_name;
							}
						}
					}
					if (self::checkArray($names))
					{
						return (string) implode($sperator,$names);
					}	
				}
				return (string) implode($sperator,$result);
			}
			return (string) json_decode($value);
		}
		return $value;
	}

	public static function isPublished($id,$type)
	{
		if ($type == 'raw')
		{
			$type = 'item';
		}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('a.published'));
		$query->from('#__ehealth_portal_'.$type.' AS a');
		$query->where('a.id = '. (int) $id);
		$query->where('a.published = 1');
		$db->setQuery($query);
		$db->execute();
		$found = $db->getNumRows();
		if($found)
		{
			return true;
		}
		return false;
	}

	public static function getGroupName($id)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(array('a.title'));
		$query->from('#__usergroups AS a');
		$query->where('a.id = '. (int) $id);
		$db->setQuery($query);
		$db->execute();
		$found = $db->getNumRows();
		if($found)
  		{
			return $db->loadResult();
		}
		return $id;
	}

	/**
	 * Get the action permissions
	 *
	 * @param  string   $view        The related view name
	 * @param  int      $record      The item to act upon
	 * @param  string   $views       The related list view name
	 * @param  mixed    $target      Only get this permission (like edit, create, delete)
	 * @param  string   $component   The target component
	 * @param  object   $user        The user whose permissions we are loading
	 *
	 * @return  object   The JObject of permission/authorised actions
	 * 
	 */
	public static function getActions($view, &$record = null, $views = null, $target = null, $component = 'ehealth_portal', $user = 'null')
	{
		// load the user if not given
		if (!self::checkObject($user))
		{
			// get the user object
			$user = JFactory::getUser();
		}
		// load the JObject
		$result = new JObject;
		// make view name safe (just incase)
		$view = self::safeString($view);
		if (self::checkString($views))
		{
			$views = self::safeString($views);
 		}
		// get all actions from component
		$actions = JAccess::getActionsFromFile(
			JPATH_ADMINISTRATOR . '/components/com_' . $component . '/access.xml',
			"/access/section[@name='component']/"
		);
		// if non found then return empty JObject
		if (empty($actions))
		{
			return $result;
		}
		// get created by if not found
		if (self::checkObject($record) && !isset($record->created_by) && isset($record->id))
		{
			$record->created_by = self::getVar($view, $record->id, 'id', 'created_by', '=', $component);
		}
		// set actions only set in component settings
		$componentActions = array('core.admin', 'core.manage', 'core.options', 'core.export');
		// check if we have a target
		$checkTarget = false;
		if ($target)
		{
			// convert to an array
			if (self::checkString($target))
			{
				$target = array($target);
			}
			// check if we are good to go
			if (self::checkArray($target))
			{
				$checkTarget = true;
			}
		}
		// loop the actions and set the permissions
		foreach ($actions as $action)
		{
			// check target action filter
			if ($checkTarget && self::filterActions($view, $action->name, $target))
			{
				continue;
			}
			// set to use component default
			$fallback = true;
			// reset permission per/action
			$permission = false;
			$catpermission = false;
			// set area
			$area = 'comp';
			// check if the record has an ID and the action is item related (not a component action)
			if (self::checkObject($record) && isset($record->id) && $record->id > 0 && !in_array($action->name, $componentActions) &&
				(strpos($action->name, 'core.') !== false || strpos($action->name, $view . '.') !== false))
			{
				// we are in item
				$area = 'item';
				// The record has been set. Check the record permissions.
				$permission = $user->authorise($action->name, 'com_' . $component . '.' . $view . '.' . (int) $record->id);
				// if no permission found, check edit own
				if (!$permission)
				{
					// With edit, if the created_by matches current user then dig deeper.
					if (($action->name === 'core.edit' || $action->name === $view . '.edit') && $record->created_by > 0 && ($record->created_by == $user->id))
					{
						// the correct target
						$coreCheck = (array) explode('.', $action->name);
						// check that we have both local and global access
						if ($user->authorise($coreCheck[0] . '.edit.own', 'com_' . $component . '.' . $view . '.' . (int) $record->id) &&
							$user->authorise($coreCheck[0]  . '.edit.own', 'com_' . $component))
						{
							// allow edit
							$result->set($action->name, true);
							// set not to use global default
							// because we already validated it
							$fallback = false;
						}
						else
						{
							// do not allow edit
							$result->set($action->name, false);
							$fallback = false;
						}
					}
				}
				elseif (self::checkString($views) && isset($record->catid) && $record->catid > 0)
				{
					// we are in item
					$area = 'category';
					// set the core check
					$coreCheck = explode('.', $action->name);
					$core = $coreCheck[0];
					// make sure we use the core. action check for the categories
					if (strpos($action->name, $view) !== false && strpos($action->name, 'core.') === false )
					{
						$coreCheck[0] = 'core';
						$categoryCheck = implode('.', $coreCheck);
					}
					else
					{
						$categoryCheck = $action->name;
					}
					// The record has a category. Check the category permissions.
					$catpermission = $user->authorise($categoryCheck, 'com_' . $component . '.' . $views . '.category.' . (int) $record->catid);
					if (!$catpermission && !is_null($catpermission))
					{
						// With edit, if the created_by matches current user then dig deeper.
						if (($action->name === 'core.edit' || $action->name === $view . '.edit') && $record->created_by > 0 && ($record->created_by == $user->id))
						{
							// check that we have both local and global access
							if ($user->authorise('core.edit.own', 'com_' . $component . '.' . $views . '.category.' . (int) $record->catid) &&
								$user->authorise($core . '.edit.own', 'com_' . $component))
							{
								// allow edit
								$result->set($action->name, true);
								// set not to use global default
								// because we already validated it
								$fallback = false;
							}
							else
							{
								// do not allow edit
								$result->set($action->name, false);
								$fallback = false;
							}
						}
					}
				}
			}
			// if allowed then fallback on component global settings
			if ($fallback)
			{
				// if item/category blocks access then don't fall back on global
				if ((($area === 'item') && !$permission) || (($area === 'category') && !$catpermission))
				{
					// do not allow
					$result->set($action->name, false);
				}
				// Finally remember the global settings have the final say. (even if item allow)
				// The local item permissions can block, but it can't open and override of global permissions.
				// Since items are created by users and global permissions is set by system admin.
				else
				{
					$result->set($action->name, $user->authorise($action->name, 'com_' . $component));
				}
			}
		}
		return $result;
	}

	/**
	 * Filter the action permissions
	 *
	 * @param  string   $action   The action to check
	 * @param  array    $targets  The array of target actions
	 *
	 * @return  boolean   true if action should be filtered out
	 * 
	 */
	protected static function filterActions(&$view, &$action, &$targets)
	{
		foreach ($targets as $target)
		{
			if (strpos($action, $view . '.' . $target) !== false ||
				strpos($action, 'core.' . $target) !== false)
			{
				return false;
				break;
			}
		}
		return true;
	}

	/**
	 * Get any component's model
	 */
	public static function getModel($name, $path = JPATH_COMPONENT_ADMINISTRATOR, $Component = 'Ehealth_portal', $config = array())
	{
		// fix the name
		$name = self::safeString($name);
		// full path to models
		$fullPathModels = $path . '/models';
		// load the model file
		JModelLegacy::addIncludePath($fullPathModels, $Component . 'Model');
		// make sure the table path is loaded
		if (!isset($config['table_path']) || !self::checkString($config['table_path']))
		{
			// This is the JCB default path to tables in Joomla 3.x
			$config['table_path'] = JPATH_ADMINISTRATOR . '/components/com_' . strtolower($Component) . '/tables';
		}
		// get instance
		$model = JModelLegacy::getInstance($name, $Component . 'Model', $config);
		// if model not found (strange)
		if ($model == false)
		{
			jimport('joomla.filesystem.file');
			// get file path
			$filePath = $path . '/' . $name . '.php';
			$fullPathModel = $fullPathModels . '/' . $name . '.php';
			// check if it exists
			if (File::exists($filePath))
			{
				// get the file
				require_once $filePath;
			}
			elseif (File::exists($fullPathModel))
			{
				// get the file
				require_once $fullPathModel;
			}
			// build class names
			$modelClass = $Component . 'Model' . $name;
			if (class_exists($modelClass))
			{
				// initialize the model
				return new $modelClass($config);
			}
		}
		return $model;
	}

	/**
	 * Add to asset Table
	 */
	public static function setAsset($id, $table, $inherit = true)
	{
		$parent = JTable::getInstance('Asset');
		$parent->loadByName('com_ehealth_portal');
		
		$parentId = $parent->id;
		$name     = 'com_ehealth_portal.'.$table.'.'.$id;
		$title    = '';

		$asset = JTable::getInstance('Asset');
		$asset->loadByName($name);

		// Check for an error.
		$error = $asset->getError();

		if ($error)
		{
			return false;
		}
		else
		{
			// Specify how a new or moved node asset is inserted into the tree.
			if ($asset->parent_id != $parentId)
			{
				$asset->setLocation($parentId, 'last-child');
			}

			// Prepare the asset to be stored.
			$asset->parent_id = $parentId;
			$asset->name      = $name;
			$asset->title     = $title;
			// get the default asset rules
			$rules = self::getDefaultAssetRules('com_ehealth_portal', $table, $inherit);
			if ($rules instanceof JAccessRules)
			{
				$asset->rules = (string) $rules;
			}

			if (!$asset->check() || !$asset->store())
			{
				JFactory::getApplication()->enqueueMessage($asset->getError(), 'warning');
				return false;
			}
			else
			{
				// Create an asset_id or heal one that is corrupted.
				$object = new stdClass();

				// Must be a valid primary key value.
				$object->id = $id;
				$object->asset_id = (int) $asset->id;

				// Update their asset_id to link to the asset table.
				return JFactory::getDbo()->updateObject('#__ehealth_portal_'.$table, $object, 'id');
			}
		}
		return false;
	}

	/**
	 * Gets the default asset Rules for a component/view.
	 */
	protected static function getDefaultAssetRules($component, $view, $inherit = true)
	{
		// if new or inherited
		$assetId = 0;
		// Only get the actual item rules if not inheriting
		if (!$inherit)
		{
			// Need to find the asset id by the name of the component.
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('id'))
				->from($db->quoteName('#__assets'))
				->where($db->quoteName('name') . ' = ' . $db->quote($component));
			$db->setQuery($query);
			$db->execute();
			// check that there is a value
			if ($db->getNumRows())
			{
				// asset already set so use saved rules
				$assetId = (int) $db->loadResult();
			}
		}
		// get asset rules
		$result =  JAccess::getAssetRules($assetId);
		if ($result instanceof JAccessRules)
		{
			$_result = (string) $result;
			$_result = json_decode($_result);
			foreach ($_result as $name => &$rule)
			{
				$v = explode('.', $name);
				if ($view !== $v[0])
				{
					// remove since it is not part of this view
					unset($_result->$name);
				}
				elseif ($inherit)
				{
					// clear the value since we inherit
					$rule = array();
				}
			}
			// check if there are any view values remaining
			if (count((array) $_result))
			{
				$_result = json_encode($_result);
				$_result = array($_result);
				// Instantiate and return the JAccessRules object for the asset rules.
				$rules = new JAccessRules($_result);
				// return filtered rules
				return $rules;
			}
		}
		return $result;
	}

	/**
	 * xmlAppend
	 *
	 * @param   SimpleXMLElement   $xml      The XML element reference in which to inject a comment
	 * @param   mixed              $node     A SimpleXMLElement node to append to the XML element reference, or a stdClass object containing a comment attribute to be injected before the XML node and a fieldXML attribute containing a SimpleXMLElement
	 *
	 * @return  null
	 *
	 */
	public static function xmlAppend(&$xml, $node)
	{
		if (!$node)
		{
			// element was not returned
			return;
		}
		switch (get_class($node))
		{
			case 'stdClass':
				if (property_exists($node, 'comment'))
				{
					self::xmlComment($xml, $node->comment);
				}
				if (property_exists($node, 'fieldXML'))
				{
					self::xmlAppend($xml, $node->fieldXML);
				}
				break;
			case 'SimpleXMLElement':
				$domXML = dom_import_simplexml($xml);
				$domNode = dom_import_simplexml($node);
				$domXML->appendChild($domXML->ownerDocument->importNode($domNode, true));
				$xml = simplexml_import_dom($domXML);
				break;
		}
	}

	/**
	 * xmlComment
	 *
	 * @param   SimpleXMLElement   $xml        The XML element reference in which to inject a comment
	 * @param   string             $comment    The comment to inject
	 *
	 * @return  null
	 *
	 */
	public static function xmlComment(&$xml, $comment)
	{
		$domXML = dom_import_simplexml($xml);
		$domComment = new DOMComment($comment);
		$nodeTarget = $domXML->ownerDocument->importNode($domComment, true);
		$domXML->appendChild($nodeTarget);
		$xml = simplexml_import_dom($domXML);
	}

	/**
	 * xmlAddAttributes
	 *
	 * @param   SimpleXMLElement   $xml          The XML element reference in which to inject a comment
	 * @param   array              $attributes   The attributes to apply to the XML element
	 *
	 * @return  null
	 *
	 */
	public static function xmlAddAttributes(&$xml, $attributes = array())
	{
		foreach ($attributes as $key => $value)
		{
			$xml->addAttribute($key, $value);
		}
	}

	/**
	 * xmlAddOptions
	 *
	 * @param   SimpleXMLElement   $xml          The XML element reference in which to inject a comment
	 * @param   array              $options      The options to apply to the XML element
	 *
	 * @return  void
	 *
	 */
	public static function xmlAddOptions(&$xml, $options = array())
	{
		foreach ($options as $key => $value)
		{
			$addOption = $xml->addChild('option');
			$addOption->addAttribute('value', $key);
			$addOption[] = $value;
		}
	}

	/**
	 * get the field object
	 *
	 * @param   array      $attributes   The array of attributes
	 * @param   string     $default      The default of the field
	 * @param   array      $options      The options to apply to the XML element
	 *
	 * @return  object
	 *
	 */
	public static function getFieldObject(&$attributes, $default = '', $options = null)
	{
		// make sure we have attributes and a type value
		if (self::checkArray($attributes) && isset($attributes['type']))
		{
			// make sure the form helper class is loaded
			if (!method_exists('JFormHelper', 'loadFieldType'))
			{
				jimport('joomla.form.form');
			}
			// get field type
			$field = JFormHelper::loadFieldType($attributes['type'], true);
			// get field xml
			$XML = self::getFieldXML($attributes, $options);
			// setup the field
			$field->setup($XML, $default);
			// return the field object
			return $field;
		}
		return false;
	}

	/**
	 * get the field xml
	 *
	 * @param   array      $attributes   The array of attributes
	 * @param   array      $options      The options to apply to the XML element
	 *
	 * @return  object
	 *
	 */
	public static function getFieldXML(&$attributes, $options = null)
	{
		// make sure we have attributes and a type value
		if (self::checkArray($attributes))
		{
			// start field xml
			$XML = new SimpleXMLElement('<field/>');
			// load the attributes
			self::xmlAddAttributes($XML, $attributes);
			// check if we have options
			if (self::checkArray($options))
			{
				// load the options
				self::xmlAddOptions($XML, $options);
			}
			// return the field xml
			return $XML;
		}
		return false;
	}

	/**
	 * Render Bool Button
	 *
	 * @param   array   $args   All the args for the button
	 *                             0) name
	 *                             1) additional (options class) // not used at this time
	 *                             2) default
	 *                             3) yes (name)
	 *                             4) no (name)
	 *
	 * @return  string    The input html of the button
	 *
	 */
	public static function renderBoolButton()
	{
		$args = func_get_args();
		// check if there is additional button class
		$additional = isset($args[1]) ? (string) $args[1] : ''; // not used at this time
		// button attributes
		$buttonAttributes = array(
			'type' => 'radio',
			'name' => isset($args[0]) ? self::htmlEscape($args[0]) : 'bool_button',
			'label' => isset($args[0]) ? self::safeString(self::htmlEscape($args[0]), 'Ww') : 'Bool Button', // not seen anyway
			'class' => 'btn-group',
			'filter' => 'INT',
			'default' => isset($args[2]) ? (int) $args[2] : 0);
		// set the button options
		$buttonOptions = array(
			'1' => isset($args[3]) ? self::htmlEscape($args[3]) : 'JYES',
			'0' => isset($args[4]) ? self::htmlEscape($args[4]) : 'JNO');
		// return the input
		return self::getFieldObject($buttonAttributes, $buttonAttributes['default'], $buttonOptions)->input;
	}

	/**
	 * Check if have an json string
	 *
	 * @input	string   The json string to check
	 *
	 * @returns bool true on success
	 */
	public static function checkJson($string)
	{
		if (self::checkString($string))
		{
			json_decode($string);
			return (json_last_error() === JSON_ERROR_NONE);
		}
		return false;
	}

	/**
	 * Check if have an object with a length
	 *
	 * @input	object   The object to check
	 *
	 * @returns bool true on success
	 */
	public static function checkObject($object)
	{
		if (isset($object) && is_object($object))
		{
			return count((array)$object) > 0;
		}
		return false;
	}

	/**
	 * Check if have an array with a length
	 *
	 * @input	array   The array to check
	 *
	 * @returns bool/int  number of items in array on success
	 */
	public static function checkArray($array, $removeEmptyString = false)
	{
		if (isset($array) && is_array($array) && ($nr = count((array)$array)) > 0)
		{
			// also make sure the empty strings are removed
			if ($removeEmptyString)
			{
				foreach ($array as $key => $string)
				{
					if (empty($string))
					{
						unset($array[$key]);
					}
				}
				return self::checkArray($array, false);
			}
			return $nr;
		}
		return false;
	}

	/**
	 * Check if have a string with a length
	 *
	 * @input	string   The string to check
	 *
	 * @returns bool true on success
	 */
	public static function checkString($string)
	{
		if (isset($string) && is_string($string) && strlen($string) > 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * Check if we are connected
	 * Thanks https://stackoverflow.com/a/4860432/1429677
	 *
	 * @returns bool true on success
	 */
	public static function isConnected()
	{
		// If example.com is down, then probably the whole internet is down, since IANA maintains the domain. Right?
		$connected = @fsockopen("www.example.com", 80); 
                // website, port  (try 80 or 443)
		if ($connected)
		{
			//action when connected
			$is_conn = true;
			fclose($connected);
		}
		else
		{
			//action in connection failure
			$is_conn = false;
		}
		return $is_conn;
	}

	/**
	 * Merge an array of array's
	 *
	 * @input	array   The arrays you would like to merge
	 *
	 * @returns array on success
	 */
	public static function mergeArrays($arrays)
	{
		if(self::checkArray($arrays))
		{
			$arrayBuket = array();
			foreach ($arrays as $array)
			{
				if (self::checkArray($array))
				{
					$arrayBuket = array_merge($arrayBuket, $array);
				}
			}
			return $arrayBuket;
		}
		return false;
	}

	// typo sorry!
	public static function sorten($string, $length = 40, $addTip = true)
	{
		return self::shorten($string, $length, $addTip);
	}

	/**
	 * Shorten a string
	 *
	 * @input	string   The you would like to shorten
	 *
	 * @returns string on success
	 */
	public static function shorten($string, $length = 40, $addTip = true)
	{
		if (self::checkString($string))
		{
			$initial = strlen($string);
			$words = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
			$words_count = count((array)$words);

			$word_length = 0;
			$last_word = 0;
			for (; $last_word < $words_count; ++$last_word)
			{
				$word_length += strlen($words[$last_word]);
				if ($word_length > $length)
				{
					break;
				}
			}

			$newString	= implode(array_slice($words, 0, $last_word));
			$final	= strlen($newString);
			if ($initial != $final && $addTip)
			{
				$title = self::shorten($string, 400 , false);
				return '<span class="hasTip" title="'.$title.'" style="cursor:help">'.trim($newString).'...</span>';
			}
			elseif ($initial != $final && !$addTip)
			{
				return trim($newString).'...';
			}
		}
		return $string;
	}

	/**
	 * Making strings safe (various ways)
	 *
	 * @input	string   The you would like to make safe
	 *
	 * @returns string on success
	 */
	public static function safeString($string, $type = 'L', $spacer = '_', $replaceNumbers = true, $keepOnlyCharacters = true)
	{
		if ($replaceNumbers === true)
		{
			// remove all numbers and replace with english text version (works well only up to millions)
			$string = self::replaceNumbers($string);
		}
		// 0nly continue if we have a string
		if (self::checkString($string))
		{
			// create file name without the extention that is safe
			if ($type === 'filename')
			{
				// make sure VDM is not in the string
				$string = str_replace('VDM', 'vDm', $string);
				// Remove anything which isn't a word, whitespace, number
				// or any of the following caracters -_()
				// If you don't need to handle multi-byte characters
				// you can use preg_replace rather than mb_ereg_replace
				// Thanks @Łukasz Rysiak!
				// $string = mb_ereg_replace("([^\w\s\d\-_\(\)])", '', $string);
				$string = preg_replace("([^\w\s\d\-_\(\)])", '', $string);
				// http://stackoverflow.com/a/2021729/1429677
				return preg_replace('/\s+/', ' ', $string);
			}
			// remove all other characters
			$string = trim($string);
			$string = preg_replace('/'.$spacer.'+/', ' ', $string);
			$string = preg_replace('/\s+/', ' ', $string);
			// Transliterate string
			$string = self::transliterate($string);
			// remove all and keep only characters
			if ($keepOnlyCharacters)
			{
				$string = preg_replace("/[^A-Za-z ]/", '', $string);
			}
			// keep both numbers and characters
			else
			{
				$string = preg_replace("/[^A-Za-z0-9 ]/", '', $string);
			}
			// select final adaptations
			if ($type === 'L' || $type === 'strtolower')
			{
				// replace white space with underscore
				$string = preg_replace('/\s+/', $spacer, $string);
				// default is to return lower
				return strtolower($string);
			}
			elseif ($type === 'W')
			{
				// return a string with all first letter of each word uppercase(no undersocre)
				return ucwords(strtolower($string));
			}
			elseif ($type === 'w' || $type === 'word')
			{
				// return a string with all lowercase(no undersocre)
				return strtolower($string);
			}
			elseif ($type === 'Ww' || $type === 'Word')
			{
				// return a string with first letter of the first word uppercase and all the rest lowercase(no undersocre)
				return ucfirst(strtolower($string));
			}
			elseif ($type === 'WW' || $type === 'WORD')
			{
				// return a string with all the uppercase(no undersocre)
				return strtoupper($string);
			}
			elseif ($type === 'U' || $type === 'strtoupper')
			{
					// replace white space with underscore
					$string = preg_replace('/\s+/', $spacer, $string);
					// return all upper
					return strtoupper($string);
			}
			elseif ($type === 'F' || $type === 'ucfirst')
			{
					// replace white space with underscore
					$string = preg_replace('/\s+/', $spacer, $string);
					// return with first caracter to upper
					return ucfirst(strtolower($string));
			}
			elseif ($type === 'cA' || $type === 'cAmel' || $type === 'camelcase')
			{
				// convert all words to first letter uppercase
				$string = ucwords(strtolower($string));
				// remove white space
				$string = preg_replace('/\s+/', '', $string);
				// now return first letter lowercase
				return lcfirst($string);
			}
			// return string
			return $string;
		}
		// not a string
		return '';
	}

	public static function transliterate($string)
	{
		// set tag only once
		if (!self::checkString(self::$langTag))
		{
			// get global value
			self::$langTag = JComponentHelper::getParams('com_ehealth_portal')->get('language', 'en-GB');
		}
		// Transliterate on the language requested
		$lang = Language::getInstance(self::$langTag);
		return $lang->transliterate($string);
	}

	public static function htmlEscape($var, $charset = 'UTF-8', $shorten = false, $length = 40)
	{
		if (self::checkString($var))
		{
			$filter = new JFilterInput();
			$string = $filter->clean(html_entity_decode(htmlentities($var, ENT_COMPAT, $charset)), 'HTML');
			if ($shorten)
			{
                                return self::shorten($string,$length);
			}
			return $string;
		}
		else
		{
			return '';
		}
	}

	public static function replaceNumbers($string)
	{
		// set numbers array
		$numbers = array();
		// first get all numbers
		preg_match_all('!\d+!', $string, $numbers);
		// check if we have any numbers
		if (isset($numbers[0]) && self::checkArray($numbers[0]))
		{
			foreach ($numbers[0] as $number)
			{
				$searchReplace[$number] = self::numberToString((int)$number);
			}
			// now replace numbers in string
			$string = str_replace(array_keys($searchReplace), array_values($searchReplace),$string);
			// check if we missed any, strange if we did.
			return self::replaceNumbers($string);
		}
		// return the string with no numbers remaining.
		return $string;
	}

	/**
	 * Convert an integer into an English word string
	 * Thanks to Tom Nicholson <http://php.net/manual/en/function.strval.php#41988>
	 *
	 * @input	an int
	 * @returns a string
	 */
	public static function numberToString($x)
	{
		$nwords = array( "zero", "one", "two", "three", "four", "five", "six", "seven",
			"eight", "nine", "ten", "eleven", "twelve", "thirteen",
			"fourteen", "fifteen", "sixteen", "seventeen", "eighteen",
			"nineteen", "twenty", 30 => "thirty", 40 => "forty",
			50 => "fifty", 60 => "sixty", 70 => "seventy", 80 => "eighty",
			90 => "ninety" );

		if(!is_numeric($x))
		{
			$w = $x;
		}
		elseif(fmod($x, 1) != 0)
		{
			$w = $x;
		}
		else
		{
			if($x < 0)
			{
				$w = 'minus ';
				$x = -$x;
			}
			else
			{
				$w = '';
				// ... now $x is a non-negative integer.
			}

			if($x < 21)   // 0 to 20
			{
				$w .= $nwords[$x];
			}
			elseif($x < 100)  // 21 to 99
			{ 
				$w .= $nwords[10 * floor($x/10)];
				$r = fmod($x, 10);
				if($r > 0)
				{
					$w .= ' '. $nwords[$r];
				}
			}
			elseif($x < 1000)  // 100 to 999
			{
				$w .= $nwords[floor($x/100)] .' hundred';
				$r = fmod($x, 100);
				if($r > 0)
				{
					$w .= ' and '. self::numberToString($r);
				}
			}
			elseif($x < 1000000)  // 1000 to 999999
			{
				$w .= self::numberToString(floor($x/1000)) .' thousand';
				$r = fmod($x, 1000);
				if($r > 0)
				{
					$w .= ' ';
					if($r < 100)
					{
						$w .= 'and ';
					}
					$w .= self::numberToString($r);
				}
			} 
			else //  millions
			{    
				$w .= self::numberToString(floor($x/1000000)) .' million';
				$r = fmod($x, 1000000);
				if($r > 0)
				{
					$w .= ' ';
					if($r < 100)
					{
						$w .= 'and ';
					}
					$w .= self::numberToString($r);
				}
			}
		}
		return $w;
	}

	/**
	 * Random Key
	 *
	 * @returns a string
	 */
	public static function randomkey($size)
	{
		$bag = "abcefghijknopqrstuwxyzABCDDEFGHIJKLLMMNOPQRSTUVVWXYZabcddefghijkllmmnopqrstuvvwxyzABCEFGHIJKNOPQRSTUWXYZ";
		$key = array();
		$bagsize = strlen($bag) - 1;
		for ($i = 0; $i < $size; $i++)
		{
			$get = rand(0, $bagsize);
			$key[] = $bag[$get];
		}
		return implode($key);
	}
}
