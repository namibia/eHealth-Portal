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
namespace VDM\Component\Ehealthportal\Administrator\Model;

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Helper\TagsHelper;
use VDM\Component\Ehealthportal\Administrator\Helper\EhealthportalHelper;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;
use VDM\Joomla\Utilities\ObjectHelper;
use VDM\Joomla\Utilities\StringHelper;

/**
 * Vmmcs List Model
 */
class VmmcsModel extends ListModel
{
	public function __construct($config = [])
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'a.id','id',
				'a.published','published',
				'a.access','access',
				'a.ordering','ordering',
				'a.created_by','created_by',
				'a.modified_by','modified_by',
				'a.patient','patient',
				'g.name','referral'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = Factory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		// Check if the form was submitted
		$formSubmited = $app->input->post->get('form_submited');

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', 0, 'int');
		if ($formSubmited)
		{
			$access = $app->input->post->get('access');
			$this->setState('filter.access', $access);
		}

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$created_by = $this->getUserStateFromRequest($this->context . '.filter.created_by', 'filter_created_by', '');
		$this->setState('filter.created_by', $created_by);

		$created = $this->getUserStateFromRequest($this->context . '.filter.created', 'filter_created');
		$this->setState('filter.created', $created);

		$sorting = $this->getUserStateFromRequest($this->context . '.filter.sorting', 'filter_sorting', 0, 'int');
		$this->setState('filter.sorting', $sorting);

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$patient = $this->getUserStateFromRequest($this->context . '.filter.patient', 'filter_patient');
		if ($formSubmited)
		{
			$patient = $app->input->post->get('patient');
			$this->setState('filter.patient', $patient);
		}

		$referral = $this->getUserStateFromRequest($this->context . '.filter.referral', 'filter_referral');
		if ($formSubmited)
		{
			$referral = $app->input->post->get('referral');
			$this->setState('filter.referral', $referral);
		}

		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		// Check in items
		$this->checkInNow();

		// load parent items
		$items = parent::getItems();

		// Set values to display correctly.
		if (UtilitiesArrayHelper::check($items))
		{
			// Get the user object if not set.
			if (!isset($user) || !ObjectHelper::check($user))
			{
				$user = Factory::getApplication()->getIdentity();
			}
			foreach ($items as $nr => &$item)
			{
				$item->patient = EhealthportalHelper::getGUIDID($item->patient, 'user_map');
			}
		}

		// set selection value to a translatable value
		if (UtilitiesArrayHelper::check($items))
		{
			foreach ($items as $nr => &$item)
			{
				// convert are_you_circumcised
				$item->are_you_circumcised = $this->selectionTranslation($item->are_you_circumcised, 'are_you_circumcised');
				// convert info_ben_vmcc
				$item->info_ben_vmcc = $this->selectionTranslation($item->info_ben_vmcc, 'info_ben_vmcc');
				// convert interested_in_vmmc
				$item->interested_in_vmmc = $this->selectionTranslation($item->interested_in_vmmc, 'interested_in_vmmc');
				// convert vmmc_gender
				$item->vmmc_gender = $this->selectionTranslation($item->vmmc_gender, 'vmmc_gender');
			}
		}


