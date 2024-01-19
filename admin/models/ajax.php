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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Utilities\ArrayHelper;

/**
 * Ehealthportal Ajax List Model
 */
class EhealthportalModelAjax extends ListModel
{
	protected $app_params;

	public function __construct()
	{
		parent::__construct();
		// get params
		$this->app_params = ComponentHelper::getParams('com_ehealthportal');

	}

	// Used in immunisation
	public function getImmunisationVaccineType($administration_part)
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select($db->quoteName( array('a.id') ));
		$query->from($db->quoteName('#__ehealthportal_immunisation_vaccine_type', 'a'));
		$query->where($db->quoteName('a.published') . ' = 1');
		// check for administration part and immunisation vaccine type
		$query->where($db->quoteName('a.administration_part') . ' = '. (int) $administration_part);
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows())
		{
			return $db->loadColumn();
		}
		return false;
	}
}
