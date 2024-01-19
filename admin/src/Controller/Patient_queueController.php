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
namespace VDM\Component\Ehealthportal\Administrator\Controller;

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use VDM\Component\Ehealthportal\Administrator\Helper\EhealthportalHelper;

/**
 * Ehealthportal Patient_queue Base Controller
 */
class Patient_queueController extends BaseController
{
	public function __construct($config)
	{
		parent::__construct($config);
	}

	public function dashboard()
	{
		$this->setRedirect(Route::_('index.php?option=com_ehealthportal', false));
		return;
	}
}
