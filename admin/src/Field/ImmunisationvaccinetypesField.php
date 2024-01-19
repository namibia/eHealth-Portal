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
namespace VDM\Component\Ehealthportal\Administrator\Field;

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Component\ComponentHelper;
use VDM\Component\Ehealthportal\Administrator\Helper\EhealthportalHelper;

/**
 * Immunisationvaccinetypes Form Field class for the Ehealthportal component
 */
class ImmunisationvaccinetypesField extends ListField
{
	/**
	 * The immunisationvaccinetypes field type.
	 *
	 * @var        string
	 */
	public $type = 'Immunisationvaccinetypes';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return  array    An array of Html options.
	 * @since   1.6
	 */
	protected function getOptions()
	{
		// Get the user object.
		$user = Factory::getUser();
		// Get the databse object.
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('a.id','a.name'),array('id','immunisation_vaccine_type_name')));
		$query->from($db->quoteName('#__ehealthportal_immunisation_vaccine_type', 'a'));
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->order('a.name ASC');
		// Implement View Level Access (if set in table)
		if (!$user->authorise('core.options', 'com_ehealthportal'))
		{
			$columns = $db->getTableColumns('#__ehealthportal_immunisation_vaccine_type');
			if(isset($columns['access']))
			{
				$groups = implode(',', $user->getAuthorisedViewLevels());
				$query->where('a.access IN (' . $groups . ')');
			}
		}
		$db->setQuery((string)$query);
		$items = $db->loadObjectList();
		$options = array();
		if ($items)
		{
			$options[] = Html::_('select.option', '', 'Select an option');
			foreach($items as $item)
			{
				$options[] = Html::_('select.option', $item->id, $item->immunisation_vaccine_type_name);
			}
		}
		return $options;
	}
}
