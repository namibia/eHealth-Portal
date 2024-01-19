<?php
/**
 * @package    eHealth Portal
 *
 * @created    13th August, 2020
 * @author     Llewellyn van der Merwe <https://git.vdm.dev/joomla/eHealth-Portal>
 * @copyright  Copyright (C) 2020 Vast Development Method. All rights reserved.
 * @license    GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Portal for mobile health clinics
 */
namespace VDM\Component\Ehealthportal\Administrator\Helper;

// No direct access to this file
\defined('_JEXEC') or die;

// register additional namespace
\spl_autoload_register(function ($class) {
	// project-specific base directories and namespace prefix
	$search = [
		'libraries/jcb_powers/VDM.Joomla' => 'VDM\\Joomla'
	];
	// Start the search and load if found
	$found = false;
	$found_base_dir = "";
	$found_len = 0;
	foreach ($search as $base_dir => $prefix)
	{
		// does the class use the namespace prefix?
		$len = strlen($prefix);
		if (strncmp($prefix, $class, $len) === 0)
		{
			// we have a match so load the values
			$found = true;
			$found_base_dir = $base_dir;
			$found_len = $len;
			// done here
			break;
		}
	}
	// check if we found a match
	if (!$found)
	{
		// not found so move to the next registered autoloader
		return;
	}
	// get the relative class name
	$relative_class = substr($class, $found_len);
	// replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .php
	$file = JPATH_ROOT . '/' . $found_base_dir . '/src' . str_replace('\\', '/', $relative_class) . '.php';
	// if the file exists, require it
	if (file_exists($file))
	{
		require $file;
	}
});

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Access\Rules as AccessRules;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Language;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;
use Joomla\Database\DatabaseInterface;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use VDM\Joomla\Utilities\StringHelper as UtilitiesStringHelper;
use VDM\Joomla\Utilities\ObjectHelper;
use VDM\Joomla\Utilities\GetHelper;
use VDM\Joomla\Utilities\JsonHelper;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;
use VDM\Joomla\Componentbuilder\Utilities\FormHelper;

/**
 * Ehealthportal component helper.
 */
abstract class EhealthportalHelper
{
	/**
	 * Composer Switch
	 *
	 * @var      array
	 */
	protected static $composer = [];

	/**
	 * The Main Active Language
	 *
	 * @var      string
	 */
	public static $langTag;

	/**
	 * set the user GUID and return it as well
	 *
	 * @param int $id
	 *
	 * @return string
	 */
	public static function setUserGUID($user, $default = 0)
	{
		if ($user > 0)
		{
			if (($guid = self::getVar('user_map', $user, 'id', 'guid')) !== false)
			{
				return $guid;
			}
			// Set the GUID if empty or not valid
			while (!self::validGUID($guid, "user_map"))
			{
				// must always be set
				$guid = (string) self::GUID();
			}
			// store the GUID
			$map = new stdClass();
			$map->id = $user;
			$map->guid = $guid;
			// store to the database
			if (JFactory::getDbo()->insertObject('#__ehealthportal_user_map', $map))
			{
				return $guid;
			}
		}
		return $default;
	}

