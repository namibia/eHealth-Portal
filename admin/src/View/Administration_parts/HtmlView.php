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
namespace VDM\Component\Ehealthportal\Administrator\View\Administration_parts;

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
 * Ehealthportal Html View class for the Administration_parts
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * Administration_parts view display method
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
		$this->canDo = EhealthportalHelper::getActions('administration_part');
		$this->canEdit = $this->canDo->get('core.edit');
		$this->canState = $this->canDo->get('core.edit.state');
		$this->canCreate = $this->canDo->get('core.create');
		$this->canDelete = $this->canDo->get('core.delete');
		$this->canBatch = ($this->canDo->get('administration_part.batch') && $this->canDo->get('core.batch'));

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
		ToolbarHelper::title(Text::_('COM_EHEALTHPORTAL_ADMINISTRATION_PARTS'), 'joomla');

		if ($this->canCreate)
		{
			ToolbarHelper::addNew('administration_part.add');
		}

		// Only load if there are items
		if (ArrayHelper::check($this->items))
		{
			if ($this->canEdit)
			{
				ToolbarHelper::editList('administration_part.edit');
			}

			if ($this->canState)
			{
				ToolbarHelper::publishList('administration_parts.publish');
				ToolbarHelper::unpublishList('administration_parts.unpublish');
				ToolbarHelper::archiveList('administration_parts.archive');

				if ($this->canDo->get('core.admin'))
				{
					ToolbarHelper::checkin('administration_parts.checkin');
				}
			}

			if ($this->state->get('filter.published') == -2 && ($this->canState && $this->canDelete))
			{
				ToolbarHelper::deleteList('', 'administration_parts.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				ToolbarHelper::trash('administration_parts.trash');
			}

			if ($this->canDo->get('core.export') && $this->canDo->get('administration_part.export'))
			{
				ToolbarHelper::custom('administration_parts.exportData', 'download', '', 'COM_EHEALTHPORTAL_EXPORT_DATA', true);
			}
		}

		if ($this->canDo->get('core.import') && $this->canDo->get('administration_part.import'))
		{
			ToolbarHelper::custom('administration_parts.importData', 'upload', '', 'COM_EHEALTHPORTAL_IMPORT_DATA', false);
		}

		// set help url for this view if found
		$this->help_url = EhealthportalHelper::getHelpUrl('administration_parts');
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
		$this->getDocument()->setTitle(Text::_('COM_EHEALTHPORTAL_ADMINISTRATION_PARTS'));
		Html::_('stylesheet', "administrator/components/com_ehealthportal/assets/css/administration_parts.css", ['version' => 'auto']);
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
			'a.name' => Text::_('COM_EHEALTHPORTAL_ADMINISTRATION_PART_NAME_LABEL'),
			'a.description' => Text::_('COM_EHEALTHPORTAL_ADMINISTRATION_PART_DESCRIPTION_LABEL'),
			'a.id' => Text::_('JGRID_HEADING_ID')
		);
	}
}
