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
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use VDM\Joomla\Utilities\StringHelper;

/**
 * Ehealthportal View class
 */
class EhealthportalViewEhealthportal extends HtmlView
{
	/**
	 * View display method
	 * @return void
	 */
	function display($tpl = null)
	{
		// Assign data to the view
		$this->icons          = $this->get('Icons');
		$this->contributors   = EhealthportalHelper::getContributors();

		// get the manifest details of the component
		$this->manifest = EhealthportalHelper::manifest();

		// Set the toolbar
		$this->addToolBar();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		$canDo = EhealthportalHelper::getActions('ehealthportal');
		ToolbarHelper::title(Text::_('COM_EHEALTHPORTAL_DASHBOARD'), 'grid-2');

		// set help url for this view if found
		$this->help_url = EhealthportalHelper::getHelpUrl('ehealthportal');
		if (StringHelper::check($this->help_url))
		{
			ToolbarHelper::help('COM_EHEALTHPORTAL_HELP_MANAGER', false, $this->help_url);
		}

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			ToolbarHelper::preferences('com_ehealthportal');
		}
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		if (!isset($this->document))
		{
			$this->document = Factory::getDocument();
		}
		// set page title
		$this->document->setTitle(Text::_('COM_EHEALTHPORTAL_DASHBOARD'));

		// add manifest to page JavaScript
		$this->document->addScriptDeclaration("var manifest = jQuery.parseJSON('" . json_encode($this->manifest) . "');", "text/javascript");

		// add dashboard style sheets
		Html::_('stylesheet', "administrator/components/com_ehealthportal/assets/css/dashboard.css", ['version' => 'auto']);
	}
}
