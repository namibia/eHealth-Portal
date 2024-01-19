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

?>
<div id="j-main-container">
	<div class="form-horizontal" style="padding: 20px;">
		<div class="row-fluid">
			<div class="span9">
				<?php echo Html::_('bootstrap.startAccordion', 'dashboard_left', array('active' => 'main')); ?>
					<?php echo Html::_('bootstrap.addSlide', 'dashboard_left', 'cPanel', 'main'); ?>
						<?php echo $this->loadTemplate('main');?>
					<?php echo Html::_('bootstrap.endSlide'); ?>
				<?php echo Html::_('bootstrap.endAccordion'); ?>
			</div>
			<div class="span3">
				<?php echo Html::_('bootstrap.startAccordion', 'dashboard_right', array('active' => 'vdm')); ?>
					<?php echo Html::_('bootstrap.addSlide', 'dashboard_right', 'Vast Development Method', 'vdm'); ?>
						<?php echo $this->loadTemplate('vdm');?>
					<?php echo Html::_('bootstrap.endSlide'); ?>
				<?php echo Html::_('bootstrap.endAccordion'); ?>
			</div>
		</div>
	</div>
</div>