		// return items
		return $items;
	}

	/**
	 * Method to convert selection values to translatable string.
	 *
	 * @return translatable string
	 */
	public function selectionTranslation($value,$name)
	{
		// Array of are_you_circumcised language strings
		if ($name === 'are_you_circumcised')
		{
			$are_you_circumcisedArray = array(
				0 => 'COM_EHEALTHPORTAL_VMMC_YES',
				1 => 'COM_EHEALTHPORTAL_VMMC_NO'
			);
			// Now check if value is found in this array
			if (isset($are_you_circumcisedArray[$value]) && StringHelper::check($are_you_circumcisedArray[$value]))
			{
				return $are_you_circumcisedArray[$value];
			}
		}
		// Array of info_ben_vmcc language strings
		if ($name === 'info_ben_vmcc')
		{
			$info_ben_vmccArray = array(
				0 => 'COM_EHEALTHPORTAL_VMMC_YES',
				1 => 'COM_EHEALTHPORTAL_VMMC_NO'
			);
			// Now check if value is found in this array
			if (isset($info_ben_vmccArray[$value]) && StringHelper::check($info_ben_vmccArray[$value]))
			{
				return $info_ben_vmccArray[$value];
			}
		}
		// Array of interested_in_vmmc language strings
		if ($name === 'interested_in_vmmc')
		{
			$interested_in_vmmcArray = array(
				0 => 'COM_EHEALTHPORTAL_VMMC_YES',
				1 => 'COM_EHEALTHPORTAL_VMMC_NO'
			);
			// Now check if value is found in this array
			if (isset($interested_in_vmmcArray[$value]) && StringHelper::check($interested_in_vmmcArray[$value]))
			{
				return $interested_in_vmmcArray[$value];
			}
		}
		// Array of vmmc_gender language strings
		if ($name === 'vmmc_gender')
		{
			$vmmc_genderArray = array(
				0 => 'COM_EHEALTHPORTAL_VMMC_MALE',
				1 => 'COM_EHEALTHPORTAL_VMMC_FEMALE'
			);
			// Now check if value is found in this array
			if (isset($vmmc_genderArray[$value]) && StringHelper::check($vmmc_genderArray[$value]))
			{
				return $vmmc_genderArray[$value];
			}
		}
		return $value;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return    string    An SQL query
	 */
	protected function getListQuery()
	{
		// Get the user object.
		$user = Factory::getApplication()->getIdentity();
		// Create a new query object.
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');

		// From the ehealthportal_item table
		$query->from($db->quoteName('#__ehealthportal_vmmc', 'a'));

		// From the ehealthportal_referral table.
		$query->select($db->quoteName('g.name','referral_name'));
		$query->join('LEFT', $db->quoteName('#__ehealthportal_referral', 'g') . ' ON (' . $db->quoteName('a.referral') . ' = ' . $db->quoteName('g.id') . ')');

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published = 0 OR a.published = 1)');
		}

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');
		// Filter by access level.
		$_access = $this->getState('filter.access');
		if ($_access && is_numeric($_access))
		{
			$query->where('a.access = ' . (int) $_access);
		}
		elseif (EhealthportalHelper::checkArray($_access))
		{
			// Secure the array for the query
			$_access = ArrayHelper::toInteger($_access);
			// Filter by the Access Array.
			$query->where('a.access IN (' . implode(',', $_access) . ')');
		}
		// Implement View Level Access
		if (!$user->authorise('core.options', 'com_ehealthportal'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}
		// Filter by search.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search) . '%');
				$query->where('(a.patient LIKE '.$search.')');
			}
		}

		// Filter by Patient.
		$_patient = $this->getState('filter.patient');
		if (is_numeric($_patient))
		{
			if (is_float($_patient))
			{
				$query->where('a.patient = ' . (float) $_patient);
			}
			else
			{
				$query->where('a.patient = ' . (int) $_patient);
			}
		}
		elseif (EhealthportalHelper::checkString($_patient))
		{
			$query->where('a.patient = ' . $db->quote($db->escape($_patient)));
		}
		// Filter by Referral.
		$_referral = $this->getState('filter.referral');
		if (is_numeric($_referral))
		{
			if (is_float($_referral))
			{
				$query->where('a.referral = ' . (float) $_referral);
			}
			else
			{
				$query->where('a.referral = ' . (int) $_referral);
			}
		}
		elseif (EhealthportalHelper::checkString($_referral))
		{
			$query->where('a.referral = ' . $db->quote($db->escape($_referral)));
		}
		elseif (EhealthportalHelper::checkArray($_referral))
		{
			// Secure the array for the query
			$_referral = array_map( function ($val) use(&$db) {
				if (is_numeric($val))
				{
					if (is_float($val))
					{
						return (float) $val;
					}
					else
					{
						return (int) $val;
					}
				}
				elseif (EhealthportalHelper::checkString($val))
				{
					return $db->quote($db->escape($val));
				}
			}, $_referral);
			// Filter by the Referral Array.
			$query->where('a.referral IN (' . implode(',', $_referral) . ')');
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'desc');
		if ($orderCol != '')
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Method to get list export data.
	 *
	 * @param   array  $pks  The ids of the items to get
	 * @param   JUser  $user  The user making the request
	 *
	 * @return mixed  An array of data items on success, false on failure.
	 */
	public function getExportData($pks, $user = null)
	{
		// setup the query
		if (($pks_size = UtilitiesArrayHelper::check($pks)) !== false || 'bulk' === $pks)
		{
			// Set a value to know this is export method. (USE IN CUSTOM CODE TO ALTER OUTCOME)
			$_export = true;
			// Get the user object if not set.
			if (!isset($user) || !ObjectHelper::check($user))
			{
				$user = Factory::getApplication()->getIdentity();
			}
			// Create a new query object.
			$db = $this->getDatabase();
			$query = $db->getQuery(true);

			// Select some fields
			$query->select('a.*');

			// From the ehealthportal_vmmc table
			$query->from($db->quoteName('#__ehealthportal_vmmc', 'a'));
			// The bulk export path
			if ('bulk' === $pks)
			{
				$query->where('a.id > 0');
			}
			// A large array of ID's will not work out well
			elseif ($pks_size > 500)
			{
				// Use lowest ID
				$query->where('a.id >= ' . (int) min($pks));
				// Use highest ID
				$query->where('a.id <= ' . (int) max($pks));
			}
			// The normal default path
			else
			{
				$query->where('a.id IN (' . implode(',',$pks) . ')');
			}
			// Get global switch to activate text only export
			$export_text_only = ComponentHelper::getParams('com_ehealthportal')->get('export_text_only', 0);
			// Add these queries only if text only is required
			if ($export_text_only)
			{

				// From the ehealthportal_referral table.
				$query->select($db->quoteName('g.name','referral'));
				$query->join('LEFT', $db->quoteName('#__ehealthportal_referral', 'g') . ' ON (' . $db->quoteName('a.referral') . ' = ' . $db->quoteName('g.id') . ')');
			}
			// Implement View Level Access
			if (!$user->authorise('core.options', 'com_ehealthportal'))
			{
				$groups = implode(',', $user->getAuthorisedViewLevels());
				$query->where('a.access IN (' . $groups . ')');
			}

			// Order the results by ordering
			$query->order('a.ordering  ASC');

			// Load the items
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows())
			{
				$items = $db->loadObjectList();

				// Set values to display correctly.
				if (UtilitiesArrayHelper::check($items))
				{
					foreach ($items as $nr => &$item)
					{
						$item->patient = EhealthportalHelper::getGUIDID($item->patient, 'user_map');
						// unset the values we don't want exported.
						unset($item->asset_id);
						unset($item->checked_out);
						unset($item->checked_out_time);
					}
				}
				// Add headers to items array.
				$headers = $this->getExImPortHeaders();
				if (ObjectHelper::check($headers))
				{
					array_unshift($items,$headers);
				}
			// Add these translation only if text only is required
			if ($export_text_only)
			{

					// set selection value to a translatable value
					if (UtilitiesArrayHelper::check($items))
					{
						foreach ($items as $nr => &$item)
						{
							// convert are_you_circumcised
							$item->are_you_circumcised = $this->selectionTranslation($item->are_you_circumcised, 'are_you_circumcised');
							// convert info_ben_vmcc
							$item->info_ben_vmcc = $this->selectionTranslation($item->info_ben_vmcc, 'info_ben_vmcc');
							// convert interested_in_vmmc
							$item->interested_in_vmmc = $this->selectionTranslation($item->interested_in_vmmc, 'interested_in_vmmc');
							// convert vmmc_gender
							$item->vmmc_gender = $this->selectionTranslation($item->vmmc_gender, 'vmmc_gender');
						}
					}

			}
				return $items;
			}
		}
		return false;
	}

	/**
	* Method to get header.
	*
	* @return mixed  An array of data items on success, false on failure.
	*/
	public function getExImPortHeaders()
	{
		// Get a db connection.
		$db = Factory::getDbo();
		// get the columns
		$columns = $db->getTableColumns("#__ehealthportal_vmmc");
		if (UtilitiesArrayHelper::check($columns))
		{
			// remove the headers you don't import/export.
			unset($columns['asset_id']);
			unset($columns['checked_out']);
			unset($columns['checked_out_time']);
			$headers = new stdClass();
			foreach ($columns as $column => $type)
			{
				$headers->{$column} = $column;
			}
			return $headers;
		}
		return false;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @return  string  A store id.
	 *
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.id');
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		// Check if the value is an array
		$_access = $this->getState('filter.access');
		if (UtilitiesArrayHelper::check($_access))
		{
			$id .= ':' . implode(':', $_access);
		}
		// Check if this is only an number or string
		elseif (is_numeric($_access)
		 || StringHelper::check($_access))
		{
			$id .= ':' . $_access;
		}
		$id .= ':' . $this->getState('filter.ordering');
		$id .= ':' . $this->getState('filter.created_by');
		$id .= ':' . $this->getState('filter.modified_by');
		$id .= ':' . $this->getState('filter.patient');
		// Check if the value is an array
		$_referral = $this->getState('filter.referral');
		if (UtilitiesArrayHelper::check($_referral))
		{
			$id .= ':' . implode(':', $_referral);
		}
		// Check if this is only an number or string
		elseif (is_numeric($_referral)
		 || StringHelper::check($_referral))
		{
			$id .= ':' . $_referral;
		}

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to checkin all items left checked out longer then a set time.
	 *
	 * @return  a bool
	 *
	 */
	protected function checkInNow()
	{
		// Get set check in time
		$time = ComponentHelper::getParams('com_ehealthportal')->get('check_in');

		if ($time)
		{

			// Get a db connection.
			$db = $this->getDatabase();
			// Reset query.
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__ehealthportal_vmmc'));
			// Only select items that are checked out.
			$query->where($db->quoteName('checked_out') . '!=0');
			$db->setQuery($query, 0, 1);
			$db->execute();
			if ($db->getNumRows())
			{
				// Get Yesterdays date.
				$date = Factory::getDate()->modify($time)->toSql();
				// Reset query.
				$query = $db->getQuery(true);

				// Fields to update.
				$fields = array(
					$db->quoteName('checked_out_time') . '=\'0000-00-00 00:00:00\'',
					$db->quoteName('checked_out') . '=0'
				);

				// Conditions for which records should be updated.
				$conditions = array(
					$db->quoteName('checked_out') . '!=0', 
					$db->quoteName('checked_out_time') . '<\''.$date.'\''
				);

				// Check table.
				$query->update($db->quoteName('#__ehealthportal_vmmc'))->set($fields)->where($conditions); 

				$db->setQuery($query);

				$db->execute();
			}
		}

		return false;
	}
}
