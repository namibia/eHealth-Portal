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
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use VDM\Joomla\Utilities\StringHelper;

/**
 * Ehealthportal Html View class for the Patient_queue
 */
class EhealthportalViewPatient_queue extends HtmlView
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		// get component params
		$this->params = ComponentHelper::getParams('com_ehealthportal');
		// get the application
		$this->app = Factory::getApplication();
		// get the user object
		$this->user = Factory::getUser();
		// get global action permissions
		$this->canDo = EhealthportalHelper::getActions('patient_queue');
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
			throw new \Exception(implode(PHP_EOL, $errors), 500);
		}

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function setDocument()
	{

		// Only load jQuery if needed. (default is true)
		if ($this->params->get('add_jquery_framework', 1) == 1)
		{
			Html::_('jquery.framework');
		}
		// Load the header checker class.
		require_once( JPATH_COMPONENT_ADMINISTRATOR.'/helpers/headercheck.php' );
		// Initialize the header checker.
		$HeaderCheck = new ehealthportalHeaderCheck();
		// add the document default css file
		Html::_('stylesheet', 'administrator/components/com_ehealthportal/assets/css/patient_queue.css', ['version' => 'auto']);
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
			$title = Text::_('COM_EHEALTHPORTAL_PATIENT_QUEUE');
		}
		// add title to the page
		ToolbarHelper::title($title,'joomla');
		// add cpanel button
		ToolbarHelper::custom('patient_queue.dashboard', 'grid-2', '', 'COM_EHEALTHPORTAL_DASH', false);

		// set help url for this view if found
		$this->help_url = EhealthportalHelper::getHelpUrl('patient_queue');
		if (StringHelper::check($this->help_url))
		{
			ToolbarHelper::help('COM_EHEALTHPORTAL_HELP_MANAGER', false, $this->help_url);
		}

		// add the options comp button
		if ($this->canDo->get('core.admin') || $this->canDo->get('core.options'))
		{
			ToolbarHelper::preferences('com_ehealthportal');
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
		return StringHelper::html($var, $this->_charset);
	}
}
?>
