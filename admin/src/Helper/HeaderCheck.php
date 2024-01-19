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
	@subpackage		HeaderCheck.php
	@author			Llewellyn van der Merwe <https://git.vdm.dev/joomla/eHealth-Portal>
	@copyright		Copyright (C) 2020 Vast Development Method. All rights reserved.
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Portal for mobile health clinics

/-----------------------------------------------------------------------------------------------------------------------------*/
namespace VDM\Component\Ehealthportal\Administrator\Helper;

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Application\CMSApplication;

/**
 * Helper class for checking loaded scripts and styles in the document header.
 *
 * @since   3.2.0
 */
class HeaderCheck
{
	/**
	 * @var CMSApplication Application object
	 *
	 * @since   3.2.0
	 */
	protected CMSApplication $app;

	/**
	 * @var Document object
	 *
	 * @since   3.2.0
	 */
	protected Document $document;

	/**
	 * Construct the app and document
	 *
	 * @since   3.2.0
	 */
	public function __construct()
	{
		// Initializes the application object.
		$this->app = Factory::getApplication();

		// Initializes the document object.
		$this->document = $this->app->getDocument();
	}

	/**
	 * Check if a JavaScript file is loaded in the document head.
	 *
	 * @param string $scriptName Name of the script to check.
	 *
	 * @return bool True if the script is loaded, false otherwise.
	 * @since   3.2.0
	 */
	public function js_loaded(string $scriptName): bool
	{
		return $this->isLoaded($scriptName, 'scripts');
	}

	/**
	 * Check if a CSS file is loaded in the document head.
	 *
	 * @param string $scriptName Name of the stylesheet to check.
	 *
	 * @return bool True if the stylesheet is loaded, false otherwise.
	 * @since   3.2.0
	 */
	public function css_loaded(string $scriptName): bool
	{
		return $this->isLoaded($scriptName, 'styleSheets');
	}

	/**
	 * Abstract method to check if a given script or stylesheet is loaded.
	 *
	 * @param string $scriptName Name of the script or stylesheet.
	 * @param string $type Type of asset to check ('scripts' or 'styleSheets').
	 *
	 * @return bool True if the asset is loaded, false otherwise.
	 * @since   3.2.0
	 */
	private function isLoaded(string $scriptName, string $type): bool
	{
		// UIkit specific check
		if ($this->isUIkit($scriptName))
		{
			return true;
		}

		$head_data = $this->document->getHeadData();
		foreach (array_keys($head_data[$type]) as $script)
		{
			if (stristr($script, $scriptName))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Check for UIkit framework specific conditions.
	 *
	 * @param string $scriptName Name of the script or stylesheet.
	 *
	 * @return bool True if UIkit specific conditions are met, false otherwise.
	 * @since   3.2.0
	 */
	private function isUIkit(string $scriptName): bool
	{
		if (strpos($scriptName, 'uikit') !== false)
		{
			$get_template_name = $this->app->getTemplate('template')->template;
			if (strpos($get_template_name, 'yoo') !== false)
			{
				return true;
			}
		}
		return false;
	}
}
