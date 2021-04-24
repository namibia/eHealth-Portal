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
 * Ehealth_portal View class for the Vmmcs
 */
class Ehealth_portalViewVmmcs extends JViewLegacy
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
			Ehealth_portalHelper::addSubmenu('vmmcs');
		}

		// Assign data to the view
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->user = JFactory::getUser();
		// Load the filter form from xml.
		$this->filterForm = $this->get('FilterForm');
		// Load the active filters.
		$this->activeFilters = $this->get('ActiveFilters');
		// Add the list ordering clause.
		$this->listOrder = $this->escape($this->state->get('list.ordering', 'a.id'));
		$this->listDirn = $this->escape($this->state->get('list.direction', 'DESC'));
		$this->saveOrder = $this->listOrder == 'a.ordering';
		// set the return here value
		$this->return_here = urlencode(base64_encode((string) JUri::getInstance()));
		// get global action permissions
		$this->canDo = Ehealth_portalHelper::getActions('vmmc');
		$this->canEdit = $this->canDo->get('core.edit');
		$this->canState = $this->canDo->get('core.edit.state');
		$this->canCreate = $this->canDo->get('core.create');
		$this->canDelete = $this->canDo->get('core.delete');
		$this->canBatch = $this->canDo->get('core.batch');

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
		JToolBarHelper::title(JText::_('COM_EHEALTH_PORTAL_VMMCS'), 'wand');
		JHtmlSidebar::setAction('index.php?option=com_ehealth_portal&view=vmmcs');
		JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

		if ($this->canCreate)
		{
			JToolBarHelper::addNew('vmmc.add');
		}

		// Only load if there are items
		if (Ehealth_portalHelper::checkArray($this->items))
		{
			if ($this->canEdit)
			{
				JToolBarHelper::editList('vmmc.edit');
			}

			if ($this->canState)
			{
				JToolBarHelper::publishList('vmmcs.publish');
				JToolBarHelper::unpublishList('vmmcs.unpublish');
				JToolBarHelper::archiveList('vmmcs.archive');

				if ($this->canDo->get('core.admin'))
				{
					JToolBarHelper::checkin('vmmcs.checkin');
				}
			}

			// Add a batch button
			if ($this->canBatch && $this->canCreate && $this->canEdit && $this->canState)
			{
				// Get the toolbar object instance
				$bar = JToolBar::getInstance('toolbar');
				// set the batch button name
				$title = JText::_('JTOOLBAR_BATCH');
				// Instantiate a new JLayoutFile instance and render the batch button
				$layout = new JLayoutFile('joomla.toolbar.batch');
				// add the button to the page
				$dhtml = $layout->render(array('title' => $title));
				$bar->appendButton('Custom', $dhtml, 'batch');
			}

			if ($this->state->get('filter.published') == -2 && ($this->canState && $this->canDelete))
			{
				JToolbarHelper::deleteList('', 'vmmcs.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				JToolbarHelper::trash('vmmcs.trash');
			}

			if ($this->canDo->get('core.export') && $this->canDo->get('vmmc.export'))
			{
				JToolBarHelper::custom('vmmcs.exportData', 'download', '', 'COM_EHEALTH_PORTAL_EXPORT_DATA', true);
			}
		}

		if ($this->canDo->get('core.import') && $this->canDo->get('vmmc.import'))
		{
			JToolBarHelper::custom('vmmcs.importData', 'upload', '', 'COM_EHEALTH_PORTAL_IMPORT_DATA', false);
		}

		// set help url for this view if found
		$help_url = Ehealth_portalHelper::getHelpUrl('vmmcs');
		if (Ehealth_portalHelper::checkString($help_url))
		{
				JToolbarHelper::help('COM_EHEALTH_PORTAL_HELP_MANAGER', false, $help_url);
		}

		// add the options comp button
		if ($this->canDo->get('core.admin') || $this->canDo->get('core.options'))
		{
			JToolBarHelper::preferences('com_ehealth_portal');
		}

		// Only load published batch if state and batch is allowed
		if ($this->canState && $this->canBatch)
		{
			JHtmlBatch_::addListSelection(
				JText::_('COM_EHEALTH_PORTAL_KEEP_ORIGINAL_STATE'),
				'batch[published]',
				JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('all' => false)), 'value', 'text', '', true)
			);
		}

		// Only load access batch if create, edit and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			JHtmlBatch_::addListSelection(
				JText::_('COM_EHEALTH_PORTAL_KEEP_ORIGINAL_ACCESS'),
				'batch[access]',
				JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text')
			);
		}

		// Only load Patient batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Patient Selection
			$this->patientOptions = JFormHelper::loadFieldType('vmmcsfilterpatient')->options;
			// We do some sanitation for Patient filter
			if (Ehealth_portalHelper::checkArray($this->patientOptions) &&
				isset($this->patientOptions[0]->value) &&
				!Ehealth_portalHelper::checkString($this->patientOptions[0]->value))
			{
				unset($this->patientOptions[0]);
			}
			// Patient Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_EHEALTH_PORTAL_VMMC_PATIENT_LABEL').' -',
				'batch[patient]',
				JHtml::_('select.options', $this->patientOptions, 'value', 'text')
			);
		}

		// Only load Referral Name batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Referral Name Selection
			$this->referralNameOptions = JFormHelper::loadFieldType('Referrals')->options;
			// We do some sanitation for Referral Name filter
			if (Ehealth_portalHelper::checkArray($this->referralNameOptions) &&
				isset($this->referralNameOptions[0]->value) &&
				!Ehealth_portalHelper::checkString($this->referralNameOptions[0]->value))
			{
				unset($this->referralNameOptions[0]);
			}
			// Referral Name Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_EHEALTH_PORTAL_VMMC_REFERRAL_LABEL').' -',
				'batch[referral]',
				JHtml::_('select.options', $this->referralNameOptions, 'value', 'text')
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
			$this->document = JFactory::getDocument();
		}
		$this->document->setTitle(JText::_('COM_EHEALTH_PORTAL_VMMCS'));
		$this->document->addStyleSheet(JURI::root() . "administrator/components/com_ehealth_portal/assets/css/vmmcs.css", (Ehealth_portalHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
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
			return Ehealth_portalHelper::htmlEscape($var, $this->_charset, true);
		}
		// use the helper htmlEscape method instead.
		return Ehealth_portalHelper::htmlEscape($var, $this->_charset);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'a.patient' => JText::_('COM_EHEALTH_PORTAL_VMMC_PATIENT_LABEL'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
