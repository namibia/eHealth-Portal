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

use Joomla\CMS\Language\Text;
use VDM\Joomla\Utilities\ArrayHelper;

?>
<img alt="<?php echo Text::_('COM_EHEALTHPORTAL'); ?>" src="components/com_ehealthportal/assets/images/vdm-component.jpg">
<ul class="list-striped">
	<li><b><?php echo Text::_('COM_EHEALTHPORTAL_VERSION'); ?>:</b> <?php echo $this->manifest->version; ?>&nbsp;&nbsp;<span class="update-notice" id="component-update-notice"></span></li>
	<li><b><?php echo Text::_('COM_EHEALTHPORTAL_DATE'); ?>:</b> <?php echo $this->manifest->creationDate; ?></li>
	<li><b><?php echo Text::_('COM_EHEALTHPORTAL_AUTHOR'); ?>:</b> <a href="mailto:<?php echo $this->manifest->authorEmail; ?>"><?php echo $this->manifest->author; ?></a></li>
	<li><b><?php echo Text::_('COM_EHEALTHPORTAL_WEBSITE'); ?>:</b> <a href="<?php echo $this->manifest->authorUrl; ?>" target="_blank"><?php echo $this->manifest->authorUrl; ?></a></li>
	<li><b><?php echo Text::_('COM_EHEALTHPORTAL_LICENSE'); ?>:</b> <?php echo $this->manifest->license; ?></li>
	<li><b><?php echo $this->manifest->copyright; ?></b></li>
</ul>
<div class="clearfix"></div>
<?php if(ArrayHelper::check($this->contributors)): ?>
	<?php if(count($this->contributors) > 1): ?>
		<h3><?php echo Text::_('COM_EHEALTHPORTAL_CONTRIBUTORS'); ?></h3>
	<?php else: ?>
		<h3><?php echo Text::_('COM_EHEALTHPORTAL_CONTRIBUTOR'); ?></h3>
	<?php endif; ?>
	<ul class="list-striped">
		<?php foreach($this->contributors as $contributor): ?>
		<li><b><?php echo $contributor['title']; ?>:</b> <?php echo $contributor['name']; ?></li>
		<?php endforeach; ?>
	</ul>
	<div class="clearfix"></div>
<?php endif; ?>