	/**
	 * Returns a GUIDv4 string
	 * 
	 * Thanks to Dave Pearson (and other)
	 * https://www.php.net/manual/en/function.com-create-guid.php#119168 
	 *
	 * Uses the best cryptographically secure method
	 * for all supported platforms with fallback to an older,
	 * less secure version.
	 *
	 * @param bool $trim
	 * @return string
	 */
	public static function GUID ($trim = true)
	{
		// Windows
		if (function_exists('com_create_guid') === true)
		{
			if ($trim === true)
			{
				return trim(com_create_guid(), '{}');
			}
			return com_create_guid();
		}

		// set the braces if needed
		$lbrace = $trim ? "" : chr(123);    // "{"
		$rbrace = $trim ? "" : chr(125);    // "}"

		// OSX/Linux
		if (function_exists('openssl_random_pseudo_bytes') === true)
		{
			$data = openssl_random_pseudo_bytes(16);
			$data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
			$data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
			return $lbrace . vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)) . $lbrace;
		}

		// Fallback (PHP 4.2+)
		mt_srand((double)microtime() * 10000);
		$charid = strtolower(md5(uniqid(rand(), true)));
		$hyphen = chr(45);                  // "-"
		$guidv4 = $lbrace.
			substr($charid,  0,  8).$hyphen.
			substr($charid,  8,  4).$hyphen.
			substr($charid, 12,  4).$hyphen.
			substr($charid, 16,  4).$hyphen.
			substr($charid, 20, 12).
			$rbrace;
		return $guidv4;
	}

	/**
	 * Validate the Globally Unique Identifier ( and check if table already has this identifier)
	 *
	 * @param string $guid
	 * @param string $table
	 * @param int      $id
	 * @return bool
	 */
	public static function validGUID ($guid, $table = null, $id = 0)
	{
		// check if we have a string
		if (self::validateGUID($guid))
		{
			// check if table already has this identifier
			if (self::checkString($table))
			{
				// Get the database object and a new query object.
				$db = \JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('COUNT(*)')
					->from('#__ehealthportal_' . (string) $table)
					->where($db->quoteName('guid') . ' = ' . $db->quote($guid));

				// remove this item from the list
				if ($id > 0)
				{
					$query->where($db->quoteName('id') . ' <> ' . (int) $id);
				}

				// Set and query the database.
				$db->setQuery($query);
				$duplicate = (bool) $db->loadResult();

				if ($duplicate)
				{
					return false;
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * get the ID of a GUID
	 *
	 * @param string $guid
	 * @param string $table
	 *
	 * @return int
	 */
	public static function getGUIDID ($guid, $table, $default = false)
	{
		// check if we have a string
		if (self::validateGUID($guid))
		{
			// check if table already has this identifier
			if (self::checkString($table))
			{
				// Get the database object and a new query object.
				$db = \JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('id')
					->from('#__ehealthportal_' . (string) $table)
					->where($db->quoteName('guid') . ' = ' . $db->quote($guid));

				// Set and query the database.
				$db->setQuery($query);
				$db->execute();

				if ($db->getNumRows())
				{
					return $db->loadResult();
				}
			}
		}
		return $default;
	}

	/**
	 * Validate the Globally Unique Identifier
	 *
	 * Thanks to Lewie
	 * https://stackoverflow.com/a/1515456/1429677
	 *
	 * @param string $guid
	 * @return bool
	 */
	protected static function validateGUID ($guid)
	{
		// check if we have a string
		if (self::checkString($guid))
		{
			return preg_match("/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)\})$/i", $guid);
		}
		return false;
	}


	/**
	 * Load the Composer Vendors
	 */
	public static function composerAutoload($target)
	{
		// insure we load the composer vendor only once
		if (!isset(self::$composer[$target]))
		{
			// get the function name
			$functionName = UtilitiesStringHelper::safe('compose' . $target);
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
		$manifestUrl = JPATH_ADMINISTRATOR."/components/com_ehealthportal/ehealthportal.xml";
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
		if (!ObjectHelper::check(self::$JVersion))
		{
			self::$JVersion = new Version();
		}
		return self::$JVersion;
	}

	/**
	 * Load the Contributors details.
	 */
	public static function getContributors()
	{
		// get params
		$params    = ComponentHelper::getParams('com_ehealthportal');
		// start contributors array
		$contributors = [];
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
				$contributors[$nr]['title']   = UtilitiesStringHelper::html($params->get("titleContributor".$nr));
				$contributors[$nr]['name']    = $link_front.UtilitiesStringHelper::html($params->get("nameContributor".$nr)).$link_back;
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
		$user = Factory::getApplication()->getIdentity();
		// load the submenus to sidebar
		JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_DASHBOARD'), 'index.php?option=com_ehealthportal&view=ehealthportal', $submenu === 'ehealthportal');
		if ($user->authorise('payment.access', 'com_ehealthportal') && $user->authorise('payment.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_PAYMENTS'), 'index.php?option=com_ehealthportal&view=payments', $submenu === 'payments');
		}
		if ($user->authorise('general_medical_check_up.access', 'com_ehealthportal') && $user->authorise('general_medical_check_up.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_GENERAL_MEDICAL_CHECK_UPS'), 'index.php?option=com_ehealthportal&view=general_medical_check_ups', $submenu === 'general_medical_check_ups');
		}
		if ($user->authorise('antenatal_care.access', 'com_ehealthportal') && $user->authorise('antenatal_care.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_ANTENATAL_CARES'), 'index.php?option=com_ehealthportal&view=antenatal_cares', $submenu === 'antenatal_cares');
		}
		if ($user->authorise('immunisation.access', 'com_ehealthportal') && $user->authorise('immunisation.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_IMMUNISATIONS'), 'index.php?option=com_ehealthportal&view=immunisations', $submenu === 'immunisations');
		}
		if ($user->authorise('vmmc.access', 'com_ehealthportal') && $user->authorise('vmmc.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_VMMCS'), 'index.php?option=com_ehealthportal&view=vmmcs', $submenu === 'vmmcs');
		}
		if ($user->authorise('prostate_and_testicular_cancer.access', 'com_ehealthportal') && $user->authorise('prostate_and_testicular_cancer.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_PROSTATE_AND_TESTICULAR_CANCERS'), 'index.php?option=com_ehealthportal&view=prostate_and_testicular_cancers', $submenu === 'prostate_and_testicular_cancers');
		}
		if ($user->authorise('tuberculosis.access', 'com_ehealthportal') && $user->authorise('tuberculosis.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_TUBERCULOSES'), 'index.php?option=com_ehealthportal&view=tuberculoses', $submenu === 'tuberculoses');
		}
		if ($user->authorise('hiv_counseling_and_testing.access', 'com_ehealthportal') && $user->authorise('hiv_counseling_and_testing.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_HIV_COUNSELINGS_AND_TESTINGS'), 'index.php?option=com_ehealthportal&view=hiv_counselings_and_testings', $submenu === 'hiv_counselings_and_testings');
		}
		if ($user->authorise('family_planning.access', 'com_ehealthportal') && $user->authorise('family_planning.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_FAMILY_PLANNINGS'), 'index.php?option=com_ehealthportal&view=family_plannings', $submenu === 'family_plannings');
		}
		if ($user->authorise('health_education.access', 'com_ehealthportal') && $user->authorise('health_education.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_HEALTH_EDUCATIONS'), 'index.php?option=com_ehealthportal&view=health_educations', $submenu === 'health_educations');
		}
		if ($user->authorise('cervical_cancer.access', 'com_ehealthportal') && $user->authorise('cervical_cancer.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_CERVICAL_CANCERS'), 'index.php?option=com_ehealthportal&view=cervical_cancers', $submenu === 'cervical_cancers');
		}
		if ($user->authorise('breast_cancer.access', 'com_ehealthportal') && $user->authorise('breast_cancer.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_BREAST_CANCERS'), 'index.php?option=com_ehealthportal&view=breast_cancers', $submenu === 'breast_cancers');
		}
		if ($user->authorise('test.access', 'com_ehealthportal') && $user->authorise('test.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_TESTS'), 'index.php?option=com_ehealthportal&view=tests', $submenu === 'tests');
		}
		if ($user->authorise('immunisation_vaccine_type.access', 'com_ehealthportal') && $user->authorise('immunisation_vaccine_type.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_IMMUNISATION_VACCINE_TYPES'), 'index.php?option=com_ehealthportal&view=immunisation_vaccine_types', $submenu === 'immunisation_vaccine_types');
		}
		if ($user->authorise('immunisation_type.access', 'com_ehealthportal') && $user->authorise('immunisation_type.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_IMMUNISATION_TYPES'), 'index.php?option=com_ehealthportal&view=immunisation_types', $submenu === 'immunisation_types');
		}
		if ($user->authorise('strength.access', 'com_ehealthportal') && $user->authorise('strength.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_STRENGTHS'), 'index.php?option=com_ehealthportal&view=strengths', $submenu === 'strengths');
		}
		if ($user->authorise('referral.access', 'com_ehealthportal') && $user->authorise('referral.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_REFERRALS'), 'index.php?option=com_ehealthportal&view=referrals', $submenu === 'referrals');
		}
		if ($user->authorise('medication.access', 'com_ehealthportal') && $user->authorise('medication.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_MEDICATIONS'), 'index.php?option=com_ehealthportal&view=medications', $submenu === 'medications');
		}
		if ($user->authorise('administration_part.access', 'com_ehealthportal') && $user->authorise('administration_part.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_ADMINISTRATION_PARTS'), 'index.php?option=com_ehealthportal&view=administration_parts', $submenu === 'administration_parts');
		}
		if ($user->authorise('unit.access', 'com_ehealthportal') && $user->authorise('unit.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_UNITS'), 'index.php?option=com_ehealthportal&view=units', $submenu === 'units');
		}
		if ($user->authorise('patient_queue.access', 'com_ehealthportal') && $user->authorise('patient_queue.submenu', 'com_ehealthportal'))
		{
			JHtmlSidebar::addEntry(Text::_('COM_EHEALTHPORTAL_SUBMENU_PATIENT_QUEUE'), 'index.php?option=com_ehealthportal&view=patient_queue', $submenu === 'patient_queue');
		}
	}

	/**
	* Prepares the xml document
	*/
	public static function xls($rows, $fileName = null, $title = null, $subjectTab = null, $creator = 'Vast Development Method', $description = null, $category = null,$keywords = null, $modified = null)
	{
		// set the user
		// set fileName if not set
		if (!$fileName)
		{
			$fileName = 'exported_'.Factory::getDate()->format('jS_F_Y');
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
		$session = Factory::getSession();
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
			$headers = [];
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
	 * @deprecated 3.3 Use GetHelper::var(...);
	 */
	public static function getVar($table, $where = null, $whereString = 'user', $what = 'id', $operator = '=', $main = 'ehealthportal')
	{
		return GetHelper::var(
			$table,
			$where,
			$whereString,
			$what,
			$operator,
			$main
		);
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
	 * @deprecated 3.3 Use GetHelper::vars(...);
	 */
	public static function getVars($table, $where = null, $whereString = 'user', $what = 'id', $operator = 'IN', $main = 'ehealthportal', $unique = true)
	{
		return GetHelper::vars(
			$table,
			$where,
			$whereString,
			$what,
			$operator,
			$main,
			$unique
		);
	}

	/**
	 * Convert a json object to a string
	 *
	 * @input    string  $value  The json string to convert
	 *
	 * @returns a string
	 * @deprecated 3.3 Use JsonHelper::string(...);
	 */
	public static function jsonToString($value, $sperator = ", ", $table = null, $id = 'id', $name = 'name')
	{
		return JsonHelper::string(
			$value,
			$sperator,
			$table,
			$id,
			$name
		);
	}

	public static function isPublished($id,$type)
	{
		if ($type == 'raw')
		{
			$type = 'item';
		}
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);
		$query->select(array('a.published'));
		$query->from('#__ehealthportal_'.$type.' AS a');
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
		$db = Factory::getContainer()->get(DatabaseInterface::class);
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
	 * @return  object   The CMSObject of permission/authorised actions
	 *
	 */
	public static function getActions($view, &$record = null, $views = null, $target = null, $component = 'ehealthportal', $user = 'null')
	{
		// load the user if not given
		if (!ObjectHelper::check($user))
		{
			// get the user object
			$user = Factory::getApplication()->getIdentity();
		}
		// load the CMSObject
		$result = new CMSObject;
		// make view name safe (just incase)
		$view = UtilitiesStringHelper::safe($view);
		if (UtilitiesStringHelper::check($views))
		{
			$views = UtilitiesStringHelper::safe($views);
		 }
		// get all actions from component
		$actions = Access::getActionsFromFile(
			JPATH_ADMINISTRATOR . '/components/com_' . $component . '/access.xml',
			"/access/section[@name='component']/"
		);
		// if non found then return empty CMSObject
		if (empty($actions))
		{
			return $result;
		}
		// get created by if not found
		if (ObjectHelper::check($record) && !isset($record->created_by) && isset($record->id))
		{
			$record->created_by = GetHelper::var($view, $record->id, 'id', 'created_by', '=', $component);
		}
		// set actions only set in component settings
		$componentActions = array('core.admin', 'core.manage', 'core.options', 'core.export');
		// check if we have a target
		$checkTarget = false;
		if ($target)
		{
			// convert to an array
			if (UtilitiesStringHelper::check($target))
			{
				$target = array($target);
			}
			// check if we are good to go
			if (UtilitiesArrayHelper::check($target))
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
			if (ObjectHelper::check($record) && isset($record->id) && $record->id > 0 && !in_array($action->name, $componentActions) &&
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
				elseif (UtilitiesStringHelper::check($views) && isset($record->catid) && $record->catid > 0)
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
	public static function getModel($name, $path = JPATH_COMPONENT_ADMINISTRATOR, $Component = 'Ehealthportal', $config = [])
	{
		// fix the name
		$name = UtilitiesStringHelper::safe($name);
		// full path to models
		$fullPathModels = $path . '/models';
		// load the model file
		BaseDatabaseModel::addIncludePath($fullPathModels, $Component . 'Model');
		// make sure the table path is loaded
		if (!isset($config['table_path']) || !UtilitiesStringHelper::check($config['table_path']))
		{
			// This is the JCB default path to tables in Joomla 3.x
			$config['table_path'] = JPATH_ADMINISTRATOR . '/components/com_' . strtolower($Component) . '/tables';
		}
		// get instance
		$model = BaseDatabaseModel::getInstance($name, $Component . 'Model', $config);
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
		$parent = Table::getInstance('Asset');
		$parent->loadByName('com_ehealthportal');

		$parentId = $parent->id;
		$name     = 'com_ehealthportal.'.$table.'.'.$id;
		$title    = '';

		$asset = Table::getInstance('Asset');
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
			$rules = self::getDefaultAssetRules('com_ehealthportal', $table, $inherit);
			if ($rules instanceof AccessRules)
			{
				$asset->rules = (string) $rules;
			}

			if (!$asset->check() || !$asset->store())
			{
				Factory::getApplication()->enqueueMessage($asset->getError(), 'warning');
				return false;
			}
			else
			{
				// Create an asset_id or heal one that is corrupted.
				$object = new \StdClass();

				// Must be a valid primary key value.
				$object->id = $id;
				$object->asset_id = (int) $asset->id;

				// Update their asset_id to link to the asset table.
				return Factory::getDbo()->updateObject('#__ehealthportal_'.$table, $object, 'id');
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
			$db = Factory::getContainer()->get(DatabaseInterface::class);
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
		$result =  Access::getAssetRules($assetId);
		if ($result instanceof AccessRules)
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
					$rule = [];
				}
			}
			// check if there are any view values remaining
			if (count((array) $_result))
			{
				$_result = json_encode($_result);
				$_result = array($_result);
				// Instantiate and return the AccessRules object for the asset rules.
				$rules = new AccessRules($_result);
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
	 * @return  void
	 * @deprecated 3.3 Use FormHelper::append($xml, $node);
	 */
	public static function xmlAppend(&$xml, $node)
	{
		FormHelper::append($xml, $node);
	}

	/**
	 * xmlComment
	 *
	 * @param   SimpleXMLElement   $xml        The XML element reference in which to inject a comment
	 * @param   string             $comment    The comment to inject
	 *
	 * @return  void
	 * @deprecated 3.3 Use FormHelper::comment($xml, $comment);
	 */
	public static function xmlComment(&$xml, $comment)
	{
		FormHelper::comment($xml, $comment);
	}

	/**
	 * xmlAddAttributes
	 *
	 * @param   SimpleXMLElement   $xml          The XML element reference in which to inject a comment
	 * @param   array              $attributes   The attributes to apply to the XML element
	 *
	 * @return  null
	 * @deprecated 3.3 Use FormHelper::attributes($xml, $attributes);
	 */
	public static function xmlAddAttributes(&$xml, $attributes = [])
	{
		FormHelper::attributes($xml, $attributes);
	}

	/**
	 * xmlAddOptions
	 *
	 * @param   SimpleXMLElement   $xml          The XML element reference in which to inject a comment
	 * @param   array              $options      The options to apply to the XML element
	 *
	 * @return  void
	 * @deprecated 3.3 Use FormHelper::options($xml, $options);
	 */
	public static function xmlAddOptions(&$xml, $options = [])
	{
		FormHelper::options($xml, $options);
	}

	/**
	 * get the field object
	 *
	 * @param   array      $attributes   The array of attributes
	 * @param   string     $default      The default of the field
	 * @param   array      $options      The options to apply to the XML element
	 *
	 * @return  object
	 * @deprecated 3.3 Use FormHelper::field($attributes, $default, $options);
	 */
	public static function getFieldObject(&$attributes, $default = '', $options = null)
	{
		return FormHelper::field($attributes, $default, $options);
	}

	/**
	 * get the field xml
	 *
	 * @param   array      $attributes   The array of attributes
	 * @param   array      $options      The options to apply to the XML element
	 *
	 * @return  object
	 * @deprecated 3.3 Use FormHelper::xml($attributes, $options);
	 */
	public static function getFieldXML(&$attributes, $options = null)
	{
		return FormHelper::xml($attributes, $options);
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
			'name' => isset($args[0]) ? UtilitiesStringHelper::html($args[0]) : 'bool_button',
			'label' => isset($args[0]) ? UtilitiesStringHelper::safe(UtilitiesStringHelper::html($args[0]), 'Ww') : 'Bool Button', // not seen anyway
			'class' => 'btn-group',
			'filter' => 'INT',
			'default' => isset($args[2]) ? (int) $args[2] : 0);
		// set the button options
		$buttonOptions = array(
			'1' => isset($args[3]) ? UtilitiesStringHelper::html($args[3]) : 'JYES',
			'0' => isset($args[4]) ? UtilitiesStringHelper::html($args[4]) : 'JNO');
		// return the input
		return FormHelper::field($buttonAttributes, $buttonAttributes['default'], $buttonOptions)->input;
	}

	/**
	 * Check if have an json string
	 *
	 * @input    string   The json string to check
	 *
	 * @returns bool true on success
	 * @deprecated 3.3 Use JsonHelper::check($string);
	 */
	public static function checkJson($string)
	{
		return JsonHelper::check($string);
	}

	/**
	 * Check if have an object with a length
	 *
	 * @input    object   The object to check
	 *
	 * @returns bool true on success
	 * @deprecated 3.3 Use ObjectHelper::check($object);
	 */
	public static function checkObject($object)
	{
		return ObjectHelper::check($object);
	}

	/**
	 * Check if have an array with a length
	 *
	 * @input    array   The array to check
	 *
	 * @returns bool/int  number of items in array on success
	 * @deprecated 3.3 Use UtilitiesArrayHelper::check($array, $removeEmptyString);
	 */
	public static function checkArray($array, $removeEmptyString = false)
	{
		return UtilitiesArrayHelper::check($array, $removeEmptyString);
	}

	/**
	 * Check if have a string with a length
	 *
	 * @input    string   The string to check
	 *
	 * @returns bool true on success
	 * @deprecated 3.3 Use UtilitiesStringHelper::check($string);
	 */
	public static function checkString($string)
	{
		return UtilitiesStringHelper::check($string);
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
	 * @input    array   The arrays you would like to merge
	 *
	 * @returns array on success
	 * @deprecated 3.3 Use UtilitiesArrayHelper::merge($arrays);
	 */
	public static function mergeArrays($arrays)
	{
		return UtilitiesArrayHelper::merge($arrays);
	}

	// typo sorry!
	public static function sorten($string, $length = 40, $addTip = true)
	{
		return self::shorten($string, $length, $addTip);
	}

	/**
	 * Shorten a string
	 *
	 * @input    string   The you would like to shorten
	 *
	 * @returns string on success
	 * @deprecated 3.3 Use UtilitiesStringHelper::shorten(...);
	 */
	public static function shorten($string, $length = 40, $addTip = true)
	{
		return UtilitiesStringHelper::shorten($string, $length, $addTip);
	}

	/**
	 * Making strings safe (various ways)
	 *
	 * @input    string   The you would like to make safe
	 *
	 * @returns string on success
	 * @deprecated 3.3 Use UtilitiesStringHelper::safe(...);
	 */
	public static function safeString($string, $type = 'L', $spacer = '_', $replaceNumbers = true, $keepOnlyCharacters = true)
	{
		return UtilitiesStringHelper::safe(
			$string,
			$type,
			$spacer,
			$replaceNumbers,
			$keepOnlyCharacters
		);
	}

	/**
	 * Convert none English strings to code usable string
	 *
	 * @input    an string
	 *
	 * @returns a string
	 * @deprecated 3.3 Use UtilitiesStringHelper::transliterate($string);
	 */
	public static function transliterate($string)
	{
		return UtilitiesStringHelper::transliterate($string);
	}

	/**
	 * make sure a string is HTML save
	 *
	 * @input    an html string
	 *
	 * @returns a string
	 * @deprecated 3.3 Use UtilitiesStringHelper::html(...);
	 */
	public static function htmlEscape($var, $charset = 'UTF-8', $shorten = false, $length = 40)
	{
		return UtilitiesStringHelper::html(
			$var,
			$charset,
			$shorten,
			$length
		);
	}

	/**
	 * Convert all int in a string to an English word string
	 *
	 * @input    an string with numbers
	 *
	 * @returns a string
	 * @deprecated 3.3 Use UtilitiesStringHelper::numbers($string);
	 */
	public static function replaceNumbers($string)
	{
		return UtilitiesStringHelper::numbers($string);
	}

	/**
	 * Convert an integer into an English word string
	 * Thanks to Tom Nicholson <http://php.net/manual/en/function.strval.php#41988>
	 *
	 * @input    an int
	 * @returns a string
	 * @deprecated 3.3 Use UtilitiesStringHelper::number($x);
	 */
	public static function numberToString($x)
	{
		return UtilitiesStringHelper::number($x);
	}

	/**
	 * Random Key
	 *
	 * @returns a string
	 * @deprecated 3.3 Use UtilitiesStringHelper::random($size);
	 */
	public static function randomkey($size)
	{
		return UtilitiesStringHelper::random($size);
	}
}
