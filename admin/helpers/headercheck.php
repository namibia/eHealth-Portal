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

class ehealthportalHeaderCheck
{
	protected $document = null;
	protected $app = null;

	function js_loaded($script_name)
	{
		// UIkit check point
		if (strpos($script_name,'uikit') !== false)
		{
			if (!$this->app)
			{
				$this->app = Factory::getApplication();
			}

			$getTemplateName = $this->app->getTemplate('template')->template;
			if (strpos($getTemplateName,'yoo') !== false)
			{
				return true;
			}
		}

		if (!$this->document)
		{
			$this->document = Factory::getDocument();
		}

		$head_data = $this->document->getHeadData();
		foreach (array_keys($head_data['scripts']) as $script)
		{
			if (stristr($script, $script_name))
			{
				return true;
			}
		}

		return false;
	}

	function css_loaded($script_name)
	{
		// UIkit check point
		if (strpos($script_name,'uikit') !== false)
		{
			if (!$this->app)
			{
				$this->app = Factory::getApplication();
			}

			$getTemplateName = $this->app->getTemplate('template')->template;
			if (strpos($getTemplateName,'yoo') !== false)
			{
				return true;
			}
		}

		if (!$this->document)
		{
			$this->document = Factory::getDocument();
		}

		$head_data = $this->document->getHeadData();
		foreach (array_keys($head_data['styleSheets']) as $script)
		{
			if (stristr($script, $script_name))
			{
				return true;
			}
		}

		return false;
	}
}
