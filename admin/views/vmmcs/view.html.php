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
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use VDM\Joomla\Utilities\ArrayHelper;
use VDM\Joomla\Utilities\StringHelper;

/**
 * Ehealthportal Html View class for the Vmmcs
 */
class EhealthportalViewVmmcs extends HtmlView
{
	/**
	 * Vmmcs view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			EhealthportalHelper::addSubmenu('vmmcs');
		}

		// Assign data to the view
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->user = Factory::getUser();
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
		$this->canDo = EhealthportalHelper::getActions('vmmc');
		$this->canEdit = $this->canDo->get('core.edit');
		$this->canState = $this->canDo->get('core.edit.state');
		$this->canCreate = $this->canDo->get('core.create');
		$this->canDelete = $this->canDo->get('core.delete');
		$this->canBatch = ($this->canDo->get('vmmc.batch') && $this->canDo->get('core.batch'));

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
			// load the batch html
			if ($this->canCreate && $this->canEdit && $this->canState)
			{
				$this->batchDisplay = JHtmlBatch_::render();
			}
		}

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
		JHtmlSidebar::setAction('index.php?option=com_ehealthportal&view=vmmcs');
		ToolbarHelper::title(Text::_('COM_EHEALTHPORTAL_VMMCS'), 'wand');
		FormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

		if ($this->canCreate)
		{
			ToolbarHelper::addNew('vmmc.add');
		}

		// Only load if there are items
		if (ArrayHelper::check($this->items))
		{
			if ($this->canEdit)
			{
				ToolbarHelper::editList('vmmc.edit');
			}

			if ($this->canState)
			{
				ToolbarHelper::publishList('vmmcs.publish');
				ToolbarHelper::unpublishList('vmmcs.unpublish');
				ToolbarHelper::archiveList('vmmcs.archive');

				if ($this->canDo->get('core.admin'))
				{
					ToolbarHelper::checkin('vmmcs.checkin');
				}
			}

			// Add a batch button
			if ($this->canBatch && $this->canCreate && $this->canEdit && $this->canState)
			{
				// Get the toolbar object instance
				$bar = Toolbar::getInstance('toolbar');
				// set the batch button name
				$title = Text::_('JTOOLBAR_BATCH');
				// Instantiate a new JLayoutFile instance and render the batch button
				$layout = new FileLayout('joomla.toolbar.batch');
				// add the button to the page
				$dhtml = $layout->render(array('title' => $title));
				$bar->appendButton('Custom', $dhtml, 'batch');
			}

			if ($this->state->get('filter.published') == -2 && ($this->canState && $this->canDelete))
			{
				ToolbarHelper::deleteList('', 'vmmcs.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				ToolbarHelper::trash('vmmcs.trash');
			}

			if ($this->canDo->get('core.export') && $this->canDo->get('vmmc.export'))
			{
				ToolbarHelper::custom('vmmcs.exportData', 'download', '', 'COM_EHEALTHPORTAL_EXPORT_DATA', true);
			}
		}

		if ($this->canDo->get('core.import') && $this->canDo->get('vmmc.import'))
		{
			ToolbarHelper::custom('vmmcs.importData', 'upload', '', 'COM_EHEALTHPORTAL_IMPORT_DATA', false);
		}

		// set help url for this view if found
		$this->help_url = EhealthportalHelper::getHelpUrl('vmmcs');
		if (StringHelper::check($this->help_url))
		{
			ToolbarHelper::help('COM_EHEALTHPORTAL_HELP_MANAGER', false, $this->help_url);
		}

		// add the options comp button
		if ($this->canDo->get('core.admin') || $this->canDo->get('core.options'))
		{
			ToolbarHelper::preferences('com_ehealthportal');
		}

		// Only load published batch if state and batch is allowed
		if ($this->canState && $this->canBatch)
		{
			JHtmlBatch_::addListSelection(
				Text::_('COM_EHEALTHPORTAL_KEEP_ORIGINAL_STATE'),
				'batch[published]',
				Html::_('select.options', Html::_('jgrid.publishedOptions', array('all' => false)), 'value', 'text', '', true)
			);
		}

		// Only load access batch if create, edit and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			JHtmlBatch_::addListSelection(
				Text::_('COM_EHEALTHPORTAL_KEEP_ORIGINAL_ACCESS'),
				'batch[access]',
				Html::_('select.options', Html::_('access.assetgroups'), 'value', 'text')
			);
		}

		// Only load Patient batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Patient Selection
			$this->patientOptions = FormHelper::loadFieldType('vmmcsfilterpatient')->options;
			// We do some sanitation for Patient filter
			if (ArrayHelper::check($this->patientOptions) &&
				isset($this->patientOptions[0]->value) &&
				!StringHelper::check($this->patientOptions[0]->value))
			{
				unset($this->patientOptions[0]);
			}
			// Patient Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.Text::_('COM_EHEALTHPORTAL_VMMC_PATIENT_LABEL').' -',
				'batch[patient]',
				Html::_('select.options', $this->patientOptions, 'value', 'text')
			);
		}

		// Only load Referral Name batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Referral Name Selection
			$this->referralNameOptions = FormHelper::loadFieldType('Referrals')->options;
			// We do some sanitation for Referral Name filter
			if (ArrayHelper::check($this->referralNameOptions) &&
				isset($this->referralNameOptions[0]->value) &&
				!StringHelper::check($this->referralNameOptions[0]->value))
			{
				unset($this->referralNameOptions[0]);
			}
			// Referral Name Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.Text::_('COM_EHEALTHPORTAL_VMMC_REFERRAL_LABEL').' -',
				'batch[referral]',
				Html::_('select.options', $this->referralNameOptions, 'value', 'text')
			);
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
		$this->document->setTitle(Text::_('COM_EHEALTHPORTAL_VMMCS'));
		Html::_('stylesheet', "administrator/components/com_ehealthportal/assets/css/vmmcs.css", ['version' => 'auto']);
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
		if(strlen($var) > 50)
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
			'a.patient' => Text::_('COM_EHEALTHPORTAL_VMMC_PATIENT_LABEL'),
			'a.id' => Text::_('JGRID_HEADING_ID')
		);
	}
}
