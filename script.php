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
	@subpackage		script.php
	@author			Oh Martin <https://github.com/namibia/eHealth-Portal>
	@copyright		Copyright (C) 2020 Vast Development Method. All rights reserved.
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Portal for mobile health clinics

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
JHTML::_('behavior.modal');

/**
 * Script File of Ehealth_portal Component
 */
class com_ehealth_portalInstallerScript
{
	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 */
	public function __construct(JAdapterInstance $parent) {}

	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(JAdapterInstance $parent) {}

	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 */
	public function uninstall(JAdapterInstance $parent)
	{
		// Get Application object
		$app = JFactory::getApplication();

		// Get The Database object
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Payment alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.payment') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$payment_found = $db->getNumRows();
		// Now check if there were any rows
		if ($payment_found)
		{
			// Since there are load the needed  payment type ids
			$payment_ids = $db->loadColumn();
			// Remove Payment from the content type table
			$payment_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.payment') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($payment_condition);
			$db->setQuery($query);
			// Execute the query to remove Payment items
			$payment_done = $db->execute();
			if ($payment_done)
			{
				// If successfully remove Payment add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.payment) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Payment items from the contentitem tag map table
			$payment_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.payment') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($payment_condition);
			$db->setQuery($query);
			// Execute the query to remove Payment items
			$payment_done = $db->execute();
			if ($payment_done)
			{
				// If successfully remove Payment add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.payment) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Payment items from the ucm content table
			$payment_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.payment') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($payment_condition);
			$db->setQuery($query);
			// Execute the query to remove Payment items
			$payment_done = $db->execute();
			if ($payment_done)
			{
				// If successfully removed Payment add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.payment) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Payment items are cleared from DB
			foreach ($payment_ids as $payment_id)
			{
				// Remove Payment items from the ucm base table
				$payment_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $payment_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($payment_condition);
				$db->setQuery($query);
				// Execute the query to remove Payment items
				$db->execute();

				// Remove Payment items from the ucm history table
				$payment_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $payment_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($payment_condition);
				$db->setQuery($query);
				// Execute the query to remove Payment items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where General_medical_check_up alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.general_medical_check_up') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$general_medical_check_up_found = $db->getNumRows();
		// Now check if there were any rows
		if ($general_medical_check_up_found)
		{
			// Since there are load the needed  general_medical_check_up type ids
			$general_medical_check_up_ids = $db->loadColumn();
			// Remove General_medical_check_up from the content type table
			$general_medical_check_up_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.general_medical_check_up') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($general_medical_check_up_condition);
			$db->setQuery($query);
			// Execute the query to remove General_medical_check_up items
			$general_medical_check_up_done = $db->execute();
			if ($general_medical_check_up_done)
			{
				// If successfully remove General_medical_check_up add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.general_medical_check_up) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove General_medical_check_up items from the contentitem tag map table
			$general_medical_check_up_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.general_medical_check_up') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($general_medical_check_up_condition);
			$db->setQuery($query);
			// Execute the query to remove General_medical_check_up items
			$general_medical_check_up_done = $db->execute();
			if ($general_medical_check_up_done)
			{
				// If successfully remove General_medical_check_up add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.general_medical_check_up) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove General_medical_check_up items from the ucm content table
			$general_medical_check_up_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.general_medical_check_up') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($general_medical_check_up_condition);
			$db->setQuery($query);
			// Execute the query to remove General_medical_check_up items
			$general_medical_check_up_done = $db->execute();
			if ($general_medical_check_up_done)
			{
				// If successfully removed General_medical_check_up add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.general_medical_check_up) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the General_medical_check_up items are cleared from DB
			foreach ($general_medical_check_up_ids as $general_medical_check_up_id)
			{
				// Remove General_medical_check_up items from the ucm base table
				$general_medical_check_up_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $general_medical_check_up_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($general_medical_check_up_condition);
				$db->setQuery($query);
				// Execute the query to remove General_medical_check_up items
				$db->execute();

				// Remove General_medical_check_up items from the ucm history table
				$general_medical_check_up_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $general_medical_check_up_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($general_medical_check_up_condition);
				$db->setQuery($query);
				// Execute the query to remove General_medical_check_up items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Antenatal_care alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.antenatal_care') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$antenatal_care_found = $db->getNumRows();
		// Now check if there were any rows
		if ($antenatal_care_found)
		{
			// Since there are load the needed  antenatal_care type ids
			$antenatal_care_ids = $db->loadColumn();
			// Remove Antenatal_care from the content type table
			$antenatal_care_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.antenatal_care') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($antenatal_care_condition);
			$db->setQuery($query);
			// Execute the query to remove Antenatal_care items
			$antenatal_care_done = $db->execute();
			if ($antenatal_care_done)
			{
				// If successfully remove Antenatal_care add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.antenatal_care) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Antenatal_care items from the contentitem tag map table
			$antenatal_care_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.antenatal_care') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($antenatal_care_condition);
			$db->setQuery($query);
			// Execute the query to remove Antenatal_care items
			$antenatal_care_done = $db->execute();
			if ($antenatal_care_done)
			{
				// If successfully remove Antenatal_care add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.antenatal_care) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Antenatal_care items from the ucm content table
			$antenatal_care_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.antenatal_care') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($antenatal_care_condition);
			$db->setQuery($query);
			// Execute the query to remove Antenatal_care items
			$antenatal_care_done = $db->execute();
			if ($antenatal_care_done)
			{
				// If successfully removed Antenatal_care add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.antenatal_care) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Antenatal_care items are cleared from DB
			foreach ($antenatal_care_ids as $antenatal_care_id)
			{
				// Remove Antenatal_care items from the ucm base table
				$antenatal_care_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $antenatal_care_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($antenatal_care_condition);
				$db->setQuery($query);
				// Execute the query to remove Antenatal_care items
				$db->execute();

				// Remove Antenatal_care items from the ucm history table
				$antenatal_care_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $antenatal_care_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($antenatal_care_condition);
				$db->setQuery($query);
				// Execute the query to remove Antenatal_care items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Immunisation alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.immunisation') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$immunisation_found = $db->getNumRows();
		// Now check if there were any rows
		if ($immunisation_found)
		{
			// Since there are load the needed  immunisation type ids
			$immunisation_ids = $db->loadColumn();
			// Remove Immunisation from the content type table
			$immunisation_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.immunisation') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($immunisation_condition);
			$db->setQuery($query);
			// Execute the query to remove Immunisation items
			$immunisation_done = $db->execute();
			if ($immunisation_done)
			{
				// If successfully remove Immunisation add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.immunisation) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Immunisation items from the contentitem tag map table
			$immunisation_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.immunisation') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($immunisation_condition);
			$db->setQuery($query);
			// Execute the query to remove Immunisation items
			$immunisation_done = $db->execute();
			if ($immunisation_done)
			{
				// If successfully remove Immunisation add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.immunisation) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Immunisation items from the ucm content table
			$immunisation_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.immunisation') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($immunisation_condition);
			$db->setQuery($query);
			// Execute the query to remove Immunisation items
			$immunisation_done = $db->execute();
			if ($immunisation_done)
			{
				// If successfully removed Immunisation add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.immunisation) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Immunisation items are cleared from DB
			foreach ($immunisation_ids as $immunisation_id)
			{
				// Remove Immunisation items from the ucm base table
				$immunisation_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $immunisation_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($immunisation_condition);
				$db->setQuery($query);
				// Execute the query to remove Immunisation items
				$db->execute();

				// Remove Immunisation items from the ucm history table
				$immunisation_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $immunisation_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($immunisation_condition);
				$db->setQuery($query);
				// Execute the query to remove Immunisation items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Vmmc alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.vmmc') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$vmmc_found = $db->getNumRows();
		// Now check if there were any rows
		if ($vmmc_found)
		{
			// Since there are load the needed  vmmc type ids
			$vmmc_ids = $db->loadColumn();
			// Remove Vmmc from the content type table
			$vmmc_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.vmmc') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($vmmc_condition);
			$db->setQuery($query);
			// Execute the query to remove Vmmc items
			$vmmc_done = $db->execute();
			if ($vmmc_done)
			{
				// If successfully remove Vmmc add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.vmmc) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Vmmc items from the contentitem tag map table
			$vmmc_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.vmmc') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($vmmc_condition);
			$db->setQuery($query);
			// Execute the query to remove Vmmc items
			$vmmc_done = $db->execute();
			if ($vmmc_done)
			{
				// If successfully remove Vmmc add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.vmmc) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Vmmc items from the ucm content table
			$vmmc_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.vmmc') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($vmmc_condition);
			$db->setQuery($query);
			// Execute the query to remove Vmmc items
			$vmmc_done = $db->execute();
			if ($vmmc_done)
			{
				// If successfully removed Vmmc add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.vmmc) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Vmmc items are cleared from DB
			foreach ($vmmc_ids as $vmmc_id)
			{
				// Remove Vmmc items from the ucm base table
				$vmmc_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $vmmc_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($vmmc_condition);
				$db->setQuery($query);
				// Execute the query to remove Vmmc items
				$db->execute();

				// Remove Vmmc items from the ucm history table
				$vmmc_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $vmmc_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($vmmc_condition);
				$db->setQuery($query);
				// Execute the query to remove Vmmc items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Prostate_and_testicular_cancer alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.prostate_and_testicular_cancer') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$prostate_and_testicular_cancer_found = $db->getNumRows();
		// Now check if there were any rows
		if ($prostate_and_testicular_cancer_found)
		{
			// Since there are load the needed  prostate_and_testicular_cancer type ids
			$prostate_and_testicular_cancer_ids = $db->loadColumn();
			// Remove Prostate_and_testicular_cancer from the content type table
			$prostate_and_testicular_cancer_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.prostate_and_testicular_cancer') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($prostate_and_testicular_cancer_condition);
			$db->setQuery($query);
			// Execute the query to remove Prostate_and_testicular_cancer items
			$prostate_and_testicular_cancer_done = $db->execute();
			if ($prostate_and_testicular_cancer_done)
			{
				// If successfully remove Prostate_and_testicular_cancer add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.prostate_and_testicular_cancer) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Prostate_and_testicular_cancer items from the contentitem tag map table
			$prostate_and_testicular_cancer_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.prostate_and_testicular_cancer') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($prostate_and_testicular_cancer_condition);
			$db->setQuery($query);
			// Execute the query to remove Prostate_and_testicular_cancer items
			$prostate_and_testicular_cancer_done = $db->execute();
			if ($prostate_and_testicular_cancer_done)
			{
				// If successfully remove Prostate_and_testicular_cancer add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.prostate_and_testicular_cancer) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Prostate_and_testicular_cancer items from the ucm content table
			$prostate_and_testicular_cancer_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.prostate_and_testicular_cancer') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($prostate_and_testicular_cancer_condition);
			$db->setQuery($query);
			// Execute the query to remove Prostate_and_testicular_cancer items
			$prostate_and_testicular_cancer_done = $db->execute();
			if ($prostate_and_testicular_cancer_done)
			{
				// If successfully removed Prostate_and_testicular_cancer add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.prostate_and_testicular_cancer) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Prostate_and_testicular_cancer items are cleared from DB
			foreach ($prostate_and_testicular_cancer_ids as $prostate_and_testicular_cancer_id)
			{
				// Remove Prostate_and_testicular_cancer items from the ucm base table
				$prostate_and_testicular_cancer_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $prostate_and_testicular_cancer_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($prostate_and_testicular_cancer_condition);
				$db->setQuery($query);
				// Execute the query to remove Prostate_and_testicular_cancer items
				$db->execute();

				// Remove Prostate_and_testicular_cancer items from the ucm history table
				$prostate_and_testicular_cancer_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $prostate_and_testicular_cancer_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($prostate_and_testicular_cancer_condition);
				$db->setQuery($query);
				// Execute the query to remove Prostate_and_testicular_cancer items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Tuberculosis alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.tuberculosis') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$tuberculosis_found = $db->getNumRows();
		// Now check if there were any rows
		if ($tuberculosis_found)
		{
			// Since there are load the needed  tuberculosis type ids
			$tuberculosis_ids = $db->loadColumn();
			// Remove Tuberculosis from the content type table
			$tuberculosis_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.tuberculosis') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($tuberculosis_condition);
			$db->setQuery($query);
			// Execute the query to remove Tuberculosis items
			$tuberculosis_done = $db->execute();
			if ($tuberculosis_done)
			{
				// If successfully remove Tuberculosis add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.tuberculosis) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Tuberculosis items from the contentitem tag map table
			$tuberculosis_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.tuberculosis') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($tuberculosis_condition);
			$db->setQuery($query);
			// Execute the query to remove Tuberculosis items
			$tuberculosis_done = $db->execute();
			if ($tuberculosis_done)
			{
				// If successfully remove Tuberculosis add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.tuberculosis) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Tuberculosis items from the ucm content table
			$tuberculosis_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.tuberculosis') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($tuberculosis_condition);
			$db->setQuery($query);
			// Execute the query to remove Tuberculosis items
			$tuberculosis_done = $db->execute();
			if ($tuberculosis_done)
			{
				// If successfully removed Tuberculosis add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.tuberculosis) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Tuberculosis items are cleared from DB
			foreach ($tuberculosis_ids as $tuberculosis_id)
			{
				// Remove Tuberculosis items from the ucm base table
				$tuberculosis_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $tuberculosis_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($tuberculosis_condition);
				$db->setQuery($query);
				// Execute the query to remove Tuberculosis items
				$db->execute();

				// Remove Tuberculosis items from the ucm history table
				$tuberculosis_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $tuberculosis_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($tuberculosis_condition);
				$db->setQuery($query);
				// Execute the query to remove Tuberculosis items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Hiv_counseling_and_testing alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.hiv_counseling_and_testing') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$hiv_counseling_and_testing_found = $db->getNumRows();
		// Now check if there were any rows
		if ($hiv_counseling_and_testing_found)
		{
			// Since there are load the needed  hiv_counseling_and_testing type ids
			$hiv_counseling_and_testing_ids = $db->loadColumn();
			// Remove Hiv_counseling_and_testing from the content type table
			$hiv_counseling_and_testing_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.hiv_counseling_and_testing') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($hiv_counseling_and_testing_condition);
			$db->setQuery($query);
			// Execute the query to remove Hiv_counseling_and_testing items
			$hiv_counseling_and_testing_done = $db->execute();
			if ($hiv_counseling_and_testing_done)
			{
				// If successfully remove Hiv_counseling_and_testing add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.hiv_counseling_and_testing) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Hiv_counseling_and_testing items from the contentitem tag map table
			$hiv_counseling_and_testing_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.hiv_counseling_and_testing') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($hiv_counseling_and_testing_condition);
			$db->setQuery($query);
			// Execute the query to remove Hiv_counseling_and_testing items
			$hiv_counseling_and_testing_done = $db->execute();
			if ($hiv_counseling_and_testing_done)
			{
				// If successfully remove Hiv_counseling_and_testing add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.hiv_counseling_and_testing) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Hiv_counseling_and_testing items from the ucm content table
			$hiv_counseling_and_testing_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.hiv_counseling_and_testing') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($hiv_counseling_and_testing_condition);
			$db->setQuery($query);
			// Execute the query to remove Hiv_counseling_and_testing items
			$hiv_counseling_and_testing_done = $db->execute();
			if ($hiv_counseling_and_testing_done)
			{
				// If successfully removed Hiv_counseling_and_testing add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.hiv_counseling_and_testing) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Hiv_counseling_and_testing items are cleared from DB
			foreach ($hiv_counseling_and_testing_ids as $hiv_counseling_and_testing_id)
			{
				// Remove Hiv_counseling_and_testing items from the ucm base table
				$hiv_counseling_and_testing_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $hiv_counseling_and_testing_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($hiv_counseling_and_testing_condition);
				$db->setQuery($query);
				// Execute the query to remove Hiv_counseling_and_testing items
				$db->execute();

				// Remove Hiv_counseling_and_testing items from the ucm history table
				$hiv_counseling_and_testing_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $hiv_counseling_and_testing_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($hiv_counseling_and_testing_condition);
				$db->setQuery($query);
				// Execute the query to remove Hiv_counseling_and_testing items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Family_planning alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.family_planning') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$family_planning_found = $db->getNumRows();
		// Now check if there were any rows
		if ($family_planning_found)
		{
			// Since there are load the needed  family_planning type ids
			$family_planning_ids = $db->loadColumn();
			// Remove Family_planning from the content type table
			$family_planning_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.family_planning') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($family_planning_condition);
			$db->setQuery($query);
			// Execute the query to remove Family_planning items
			$family_planning_done = $db->execute();
			if ($family_planning_done)
			{
				// If successfully remove Family_planning add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.family_planning) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Family_planning items from the contentitem tag map table
			$family_planning_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.family_planning') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($family_planning_condition);
			$db->setQuery($query);
			// Execute the query to remove Family_planning items
			$family_planning_done = $db->execute();
			if ($family_planning_done)
			{
				// If successfully remove Family_planning add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.family_planning) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Family_planning items from the ucm content table
			$family_planning_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.family_planning') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($family_planning_condition);
			$db->setQuery($query);
			// Execute the query to remove Family_planning items
			$family_planning_done = $db->execute();
			if ($family_planning_done)
			{
				// If successfully removed Family_planning add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.family_planning) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Family_planning items are cleared from DB
			foreach ($family_planning_ids as $family_planning_id)
			{
				// Remove Family_planning items from the ucm base table
				$family_planning_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $family_planning_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($family_planning_condition);
				$db->setQuery($query);
				// Execute the query to remove Family_planning items
				$db->execute();

				// Remove Family_planning items from the ucm history table
				$family_planning_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $family_planning_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($family_planning_condition);
				$db->setQuery($query);
				// Execute the query to remove Family_planning items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Health_education alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.health_education') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$health_education_found = $db->getNumRows();
		// Now check if there were any rows
		if ($health_education_found)
		{
			// Since there are load the needed  health_education type ids
			$health_education_ids = $db->loadColumn();
			// Remove Health_education from the content type table
			$health_education_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.health_education') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($health_education_condition);
			$db->setQuery($query);
			// Execute the query to remove Health_education items
			$health_education_done = $db->execute();
			if ($health_education_done)
			{
				// If successfully remove Health_education add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.health_education) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Health_education items from the contentitem tag map table
			$health_education_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.health_education') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($health_education_condition);
			$db->setQuery($query);
			// Execute the query to remove Health_education items
			$health_education_done = $db->execute();
			if ($health_education_done)
			{
				// If successfully remove Health_education add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.health_education) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Health_education items from the ucm content table
			$health_education_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.health_education') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($health_education_condition);
			$db->setQuery($query);
			// Execute the query to remove Health_education items
			$health_education_done = $db->execute();
			if ($health_education_done)
			{
				// If successfully removed Health_education add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.health_education) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Health_education items are cleared from DB
			foreach ($health_education_ids as $health_education_id)
			{
				// Remove Health_education items from the ucm base table
				$health_education_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $health_education_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($health_education_condition);
				$db->setQuery($query);
				// Execute the query to remove Health_education items
				$db->execute();

				// Remove Health_education items from the ucm history table
				$health_education_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $health_education_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($health_education_condition);
				$db->setQuery($query);
				// Execute the query to remove Health_education items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Cervical_cancer alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.cervical_cancer') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$cervical_cancer_found = $db->getNumRows();
		// Now check if there were any rows
		if ($cervical_cancer_found)
		{
			// Since there are load the needed  cervical_cancer type ids
			$cervical_cancer_ids = $db->loadColumn();
			// Remove Cervical_cancer from the content type table
			$cervical_cancer_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.cervical_cancer') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($cervical_cancer_condition);
			$db->setQuery($query);
			// Execute the query to remove Cervical_cancer items
			$cervical_cancer_done = $db->execute();
			if ($cervical_cancer_done)
			{
				// If successfully remove Cervical_cancer add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.cervical_cancer) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Cervical_cancer items from the contentitem tag map table
			$cervical_cancer_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.cervical_cancer') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($cervical_cancer_condition);
			$db->setQuery($query);
			// Execute the query to remove Cervical_cancer items
			$cervical_cancer_done = $db->execute();
			if ($cervical_cancer_done)
			{
				// If successfully remove Cervical_cancer add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.cervical_cancer) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Cervical_cancer items from the ucm content table
			$cervical_cancer_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.cervical_cancer') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($cervical_cancer_condition);
			$db->setQuery($query);
			// Execute the query to remove Cervical_cancer items
			$cervical_cancer_done = $db->execute();
			if ($cervical_cancer_done)
			{
				// If successfully removed Cervical_cancer add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.cervical_cancer) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Cervical_cancer items are cleared from DB
			foreach ($cervical_cancer_ids as $cervical_cancer_id)
			{
				// Remove Cervical_cancer items from the ucm base table
				$cervical_cancer_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $cervical_cancer_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($cervical_cancer_condition);
				$db->setQuery($query);
				// Execute the query to remove Cervical_cancer items
				$db->execute();

				// Remove Cervical_cancer items from the ucm history table
				$cervical_cancer_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $cervical_cancer_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($cervical_cancer_condition);
				$db->setQuery($query);
				// Execute the query to remove Cervical_cancer items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Breast_cancer alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.breast_cancer') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$breast_cancer_found = $db->getNumRows();
		// Now check if there were any rows
		if ($breast_cancer_found)
		{
			// Since there are load the needed  breast_cancer type ids
			$breast_cancer_ids = $db->loadColumn();
			// Remove Breast_cancer from the content type table
			$breast_cancer_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.breast_cancer') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($breast_cancer_condition);
			$db->setQuery($query);
			// Execute the query to remove Breast_cancer items
			$breast_cancer_done = $db->execute();
			if ($breast_cancer_done)
			{
				// If successfully remove Breast_cancer add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.breast_cancer) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Breast_cancer items from the contentitem tag map table
			$breast_cancer_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.breast_cancer') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($breast_cancer_condition);
			$db->setQuery($query);
			// Execute the query to remove Breast_cancer items
			$breast_cancer_done = $db->execute();
			if ($breast_cancer_done)
			{
				// If successfully remove Breast_cancer add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.breast_cancer) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Breast_cancer items from the ucm content table
			$breast_cancer_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.breast_cancer') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($breast_cancer_condition);
			$db->setQuery($query);
			// Execute the query to remove Breast_cancer items
			$breast_cancer_done = $db->execute();
			if ($breast_cancer_done)
			{
				// If successfully removed Breast_cancer add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.breast_cancer) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Breast_cancer items are cleared from DB
			foreach ($breast_cancer_ids as $breast_cancer_id)
			{
				// Remove Breast_cancer items from the ucm base table
				$breast_cancer_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $breast_cancer_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($breast_cancer_condition);
				$db->setQuery($query);
				// Execute the query to remove Breast_cancer items
				$db->execute();

				// Remove Breast_cancer items from the ucm history table
				$breast_cancer_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $breast_cancer_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($breast_cancer_condition);
				$db->setQuery($query);
				// Execute the query to remove Breast_cancer items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Test alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.test') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$test_found = $db->getNumRows();
		// Now check if there were any rows
		if ($test_found)
		{
			// Since there are load the needed  test type ids
			$test_ids = $db->loadColumn();
			// Remove Test from the content type table
			$test_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.test') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($test_condition);
			$db->setQuery($query);
			// Execute the query to remove Test items
			$test_done = $db->execute();
			if ($test_done)
			{
				// If successfully remove Test add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.test) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Test items from the contentitem tag map table
			$test_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.test') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($test_condition);
			$db->setQuery($query);
			// Execute the query to remove Test items
			$test_done = $db->execute();
			if ($test_done)
			{
				// If successfully remove Test add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.test) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Test items from the ucm content table
			$test_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.test') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($test_condition);
			$db->setQuery($query);
			// Execute the query to remove Test items
			$test_done = $db->execute();
			if ($test_done)
			{
				// If successfully removed Test add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.test) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Test items are cleared from DB
			foreach ($test_ids as $test_id)
			{
				// Remove Test items from the ucm base table
				$test_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $test_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($test_condition);
				$db->setQuery($query);
				// Execute the query to remove Test items
				$db->execute();

				// Remove Test items from the ucm history table
				$test_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $test_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($test_condition);
				$db->setQuery($query);
				// Execute the query to remove Test items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Immunisation_vaccine_type alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.immunisation_vaccine_type') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$immunisation_vaccine_type_found = $db->getNumRows();
		// Now check if there were any rows
		if ($immunisation_vaccine_type_found)
		{
			// Since there are load the needed  immunisation_vaccine_type type ids
			$immunisation_vaccine_type_ids = $db->loadColumn();
			// Remove Immunisation_vaccine_type from the content type table
			$immunisation_vaccine_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.immunisation_vaccine_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($immunisation_vaccine_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Immunisation_vaccine_type items
			$immunisation_vaccine_type_done = $db->execute();
			if ($immunisation_vaccine_type_done)
			{
				// If successfully remove Immunisation_vaccine_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.immunisation_vaccine_type) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Immunisation_vaccine_type items from the contentitem tag map table
			$immunisation_vaccine_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.immunisation_vaccine_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($immunisation_vaccine_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Immunisation_vaccine_type items
			$immunisation_vaccine_type_done = $db->execute();
			if ($immunisation_vaccine_type_done)
			{
				// If successfully remove Immunisation_vaccine_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.immunisation_vaccine_type) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Immunisation_vaccine_type items from the ucm content table
			$immunisation_vaccine_type_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.immunisation_vaccine_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($immunisation_vaccine_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Immunisation_vaccine_type items
			$immunisation_vaccine_type_done = $db->execute();
			if ($immunisation_vaccine_type_done)
			{
				// If successfully removed Immunisation_vaccine_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.immunisation_vaccine_type) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Immunisation_vaccine_type items are cleared from DB
			foreach ($immunisation_vaccine_type_ids as $immunisation_vaccine_type_id)
			{
				// Remove Immunisation_vaccine_type items from the ucm base table
				$immunisation_vaccine_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $immunisation_vaccine_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($immunisation_vaccine_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Immunisation_vaccine_type items
				$db->execute();

				// Remove Immunisation_vaccine_type items from the ucm history table
				$immunisation_vaccine_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $immunisation_vaccine_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($immunisation_vaccine_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Immunisation_vaccine_type items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Foetal_presentation alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.foetal_presentation') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$foetal_presentation_found = $db->getNumRows();
		// Now check if there were any rows
		if ($foetal_presentation_found)
		{
			// Since there are load the needed  foetal_presentation type ids
			$foetal_presentation_ids = $db->loadColumn();
			// Remove Foetal_presentation from the content type table
			$foetal_presentation_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.foetal_presentation') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($foetal_presentation_condition);
			$db->setQuery($query);
			// Execute the query to remove Foetal_presentation items
			$foetal_presentation_done = $db->execute();
			if ($foetal_presentation_done)
			{
				// If successfully remove Foetal_presentation add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.foetal_presentation) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Foetal_presentation items from the contentitem tag map table
			$foetal_presentation_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.foetal_presentation') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($foetal_presentation_condition);
			$db->setQuery($query);
			// Execute the query to remove Foetal_presentation items
			$foetal_presentation_done = $db->execute();
			if ($foetal_presentation_done)
			{
				// If successfully remove Foetal_presentation add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.foetal_presentation) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Foetal_presentation items from the ucm content table
			$foetal_presentation_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.foetal_presentation') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($foetal_presentation_condition);
			$db->setQuery($query);
			// Execute the query to remove Foetal_presentation items
			$foetal_presentation_done = $db->execute();
			if ($foetal_presentation_done)
			{
				// If successfully removed Foetal_presentation add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.foetal_presentation) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Foetal_presentation items are cleared from DB
			foreach ($foetal_presentation_ids as $foetal_presentation_id)
			{
				// Remove Foetal_presentation items from the ucm base table
				$foetal_presentation_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $foetal_presentation_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($foetal_presentation_condition);
				$db->setQuery($query);
				// Execute the query to remove Foetal_presentation items
				$db->execute();

				// Remove Foetal_presentation items from the ucm history table
				$foetal_presentation_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $foetal_presentation_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($foetal_presentation_condition);
				$db->setQuery($query);
				// Execute the query to remove Foetal_presentation items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Foetal_lie alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.foetal_lie') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$foetal_lie_found = $db->getNumRows();
		// Now check if there were any rows
		if ($foetal_lie_found)
		{
			// Since there are load the needed  foetal_lie type ids
			$foetal_lie_ids = $db->loadColumn();
			// Remove Foetal_lie from the content type table
			$foetal_lie_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.foetal_lie') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($foetal_lie_condition);
			$db->setQuery($query);
			// Execute the query to remove Foetal_lie items
			$foetal_lie_done = $db->execute();
			if ($foetal_lie_done)
			{
				// If successfully remove Foetal_lie add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.foetal_lie) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Foetal_lie items from the contentitem tag map table
			$foetal_lie_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.foetal_lie') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($foetal_lie_condition);
			$db->setQuery($query);
			// Execute the query to remove Foetal_lie items
			$foetal_lie_done = $db->execute();
			if ($foetal_lie_done)
			{
				// If successfully remove Foetal_lie add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.foetal_lie) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Foetal_lie items from the ucm content table
			$foetal_lie_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.foetal_lie') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($foetal_lie_condition);
			$db->setQuery($query);
			// Execute the query to remove Foetal_lie items
			$foetal_lie_done = $db->execute();
			if ($foetal_lie_done)
			{
				// If successfully removed Foetal_lie add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.foetal_lie) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Foetal_lie items are cleared from DB
			foreach ($foetal_lie_ids as $foetal_lie_id)
			{
				// Remove Foetal_lie items from the ucm base table
				$foetal_lie_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $foetal_lie_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($foetal_lie_condition);
				$db->setQuery($query);
				// Execute the query to remove Foetal_lie items
				$db->execute();

				// Remove Foetal_lie items from the ucm history table
				$foetal_lie_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $foetal_lie_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($foetal_lie_condition);
				$db->setQuery($query);
				// Execute the query to remove Foetal_lie items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Counseling_type alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.counseling_type') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$counseling_type_found = $db->getNumRows();
		// Now check if there were any rows
		if ($counseling_type_found)
		{
			// Since there are load the needed  counseling_type type ids
			$counseling_type_ids = $db->loadColumn();
			// Remove Counseling_type from the content type table
			$counseling_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.counseling_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($counseling_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Counseling_type items
			$counseling_type_done = $db->execute();
			if ($counseling_type_done)
			{
				// If successfully remove Counseling_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.counseling_type) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Counseling_type items from the contentitem tag map table
			$counseling_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.counseling_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($counseling_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Counseling_type items
			$counseling_type_done = $db->execute();
			if ($counseling_type_done)
			{
				// If successfully remove Counseling_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.counseling_type) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Counseling_type items from the ucm content table
			$counseling_type_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.counseling_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($counseling_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Counseling_type items
			$counseling_type_done = $db->execute();
			if ($counseling_type_done)
			{
				// If successfully removed Counseling_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.counseling_type) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Counseling_type items are cleared from DB
			foreach ($counseling_type_ids as $counseling_type_id)
			{
				// Remove Counseling_type items from the ucm base table
				$counseling_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $counseling_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($counseling_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Counseling_type items
				$db->execute();

				// Remove Counseling_type items from the ucm history table
				$counseling_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $counseling_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($counseling_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Counseling_type items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Foetal_engagement alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.foetal_engagement') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$foetal_engagement_found = $db->getNumRows();
		// Now check if there were any rows
		if ($foetal_engagement_found)
		{
			// Since there are load the needed  foetal_engagement type ids
			$foetal_engagement_ids = $db->loadColumn();
			// Remove Foetal_engagement from the content type table
			$foetal_engagement_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.foetal_engagement') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($foetal_engagement_condition);
			$db->setQuery($query);
			// Execute the query to remove Foetal_engagement items
			$foetal_engagement_done = $db->execute();
			if ($foetal_engagement_done)
			{
				// If successfully remove Foetal_engagement add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.foetal_engagement) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Foetal_engagement items from the contentitem tag map table
			$foetal_engagement_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.foetal_engagement') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($foetal_engagement_condition);
			$db->setQuery($query);
			// Execute the query to remove Foetal_engagement items
			$foetal_engagement_done = $db->execute();
			if ($foetal_engagement_done)
			{
				// If successfully remove Foetal_engagement add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.foetal_engagement) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Foetal_engagement items from the ucm content table
			$foetal_engagement_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.foetal_engagement') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($foetal_engagement_condition);
			$db->setQuery($query);
			// Execute the query to remove Foetal_engagement items
			$foetal_engagement_done = $db->execute();
			if ($foetal_engagement_done)
			{
				// If successfully removed Foetal_engagement add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.foetal_engagement) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Foetal_engagement items are cleared from DB
			foreach ($foetal_engagement_ids as $foetal_engagement_id)
			{
				// Remove Foetal_engagement items from the ucm base table
				$foetal_engagement_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $foetal_engagement_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($foetal_engagement_condition);
				$db->setQuery($query);
				// Execute the query to remove Foetal_engagement items
				$db->execute();

				// Remove Foetal_engagement items from the ucm history table
				$foetal_engagement_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $foetal_engagement_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($foetal_engagement_condition);
				$db->setQuery($query);
				// Execute the query to remove Foetal_engagement items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Health_education_topic alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.health_education_topic') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$health_education_topic_found = $db->getNumRows();
		// Now check if there were any rows
		if ($health_education_topic_found)
		{
			// Since there are load the needed  health_education_topic type ids
			$health_education_topic_ids = $db->loadColumn();
			// Remove Health_education_topic from the content type table
			$health_education_topic_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.health_education_topic') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($health_education_topic_condition);
			$db->setQuery($query);
			// Execute the query to remove Health_education_topic items
			$health_education_topic_done = $db->execute();
			if ($health_education_topic_done)
			{
				// If successfully remove Health_education_topic add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.health_education_topic) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Health_education_topic items from the contentitem tag map table
			$health_education_topic_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.health_education_topic') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($health_education_topic_condition);
			$db->setQuery($query);
			// Execute the query to remove Health_education_topic items
			$health_education_topic_done = $db->execute();
			if ($health_education_topic_done)
			{
				// If successfully remove Health_education_topic add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.health_education_topic) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Health_education_topic items from the ucm content table
			$health_education_topic_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.health_education_topic') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($health_education_topic_condition);
			$db->setQuery($query);
			// Execute the query to remove Health_education_topic items
			$health_education_topic_done = $db->execute();
			if ($health_education_topic_done)
			{
				// If successfully removed Health_education_topic add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.health_education_topic) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Health_education_topic items are cleared from DB
			foreach ($health_education_topic_ids as $health_education_topic_id)
			{
				// Remove Health_education_topic items from the ucm base table
				$health_education_topic_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $health_education_topic_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($health_education_topic_condition);
				$db->setQuery($query);
				// Execute the query to remove Health_education_topic items
				$db->execute();

				// Remove Health_education_topic items from the ucm history table
				$health_education_topic_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $health_education_topic_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($health_education_topic_condition);
				$db->setQuery($query);
				// Execute the query to remove Health_education_topic items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Testing_reason alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.testing_reason') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$testing_reason_found = $db->getNumRows();
		// Now check if there were any rows
		if ($testing_reason_found)
		{
			// Since there are load the needed  testing_reason type ids
			$testing_reason_ids = $db->loadColumn();
			// Remove Testing_reason from the content type table
			$testing_reason_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.testing_reason') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($testing_reason_condition);
			$db->setQuery($query);
			// Execute the query to remove Testing_reason items
			$testing_reason_done = $db->execute();
			if ($testing_reason_done)
			{
				// If successfully remove Testing_reason add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.testing_reason) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Testing_reason items from the contentitem tag map table
			$testing_reason_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.testing_reason') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($testing_reason_condition);
			$db->setQuery($query);
			// Execute the query to remove Testing_reason items
			$testing_reason_done = $db->execute();
			if ($testing_reason_done)
			{
				// If successfully remove Testing_reason add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.testing_reason) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Testing_reason items from the ucm content table
			$testing_reason_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.testing_reason') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($testing_reason_condition);
			$db->setQuery($query);
			// Execute the query to remove Testing_reason items
			$testing_reason_done = $db->execute();
			if ($testing_reason_done)
			{
				// If successfully removed Testing_reason add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.testing_reason) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Testing_reason items are cleared from DB
			foreach ($testing_reason_ids as $testing_reason_id)
			{
				// Remove Testing_reason items from the ucm base table
				$testing_reason_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $testing_reason_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($testing_reason_condition);
				$db->setQuery($query);
				// Execute the query to remove Testing_reason items
				$db->execute();

				// Remove Testing_reason items from the ucm history table
				$testing_reason_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $testing_reason_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($testing_reason_condition);
				$db->setQuery($query);
				// Execute the query to remove Testing_reason items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Clinic alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.clinic') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$clinic_found = $db->getNumRows();
		// Now check if there were any rows
		if ($clinic_found)
		{
			// Since there are load the needed  clinic type ids
			$clinic_ids = $db->loadColumn();
			// Remove Clinic from the content type table
			$clinic_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.clinic') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($clinic_condition);
			$db->setQuery($query);
			// Execute the query to remove Clinic items
			$clinic_done = $db->execute();
			if ($clinic_done)
			{
				// If successfully remove Clinic add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.clinic) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Clinic items from the contentitem tag map table
			$clinic_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.clinic') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($clinic_condition);
			$db->setQuery($query);
			// Execute the query to remove Clinic items
			$clinic_done = $db->execute();
			if ($clinic_done)
			{
				// If successfully remove Clinic add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.clinic) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Clinic items from the ucm content table
			$clinic_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.clinic') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($clinic_condition);
			$db->setQuery($query);
			// Execute the query to remove Clinic items
			$clinic_done = $db->execute();
			if ($clinic_done)
			{
				// If successfully removed Clinic add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.clinic) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Clinic items are cleared from DB
			foreach ($clinic_ids as $clinic_id)
			{
				// Remove Clinic items from the ucm base table
				$clinic_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $clinic_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($clinic_condition);
				$db->setQuery($query);
				// Execute the query to remove Clinic items
				$db->execute();

				// Remove Clinic items from the ucm history table
				$clinic_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $clinic_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($clinic_condition);
				$db->setQuery($query);
				// Execute the query to remove Clinic items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Immunisation_type alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.immunisation_type') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$immunisation_type_found = $db->getNumRows();
		// Now check if there were any rows
		if ($immunisation_type_found)
		{
			// Since there are load the needed  immunisation_type type ids
			$immunisation_type_ids = $db->loadColumn();
			// Remove Immunisation_type from the content type table
			$immunisation_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.immunisation_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($immunisation_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Immunisation_type items
			$immunisation_type_done = $db->execute();
			if ($immunisation_type_done)
			{
				// If successfully remove Immunisation_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.immunisation_type) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Immunisation_type items from the contentitem tag map table
			$immunisation_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.immunisation_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($immunisation_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Immunisation_type items
			$immunisation_type_done = $db->execute();
			if ($immunisation_type_done)
			{
				// If successfully remove Immunisation_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.immunisation_type) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Immunisation_type items from the ucm content table
			$immunisation_type_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.immunisation_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($immunisation_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Immunisation_type items
			$immunisation_type_done = $db->execute();
			if ($immunisation_type_done)
			{
				// If successfully removed Immunisation_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.immunisation_type) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Immunisation_type items are cleared from DB
			foreach ($immunisation_type_ids as $immunisation_type_id)
			{
				// Remove Immunisation_type items from the ucm base table
				$immunisation_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $immunisation_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($immunisation_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Immunisation_type items
				$db->execute();

				// Remove Immunisation_type items from the ucm history table
				$immunisation_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $immunisation_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($immunisation_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Immunisation_type items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Unit alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.unit') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$unit_found = $db->getNumRows();
		// Now check if there were any rows
		if ($unit_found)
		{
			// Since there are load the needed  unit type ids
			$unit_ids = $db->loadColumn();
			// Remove Unit from the content type table
			$unit_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.unit') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($unit_condition);
			$db->setQuery($query);
			// Execute the query to remove Unit items
			$unit_done = $db->execute();
			if ($unit_done)
			{
				// If successfully remove Unit add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.unit) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Unit items from the contentitem tag map table
			$unit_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.unit') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($unit_condition);
			$db->setQuery($query);
			// Execute the query to remove Unit items
			$unit_done = $db->execute();
			if ($unit_done)
			{
				// If successfully remove Unit add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.unit) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Unit items from the ucm content table
			$unit_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.unit') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($unit_condition);
			$db->setQuery($query);
			// Execute the query to remove Unit items
			$unit_done = $db->execute();
			if ($unit_done)
			{
				// If successfully removed Unit add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.unit) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Unit items are cleared from DB
			foreach ($unit_ids as $unit_id)
			{
				// Remove Unit items from the ucm base table
				$unit_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $unit_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($unit_condition);
				$db->setQuery($query);
				// Execute the query to remove Unit items
				$db->execute();

				// Remove Unit items from the ucm history table
				$unit_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $unit_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($unit_condition);
				$db->setQuery($query);
				// Execute the query to remove Unit items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Referral alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.referral') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$referral_found = $db->getNumRows();
		// Now check if there were any rows
		if ($referral_found)
		{
			// Since there are load the needed  referral type ids
			$referral_ids = $db->loadColumn();
			// Remove Referral from the content type table
			$referral_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.referral') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($referral_condition);
			$db->setQuery($query);
			// Execute the query to remove Referral items
			$referral_done = $db->execute();
			if ($referral_done)
			{
				// If successfully remove Referral add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.referral) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Referral items from the contentitem tag map table
			$referral_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.referral') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($referral_condition);
			$db->setQuery($query);
			// Execute the query to remove Referral items
			$referral_done = $db->execute();
			if ($referral_done)
			{
				// If successfully remove Referral add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.referral) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Referral items from the ucm content table
			$referral_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.referral') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($referral_condition);
			$db->setQuery($query);
			// Execute the query to remove Referral items
			$referral_done = $db->execute();
			if ($referral_done)
			{
				// If successfully removed Referral add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.referral) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Referral items are cleared from DB
			foreach ($referral_ids as $referral_id)
			{
				// Remove Referral items from the ucm base table
				$referral_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $referral_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($referral_condition);
				$db->setQuery($query);
				// Execute the query to remove Referral items
				$db->execute();

				// Remove Referral items from the ucm history table
				$referral_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $referral_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($referral_condition);
				$db->setQuery($query);
				// Execute the query to remove Referral items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Planning_type alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.planning_type') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$planning_type_found = $db->getNumRows();
		// Now check if there were any rows
		if ($planning_type_found)
		{
			// Since there are load the needed  planning_type type ids
			$planning_type_ids = $db->loadColumn();
			// Remove Planning_type from the content type table
			$planning_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.planning_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($planning_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Planning_type items
			$planning_type_done = $db->execute();
			if ($planning_type_done)
			{
				// If successfully remove Planning_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.planning_type) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Planning_type items from the contentitem tag map table
			$planning_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.planning_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($planning_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Planning_type items
			$planning_type_done = $db->execute();
			if ($planning_type_done)
			{
				// If successfully remove Planning_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.planning_type) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Planning_type items from the ucm content table
			$planning_type_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.planning_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($planning_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Planning_type items
			$planning_type_done = $db->execute();
			if ($planning_type_done)
			{
				// If successfully removed Planning_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.planning_type) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Planning_type items are cleared from DB
			foreach ($planning_type_ids as $planning_type_id)
			{
				// Remove Planning_type items from the ucm base table
				$planning_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $planning_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($planning_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Planning_type items
				$db->execute();

				// Remove Planning_type items from the ucm history table
				$planning_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $planning_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($planning_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Planning_type items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Diagnosis_type alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.diagnosis_type') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$diagnosis_type_found = $db->getNumRows();
		// Now check if there were any rows
		if ($diagnosis_type_found)
		{
			// Since there are load the needed  diagnosis_type type ids
			$diagnosis_type_ids = $db->loadColumn();
			// Remove Diagnosis_type from the content type table
			$diagnosis_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.diagnosis_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($diagnosis_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Diagnosis_type items
			$diagnosis_type_done = $db->execute();
			if ($diagnosis_type_done)
			{
				// If successfully remove Diagnosis_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.diagnosis_type) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Diagnosis_type items from the contentitem tag map table
			$diagnosis_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.diagnosis_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($diagnosis_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Diagnosis_type items
			$diagnosis_type_done = $db->execute();
			if ($diagnosis_type_done)
			{
				// If successfully remove Diagnosis_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.diagnosis_type) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Diagnosis_type items from the ucm content table
			$diagnosis_type_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.diagnosis_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($diagnosis_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Diagnosis_type items
			$diagnosis_type_done = $db->execute();
			if ($diagnosis_type_done)
			{
				// If successfully removed Diagnosis_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.diagnosis_type) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Diagnosis_type items are cleared from DB
			foreach ($diagnosis_type_ids as $diagnosis_type_id)
			{
				// Remove Diagnosis_type items from the ucm base table
				$diagnosis_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $diagnosis_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($diagnosis_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Diagnosis_type items
				$db->execute();

				// Remove Diagnosis_type items from the ucm history table
				$diagnosis_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $diagnosis_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($diagnosis_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Diagnosis_type items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Nonpay_reason alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.nonpay_reason') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$nonpay_reason_found = $db->getNumRows();
		// Now check if there were any rows
		if ($nonpay_reason_found)
		{
			// Since there are load the needed  nonpay_reason type ids
			$nonpay_reason_ids = $db->loadColumn();
			// Remove Nonpay_reason from the content type table
			$nonpay_reason_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.nonpay_reason') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($nonpay_reason_condition);
			$db->setQuery($query);
			// Execute the query to remove Nonpay_reason items
			$nonpay_reason_done = $db->execute();
			if ($nonpay_reason_done)
			{
				// If successfully remove Nonpay_reason add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.nonpay_reason) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Nonpay_reason items from the contentitem tag map table
			$nonpay_reason_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.nonpay_reason') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($nonpay_reason_condition);
			$db->setQuery($query);
			// Execute the query to remove Nonpay_reason items
			$nonpay_reason_done = $db->execute();
			if ($nonpay_reason_done)
			{
				// If successfully remove Nonpay_reason add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.nonpay_reason) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Nonpay_reason items from the ucm content table
			$nonpay_reason_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.nonpay_reason') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($nonpay_reason_condition);
			$db->setQuery($query);
			// Execute the query to remove Nonpay_reason items
			$nonpay_reason_done = $db->execute();
			if ($nonpay_reason_done)
			{
				// If successfully removed Nonpay_reason add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.nonpay_reason) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Nonpay_reason items are cleared from DB
			foreach ($nonpay_reason_ids as $nonpay_reason_id)
			{
				// Remove Nonpay_reason items from the ucm base table
				$nonpay_reason_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $nonpay_reason_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($nonpay_reason_condition);
				$db->setQuery($query);
				// Execute the query to remove Nonpay_reason items
				$db->execute();

				// Remove Nonpay_reason items from the ucm history table
				$nonpay_reason_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $nonpay_reason_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($nonpay_reason_condition);
				$db->setQuery($query);
				// Execute the query to remove Nonpay_reason items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Medication alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.medication') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$medication_found = $db->getNumRows();
		// Now check if there were any rows
		if ($medication_found)
		{
			// Since there are load the needed  medication type ids
			$medication_ids = $db->loadColumn();
			// Remove Medication from the content type table
			$medication_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.medication') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($medication_condition);
			$db->setQuery($query);
			// Execute the query to remove Medication items
			$medication_done = $db->execute();
			if ($medication_done)
			{
				// If successfully remove Medication add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.medication) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Medication items from the contentitem tag map table
			$medication_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.medication') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($medication_condition);
			$db->setQuery($query);
			// Execute the query to remove Medication items
			$medication_done = $db->execute();
			if ($medication_done)
			{
				// If successfully remove Medication add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.medication) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Medication items from the ucm content table
			$medication_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.medication') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($medication_condition);
			$db->setQuery($query);
			// Execute the query to remove Medication items
			$medication_done = $db->execute();
			if ($medication_done)
			{
				// If successfully removed Medication add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.medication) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Medication items are cleared from DB
			foreach ($medication_ids as $medication_id)
			{
				// Remove Medication items from the ucm base table
				$medication_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $medication_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($medication_condition);
				$db->setQuery($query);
				// Execute the query to remove Medication items
				$db->execute();

				// Remove Medication items from the ucm history table
				$medication_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $medication_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($medication_condition);
				$db->setQuery($query);
				// Execute the query to remove Medication items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Payment_amount alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.payment_amount') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$payment_amount_found = $db->getNumRows();
		// Now check if there were any rows
		if ($payment_amount_found)
		{
			// Since there are load the needed  payment_amount type ids
			$payment_amount_ids = $db->loadColumn();
			// Remove Payment_amount from the content type table
			$payment_amount_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.payment_amount') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($payment_amount_condition);
			$db->setQuery($query);
			// Execute the query to remove Payment_amount items
			$payment_amount_done = $db->execute();
			if ($payment_amount_done)
			{
				// If successfully remove Payment_amount add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.payment_amount) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Payment_amount items from the contentitem tag map table
			$payment_amount_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.payment_amount') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($payment_amount_condition);
			$db->setQuery($query);
			// Execute the query to remove Payment_amount items
			$payment_amount_done = $db->execute();
			if ($payment_amount_done)
			{
				// If successfully remove Payment_amount add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.payment_amount) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Payment_amount items from the ucm content table
			$payment_amount_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.payment_amount') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($payment_amount_condition);
			$db->setQuery($query);
			// Execute the query to remove Payment_amount items
			$payment_amount_done = $db->execute();
			if ($payment_amount_done)
			{
				// If successfully removed Payment_amount add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.payment_amount) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Payment_amount items are cleared from DB
			foreach ($payment_amount_ids as $payment_amount_id)
			{
				// Remove Payment_amount items from the ucm base table
				$payment_amount_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $payment_amount_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($payment_amount_condition);
				$db->setQuery($query);
				// Execute the query to remove Payment_amount items
				$db->execute();

				// Remove Payment_amount items from the ucm history table
				$payment_amount_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $payment_amount_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($payment_amount_condition);
				$db->setQuery($query);
				// Execute the query to remove Payment_amount items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Administration_part alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.administration_part') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$administration_part_found = $db->getNumRows();
		// Now check if there were any rows
		if ($administration_part_found)
		{
			// Since there are load the needed  administration_part type ids
			$administration_part_ids = $db->loadColumn();
			// Remove Administration_part from the content type table
			$administration_part_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.administration_part') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($administration_part_condition);
			$db->setQuery($query);
			// Execute the query to remove Administration_part items
			$administration_part_done = $db->execute();
			if ($administration_part_done)
			{
				// If successfully remove Administration_part add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.administration_part) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Administration_part items from the contentitem tag map table
			$administration_part_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.administration_part') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($administration_part_condition);
			$db->setQuery($query);
			// Execute the query to remove Administration_part items
			$administration_part_done = $db->execute();
			if ($administration_part_done)
			{
				// If successfully remove Administration_part add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.administration_part) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Administration_part items from the ucm content table
			$administration_part_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.administration_part') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($administration_part_condition);
			$db->setQuery($query);
			// Execute the query to remove Administration_part items
			$administration_part_done = $db->execute();
			if ($administration_part_done)
			{
				// If successfully removed Administration_part add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.administration_part) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Administration_part items are cleared from DB
			foreach ($administration_part_ids as $administration_part_id)
			{
				// Remove Administration_part items from the ucm base table
				$administration_part_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $administration_part_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($administration_part_condition);
				$db->setQuery($query);
				// Execute the query to remove Administration_part items
				$db->execute();

				// Remove Administration_part items from the ucm history table
				$administration_part_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $administration_part_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($administration_part_condition);
				$db->setQuery($query);
				// Execute the query to remove Administration_part items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Payment_type alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.payment_type') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$payment_type_found = $db->getNumRows();
		// Now check if there were any rows
		if ($payment_type_found)
		{
			// Since there are load the needed  payment_type type ids
			$payment_type_ids = $db->loadColumn();
			// Remove Payment_type from the content type table
			$payment_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.payment_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($payment_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Payment_type items
			$payment_type_done = $db->execute();
			if ($payment_type_done)
			{
				// If successfully remove Payment_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.payment_type) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Payment_type items from the contentitem tag map table
			$payment_type_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.payment_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($payment_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Payment_type items
			$payment_type_done = $db->execute();
			if ($payment_type_done)
			{
				// If successfully remove Payment_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.payment_type) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Payment_type items from the ucm content table
			$payment_type_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.payment_type') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($payment_type_condition);
			$db->setQuery($query);
			// Execute the query to remove Payment_type items
			$payment_type_done = $db->execute();
			if ($payment_type_done)
			{
				// If successfully removed Payment_type add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.payment_type) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Payment_type items are cleared from DB
			foreach ($payment_type_ids as $payment_type_id)
			{
				// Remove Payment_type items from the ucm base table
				$payment_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $payment_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($payment_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Payment_type items
				$db->execute();

				// Remove Payment_type items from the ucm history table
				$payment_type_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $payment_type_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($payment_type_condition);
				$db->setQuery($query);
				// Execute the query to remove Payment_type items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Strength alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.strength') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$strength_found = $db->getNumRows();
		// Now check if there were any rows
		if ($strength_found)
		{
			// Since there are load the needed  strength type ids
			$strength_ids = $db->loadColumn();
			// Remove Strength from the content type table
			$strength_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.strength') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($strength_condition);
			$db->setQuery($query);
			// Execute the query to remove Strength items
			$strength_done = $db->execute();
			if ($strength_done)
			{
				// If successfully remove Strength add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.strength) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Strength items from the contentitem tag map table
			$strength_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.strength') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($strength_condition);
			$db->setQuery($query);
			// Execute the query to remove Strength items
			$strength_done = $db->execute();
			if ($strength_done)
			{
				// If successfully remove Strength add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.strength) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Strength items from the ucm content table
			$strength_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.strength') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($strength_condition);
			$db->setQuery($query);
			// Execute the query to remove Strength items
			$strength_done = $db->execute();
			if ($strength_done)
			{
				// If successfully removed Strength add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.strength) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Strength items are cleared from DB
			foreach ($strength_ids as $strength_id)
			{
				// Remove Strength items from the ucm base table
				$strength_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $strength_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($strength_condition);
				$db->setQuery($query);
				// Execute the query to remove Strength items
				$db->execute();

				// Remove Strength items from the ucm history table
				$strength_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $strength_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($strength_condition);
				$db->setQuery($query);
				// Execute the query to remove Strength items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Site alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.site') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$site_found = $db->getNumRows();
		// Now check if there were any rows
		if ($site_found)
		{
			// Since there are load the needed  site type ids
			$site_ids = $db->loadColumn();
			// Remove Site from the content type table
			$site_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.site') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($site_condition);
			$db->setQuery($query);
			// Execute the query to remove Site items
			$site_done = $db->execute();
			if ($site_done)
			{
				// If successfully remove Site add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.site) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Site items from the contentitem tag map table
			$site_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_ehealth_portal.site') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($site_condition);
			$db->setQuery($query);
			// Execute the query to remove Site items
			$site_done = $db->execute();
			if ($site_done)
			{
				// If successfully remove Site add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.site) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Site items from the ucm content table
			$site_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_ehealth_portal.site') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($site_condition);
			$db->setQuery($query);
			// Execute the query to remove Site items
			$site_done = $db->execute();
			if ($site_done)
			{
				// If successfully removed Site add queued success message.
				$app->enqueueMessage(JText::_('The (com_ehealth_portal.site) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Site items are cleared from DB
			foreach ($site_ids as $site_id)
			{
				// Remove Site items from the ucm base table
				$site_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $site_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($site_condition);
				$db->setQuery($query);
				// Execute the query to remove Site items
				$db->execute();

				// Remove Site items from the ucm history table
				$site_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $site_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($site_condition);
				$db->setQuery($query);
				// Execute the query to remove Site items
				$db->execute();
			}
		}

		// If All related items was removed queued success message.
		$app->enqueueMessage(JText::_('All related items was removed from the <b>#__ucm_base</b> table'));
		$app->enqueueMessage(JText::_('All related items was removed from the <b>#__ucm_history</b> table'));

		// Remove ehealth_portal assets from the assets table
		$ehealth_portal_condition = array( $db->quoteName('name') . ' LIKE ' . $db->quote('com_ehealth_portal%') );

		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__assets'));
		$query->where($ehealth_portal_condition);
		$db->setQuery($query);
		$site_done = $db->execute();
		if ($site_done)
		{
			// If successfully removed ehealth_portal add queued success message.
			$app->enqueueMessage(JText::_('All related items was removed from the <b>#__assets</b> table'));
		}

		// Get the biggest rule column in the assets table at this point.
		$get_rule_length = "SELECT CHAR_LENGTH(`rules`) as rule_size FROM #__assets ORDER BY rule_size DESC LIMIT 1";
		$db->setQuery($get_rule_length);
		if ($db->execute())
		{
			$rule_length = $db->loadResult();
			// Check the size of the rules column
			if ($rule_length < 5120)
			{
				// Revert the assets table rules column back to the default
				$revert_rule = "ALTER TABLE `#__assets` CHANGE `rules` `rules` varchar(5120) NOT NULL COMMENT 'JSON encoded access control.';";
				$db->setQuery($revert_rule);
				$db->execute();
				$app->enqueueMessage(JText::_('Reverted the <b>#__assets</b> table rules column back to its default size of varchar(5120)'));
			}
			else
			{

				$app->enqueueMessage(JText::_('Could not revert the <b>#__assets</b> table rules column back to its default size of varchar(5120), since there is still one or more components that still requires the column to be larger.'));
			}
		}
		// little notice as after service, in case of bad experience with component.
		echo '<h2>Did something go wrong? Are you disappointed?</h2>
		<p>Please let me know at <a href="mailto:oh.martin@vdm.io">oh.martin@vdm.io</a>.
		<br />We at Vast Development Method are committed to building extensions that performs proficiently! You can help us, really!
		<br />Send me your thoughts on improvements that is needed, trust me, I will be very grateful!
		<br />Visit us at <a href="https://github.com/namibia/eHealth-Portal" target="_blank">https://github.com/namibia/eHealth-Portal</a> today!</p>';
	}

	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update(JAdapterInstance $parent){}

	/**
	 * Called before any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($type, JAdapterInstance $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// is redundant or so it seems ...hmmm let me know if it works again
		if ($type === 'uninstall')
		{
			return true;
		}
		// the default for both install and update
		$jversion = new JVersion();
		if (!$jversion->isCompatible('3.8.0'))
		{
			$app->enqueueMessage('Please upgrade to at least Joomla! 3.8.0 before continuing!', 'error');
			return false;
		}
		// do any updates needed
		if ($type === 'update')
		{
		}
		// do any install needed
		if ($type === 'install')
		{
		}
		// check if the PHPExcel stuff is still around
		if (File::exists(JPATH_ADMINISTRATOR . '/components/com_ehealth_portal/helpers/PHPExcel.php'))
		{
			// We need to remove this old PHPExcel folder
			$this->removeFolder(JPATH_ADMINISTRATOR . '/components/com_ehealth_portal/helpers/PHPExcel');
			// We need to remove this old PHPExcel file
			File::delete(JPATH_ADMINISTRATOR . '/components/com_ehealth_portal/helpers/PHPExcel.php');
		}
		return true;
	}

	/**
	 * Called after any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($type, JAdapterInstance $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// We check if we have dynamic folders to copy
		$this->setDynamicF0ld3rs($app, $parent);
		// set the default component settings
		if ($type === 'install')
		{

			// Get The Database object
			$db = JFactory::getDbo();

			// Create the payment content type object.
			$payment = new stdClass();
			$payment->type_title = 'Ehealth_portal Payment';
			$payment->type_alias = 'com_ehealth_portal.payment';
			$payment->table = '{"special": {"dbtable": "#__ehealth_portal_payment","key": "id","type": "Payment","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$payment->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","payment_category":"payment_category","payment_type":"payment_type","payment_amount":"payment_amount","nonpay_reason":"nonpay_reason","receipt_no":"receipt_no"}}';
			$payment->router = 'Ehealth_portalHelperRoute::getPaymentRoute';
			$payment->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/payment.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","payment_type","nonpay_reason"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "payment_type","targetTable": "#__ehealth_portal_payment_type","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "nonpay_reason","targetTable": "#__ehealth_portal_nonpay_reason","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$payment_Inserted = $db->insertObject('#__content_types', $payment);

			// Create the general_medical_check_up content type object.
			$general_medical_check_up = new stdClass();
			$general_medical_check_up->type_title = 'Ehealth_portal General_medical_check_up';
			$general_medical_check_up->type_alias = 'com_ehealth_portal.general_medical_check_up';
			$general_medical_check_up->table = '{"special": {"dbtable": "#__ehealth_portal_general_medical_check_up","key": "id","type": "General_medical_check_up","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$general_medical_check_up->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","bp_diastolic_one":"bp_diastolic_one","bp_systolic_one":"bp_systolic_one","temp_one":"temp_one","weight":"weight","pulse":"pulse","chronic_medication":"chronic_medication","bp_diastolic_two":"bp_diastolic_two","bp_systolic_two":"bp_systolic_two","temp_two":"temp_two","height":"height","bmi":"bmi","complaint":"complaint","investigations":"investigations","notes":"notes","diagnosis":"diagnosis","referral":"referral","reason":"reason"}}';
			$general_medical_check_up->router = 'Ehealth_portalHelperRoute::getGeneral_medical_check_upRoute';
			$general_medical_check_up->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/general_medical_check_up.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","bp_diastolic_one","bp_systolic_one","pulse","bp_diastolic_two","bp_systolic_two","diagnosis","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "diagnosis","targetTable": "#__ehealth_portal_diagnosis_type","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealth_portal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$general_medical_check_up_Inserted = $db->insertObject('#__content_types', $general_medical_check_up);

			// Create the antenatal_care content type object.
			$antenatal_care = new stdClass();
			$antenatal_care->type_title = 'Ehealth_portal Antenatal_care';
			$antenatal_care->type_alias = 'com_ehealth_portal.antenatal_care';
			$antenatal_care->table = '{"special": {"dbtable": "#__ehealth_portal_antenatal_care","key": "id","type": "Antenatal_care","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$antenatal_care->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","foetal_lie":"foetal_lie","foetal_presentation":"foetal_presentation","foetal_engagement":"foetal_engagement","foetal_heart_rate":"foetal_heart_rate","foetal_movements":"foetal_movements","caesarean_sections":"caesarean_sections","last_menstrual_period":"last_menstrual_period","normal_births":"normal_births","still_births":"still_births","miscarriages":"miscarriages","live_births":"live_births","pregnancies_excl":"pregnancies_excl"}}';
			$antenatal_care->router = 'Ehealth_portalHelperRoute::getAntenatal_careRoute';
			$antenatal_care->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/antenatal_care.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","foetal_lie","foetal_presentation","foetal_engagement","foetal_heart_rate","caesarean_sections","normal_births","still_births","miscarriages","live_births","pregnancies_excl"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "foetal_lie","targetTable": "#__ehealth_portal_foetal_lie","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "foetal_presentation","targetTable": "#__ehealth_portal_foetal_presentation","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "foetal_engagement","targetTable": "#__ehealth_portal_foetal_engagement","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$antenatal_care_Inserted = $db->insertObject('#__content_types', $antenatal_care);

			// Create the immunisation content type object.
			$immunisation = new stdClass();
			$immunisation->type_title = 'Ehealth_portal Immunisation';
			$immunisation->type_alias = 'com_ehealth_portal.immunisation';
			$immunisation->table = '{"special": {"dbtable": "#__ehealth_portal_immunisation","key": "id","type": "Immunisation","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$immunisation->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","referral":"referral","immunisation_up_to_date":"immunisation_up_to_date","reason":"reason","immunisation_vaccine_type":"immunisation_vaccine_type","administration_part":"administration_part"}}';
			$immunisation->router = 'Ehealth_portalHelperRoute::getImmunisationRoute';
			$immunisation->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/immunisation.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","referral","immunisation_vaccine_type","administration_part"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealth_portal_referral","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "immunisation_vaccine_type","targetTable": "#__ehealth_portal_immunisation_vaccine_type","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "administration_part","targetTable": "#__ehealth_portal_administration_part","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$immunisation_Inserted = $db->insertObject('#__content_types', $immunisation);

			// Create the vmmc content type object.
			$vmmc = new stdClass();
			$vmmc->type_title = 'Ehealth_portal Vmmc';
			$vmmc->type_alias = 'com_ehealth_portal.vmmc';
			$vmmc->table = '{"special": {"dbtable": "#__ehealth_portal_vmmc","key": "id","type": "Vmmc","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$vmmc->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","are_you_circumcised":"are_you_circumcised","info_ben_vmcc":"info_ben_vmcc","interested_in_vmmc":"interested_in_vmmc","vmmc_gender":"vmmc_gender","reason":"reason","referral":"referral","partner_circumcised":"partner_circumcised"}}';
			$vmmc->router = 'Ehealth_portalHelperRoute::getVmmcRoute';
			$vmmc->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/vmmc.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealth_portal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$vmmc_Inserted = $db->insertObject('#__content_types', $vmmc);

			// Create the prostate_and_testicular_cancer content type object.
			$prostate_and_testicular_cancer = new stdClass();
			$prostate_and_testicular_cancer->type_title = 'Ehealth_portal Prostate_and_testicular_cancer';
			$prostate_and_testicular_cancer->type_alias = 'com_ehealth_portal.prostate_and_testicular_cancer';
			$prostate_and_testicular_cancer->table = '{"special": {"dbtable": "#__ehealth_portal_prostate_and_testicular_cancer","key": "id","type": "Prostate_and_testicular_cancer","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$prostate_and_testicular_cancer->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","ptc_age":"ptc_age","ptc_fam_history":"ptc_fam_history","ptc_diet":"ptc_diet","ptc_phy_activity":"ptc_phy_activity","ptc_overweight":"ptc_overweight","ptc_urinate":"ptc_urinate","ptc_urine_freq":"ptc_urine_freq","referral":"referral","reason":"reason","txt_ptc_fam_history":"txt_ptc_fam_history","txt_ptc_urine_freq":"txt_ptc_urine_freq","txt_ptc_urinate":"txt_ptc_urinate","txt_ptc_overweight":"txt_ptc_overweight","txt_ptc_age":"txt_ptc_age","txt_ptc_phy_activity":"txt_ptc_phy_activity","txt_ptc_diet":"txt_ptc_diet"}}';
			$prostate_and_testicular_cancer->router = 'Ehealth_portalHelperRoute::getProstate_and_testicular_cancerRoute';
			$prostate_and_testicular_cancer->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/prostate_and_testicular_cancer.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealth_portal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$prostate_and_testicular_cancer_Inserted = $db->insertObject('#__content_types', $prostate_and_testicular_cancer);

			// Create the tuberculosis content type object.
			$tuberculosis = new stdClass();
			$tuberculosis->type_title = 'Ehealth_portal Tuberculosis';
			$tuberculosis->type_alias = 'com_ehealth_portal.tuberculosis';
			$tuberculosis->table = '{"special": {"dbtable": "#__ehealth_portal_tuberculosis","key": "id","type": "Tuberculosis","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$tuberculosis->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","recurring_night_sweats":"recurring_night_sweats","tb_fever":"tb_fever","persistent_cough":"persistent_cough","blood_streaked_sputum":"blood_streaked_sputum","unusual_tiredness":"unusual_tiredness","pain_in_chest":"pain_in_chest","shortness_of_breath":"shortness_of_breath","diagnosed_with_disease":"diagnosed_with_disease","tb_exposed":"tb_exposed","tb_treatment":"tb_treatment","date_of_treatment":"date_of_treatment","treating_dhc":"treating_dhc","sputum_collection_one":"sputum_collection_one","tb_reason_one":"tb_reason_one","sputum_result_one":"sputum_result_one","referred_second_sputum":"referred_second_sputum","tb_reason_two":"tb_reason_two","sputum_result_two":"sputum_result_two","weight_loss_wdieting":"weight_loss_wdieting","referral":"referral","reason":"reason"}}';
			$tuberculosis->router = 'Ehealth_portalHelperRoute::getTuberculosisRoute';
			$tuberculosis->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/tuberculosis.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealth_portal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$tuberculosis_Inserted = $db->insertObject('#__content_types', $tuberculosis);

			// Create the hiv_counseling_and_testing content type object.
			$hiv_counseling_and_testing = new stdClass();
			$hiv_counseling_and_testing->type_title = 'Ehealth_portal Hiv_counseling_and_testing';
			$hiv_counseling_and_testing->type_alias = 'com_ehealth_portal.hiv_counseling_and_testing';
			$hiv_counseling_and_testing->table = '{"special": {"dbtable": "#__ehealth_portal_hiv_counseling_and_testing","key": "id","type": "Hiv_counseling_and_testing","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$hiv_counseling_and_testing->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","counseling_type":"counseling_type","testing_reason":"testing_reason","last_test_date":"last_test_date","prev_test_result":"prev_test_result","test_result_one":"test_result_one","test_result_two":"test_result_two","final_test_result":"final_test_result","eqa":"eqa","referral":"referral","reason":"reason"}}';
			$hiv_counseling_and_testing->router = 'Ehealth_portalHelperRoute::getHiv_counseling_and_testingRoute';
			$hiv_counseling_and_testing->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/hiv_counseling_and_testing.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","testing_reason","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "testing_reason","targetTable": "#__ehealth_portal_testing_reason","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealth_portal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$hiv_counseling_and_testing_Inserted = $db->insertObject('#__content_types', $hiv_counseling_and_testing);

			// Create the family_planning content type object.
			$family_planning = new stdClass();
			$family_planning->type_title = 'Ehealth_portal Family_planning';
			$family_planning->type_alias = 'com_ehealth_portal.family_planning';
			$family_planning->table = '{"special": {"dbtable": "#__ehealth_portal_family_planning","key": "id","type": "Family_planning","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$family_planning->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","diagnosis":"diagnosis"}}';
			$family_planning->router = 'Ehealth_portalHelperRoute::getFamily_planningRoute';
			$family_planning->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/family_planning.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","diagnosis"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "diagnosis","targetTable": "#__ehealth_portal_planning_type","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$family_planning_Inserted = $db->insertObject('#__content_types', $family_planning);

			// Create the health_education content type object.
			$health_education = new stdClass();
			$health_education->type_title = 'Ehealth_portal Health_education';
			$health_education->type_alias = 'com_ehealth_portal.health_education';
			$health_education->table = '{"special": {"dbtable": "#__ehealth_portal_health_education","key": "id","type": "Health_education","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$health_education->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"education_type":"education_type","health_education_topic":"health_education_topic","patient":"patient"}}';
			$health_education->router = 'Ehealth_portalHelperRoute::getHealth_educationRoute';
			$health_education->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/health_education.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","health_education_topic","patient"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "health_education_topic","targetTable": "#__ehealth_portal_health_education_topic","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$health_education_Inserted = $db->insertObject('#__content_types', $health_education);

			// Create the cervical_cancer content type object.
			$cervical_cancer = new stdClass();
			$cervical_cancer->type_title = 'Ehealth_portal Cervical_cancer';
			$cervical_cancer->type_alias = 'com_ehealth_portal.cervical_cancer';
			$cervical_cancer->table = '{"special": {"dbtable": "#__ehealth_portal_cervical_cancer","key": "id","type": "Cervical_cancer","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$cervical_cancer->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","cc_viginal_bleeding":"cc_viginal_bleeding","cc_v_discharge":"cc_v_discharge","cc_periods":"cc_periods","cc_smoking":"cc_smoking","cc_sex_actve":"cc_sex_actve","cc_sex_partner":"cc_sex_partner","pap_smear_collection":"pap_smear_collection","cc_result":"cc_result","referral":"referral","reason":"reason","txt_cc_v_discharge":"txt_cc_v_discharge","txt_cc_sex_actve":"txt_cc_sex_actve","txt_cc_periods":"txt_cc_periods","cc_reason":"cc_reason","txt_cc_smoking":"txt_cc_smoking","txt_cc_viginal_bleeding":"txt_cc_viginal_bleeding","txt_cc_sex_partner":"txt_cc_sex_partner"}}';
			$cervical_cancer->router = 'Ehealth_portalHelperRoute::getCervical_cancerRoute';
			$cervical_cancer->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/cervical_cancer.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealth_portal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$cervical_cancer_Inserted = $db->insertObject('#__content_types', $cervical_cancer);

			// Create the breast_cancer content type object.
			$breast_cancer = new stdClass();
			$breast_cancer->type_title = 'Ehealth_portal Breast_cancer';
			$breast_cancer->type_alias = 'com_ehealth_portal.breast_cancer';
			$breast_cancer->table = '{"special": {"dbtable": "#__ehealth_portal_breast_cancer","key": "id","type": "Breast_cancer","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$breast_cancer->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","bc_age_range":"bc_age_range","bc_family_history":"bc_family_history","bc_race":"bc_race","bc_breastfeeding":"bc_breastfeeding","bc_preg_freq":"bc_preg_freq","bc_preg_age":"bc_preg_age","bc_history_hrt":"bc_history_hrt","bc_reg_exercise":"bc_reg_exercise","bc_overweight":"bc_overweight","bc_lump_near_breast":"bc_lump_near_breast","bc_dimpling":"bc_dimpling","bc_inward_nipple":"bc_inward_nipple","bc_nipple_discharge":"bc_nipple_discharge","bc_abnormal_skin":"bc_abnormal_skin","bc_breast_shape":"bc_breast_shape","referral":"referral","reason":"reason","txt_bc_breast_shape":"txt_bc_breast_shape","txt_bc_abnormal_skin":"txt_bc_abnormal_skin","txt_bc_nipple_discharge":"txt_bc_nipple_discharge","txt_bc_dimpling":"txt_bc_dimpling","txt_bc_lump_near_breast":"txt_bc_lump_near_breast","txt_bc_inward_nipple":"txt_bc_inward_nipple"}}';
			$breast_cancer->router = 'Ehealth_portalHelperRoute::getBreast_cancerRoute';
			$breast_cancer->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/breast_cancer.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","bc_preg_freq","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealth_portal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$breast_cancer_Inserted = $db->insertObject('#__content_types', $breast_cancer);

			// Create the test content type object.
			$test = new stdClass();
			$test->type_title = 'Ehealth_portal Test';
			$test->type_alias = 'com_ehealth_portal.test';
			$test->table = '{"special": {"dbtable": "#__ehealth_portal_test","key": "id","type": "Test","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$test->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","urine_test_result":"urine_test_result","glucose_first_reading":"glucose_first_reading","glucose_second_reading":"glucose_second_reading","haemoglobin_reading":"haemoglobin_reading","cholesterol_reading":"cholesterol_reading","syphilis_first_reading":"syphilis_first_reading","syphilis_second_reading":"syphilis_second_reading","hepatitis_first_reading":"hepatitis_first_reading","hepatitis_second_reading":"hepatitis_second_reading","malaria_first_reading":"malaria_first_reading","malaria_second_reading":"malaria_second_reading","pregnancy_first_reading":"pregnancy_first_reading","pregnancy_second_reading":"pregnancy_second_reading","referral":"referral","reason":"reason"}}';
			$test->router = 'Ehealth_portalHelperRoute::getTestRoute';
			$test->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/test.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","glucose_first_reading","glucose_second_reading","haemoglobin_reading","cholesterol_reading","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealth_portal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$test_Inserted = $db->insertObject('#__content_types', $test);

			// Create the immunisation_vaccine_type content type object.
			$immunisation_vaccine_type = new stdClass();
			$immunisation_vaccine_type->type_title = 'Ehealth_portal Immunisation_vaccine_type';
			$immunisation_vaccine_type->type_alias = 'com_ehealth_portal.immunisation_vaccine_type';
			$immunisation_vaccine_type->table = '{"special": {"dbtable": "#__ehealth_portal_immunisation_vaccine_type","key": "id","type": "Immunisation_vaccine_type","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$immunisation_vaccine_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","administration_part":"administration_part","description":"description","alias":"alias"}}';
			$immunisation_vaccine_type->router = 'Ehealth_portalHelperRoute::getImmunisation_vaccine_typeRoute';
			$immunisation_vaccine_type->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/immunisation_vaccine_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","administration_part"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "administration_part","targetTable": "#__ehealth_portal_administration_part","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$immunisation_vaccine_type_Inserted = $db->insertObject('#__content_types', $immunisation_vaccine_type);

			// Create the foetal_presentation content type object.
			$foetal_presentation = new stdClass();
			$foetal_presentation->type_title = 'Ehealth_portal Foetal_presentation';
			$foetal_presentation->type_alias = 'com_ehealth_portal.foetal_presentation';
			$foetal_presentation->table = '{"special": {"dbtable": "#__ehealth_portal_foetal_presentation","key": "id","type": "Foetal_presentation","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$foetal_presentation->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$foetal_presentation->router = 'Ehealth_portalHelperRoute::getFoetal_presentationRoute';
			$foetal_presentation->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/foetal_presentation.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$foetal_presentation_Inserted = $db->insertObject('#__content_types', $foetal_presentation);

			// Create the foetal_lie content type object.
			$foetal_lie = new stdClass();
			$foetal_lie->type_title = 'Ehealth_portal Foetal_lie';
			$foetal_lie->type_alias = 'com_ehealth_portal.foetal_lie';
			$foetal_lie->table = '{"special": {"dbtable": "#__ehealth_portal_foetal_lie","key": "id","type": "Foetal_lie","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$foetal_lie->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$foetal_lie->router = 'Ehealth_portalHelperRoute::getFoetal_lieRoute';
			$foetal_lie->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/foetal_lie.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$foetal_lie_Inserted = $db->insertObject('#__content_types', $foetal_lie);

			// Create the counseling_type content type object.
			$counseling_type = new stdClass();
			$counseling_type->type_title = 'Ehealth_portal Counseling_type';
			$counseling_type->type_alias = 'com_ehealth_portal.counseling_type';
			$counseling_type->table = '{"special": {"dbtable": "#__ehealth_portal_counseling_type","key": "id","type": "Counseling_type","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$counseling_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$counseling_type->router = 'Ehealth_portalHelperRoute::getCounseling_typeRoute';
			$counseling_type->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/counseling_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$counseling_type_Inserted = $db->insertObject('#__content_types', $counseling_type);

			// Create the foetal_engagement content type object.
			$foetal_engagement = new stdClass();
			$foetal_engagement->type_title = 'Ehealth_portal Foetal_engagement';
			$foetal_engagement->type_alias = 'com_ehealth_portal.foetal_engagement';
			$foetal_engagement->table = '{"special": {"dbtable": "#__ehealth_portal_foetal_engagement","key": "id","type": "Foetal_engagement","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$foetal_engagement->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$foetal_engagement->router = 'Ehealth_portalHelperRoute::getFoetal_engagementRoute';
			$foetal_engagement->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/foetal_engagement.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$foetal_engagement_Inserted = $db->insertObject('#__content_types', $foetal_engagement);

			// Create the health_education_topic content type object.
			$health_education_topic = new stdClass();
			$health_education_topic->type_title = 'Ehealth_portal Health_education_topic';
			$health_education_topic->type_alias = 'com_ehealth_portal.health_education_topic';
			$health_education_topic->table = '{"special": {"dbtable": "#__ehealth_portal_health_education_topic","key": "id","type": "Health_education_topic","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$health_education_topic->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$health_education_topic->router = 'Ehealth_portalHelperRoute::getHealth_education_topicRoute';
			$health_education_topic->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/health_education_topic.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$health_education_topic_Inserted = $db->insertObject('#__content_types', $health_education_topic);

			// Create the testing_reason content type object.
			$testing_reason = new stdClass();
			$testing_reason->type_title = 'Ehealth_portal Testing_reason';
			$testing_reason->type_alias = 'com_ehealth_portal.testing_reason';
			$testing_reason->table = '{"special": {"dbtable": "#__ehealth_portal_testing_reason","key": "id","type": "Testing_reason","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$testing_reason->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$testing_reason->router = 'Ehealth_portalHelperRoute::getTesting_reasonRoute';
			$testing_reason->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/testing_reason.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$testing_reason_Inserted = $db->insertObject('#__content_types', $testing_reason);

			// Create the clinic content type object.
			$clinic = new stdClass();
			$clinic->type_title = 'Ehealth_portal Clinic';
			$clinic->type_alias = 'com_ehealth_portal.clinic';
			$clinic->table = '{"special": {"dbtable": "#__ehealth_portal_clinic","key": "id","type": "Clinic","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$clinic->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "clinic_name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"clinic_name":"clinic_name","description":"description","clinic_type":"clinic_type","alias":"alias"}}';
			$clinic->router = 'Ehealth_portalHelperRoute::getClinicRoute';
			$clinic->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/clinic.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$clinic_Inserted = $db->insertObject('#__content_types', $clinic);

			// Create the immunisation_type content type object.
			$immunisation_type = new stdClass();
			$immunisation_type->type_title = 'Ehealth_portal Immunisation_type';
			$immunisation_type->type_alias = 'com_ehealth_portal.immunisation_type';
			$immunisation_type->table = '{"special": {"dbtable": "#__ehealth_portal_immunisation_type","key": "id","type": "Immunisation_type","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$immunisation_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$immunisation_type->router = 'Ehealth_portalHelperRoute::getImmunisation_typeRoute';
			$immunisation_type->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/immunisation_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$immunisation_type_Inserted = $db->insertObject('#__content_types', $immunisation_type);

			// Create the unit content type object.
			$unit = new stdClass();
			$unit->type_title = 'Ehealth_portal Unit';
			$unit->type_alias = 'com_ehealth_portal.unit';
			$unit->table = '{"special": {"dbtable": "#__ehealth_portal_unit","key": "id","type": "Unit","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$unit->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$unit->router = 'Ehealth_portalHelperRoute::getUnitRoute';
			$unit->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/unit.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$unit_Inserted = $db->insertObject('#__content_types', $unit);

			// Create the referral content type object.
			$referral = new stdClass();
			$referral->type_title = 'Ehealth_portal Referral';
			$referral->type_alias = 'com_ehealth_portal.referral';
			$referral->table = '{"special": {"dbtable": "#__ehealth_portal_referral","key": "id","type": "Referral","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$referral->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$referral->router = 'Ehealth_portalHelperRoute::getReferralRoute';
			$referral->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/referral.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$referral_Inserted = $db->insertObject('#__content_types', $referral);

			// Create the planning_type content type object.
			$planning_type = new stdClass();
			$planning_type->type_title = 'Ehealth_portal Planning_type';
			$planning_type->type_alias = 'com_ehealth_portal.planning_type';
			$planning_type->table = '{"special": {"dbtable": "#__ehealth_portal_planning_type","key": "id","type": "Planning_type","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$planning_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$planning_type->router = 'Ehealth_portalHelperRoute::getPlanning_typeRoute';
			$planning_type->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/planning_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$planning_type_Inserted = $db->insertObject('#__content_types', $planning_type);

			// Create the diagnosis_type content type object.
			$diagnosis_type = new stdClass();
			$diagnosis_type->type_title = 'Ehealth_portal Diagnosis_type';
			$diagnosis_type->type_alias = 'com_ehealth_portal.diagnosis_type';
			$diagnosis_type->table = '{"special": {"dbtable": "#__ehealth_portal_diagnosis_type","key": "id","type": "Diagnosis_type","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$diagnosis_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$diagnosis_type->router = 'Ehealth_portalHelperRoute::getDiagnosis_typeRoute';
			$diagnosis_type->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/diagnosis_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$diagnosis_type_Inserted = $db->insertObject('#__content_types', $diagnosis_type);

			// Create the nonpay_reason content type object.
			$nonpay_reason = new stdClass();
			$nonpay_reason->type_title = 'Ehealth_portal Nonpay_reason';
			$nonpay_reason->type_alias = 'com_ehealth_portal.nonpay_reason';
			$nonpay_reason->table = '{"special": {"dbtable": "#__ehealth_portal_nonpay_reason","key": "id","type": "Nonpay_reason","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$nonpay_reason->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$nonpay_reason->router = 'Ehealth_portalHelperRoute::getNonpay_reasonRoute';
			$nonpay_reason->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/nonpay_reason.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$nonpay_reason_Inserted = $db->insertObject('#__content_types', $nonpay_reason);

			// Create the medication content type object.
			$medication = new stdClass();
			$medication->type_title = 'Ehealth_portal Medication';
			$medication->type_alias = 'com_ehealth_portal.medication';
			$medication->table = '{"special": {"dbtable": "#__ehealth_portal_medication","key": "id","type": "Medication","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$medication->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$medication->router = 'Ehealth_portalHelperRoute::getMedicationRoute';
			$medication->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/medication.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$medication_Inserted = $db->insertObject('#__content_types', $medication);

			// Create the payment_amount content type object.
			$payment_amount = new stdClass();
			$payment_amount->type_title = 'Ehealth_portal Payment_amount';
			$payment_amount->type_alias = 'com_ehealth_portal.payment_amount';
			$payment_amount->table = '{"special": {"dbtable": "#__ehealth_portal_payment_amount","key": "id","type": "Payment_amount","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$payment_amount->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$payment_amount->router = 'Ehealth_portalHelperRoute::getPayment_amountRoute';
			$payment_amount->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/payment_amount.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$payment_amount_Inserted = $db->insertObject('#__content_types', $payment_amount);

			// Create the administration_part content type object.
			$administration_part = new stdClass();
			$administration_part->type_title = 'Ehealth_portal Administration_part';
			$administration_part->type_alias = 'com_ehealth_portal.administration_part';
			$administration_part->table = '{"special": {"dbtable": "#__ehealth_portal_administration_part","key": "id","type": "Administration_part","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$administration_part->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$administration_part->router = 'Ehealth_portalHelperRoute::getAdministration_partRoute';
			$administration_part->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/administration_part.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$administration_part_Inserted = $db->insertObject('#__content_types', $administration_part);

			// Create the payment_type content type object.
			$payment_type = new stdClass();
			$payment_type->type_title = 'Ehealth_portal Payment_type';
			$payment_type->type_alias = 'com_ehealth_portal.payment_type';
			$payment_type->table = '{"special": {"dbtable": "#__ehealth_portal_payment_type","key": "id","type": "Payment_type","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$payment_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$payment_type->router = 'Ehealth_portalHelperRoute::getPayment_typeRoute';
			$payment_type->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/payment_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$payment_type_Inserted = $db->insertObject('#__content_types', $payment_type);

			// Create the strength content type object.
			$strength = new stdClass();
			$strength->type_title = 'Ehealth_portal Strength';
			$strength->type_alias = 'com_ehealth_portal.strength';
			$strength->table = '{"special": {"dbtable": "#__ehealth_portal_strength","key": "id","type": "Strength","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$strength->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$strength->router = 'Ehealth_portalHelperRoute::getStrengthRoute';
			$strength->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/strength.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$strength_Inserted = $db->insertObject('#__content_types', $strength);

			// Create the site content type object.
			$site = new stdClass();
			$site->type_title = 'Ehealth_portal Site';
			$site->type_alias = 'com_ehealth_portal.site';
			$site->table = '{"special": {"dbtable": "#__ehealth_portal_site","key": "id","type": "Site","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$site->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "site_name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"site_name":"site_name","description":"description","site_region":"site_region","alias":"alias"}}';
			$site->router = 'Ehealth_portalHelperRoute::getSiteRoute';
			$site->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/site.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$site_Inserted = $db->insertObject('#__content_types', $site);


			// Install the global extenstion params.
			$query = $db->getQuery(true);
			// Field to update.
			$fields = array(
				$db->quoteName('params') . ' = ' . $db->quote('{"autorName":"Oh Martin","autorEmail":"oh.martin@vdm.io","check_in":"-1 day","save_history":"1","history_limit":"10"}'),
			);
			// Condition.
			$conditions = array(
				$db->quoteName('element') . ' = ' . $db->quote('com_ehealth_portal')
			);
			$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$allDone = $db->execute();

			// Get the biggest rule column in the assets table at this point.
			$get_rule_length = "SELECT CHAR_LENGTH(`rules`) as rule_size FROM #__assets ORDER BY rule_size DESC LIMIT 1";
			$db->setQuery($get_rule_length);
			if ($db->execute())
			{
				$rule_length = $db->loadResult();
				// Check the size of the rules column
				if ($rule_length <= 44000)
				{
					// Fix the assets table rules column size
					$fix_rules_size = "ALTER TABLE `#__assets` CHANGE `rules` `rules` TEXT NOT NULL COMMENT 'JSON encoded access control. Enlarged to TEXT by JCB';";
					$db->setQuery($fix_rules_size);
					$db->execute();
					$app->enqueueMessage(JText::_('The <b>#__assets</b> table rules column was resized to the TEXT datatype for the components possible large permission rules.'));
				}
			}
			echo '<a target="_blank" href="https://github.com/namibia/eHealth-Portal" title="eHealth Portal">
				<img src="components/com_ehealth_portal/assets/images/vdm-component.jpg"/>
				</a>';
		}
		// do any updates needed
		if ($type === 'update')
		{

			// Get The Database object
			$db = JFactory::getDbo();

			// Create the payment content type object.
			$payment = new stdClass();
			$payment->type_title = 'Ehealth_portal Payment';
			$payment->type_alias = 'com_ehealth_portal.payment';
			$payment->table = '{"special": {"dbtable": "#__ehealth_portal_payment","key": "id","type": "Payment","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$payment->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","payment_category":"payment_category","payment_type":"payment_type","payment_amount":"payment_amount","nonpay_reason":"nonpay_reason","receipt_no":"receipt_no"}}';
			$payment->router = 'Ehealth_portalHelperRoute::getPaymentRoute';
			$payment->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/payment.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","payment_type","nonpay_reason"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "payment_type","targetTable": "#__ehealth_portal_payment_type","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "nonpay_reason","targetTable": "#__ehealth_portal_nonpay_reason","targetColumn": "id","displayColumn": "name"}]}';

			// Check if payment type is already in content_type DB.
			$payment_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($payment->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$payment->type_id = $db->loadResult();
				$payment_Updated = $db->updateObject('#__content_types', $payment, 'type_id');
			}
			else
			{
				$payment_Inserted = $db->insertObject('#__content_types', $payment);
			}

			// Create the general_medical_check_up content type object.
			$general_medical_check_up = new stdClass();
			$general_medical_check_up->type_title = 'Ehealth_portal General_medical_check_up';
			$general_medical_check_up->type_alias = 'com_ehealth_portal.general_medical_check_up';
			$general_medical_check_up->table = '{"special": {"dbtable": "#__ehealth_portal_general_medical_check_up","key": "id","type": "General_medical_check_up","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$general_medical_check_up->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","bp_diastolic_one":"bp_diastolic_one","bp_systolic_one":"bp_systolic_one","temp_one":"temp_one","weight":"weight","pulse":"pulse","chronic_medication":"chronic_medication","bp_diastolic_two":"bp_diastolic_two","bp_systolic_two":"bp_systolic_two","temp_two":"temp_two","height":"height","bmi":"bmi","complaint":"complaint","investigations":"investigations","notes":"notes","diagnosis":"diagnosis","referral":"referral","reason":"reason"}}';
			$general_medical_check_up->router = 'Ehealth_portalHelperRoute::getGeneral_medical_check_upRoute';
			$general_medical_check_up->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/general_medical_check_up.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","bp_diastolic_one","bp_systolic_one","pulse","bp_diastolic_two","bp_systolic_two","diagnosis","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "diagnosis","targetTable": "#__ehealth_portal_diagnosis_type","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealth_portal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Check if general_medical_check_up type is already in content_type DB.
			$general_medical_check_up_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($general_medical_check_up->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$general_medical_check_up->type_id = $db->loadResult();
				$general_medical_check_up_Updated = $db->updateObject('#__content_types', $general_medical_check_up, 'type_id');
			}
			else
			{
				$general_medical_check_up_Inserted = $db->insertObject('#__content_types', $general_medical_check_up);
			}

			// Create the antenatal_care content type object.
			$antenatal_care = new stdClass();
			$antenatal_care->type_title = 'Ehealth_portal Antenatal_care';
			$antenatal_care->type_alias = 'com_ehealth_portal.antenatal_care';
			$antenatal_care->table = '{"special": {"dbtable": "#__ehealth_portal_antenatal_care","key": "id","type": "Antenatal_care","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$antenatal_care->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","foetal_lie":"foetal_lie","foetal_presentation":"foetal_presentation","foetal_engagement":"foetal_engagement","foetal_heart_rate":"foetal_heart_rate","foetal_movements":"foetal_movements","caesarean_sections":"caesarean_sections","last_menstrual_period":"last_menstrual_period","normal_births":"normal_births","still_births":"still_births","miscarriages":"miscarriages","live_births":"live_births","pregnancies_excl":"pregnancies_excl"}}';
			$antenatal_care->router = 'Ehealth_portalHelperRoute::getAntenatal_careRoute';
			$antenatal_care->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/antenatal_care.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","foetal_lie","foetal_presentation","foetal_engagement","foetal_heart_rate","caesarean_sections","normal_births","still_births","miscarriages","live_births","pregnancies_excl"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "foetal_lie","targetTable": "#__ehealth_portal_foetal_lie","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "foetal_presentation","targetTable": "#__ehealth_portal_foetal_presentation","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "foetal_engagement","targetTable": "#__ehealth_portal_foetal_engagement","targetColumn": "id","displayColumn": "name"}]}';

			// Check if antenatal_care type is already in content_type DB.
			$antenatal_care_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($antenatal_care->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$antenatal_care->type_id = $db->loadResult();
				$antenatal_care_Updated = $db->updateObject('#__content_types', $antenatal_care, 'type_id');
			}
			else
			{
				$antenatal_care_Inserted = $db->insertObject('#__content_types', $antenatal_care);
			}

			// Create the immunisation content type object.
			$immunisation = new stdClass();
			$immunisation->type_title = 'Ehealth_portal Immunisation';
			$immunisation->type_alias = 'com_ehealth_portal.immunisation';
			$immunisation->table = '{"special": {"dbtable": "#__ehealth_portal_immunisation","key": "id","type": "Immunisation","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$immunisation->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","referral":"referral","immunisation_up_to_date":"immunisation_up_to_date","reason":"reason","immunisation_vaccine_type":"immunisation_vaccine_type","administration_part":"administration_part"}}';
			$immunisation->router = 'Ehealth_portalHelperRoute::getImmunisationRoute';
			$immunisation->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/immunisation.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","referral","immunisation_vaccine_type","administration_part"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealth_portal_referral","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "immunisation_vaccine_type","targetTable": "#__ehealth_portal_immunisation_vaccine_type","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "administration_part","targetTable": "#__ehealth_portal_administration_part","targetColumn": "id","displayColumn": "name"}]}';

			// Check if immunisation type is already in content_type DB.
			$immunisation_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($immunisation->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$immunisation->type_id = $db->loadResult();
				$immunisation_Updated = $db->updateObject('#__content_types', $immunisation, 'type_id');
			}
			else
			{
				$immunisation_Inserted = $db->insertObject('#__content_types', $immunisation);
			}

			// Create the vmmc content type object.
			$vmmc = new stdClass();
			$vmmc->type_title = 'Ehealth_portal Vmmc';
			$vmmc->type_alias = 'com_ehealth_portal.vmmc';
			$vmmc->table = '{"special": {"dbtable": "#__ehealth_portal_vmmc","key": "id","type": "Vmmc","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$vmmc->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","are_you_circumcised":"are_you_circumcised","info_ben_vmcc":"info_ben_vmcc","interested_in_vmmc":"interested_in_vmmc","vmmc_gender":"vmmc_gender","reason":"reason","referral":"referral","partner_circumcised":"partner_circumcised"}}';
			$vmmc->router = 'Ehealth_portalHelperRoute::getVmmcRoute';
			$vmmc->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/vmmc.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealth_portal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Check if vmmc type is already in content_type DB.
			$vmmc_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($vmmc->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$vmmc->type_id = $db->loadResult();
				$vmmc_Updated = $db->updateObject('#__content_types', $vmmc, 'type_id');
			}
			else
			{
				$vmmc_Inserted = $db->insertObject('#__content_types', $vmmc);
			}

			// Create the prostate_and_testicular_cancer content type object.
			$prostate_and_testicular_cancer = new stdClass();
			$prostate_and_testicular_cancer->type_title = 'Ehealth_portal Prostate_and_testicular_cancer';
			$prostate_and_testicular_cancer->type_alias = 'com_ehealth_portal.prostate_and_testicular_cancer';
			$prostate_and_testicular_cancer->table = '{"special": {"dbtable": "#__ehealth_portal_prostate_and_testicular_cancer","key": "id","type": "Prostate_and_testicular_cancer","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$prostate_and_testicular_cancer->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","ptc_age":"ptc_age","ptc_fam_history":"ptc_fam_history","ptc_diet":"ptc_diet","ptc_phy_activity":"ptc_phy_activity","ptc_overweight":"ptc_overweight","ptc_urinate":"ptc_urinate","ptc_urine_freq":"ptc_urine_freq","referral":"referral","reason":"reason","txt_ptc_fam_history":"txt_ptc_fam_history","txt_ptc_urine_freq":"txt_ptc_urine_freq","txt_ptc_urinate":"txt_ptc_urinate","txt_ptc_overweight":"txt_ptc_overweight","txt_ptc_age":"txt_ptc_age","txt_ptc_phy_activity":"txt_ptc_phy_activity","txt_ptc_diet":"txt_ptc_diet"}}';
			$prostate_and_testicular_cancer->router = 'Ehealth_portalHelperRoute::getProstate_and_testicular_cancerRoute';
			$prostate_and_testicular_cancer->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/prostate_and_testicular_cancer.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealth_portal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Check if prostate_and_testicular_cancer type is already in content_type DB.
			$prostate_and_testicular_cancer_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($prostate_and_testicular_cancer->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$prostate_and_testicular_cancer->type_id = $db->loadResult();
				$prostate_and_testicular_cancer_Updated = $db->updateObject('#__content_types', $prostate_and_testicular_cancer, 'type_id');
			}
			else
			{
				$prostate_and_testicular_cancer_Inserted = $db->insertObject('#__content_types', $prostate_and_testicular_cancer);
			}

			// Create the tuberculosis content type object.
			$tuberculosis = new stdClass();
			$tuberculosis->type_title = 'Ehealth_portal Tuberculosis';
			$tuberculosis->type_alias = 'com_ehealth_portal.tuberculosis';
			$tuberculosis->table = '{"special": {"dbtable": "#__ehealth_portal_tuberculosis","key": "id","type": "Tuberculosis","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$tuberculosis->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","recurring_night_sweats":"recurring_night_sweats","tb_fever":"tb_fever","persistent_cough":"persistent_cough","blood_streaked_sputum":"blood_streaked_sputum","unusual_tiredness":"unusual_tiredness","pain_in_chest":"pain_in_chest","shortness_of_breath":"shortness_of_breath","diagnosed_with_disease":"diagnosed_with_disease","tb_exposed":"tb_exposed","tb_treatment":"tb_treatment","date_of_treatment":"date_of_treatment","treating_dhc":"treating_dhc","sputum_collection_one":"sputum_collection_one","tb_reason_one":"tb_reason_one","sputum_result_one":"sputum_result_one","referred_second_sputum":"referred_second_sputum","tb_reason_two":"tb_reason_two","sputum_result_two":"sputum_result_two","weight_loss_wdieting":"weight_loss_wdieting","referral":"referral","reason":"reason"}}';
			$tuberculosis->router = 'Ehealth_portalHelperRoute::getTuberculosisRoute';
			$tuberculosis->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/tuberculosis.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealth_portal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Check if tuberculosis type is already in content_type DB.
			$tuberculosis_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($tuberculosis->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$tuberculosis->type_id = $db->loadResult();
				$tuberculosis_Updated = $db->updateObject('#__content_types', $tuberculosis, 'type_id');
			}
			else
			{
				$tuberculosis_Inserted = $db->insertObject('#__content_types', $tuberculosis);
			}

			// Create the hiv_counseling_and_testing content type object.
			$hiv_counseling_and_testing = new stdClass();
			$hiv_counseling_and_testing->type_title = 'Ehealth_portal Hiv_counseling_and_testing';
			$hiv_counseling_and_testing->type_alias = 'com_ehealth_portal.hiv_counseling_and_testing';
			$hiv_counseling_and_testing->table = '{"special": {"dbtable": "#__ehealth_portal_hiv_counseling_and_testing","key": "id","type": "Hiv_counseling_and_testing","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$hiv_counseling_and_testing->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","counseling_type":"counseling_type","testing_reason":"testing_reason","last_test_date":"last_test_date","prev_test_result":"prev_test_result","test_result_one":"test_result_one","test_result_two":"test_result_two","final_test_result":"final_test_result","eqa":"eqa","referral":"referral","reason":"reason"}}';
			$hiv_counseling_and_testing->router = 'Ehealth_portalHelperRoute::getHiv_counseling_and_testingRoute';
			$hiv_counseling_and_testing->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/hiv_counseling_and_testing.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","testing_reason","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "testing_reason","targetTable": "#__ehealth_portal_testing_reason","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealth_portal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Check if hiv_counseling_and_testing type is already in content_type DB.
			$hiv_counseling_and_testing_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($hiv_counseling_and_testing->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$hiv_counseling_and_testing->type_id = $db->loadResult();
				$hiv_counseling_and_testing_Updated = $db->updateObject('#__content_types', $hiv_counseling_and_testing, 'type_id');
			}
			else
			{
				$hiv_counseling_and_testing_Inserted = $db->insertObject('#__content_types', $hiv_counseling_and_testing);
			}

			// Create the family_planning content type object.
			$family_planning = new stdClass();
			$family_planning->type_title = 'Ehealth_portal Family_planning';
			$family_planning->type_alias = 'com_ehealth_portal.family_planning';
			$family_planning->table = '{"special": {"dbtable": "#__ehealth_portal_family_planning","key": "id","type": "Family_planning","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$family_planning->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","diagnosis":"diagnosis"}}';
			$family_planning->router = 'Ehealth_portalHelperRoute::getFamily_planningRoute';
			$family_planning->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/family_planning.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","diagnosis"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "diagnosis","targetTable": "#__ehealth_portal_planning_type","targetColumn": "id","displayColumn": "name"}]}';

			// Check if family_planning type is already in content_type DB.
			$family_planning_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($family_planning->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$family_planning->type_id = $db->loadResult();
				$family_planning_Updated = $db->updateObject('#__content_types', $family_planning, 'type_id');
			}
			else
			{
				$family_planning_Inserted = $db->insertObject('#__content_types', $family_planning);
			}

			// Create the health_education content type object.
			$health_education = new stdClass();
			$health_education->type_title = 'Ehealth_portal Health_education';
			$health_education->type_alias = 'com_ehealth_portal.health_education';
			$health_education->table = '{"special": {"dbtable": "#__ehealth_portal_health_education","key": "id","type": "Health_education","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$health_education->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"education_type":"education_type","health_education_topic":"health_education_topic","patient":"patient"}}';
			$health_education->router = 'Ehealth_portalHelperRoute::getHealth_educationRoute';
			$health_education->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/health_education.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","health_education_topic","patient"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "health_education_topic","targetTable": "#__ehealth_portal_health_education_topic","targetColumn": "id","displayColumn": "name"}]}';

			// Check if health_education type is already in content_type DB.
			$health_education_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($health_education->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$health_education->type_id = $db->loadResult();
				$health_education_Updated = $db->updateObject('#__content_types', $health_education, 'type_id');
			}
			else
			{
				$health_education_Inserted = $db->insertObject('#__content_types', $health_education);
			}

			// Create the cervical_cancer content type object.
			$cervical_cancer = new stdClass();
			$cervical_cancer->type_title = 'Ehealth_portal Cervical_cancer';
			$cervical_cancer->type_alias = 'com_ehealth_portal.cervical_cancer';
			$cervical_cancer->table = '{"special": {"dbtable": "#__ehealth_portal_cervical_cancer","key": "id","type": "Cervical_cancer","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$cervical_cancer->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","cc_viginal_bleeding":"cc_viginal_bleeding","cc_v_discharge":"cc_v_discharge","cc_periods":"cc_periods","cc_smoking":"cc_smoking","cc_sex_actve":"cc_sex_actve","cc_sex_partner":"cc_sex_partner","pap_smear_collection":"pap_smear_collection","cc_result":"cc_result","referral":"referral","reason":"reason","txt_cc_v_discharge":"txt_cc_v_discharge","txt_cc_sex_actve":"txt_cc_sex_actve","txt_cc_periods":"txt_cc_periods","cc_reason":"cc_reason","txt_cc_smoking":"txt_cc_smoking","txt_cc_viginal_bleeding":"txt_cc_viginal_bleeding","txt_cc_sex_partner":"txt_cc_sex_partner"}}';
			$cervical_cancer->router = 'Ehealth_portalHelperRoute::getCervical_cancerRoute';
			$cervical_cancer->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/cervical_cancer.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealth_portal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Check if cervical_cancer type is already in content_type DB.
			$cervical_cancer_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($cervical_cancer->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$cervical_cancer->type_id = $db->loadResult();
				$cervical_cancer_Updated = $db->updateObject('#__content_types', $cervical_cancer, 'type_id');
			}
			else
			{
				$cervical_cancer_Inserted = $db->insertObject('#__content_types', $cervical_cancer);
			}

			// Create the breast_cancer content type object.
			$breast_cancer = new stdClass();
			$breast_cancer->type_title = 'Ehealth_portal Breast_cancer';
			$breast_cancer->type_alias = 'com_ehealth_portal.breast_cancer';
			$breast_cancer->table = '{"special": {"dbtable": "#__ehealth_portal_breast_cancer","key": "id","type": "Breast_cancer","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$breast_cancer->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","bc_age_range":"bc_age_range","bc_family_history":"bc_family_history","bc_race":"bc_race","bc_breastfeeding":"bc_breastfeeding","bc_preg_freq":"bc_preg_freq","bc_preg_age":"bc_preg_age","bc_history_hrt":"bc_history_hrt","bc_reg_exercise":"bc_reg_exercise","bc_overweight":"bc_overweight","bc_lump_near_breast":"bc_lump_near_breast","bc_dimpling":"bc_dimpling","bc_inward_nipple":"bc_inward_nipple","bc_nipple_discharge":"bc_nipple_discharge","bc_abnormal_skin":"bc_abnormal_skin","bc_breast_shape":"bc_breast_shape","referral":"referral","reason":"reason","txt_bc_breast_shape":"txt_bc_breast_shape","txt_bc_abnormal_skin":"txt_bc_abnormal_skin","txt_bc_nipple_discharge":"txt_bc_nipple_discharge","txt_bc_dimpling":"txt_bc_dimpling","txt_bc_lump_near_breast":"txt_bc_lump_near_breast","txt_bc_inward_nipple":"txt_bc_inward_nipple"}}';
			$breast_cancer->router = 'Ehealth_portalHelperRoute::getBreast_cancerRoute';
			$breast_cancer->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/breast_cancer.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","bc_preg_freq","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealth_portal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Check if breast_cancer type is already in content_type DB.
			$breast_cancer_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($breast_cancer->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$breast_cancer->type_id = $db->loadResult();
				$breast_cancer_Updated = $db->updateObject('#__content_types', $breast_cancer, 'type_id');
			}
			else
			{
				$breast_cancer_Inserted = $db->insertObject('#__content_types', $breast_cancer);
			}

			// Create the test content type object.
			$test = new stdClass();
			$test->type_title = 'Ehealth_portal Test';
			$test->type_alias = 'com_ehealth_portal.test';
			$test->table = '{"special": {"dbtable": "#__ehealth_portal_test","key": "id","type": "Test","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$test->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"patient":"patient","urine_test_result":"urine_test_result","glucose_first_reading":"glucose_first_reading","glucose_second_reading":"glucose_second_reading","haemoglobin_reading":"haemoglobin_reading","cholesterol_reading":"cholesterol_reading","syphilis_first_reading":"syphilis_first_reading","syphilis_second_reading":"syphilis_second_reading","hepatitis_first_reading":"hepatitis_first_reading","hepatitis_second_reading":"hepatitis_second_reading","malaria_first_reading":"malaria_first_reading","malaria_second_reading":"malaria_second_reading","pregnancy_first_reading":"pregnancy_first_reading","pregnancy_second_reading":"pregnancy_second_reading","referral":"referral","reason":"reason"}}';
			$test->router = 'Ehealth_portalHelperRoute::getTestRoute';
			$test->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/test.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","patient","glucose_first_reading","glucose_second_reading","haemoglobin_reading","cholesterol_reading","referral"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "referral","targetTable": "#__ehealth_portal_referral","targetColumn": "id","displayColumn": "name"}]}';

			// Check if test type is already in content_type DB.
			$test_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($test->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$test->type_id = $db->loadResult();
				$test_Updated = $db->updateObject('#__content_types', $test, 'type_id');
			}
			else
			{
				$test_Inserted = $db->insertObject('#__content_types', $test);
			}

			// Create the immunisation_vaccine_type content type object.
			$immunisation_vaccine_type = new stdClass();
			$immunisation_vaccine_type->type_title = 'Ehealth_portal Immunisation_vaccine_type';
			$immunisation_vaccine_type->type_alias = 'com_ehealth_portal.immunisation_vaccine_type';
			$immunisation_vaccine_type->table = '{"special": {"dbtable": "#__ehealth_portal_immunisation_vaccine_type","key": "id","type": "Immunisation_vaccine_type","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$immunisation_vaccine_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","administration_part":"administration_part","description":"description","alias":"alias"}}';
			$immunisation_vaccine_type->router = 'Ehealth_portalHelperRoute::getImmunisation_vaccine_typeRoute';
			$immunisation_vaccine_type->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/immunisation_vaccine_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","administration_part"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "administration_part","targetTable": "#__ehealth_portal_administration_part","targetColumn": "id","displayColumn": "name"}]}';

			// Check if immunisation_vaccine_type type is already in content_type DB.
			$immunisation_vaccine_type_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($immunisation_vaccine_type->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$immunisation_vaccine_type->type_id = $db->loadResult();
				$immunisation_vaccine_type_Updated = $db->updateObject('#__content_types', $immunisation_vaccine_type, 'type_id');
			}
			else
			{
				$immunisation_vaccine_type_Inserted = $db->insertObject('#__content_types', $immunisation_vaccine_type);
			}

			// Create the foetal_presentation content type object.
			$foetal_presentation = new stdClass();
			$foetal_presentation->type_title = 'Ehealth_portal Foetal_presentation';
			$foetal_presentation->type_alias = 'com_ehealth_portal.foetal_presentation';
			$foetal_presentation->table = '{"special": {"dbtable": "#__ehealth_portal_foetal_presentation","key": "id","type": "Foetal_presentation","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$foetal_presentation->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$foetal_presentation->router = 'Ehealth_portalHelperRoute::getFoetal_presentationRoute';
			$foetal_presentation->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/foetal_presentation.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if foetal_presentation type is already in content_type DB.
			$foetal_presentation_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($foetal_presentation->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$foetal_presentation->type_id = $db->loadResult();
				$foetal_presentation_Updated = $db->updateObject('#__content_types', $foetal_presentation, 'type_id');
			}
			else
			{
				$foetal_presentation_Inserted = $db->insertObject('#__content_types', $foetal_presentation);
			}

			// Create the foetal_lie content type object.
			$foetal_lie = new stdClass();
			$foetal_lie->type_title = 'Ehealth_portal Foetal_lie';
			$foetal_lie->type_alias = 'com_ehealth_portal.foetal_lie';
			$foetal_lie->table = '{"special": {"dbtable": "#__ehealth_portal_foetal_lie","key": "id","type": "Foetal_lie","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$foetal_lie->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$foetal_lie->router = 'Ehealth_portalHelperRoute::getFoetal_lieRoute';
			$foetal_lie->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/foetal_lie.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if foetal_lie type is already in content_type DB.
			$foetal_lie_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($foetal_lie->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$foetal_lie->type_id = $db->loadResult();
				$foetal_lie_Updated = $db->updateObject('#__content_types', $foetal_lie, 'type_id');
			}
			else
			{
				$foetal_lie_Inserted = $db->insertObject('#__content_types', $foetal_lie);
			}

			// Create the counseling_type content type object.
			$counseling_type = new stdClass();
			$counseling_type->type_title = 'Ehealth_portal Counseling_type';
			$counseling_type->type_alias = 'com_ehealth_portal.counseling_type';
			$counseling_type->table = '{"special": {"dbtable": "#__ehealth_portal_counseling_type","key": "id","type": "Counseling_type","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$counseling_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$counseling_type->router = 'Ehealth_portalHelperRoute::getCounseling_typeRoute';
			$counseling_type->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/counseling_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if counseling_type type is already in content_type DB.
			$counseling_type_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($counseling_type->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$counseling_type->type_id = $db->loadResult();
				$counseling_type_Updated = $db->updateObject('#__content_types', $counseling_type, 'type_id');
			}
			else
			{
				$counseling_type_Inserted = $db->insertObject('#__content_types', $counseling_type);
			}

			// Create the foetal_engagement content type object.
			$foetal_engagement = new stdClass();
			$foetal_engagement->type_title = 'Ehealth_portal Foetal_engagement';
			$foetal_engagement->type_alias = 'com_ehealth_portal.foetal_engagement';
			$foetal_engagement->table = '{"special": {"dbtable": "#__ehealth_portal_foetal_engagement","key": "id","type": "Foetal_engagement","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$foetal_engagement->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$foetal_engagement->router = 'Ehealth_portalHelperRoute::getFoetal_engagementRoute';
			$foetal_engagement->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/foetal_engagement.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if foetal_engagement type is already in content_type DB.
			$foetal_engagement_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($foetal_engagement->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$foetal_engagement->type_id = $db->loadResult();
				$foetal_engagement_Updated = $db->updateObject('#__content_types', $foetal_engagement, 'type_id');
			}
			else
			{
				$foetal_engagement_Inserted = $db->insertObject('#__content_types', $foetal_engagement);
			}

			// Create the health_education_topic content type object.
			$health_education_topic = new stdClass();
			$health_education_topic->type_title = 'Ehealth_portal Health_education_topic';
			$health_education_topic->type_alias = 'com_ehealth_portal.health_education_topic';
			$health_education_topic->table = '{"special": {"dbtable": "#__ehealth_portal_health_education_topic","key": "id","type": "Health_education_topic","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$health_education_topic->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$health_education_topic->router = 'Ehealth_portalHelperRoute::getHealth_education_topicRoute';
			$health_education_topic->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/health_education_topic.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if health_education_topic type is already in content_type DB.
			$health_education_topic_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($health_education_topic->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$health_education_topic->type_id = $db->loadResult();
				$health_education_topic_Updated = $db->updateObject('#__content_types', $health_education_topic, 'type_id');
			}
			else
			{
				$health_education_topic_Inserted = $db->insertObject('#__content_types', $health_education_topic);
			}

			// Create the testing_reason content type object.
			$testing_reason = new stdClass();
			$testing_reason->type_title = 'Ehealth_portal Testing_reason';
			$testing_reason->type_alias = 'com_ehealth_portal.testing_reason';
			$testing_reason->table = '{"special": {"dbtable": "#__ehealth_portal_testing_reason","key": "id","type": "Testing_reason","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$testing_reason->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$testing_reason->router = 'Ehealth_portalHelperRoute::getTesting_reasonRoute';
			$testing_reason->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/testing_reason.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if testing_reason type is already in content_type DB.
			$testing_reason_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($testing_reason->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$testing_reason->type_id = $db->loadResult();
				$testing_reason_Updated = $db->updateObject('#__content_types', $testing_reason, 'type_id');
			}
			else
			{
				$testing_reason_Inserted = $db->insertObject('#__content_types', $testing_reason);
			}

			// Create the clinic content type object.
			$clinic = new stdClass();
			$clinic->type_title = 'Ehealth_portal Clinic';
			$clinic->type_alias = 'com_ehealth_portal.clinic';
			$clinic->table = '{"special": {"dbtable": "#__ehealth_portal_clinic","key": "id","type": "Clinic","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$clinic->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "clinic_name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"clinic_name":"clinic_name","description":"description","clinic_type":"clinic_type","alias":"alias"}}';
			$clinic->router = 'Ehealth_portalHelperRoute::getClinicRoute';
			$clinic->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/clinic.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if clinic type is already in content_type DB.
			$clinic_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($clinic->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$clinic->type_id = $db->loadResult();
				$clinic_Updated = $db->updateObject('#__content_types', $clinic, 'type_id');
			}
			else
			{
				$clinic_Inserted = $db->insertObject('#__content_types', $clinic);
			}

			// Create the immunisation_type content type object.
			$immunisation_type = new stdClass();
			$immunisation_type->type_title = 'Ehealth_portal Immunisation_type';
			$immunisation_type->type_alias = 'com_ehealth_portal.immunisation_type';
			$immunisation_type->table = '{"special": {"dbtable": "#__ehealth_portal_immunisation_type","key": "id","type": "Immunisation_type","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$immunisation_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$immunisation_type->router = 'Ehealth_portalHelperRoute::getImmunisation_typeRoute';
			$immunisation_type->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/immunisation_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if immunisation_type type is already in content_type DB.
			$immunisation_type_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($immunisation_type->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$immunisation_type->type_id = $db->loadResult();
				$immunisation_type_Updated = $db->updateObject('#__content_types', $immunisation_type, 'type_id');
			}
			else
			{
				$immunisation_type_Inserted = $db->insertObject('#__content_types', $immunisation_type);
			}

			// Create the unit content type object.
			$unit = new stdClass();
			$unit->type_title = 'Ehealth_portal Unit';
			$unit->type_alias = 'com_ehealth_portal.unit';
			$unit->table = '{"special": {"dbtable": "#__ehealth_portal_unit","key": "id","type": "Unit","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$unit->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$unit->router = 'Ehealth_portalHelperRoute::getUnitRoute';
			$unit->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/unit.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if unit type is already in content_type DB.
			$unit_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($unit->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$unit->type_id = $db->loadResult();
				$unit_Updated = $db->updateObject('#__content_types', $unit, 'type_id');
			}
			else
			{
				$unit_Inserted = $db->insertObject('#__content_types', $unit);
			}

			// Create the referral content type object.
			$referral = new stdClass();
			$referral->type_title = 'Ehealth_portal Referral';
			$referral->type_alias = 'com_ehealth_portal.referral';
			$referral->table = '{"special": {"dbtable": "#__ehealth_portal_referral","key": "id","type": "Referral","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$referral->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$referral->router = 'Ehealth_portalHelperRoute::getReferralRoute';
			$referral->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/referral.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if referral type is already in content_type DB.
			$referral_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($referral->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$referral->type_id = $db->loadResult();
				$referral_Updated = $db->updateObject('#__content_types', $referral, 'type_id');
			}
			else
			{
				$referral_Inserted = $db->insertObject('#__content_types', $referral);
			}

			// Create the planning_type content type object.
			$planning_type = new stdClass();
			$planning_type->type_title = 'Ehealth_portal Planning_type';
			$planning_type->type_alias = 'com_ehealth_portal.planning_type';
			$planning_type->table = '{"special": {"dbtable": "#__ehealth_portal_planning_type","key": "id","type": "Planning_type","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$planning_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$planning_type->router = 'Ehealth_portalHelperRoute::getPlanning_typeRoute';
			$planning_type->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/planning_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if planning_type type is already in content_type DB.
			$planning_type_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($planning_type->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$planning_type->type_id = $db->loadResult();
				$planning_type_Updated = $db->updateObject('#__content_types', $planning_type, 'type_id');
			}
			else
			{
				$planning_type_Inserted = $db->insertObject('#__content_types', $planning_type);
			}

			// Create the diagnosis_type content type object.
			$diagnosis_type = new stdClass();
			$diagnosis_type->type_title = 'Ehealth_portal Diagnosis_type';
			$diagnosis_type->type_alias = 'com_ehealth_portal.diagnosis_type';
			$diagnosis_type->table = '{"special": {"dbtable": "#__ehealth_portal_diagnosis_type","key": "id","type": "Diagnosis_type","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$diagnosis_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$diagnosis_type->router = 'Ehealth_portalHelperRoute::getDiagnosis_typeRoute';
			$diagnosis_type->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/diagnosis_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if diagnosis_type type is already in content_type DB.
			$diagnosis_type_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($diagnosis_type->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$diagnosis_type->type_id = $db->loadResult();
				$diagnosis_type_Updated = $db->updateObject('#__content_types', $diagnosis_type, 'type_id');
			}
			else
			{
				$diagnosis_type_Inserted = $db->insertObject('#__content_types', $diagnosis_type);
			}

			// Create the nonpay_reason content type object.
			$nonpay_reason = new stdClass();
			$nonpay_reason->type_title = 'Ehealth_portal Nonpay_reason';
			$nonpay_reason->type_alias = 'com_ehealth_portal.nonpay_reason';
			$nonpay_reason->table = '{"special": {"dbtable": "#__ehealth_portal_nonpay_reason","key": "id","type": "Nonpay_reason","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$nonpay_reason->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$nonpay_reason->router = 'Ehealth_portalHelperRoute::getNonpay_reasonRoute';
			$nonpay_reason->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/nonpay_reason.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if nonpay_reason type is already in content_type DB.
			$nonpay_reason_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($nonpay_reason->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$nonpay_reason->type_id = $db->loadResult();
				$nonpay_reason_Updated = $db->updateObject('#__content_types', $nonpay_reason, 'type_id');
			}
			else
			{
				$nonpay_reason_Inserted = $db->insertObject('#__content_types', $nonpay_reason);
			}

			// Create the medication content type object.
			$medication = new stdClass();
			$medication->type_title = 'Ehealth_portal Medication';
			$medication->type_alias = 'com_ehealth_portal.medication';
			$medication->table = '{"special": {"dbtable": "#__ehealth_portal_medication","key": "id","type": "Medication","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$medication->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$medication->router = 'Ehealth_portalHelperRoute::getMedicationRoute';
			$medication->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/medication.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if medication type is already in content_type DB.
			$medication_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($medication->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$medication->type_id = $db->loadResult();
				$medication_Updated = $db->updateObject('#__content_types', $medication, 'type_id');
			}
			else
			{
				$medication_Inserted = $db->insertObject('#__content_types', $medication);
			}

			// Create the payment_amount content type object.
			$payment_amount = new stdClass();
			$payment_amount->type_title = 'Ehealth_portal Payment_amount';
			$payment_amount->type_alias = 'com_ehealth_portal.payment_amount';
			$payment_amount->table = '{"special": {"dbtable": "#__ehealth_portal_payment_amount","key": "id","type": "Payment_amount","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$payment_amount->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$payment_amount->router = 'Ehealth_portalHelperRoute::getPayment_amountRoute';
			$payment_amount->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/payment_amount.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if payment_amount type is already in content_type DB.
			$payment_amount_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($payment_amount->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$payment_amount->type_id = $db->loadResult();
				$payment_amount_Updated = $db->updateObject('#__content_types', $payment_amount, 'type_id');
			}
			else
			{
				$payment_amount_Inserted = $db->insertObject('#__content_types', $payment_amount);
			}

			// Create the administration_part content type object.
			$administration_part = new stdClass();
			$administration_part->type_title = 'Ehealth_portal Administration_part';
			$administration_part->type_alias = 'com_ehealth_portal.administration_part';
			$administration_part->table = '{"special": {"dbtable": "#__ehealth_portal_administration_part","key": "id","type": "Administration_part","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$administration_part->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$administration_part->router = 'Ehealth_portalHelperRoute::getAdministration_partRoute';
			$administration_part->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/administration_part.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if administration_part type is already in content_type DB.
			$administration_part_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($administration_part->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$administration_part->type_id = $db->loadResult();
				$administration_part_Updated = $db->updateObject('#__content_types', $administration_part, 'type_id');
			}
			else
			{
				$administration_part_Inserted = $db->insertObject('#__content_types', $administration_part);
			}

			// Create the payment_type content type object.
			$payment_type = new stdClass();
			$payment_type->type_title = 'Ehealth_portal Payment_type';
			$payment_type->type_alias = 'com_ehealth_portal.payment_type';
			$payment_type->table = '{"special": {"dbtable": "#__ehealth_portal_payment_type","key": "id","type": "Payment_type","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$payment_type->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$payment_type->router = 'Ehealth_portalHelperRoute::getPayment_typeRoute';
			$payment_type->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/payment_type.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if payment_type type is already in content_type DB.
			$payment_type_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($payment_type->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$payment_type->type_id = $db->loadResult();
				$payment_type_Updated = $db->updateObject('#__content_types', $payment_type, 'type_id');
			}
			else
			{
				$payment_type_Inserted = $db->insertObject('#__content_types', $payment_type);
			}

			// Create the strength content type object.
			$strength = new stdClass();
			$strength->type_title = 'Ehealth_portal Strength';
			$strength->type_alias = 'com_ehealth_portal.strength';
			$strength->table = '{"special": {"dbtable": "#__ehealth_portal_strength","key": "id","type": "Strength","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$strength->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","alias":"alias"}}';
			$strength->router = 'Ehealth_portalHelperRoute::getStrengthRoute';
			$strength->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/strength.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if strength type is already in content_type DB.
			$strength_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($strength->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$strength->type_id = $db->loadResult();
				$strength_Updated = $db->updateObject('#__content_types', $strength, 'type_id');
			}
			else
			{
				$strength_Inserted = $db->insertObject('#__content_types', $strength);
			}

			// Create the site content type object.
			$site = new stdClass();
			$site->type_title = 'Ehealth_portal Site';
			$site->type_alias = 'com_ehealth_portal.site';
			$site->table = '{"special": {"dbtable": "#__ehealth_portal_site","key": "id","type": "Site","prefix": "ehealth_portalTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$site->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "site_name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"site_name":"site_name","description":"description","site_region":"site_region","alias":"alias"}}';
			$site->router = 'Ehealth_portalHelperRoute::getSiteRoute';
			$site->content_history_options = '{"formFile": "administrator/components/com_ehealth_portal/models/forms/site.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if site type is already in content_type DB.
			$site_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($site->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$site->type_id = $db->loadResult();
				$site_Updated = $db->updateObject('#__content_types', $site, 'type_id');
			}
			else
			{
				$site_Inserted = $db->insertObject('#__content_types', $site);
			}


			echo '<a target="_blank" href="https://github.com/namibia/eHealth-Portal" title="eHealth Portal">
				<img src="components/com_ehealth_portal/assets/images/vdm-component.jpg"/>
				</a>
				<h3>Upgrade to Version 1.0.5 Was Successful! Let us know if anything is not working as expected.</h3>';
		}
		return true;
	}

	/**
	 * Remove folders with files
	 * 
	 * @param   string   $dir     The path to folder to remove
	 * @param   boolean  $ignore  The folders and files to ignore and not remove
	 *
	 * @return  boolean   True in all is removed
	 * 
	 */
	protected function removeFolder($dir, $ignore = false)
	{
		if (Folder::exists($dir))
		{
			$it = new RecursiveDirectoryIterator($dir);
			$it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
			// remove ending /
			$dir = rtrim($dir, '/');
			// now loop the files & folders
			foreach ($it as $file)
			{
				if ('.' === $file->getBasename() || '..' ===  $file->getBasename()) continue;
				// set file dir
				$file_dir = $file->getPathname();
				// check if this is a dir or a file
				if ($file->isDir())
				{
					$keeper = false;
					if ($this->checkArray($ignore))
					{
						foreach ($ignore as $keep)
						{
							if (strpos($file_dir, $dir.'/'.$keep) !== false)
							{
								$keeper = true;
							}
						}
					}
					if ($keeper)
					{
						continue;
					}
					Folder::delete($file_dir);
				}
				else
				{
					$keeper = false;
					if ($this->checkArray($ignore))
					{
						foreach ($ignore as $keep)
						{
							if (strpos($file_dir, $dir.'/'.$keep) !== false)
							{
								$keeper = true;
							}
						}
					}
					if ($keeper)
					{
						continue;
					}
					File::delete($file_dir);
				}
			}
			// delete the root folder if not ignore found
			if (!$this->checkArray($ignore))
			{
				return Folder::delete($dir);
			}
			return true;
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
	protected function checkArray($array, $removeEmptyString = false)
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
				return $this->checkArray($array, false);
			}
			return $nr;
		}
		return false;
	}

	/**
	 * Method to set/copy dynamic folders into place (use with caution)
	 *
	 * @return void
	 */
	protected function setDynamicF0ld3rs($app, $parent)
	{
		// get the instalation path
		$installer = $parent->getParent();
		$installPath = $installer->getPath('source');
		// get all the folders
		$folders = Folder::folders($installPath);
		// check if we have folders we may want to copy
		$doNotCopy = array('media','admin','site'); // Joomla already deals with these
		if (count((array) $folders) > 1)
		{
			foreach ($folders as $folder)
			{
				// Only copy if not a standard folders
				if (!in_array($folder, $doNotCopy))
				{
					// set the source path
					$src = $installPath.'/'.$folder;
					// set the destination path
					$dest = JPATH_ROOT.'/'.$folder;
					// now try to copy the folder
					if (!Folder::copy($src, $dest, '', true))
					{
						$app->enqueueMessage('Could not copy '.$folder.' folder into place, please make sure destination is writable!', 'error');
					}
				}
			}
		}
	}
}
