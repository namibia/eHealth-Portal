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
	@subpackage		patient_queue.php
	@author			Oh Martin <https://github.com/namibia/eHealth-Portal>
	@copyright		Copyright (C) 2020 Vast Development Method. All rights reserved.
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Portal for mobile health clinics

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;

/**
 * Ehealth_portal Patient_queue Model
 */
class Ehealth_portalModelPatient_queue extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @var        string
	 */
	protected $_context = 'com_ehealth_portal.patient_queue';

        /**
	 * Model user data.
	 *
	 * @var        strings
	 */
	protected $user;
	protected $userId;
	protected $guest;
	protected $groups;
	protected $levels;
	protected $app;
	protected $input;
	protected $uikitComp;

	/**
	 * @var object item
	 */
	protected $item;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 *
	 * @return void
	 */
	protected function populateState()
	{
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
		// Get the item main id
		$id = $this->input->getInt('id', null);
		$this->setState('patient_queue.id', $id);

		// Load the parameters.
		parent::populateState();
	}

	/**
	 * Method to get article data.
	 *
	 * @param   integer  $pk  The id of the article.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$this->user	= JFactory::getUser();
		// check if this user has permission to access item
		if (!$this->user->authorise('patient_queue.access', 'com_ehealth_portal'))
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('Not authorised!'), 'error');
			// redirect away if not a correct to cPanel/default view
			$app->redirect('index.php?option=com_ehealth_portal');
			return false;
		}
		$this->userId = $this->user->get('id');
		$this->guest = $this->user->get('guest');
		$this->groups = $this->user->get('groups');
		$this->authorisedGroups = $this->user->getAuthorisedGroups();
		$this->levels = $this->user->getAuthorisedViewLevels();
		$this->initSet = true;

		$pk = (!empty($pk)) ? $pk : (int) $this->getState('patient_queue.id');
		
		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				// Get a db connection.
				$db = JFactory::getDbo();

				// Create a new query object.
				$query = $db->getQuery(true);

				// Get from #__users as a
				$query->select($db->quoteName(
			array('a.id','a.name','a.username','a.email','a.password','a.block','a.sendEmail','a.registerDate','a.lastvisitDate','a.activation','a.params','a.lastResetTime','a.resetCount','a.otpKey','a.otep','a.requireReset'),
			array('id','name','username','email','password','block','sendEmail','registerDate','lastvisitDate','activation','params','lastResetTime','resetCount','otpKey','otep','requireReset')));
				$query->from($db->quoteName('#__users', 'a'));

				// Reset the query using our newly populated query object.
				$db->setQuery($query);
				// Load the results as a stdClass object.
				$data = $db->loadObject();

				if (empty($data))
				{
					$app = JFactory::getApplication();
					// If no data is found redirect to default page and show warning.
					$app->enqueueMessage(JText::_('COM_EHEALTH_PORTAL_NOT_FOUND_OR_ACCESS_DENIED'), 'warning');
					$app->redirect('index.php?option=com_ehealth_portal');
					return false;
				}

				// set data object to item.
				$this->_item[$pk] = $data;
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404)
				{
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseWarning(404, $e->getMessage());
				}
				else
				{
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}
}
