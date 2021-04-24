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
	@subpackage		view.html.php
	@author			Oh Martin <https://github.com/namibia/eHealth-Portal>
	@copyright		Copyright (C) 2020 Vast Development Method. All rights reserved.
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Portal for mobile health clinics

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access'); 

/**
 * Ehealth_portal View class for the Patient_queue
 */
class Ehealth_portalViewPatient_queue extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		// get component params
		$this->params = JComponentHelper::getParams('com_ehealth_portal');
		// get the application
		$this->app = JFactory::getApplication();
		// get the user object
		$this->user = JFactory::getUser();
		// get global action permissions
		$this->canDo = Ehealth_portalHelper::getActions('patient_queue');
		// Initialise variables.
		$this->item = $this->get('Item');

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			// add the tool bar
			$this->addToolBar();
		}

		// set the document
		$this->setDocument();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode(PHP_EOL, $errors), 500);
		}

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function setDocument()
	{

		// always make sure jquery is loaded.
		JHtml::_('jquery.framework');
		// Load the header checker class.
		require_once( JPATH_COMPONENT_ADMINISTRATOR.'/helpers/headercheck.php' );
		// Initialize the header checker.
		$HeaderCheck = new ehealth_portalHeaderCheck;
		// add the document default css file
		$this->document->addStyleSheet(JURI::root(true) .'/administrator/components/com_ehealth_portal/assets/css/patient_queue.css', (Ehealth_portalHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		// hide the main menu
		$this->app->input->set('hidemainmenu', true);
		// set the title
		if (isset($this->item->name) && $this->item->name)
		{
			$title = $this->item->name;
		}
		// Check for empty title and add view name if param is set
		if (empty($title))
		{
			$title = JText::_('COM_EHEALTH_PORTAL_PATIENT_QUEUE');
		}
		// add title to the page
		JToolbarHelper::title($title,'joomla');
		// add cpanel button
		JToolBarHelper::custom('patient_queue.dashboard', 'grid-2', '', 'COM_EHEALTH_PORTAL_DASH', false);

		// set help url for this view if found
		$help_url = Ehealth_portalHelper::getHelpUrl('patient_queue');
		if (Ehealth_portalHelper::checkString($help_url))
		{
			JToolbarHelper::help('COM_EHEALTH_PORTAL_HELP_MANAGER', false, $help_url);
		}

		// add the options comp button
		if ($this->canDo->get('core.admin') || $this->canDo->get('core.options'))
		{
			JToolBarHelper::preferences('com_ehealth_portal');
		}
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 */
	public function escape($var)
	{
		// use the helper htmlEscape method instead.
		return Ehealth_portalHelper::htmlEscape($var, $this->_charset);
	}
}
?>
