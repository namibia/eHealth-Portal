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

?>
<?php if(isset($this->icons['main']) && is_array($this->icons['main'])) :?>
	<?php foreach($this->icons['main'] as $icon): ?>
		<div class="dashboard-wraper">
			<div class="dashboard-content">
				<a class="icon" href="<?php echo $icon->url; ?>">
					<img alt="<?php echo $icon->alt; ?>" src="components/com_ehealthportal/assets/images/icons/<?php  echo $icon->image; ?>">
					<span class="dashboard-title"><?php echo Text::_($icon->name); ?></span>
				</a>
			 </div>
		</div>
	<?php endforeach; ?>
	<div class="clearfix"></div>
<?php else: ?>
	<div class="alert alert-error"><h4 class="alert-heading"><?php echo Text::_("Permission denied, or not correctly set"); ?></h4><div class="alert-message"><?php echo Text::_("Please notify your System Administrator if result is unexpected."); ?></div></div>
<?php endif; ?>