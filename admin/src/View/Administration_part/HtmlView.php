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
namespace VDM\Component\Ehealthportal\Administrator\View\Administration_part;

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
use VDM\Joomla\Utilities\StringHelper;

/**
 * Administration_part Html View class
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * display method of View
	 * @return void
	 */
	public function display($tpl = null)
	{
		// set params
		$this->params = ComponentHelper::getParams('com_ehealthportal');
		// Assign the variables
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->script = $this->get('Script');
		$this->state = $this->get('State');
		// get action permissions
		$this->canDo = EhealthportalHelper::getActions('administration_part', $this->item);
		// get input
		$jinput = Factory::getApplication()->input;
		$this->ref = $jinput->get('ref', 0, 'word');
		$this->refid = $jinput->get('refid', 0, 'int');
		$return = $jinput->get('return', null, 'base64');
		// set the referral string
		$this->referral = '';
		if ($this->refid && $this->ref)
		{
			// return to the item that referred to this item
			$this->referral = '&ref=' . (string)$this->ref . '&refid=' . (int)$this->refid;
		}
		elseif($this->ref)
		{
			// return to the list view that referred to this item
			$this->referral = '&ref=' . (string)$this->ref;
		}
		// check return value
		if (!is_null($return))
		{
			// add the return value
			$this->referral .= '&return=' . (string)$return;
		}

		// Get Linked view data
		$this->vvyvaccines = $this->get('Vvyvaccines');

		// Set the toolbar
		$this->addToolBar();

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
		Factory::getApplication()->input->set('hidemainmenu', true);
		$user = Factory::getApplication()->getIdentity();
		$userId	= $user->id;
		$isNew = $this->item->id == 0;

		ToolbarHelper::title( Text::_($isNew ? 'COM_EHEALTHPORTAL_ADMINISTRATION_PART_NEW' : 'COM_EHEALTHPORTAL_ADMINISTRATION_PART_EDIT'), 'pencil-2 article-add');
		// Built the actions for new and existing records.
		if (StringHelper::check($this->referral))
		{
			if ($this->canDo->get('core.create') && $isNew)
			{
				// We can create the record.
				ToolbarHelper::save('administration_part.save', 'JTOOLBAR_SAVE');
			}
			elseif ($this->canDo->get('core.edit'))
			{
				// We can save the record.
				ToolbarHelper::save('administration_part.save', 'JTOOLBAR_SAVE');
			}
			if ($isNew)
			{
				// Do not creat but cancel.
				ToolbarHelper::cancel('administration_part.cancel', 'JTOOLBAR_CANCEL');
			}
			else
			{
				// We can close it.
				ToolbarHelper::cancel('administration_part.cancel', 'JTOOLBAR_CLOSE');
			}
		}
		else
		{
			if ($isNew)
			{
				// For new records, check the create permission.
				if ($this->canDo->get('core.create'))
				{
					ToolbarHelper::apply('administration_part.apply', 'JTOOLBAR_APPLY');
					ToolbarHelper::save('administration_part.save', 'JTOOLBAR_SAVE');
					ToolbarHelper::custom('administration_part.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				};
				ToolbarHelper::cancel('administration_part.cancel', 'JTOOLBAR_CANCEL');
			}
			else
			{
				if ($this->canDo->get('core.edit'))
				{
					// We can save the new record
					ToolbarHelper::apply('administration_part.apply', 'JTOOLBAR_APPLY');
					ToolbarHelper::save('administration_part.save', 'JTOOLBAR_SAVE');
					// We can save this record, but check the create permission to see
					// if we can return to make a new one.
					if ($this->canDo->get('core.create'))
					{
						ToolbarHelper::custom('administration_part.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
					}
				}
				$canVersion = ($this->canDo->get('core.version') && $this->canDo->get('administration_part.version'));
				if ($this->state->params->get('save_history', 1) && $this->canDo->get('core.edit') && $canVersion)
				{
					ToolbarHelper::versions('com_ehealthportal.administration_part', $this->item->id);
				}
				if ($this->canDo->get('core.create'))
				{
					ToolbarHelper::custom('administration_part.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
				}
				ToolbarHelper::cancel('administration_part.cancel', 'JTOOLBAR_CLOSE');
			}
		}
		ToolbarHelper::divider();
		// set help url for this view if found
		$this->help_url = EhealthportalHelper::getHelpUrl('administration_part');
		if (StringHelper::check($this->help_url))
		{
			ToolbarHelper::help('COM_EHEALTHPORTAL_HELP_MANAGER', false, $this->help_url);
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
		if (!is_string($var))
		{
				return $var;
		}
		elseif(strlen($var) > 30)
		{
			// use the helper htmlEscape method instead and shorten the string
			return StringHelper::html($var, $this->_charset, true, 30);
		}
		// use the helper htmlEscape method instead.
		return StringHelper::html($var, $this->_charset);
	}

	/**
	 * Set this html view document related stuff.
	 *
	 * @return void
	 * @since   4.4.0
	 */
	protected function setHtmlViewDoc(): void
	{
		$isNew = ($this->item->id < 1);
		$this->getDocument()->setTitle(Text::_($isNew ? 'COM_EHEALTHPORTAL_ADMINISTRATION_PART_NEW' : 'COM_EHEALTHPORTAL_ADMINISTRATION_PART_EDIT'));
		Html::_('stylesheet', "administrator/components/com_ehealthportal/assets/css/administration_part.css", ['version' => 'auto']);

		// Add the CSS for Footable.
		Html::_('stylesheet', 'media/com_ehealthportal/footable-v2/css/footable.core.min.css', ['version' => 'auto']);

		// Use the Metro Style
		if (!isset($this->fooTableStyle) || 0 == $this->fooTableStyle)
		{
			Html::_('stylesheet', 'media/com_ehealthportal/footable-v2/css/footable.metro.min.css', ['version' => 'auto']);
		}
		// Use the Legacy Style.
		elseif (isset($this->fooTableStyle) && 1 == $this->fooTableStyle)
		{
			Html::_('stylesheet', 'media/com_ehealthportal/footable-v2/css/footable.standalone.min.css', ['version' => 'auto']);
		}

		// Add the JavaScript for Footable
		Html::_('script', 'media/com_ehealthportal/footable-v2/js/footable.js', ['version' => 'auto']);
		Html::_('script', 'media/com_ehealthportal/footable-v2/js/footable.sort.js', ['version' => 'auto']);
		Html::_('script', 'media/com_ehealthportal/footable-v2/js/footable.filter.js', ['version' => 'auto']);
		Html::_('script', 'media/com_ehealthportal/footable-v2/js/footable.paginate.js', ['version' => 'auto']);

		$footable = "jQuery(document).ready(function() { jQuery(function () { jQuery('.footable').footable(); }); jQuery('.nav-tabs').on('click', 'li', function() { setTimeout(tableFix, 10); }); }); function tableFix() { jQuery('.footable').trigger('footable_resize'); }";
		$this->document->addScriptDeclaration($footable);

		Html::_('script', $this->script, ['version' => 'auto']);
		Html::_('script', "administrator/components/com_ehealthportal/views/administration_part/submitbutton.js", ['version' => 'auto']);
		Text::script('view not acceptable. Error');
	}
}
