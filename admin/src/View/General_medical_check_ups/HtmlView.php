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

	@version		4.0.x
	@build			19th January, 2024
	@created		13th August, 2020
	@package		eHealth Portal
	@subpackage		HtmlView.php
	@author			Llewellyn van der Merwe <https://git.vdm.dev/joomla/eHealth-Portal>
	@copyright		Copyright (C) 2020 Vast Development Method. All rights reserved.
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Portal for mobile health clinics

/-----------------------------------------------------------------------------------------------------------------------------*/
namespace VDM\Component\Ehealthportal\Administrator\View\General_medical_check_ups;

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Document\Document;
use VDM\Component\Ehealthportal\Administrator\Helper\EhealthportalHelper;
use VDM\Joomla\Utilities\ArrayHelper;
use VDM\Joomla\Utilities\StringHelper;

/**
 * Ehealthportal Html View class for the General_medical_check_ups
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * General_medical_check_ups view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		// Assign data to the view
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->user = Factory::getApplication()->getIdentity();
		// Load the filter form from xml.
		$this->filterForm = $this->get('FilterForm');
		// Load the active filters.
		$this->activeFilters = $this->get('ActiveFilters');
		// Add the list ordering clause.
		$this->listOrder = $this->escape($this->state->get('list.ordering', 'a.id'));
		$this->listDirn = $this->escape($this->state->get('list.direction', 'DESC'));
		$this->saveOrder = $this->listOrder == 'a.ordering';
		// set the return here value
		$this->return_here = urlencode(base64_encode((string) Uri::getInstance()));
		// get global action permissions
		$this->canDo = EhealthportalHelper::getActions('general_medical_check_up');
		$this->canEdit = $this->canDo->get('core.edit');
		$this->canState = $this->canDo->get('core.edit.state');
		$this->canCreate = $this->canDo->get('core.create');
		$this->canDelete = $this->canDo->get('core.delete');
		$this->canBatch = ($this->canDo->get('general_medical_check_up.batch') && $this->canDo->get('core.batch'));

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode("\n", $errors), 500);
		}

		// Display the template
		parent::display($tpl);

		// Set the html view document stuff
		$this->setHtmlViewDoc();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		ToolbarHelper::title(Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UPS'), 'health');

		if ($this->canCreate)
		{
			ToolbarHelper::addNew('general_medical_check_up.add');
		}

		// Only load if there are items
		if (ArrayHelper::check($this->items))
		{
			if ($this->canEdit)
			{
				ToolbarHelper::editList('general_medical_check_up.edit');
			}

			if ($this->canState)
			{
				ToolbarHelper::publishList('general_medical_check_ups.publish');
				ToolbarHelper::unpublishList('general_medical_check_ups.unpublish');
				ToolbarHelper::archiveList('general_medical_check_ups.archive');

				if ($this->canDo->get('core.admin'))
				{
					ToolbarHelper::checkin('general_medical_check_ups.checkin');
				}
			}

			if ($this->state->get('filter.published') == -2 && ($this->canState && $this->canDelete))
			{
				ToolbarHelper::deleteList('', 'general_medical_check_ups.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				ToolbarHelper::trash('general_medical_check_ups.trash');
			}

			if ($this->canDo->get('core.export') && $this->canDo->get('general_medical_check_up.export'))
			{
				ToolbarHelper::custom('general_medical_check_ups.exportData', 'download', '', 'COM_EHEALTHPORTAL_EXPORT_DATA', true);
			}
		}

		if ($this->canDo->get('core.import') && $this->canDo->get('general_medical_check_up.import'))
		{
			ToolbarHelper::custom('general_medical_check_ups.importData', 'upload', '', 'COM_EHEALTHPORTAL_IMPORT_DATA', false);
		}

		// set help url for this view if found
		$this->help_url = EhealthportalHelper::getHelpUrl('general_medical_check_ups');
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
	 * Set this html view document related stuff.
	 *
	 * @return void
	 * @since   4.4.0
	 */
	protected function setHtmlViewDoc(): void
	{
		$this->getDocument()->setTitle(Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UPS'));
		Html::_('stylesheet', "administrator/components/com_ehealthportal/assets/css/general_medical_check_ups.css", ['version' => 'auto']);
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
		if (!is_string($var))
		{
				return $var;
		}
		elseif(strlen($var) > 50)
		{
			// use the helper htmlEscape method instead and shorten the string
			return StringHelper::html($var, $this->_charset, true);
		}
		// use the helper htmlEscape method instead.
		return StringHelper::html($var, $this->_charset);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array   Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering' => Text::_('JGRID_HEADING_ORDERING'),
			'a.published' => Text::_('JSTATUS'),
			'a.patient' => Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_PATIENT_LABEL'),
			'a.bp_diastolic_one' => Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_BP_DIASTOLIC_ONE_LABEL'),
			'a.bp_systolic_one' => Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_BP_SYSTOLIC_ONE_LABEL'),
			'a.temp_one' => Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_TEMP_ONE_LABEL'),
			'a.weight' => Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_WEIGHT_LABEL'),
			'a.pulse' => Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_PULSE_LABEL'),
			'a.bp_diastolic_two' => Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_BP_DIASTOLIC_TWO_LABEL'),
			'a.bp_systolic_two' => Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_BP_SYSTOLIC_TWO_LABEL'),
			'a.temp_two' => Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_TEMP_TWO_LABEL'),
			'a.height' => Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_HEIGHT_LABEL'),
			'h.name' => Text::_('COM_EHEALTHPORTAL_GENERAL_MEDICAL_CHECK_UP_REFERRAL_LABEL'),
			'a.id' => Text::_('JGRID_HEADING_ID')
		);
	}
}
