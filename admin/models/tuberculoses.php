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

	@version		3.0.0
	@build			19th January, 2024
	@created		19th January, 2024
	@package		eHealth Portal
	@subpackage		tuberculoses.php
	@author			Llewellyn van der Merwe <https://git.vdm.dev/joomla/eHealth-Portal>
	@copyright		Copyright (C) 2020 Vast Development Method. All rights reserved.
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Portal for mobile health clinics

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Helper\TagsHelper;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;
use VDM\Joomla\Utilities\ObjectHelper;
use VDM\Joomla\Utilities\StringHelper;

/**
 * Tuberculoses List Model
 */
class EhealthportalModelTuberculoses extends ListModel
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
				'a.patient','patient'
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
				$user = Factory::getUser();
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
				// convert recurring_night_sweats
				$item->recurring_night_sweats = $this->selectionTranslation($item->recurring_night_sweats, 'recurring_night_sweats');
				// convert tb_fever
				$item->tb_fever = $this->selectionTranslation($item->tb_fever, 'tb_fever');
				// convert persistent_cough
				$item->persistent_cough = $this->selectionTranslation($item->persistent_cough, 'persistent_cough');
				// convert blood_streaked_sputum
				$item->blood_streaked_sputum = $this->selectionTranslation($item->blood_streaked_sputum, 'blood_streaked_sputum');
				// convert unusual_tiredness
				$item->unusual_tiredness = $this->selectionTranslation($item->unusual_tiredness, 'unusual_tiredness');
				// convert pain_in_chest
				$item->pain_in_chest = $this->selectionTranslation($item->pain_in_chest, 'pain_in_chest');
				// convert shortness_of_breath
				$item->shortness_of_breath = $this->selectionTranslation($item->shortness_of_breath, 'shortness_of_breath');
				// convert diagnosed_with_disease
				$item->diagnosed_with_disease = $this->selectionTranslation($item->diagnosed_with_disease, 'diagnosed_with_disease');
				// convert tb_exposed
				$item->tb_exposed = $this->selectionTranslation($item->tb_exposed, 'tb_exposed');
				// convert tb_treatment
				$item->tb_treatment = $this->selectionTranslation($item->tb_treatment, 'tb_treatment');
				// convert sputum_collection_one
				$item->sputum_collection_one = $this->selectionTranslation($item->sputum_collection_one, 'sputum_collection_one');
				// convert sputum_result_one
				$item->sputum_result_one = $this->selectionTranslation($item->sputum_result_one, 'sputum_result_one');
				// convert referred_second_sputum
				$item->referred_second_sputum = $this->selectionTranslation($item->referred_second_sputum, 'referred_second_sputum');
				// convert sputum_result_two
				$item->sputum_result_two = $this->selectionTranslation($item->sputum_result_two, 'sputum_result_two');
				// convert weight_loss_wdieting
				$item->weight_loss_wdieting = $this->selectionTranslation($item->weight_loss_wdieting, 'weight_loss_wdieting');
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
		// Array of recurring_night_sweats language strings
		if ($name === 'recurring_night_sweats')
		{
			$recurring_night_sweatsArray = array(
				0 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_YES',
				1 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_NO',
				2 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_UNCERTAIN'
			);
			// Now check if value is found in this array
			if (isset($recurring_night_sweatsArray[$value]) && StringHelper::check($recurring_night_sweatsArray[$value]))
			{
				return $recurring_night_sweatsArray[$value];
			}
		}
		// Array of tb_fever language strings
		if ($name === 'tb_fever')
		{
			$tb_feverArray = array(
				0 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_YES',
				1 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_NO',
				2 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_UNCERTAIN'
			);
			// Now check if value is found in this array
			if (isset($tb_feverArray[$value]) && StringHelper::check($tb_feverArray[$value]))
			{
				return $tb_feverArray[$value];
			}
		}
		// Array of persistent_cough language strings
		if ($name === 'persistent_cough')
		{
			$persistent_coughArray = array(
				0 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_YES',
				1 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_NO',
				2 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_UNCERTAIN'
			);
			// Now check if value is found in this array
			if (isset($persistent_coughArray[$value]) && StringHelper::check($persistent_coughArray[$value]))
			{
				return $persistent_coughArray[$value];
			}
		}
		// Array of blood_streaked_sputum language strings
		if ($name === 'blood_streaked_sputum')
		{
			$blood_streaked_sputumArray = array(
				0 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_YES',
				1 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_NO',
				2 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_UNCERTAIN'
			);
			// Now check if value is found in this array
			if (isset($blood_streaked_sputumArray[$value]) && StringHelper::check($blood_streaked_sputumArray[$value]))
			{
				return $blood_streaked_sputumArray[$value];
			}
		}
		// Array of unusual_tiredness language strings
		if ($name === 'unusual_tiredness')
		{
			$unusual_tirednessArray = array(
				0 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_YES',
				1 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_NO',
				2 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_UNCERTAIN'
			);
			// Now check if value is found in this array
			if (isset($unusual_tirednessArray[$value]) && StringHelper::check($unusual_tirednessArray[$value]))
			{
				return $unusual_tirednessArray[$value];
			}
		}
		// Array of pain_in_chest language strings
		if ($name === 'pain_in_chest')
		{
			$pain_in_chestArray = array(
				0 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_YES',
				1 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_NO',
				2 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_UNCERTAIN'
			);
			// Now check if value is found in this array
			if (isset($pain_in_chestArray[$value]) && StringHelper::check($pain_in_chestArray[$value]))
			{
				return $pain_in_chestArray[$value];
			}
		}
		// Array of shortness_of_breath language strings
		if ($name === 'shortness_of_breath')
		{
			$shortness_of_breathArray = array(
				0 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_YES',
				1 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_NO',
				2 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_UNCERTAIN'
			);
			// Now check if value is found in this array
			if (isset($shortness_of_breathArray[$value]) && StringHelper::check($shortness_of_breathArray[$value]))
			{
				return $shortness_of_breathArray[$value];
			}
		}
		// Array of diagnosed_with_disease language strings
		if ($name === 'diagnosed_with_disease')
		{
			$diagnosed_with_diseaseArray = array(
				0 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_YES',
				1 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_NO'
			);
			// Now check if value is found in this array
			if (isset($diagnosed_with_diseaseArray[$value]) && StringHelper::check($diagnosed_with_diseaseArray[$value]))
			{
				return $diagnosed_with_diseaseArray[$value];
			}
		}
		// Array of tb_exposed language strings
		if ($name === 'tb_exposed')
		{
			$tb_exposedArray = array(
				0 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_YES',
				1 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_NO',
				2 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_UNCERTAIN'
			);
			// Now check if value is found in this array
			if (isset($tb_exposedArray[$value]) && StringHelper::check($tb_exposedArray[$value]))
			{
				return $tb_exposedArray[$value];
			}
		}
		// Array of tb_treatment language strings
		if ($name === 'tb_treatment')
		{
			$tb_treatmentArray = array(
				0 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_YES',
				1 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_NO'
			);
			// Now check if value is found in this array
			if (isset($tb_treatmentArray[$value]) && StringHelper::check($tb_treatmentArray[$value]))
			{
				return $tb_treatmentArray[$value];
			}
		}
		// Array of sputum_collection_one language strings
		if ($name === 'sputum_collection_one')
		{
			$sputum_collection_oneArray = array(
				0 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_YES',
				1 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_NO'
			);
			// Now check if value is found in this array
			if (isset($sputum_collection_oneArray[$value]) && StringHelper::check($sputum_collection_oneArray[$value]))
			{
				return $sputum_collection_oneArray[$value];
			}
		}
		// Array of sputum_result_one language strings
		if ($name === 'sputum_result_one')
		{
			$sputum_result_oneArray = array(
				0 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_POSITIVE',
				1 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_NEGATIVE',
				2 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_INCONCLUSIVE'
			);
			// Now check if value is found in this array
			if (isset($sputum_result_oneArray[$value]) && StringHelper::check($sputum_result_oneArray[$value]))
			{
				return $sputum_result_oneArray[$value];
			}
		}
		// Array of referred_second_sputum language strings
		if ($name === 'referred_second_sputum')
		{
			$referred_second_sputumArray = array(
				0 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_YES',
				1 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_NO'
			);
			// Now check if value is found in this array
			if (isset($referred_second_sputumArray[$value]) && StringHelper::check($referred_second_sputumArray[$value]))
			{
				return $referred_second_sputumArray[$value];
			}
		}
		// Array of sputum_result_two language strings
		if ($name === 'sputum_result_two')
		{
			$sputum_result_twoArray = array(
				0 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_POSITIVE',
				1 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_NEGATIVE',
				2 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_INCONCLUSIVE'
			);
			// Now check if value is found in this array
			if (isset($sputum_result_twoArray[$value]) && StringHelper::check($sputum_result_twoArray[$value]))
			{
				return $sputum_result_twoArray[$value];
			}
		}
		// Array of weight_loss_wdieting language strings
		if ($name === 'weight_loss_wdieting')
		{
			$weight_loss_wdietingArray = array(
				0 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_YES',
				1 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_NO',
				2 => 'COM_EHEALTHPORTAL_TUBERCULOSIS_UNCERTAIN'
			);
			// Now check if value is found in this array
			if (isset($weight_loss_wdietingArray[$value]) && StringHelper::check($weight_loss_wdietingArray[$value]))
			{
				return $weight_loss_wdietingArray[$value];
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
		$user = Factory::getUser();
		// Create a new query object.
		$db = Factory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');

		// From the ehealthportal_item table
		$query->from($db->quoteName('#__ehealthportal_tuberculosis', 'a'));

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
				$user = Factory::getUser();
			}
			// Create a new query object.
			$db = Factory::getDBO();
			$query = $db->getQuery(true);

			// Select some fields
			$query->select('a.*');

			// From the ehealthportal_tuberculosis table
			$query->from($db->quoteName('#__ehealthportal_tuberculosis', 'a'));
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
							// convert recurring_night_sweats
							$item->recurring_night_sweats = $this->selectionTranslation($item->recurring_night_sweats, 'recurring_night_sweats');
							// convert tb_fever
							$item->tb_fever = $this->selectionTranslation($item->tb_fever, 'tb_fever');
							// convert persistent_cough
							$item->persistent_cough = $this->selectionTranslation($item->persistent_cough, 'persistent_cough');
							// convert blood_streaked_sputum
							$item->blood_streaked_sputum = $this->selectionTranslation($item->blood_streaked_sputum, 'blood_streaked_sputum');
							// convert unusual_tiredness
							$item->unusual_tiredness = $this->selectionTranslation($item->unusual_tiredness, 'unusual_tiredness');
							// convert pain_in_chest
							$item->pain_in_chest = $this->selectionTranslation($item->pain_in_chest, 'pain_in_chest');
							// convert shortness_of_breath
							$item->shortness_of_breath = $this->selectionTranslation($item->shortness_of_breath, 'shortness_of_breath');
							// convert diagnosed_with_disease
							$item->diagnosed_with_disease = $this->selectionTranslation($item->diagnosed_with_disease, 'diagnosed_with_disease');
							// convert tb_exposed
							$item->tb_exposed = $this->selectionTranslation($item->tb_exposed, 'tb_exposed');
							// convert tb_treatment
							$item->tb_treatment = $this->selectionTranslation($item->tb_treatment, 'tb_treatment');
							// convert sputum_collection_one
							$item->sputum_collection_one = $this->selectionTranslation($item->sputum_collection_one, 'sputum_collection_one');
							// convert sputum_result_one
							$item->sputum_result_one = $this->selectionTranslation($item->sputum_result_one, 'sputum_result_one');
							// convert referred_second_sputum
							$item->referred_second_sputum = $this->selectionTranslation($item->referred_second_sputum, 'referred_second_sputum');
							// convert sputum_result_two
							$item->sputum_result_two = $this->selectionTranslation($item->sputum_result_two, 'sputum_result_two');
							// convert weight_loss_wdieting
							$item->weight_loss_wdieting = $this->selectionTranslation($item->weight_loss_wdieting, 'weight_loss_wdieting');
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
		$columns = $db->getTableColumns("#__ehealthportal_tuberculosis");
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
			$db = Factory::getDbo();
			// Reset query.
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__ehealthportal_tuberculosis'));
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
				$query->update($db->quoteName('#__ehealthportal_tuberculosis'))->set($fields)->where($conditions); 

				$db->setQuery($query);

				$db->execute();
			}
		}

		return false;
	}
}
