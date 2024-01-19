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
<!-- clear the batch values if cancel -->
<button class="btn" type="button" onclick="" data-dismiss="modal">
	<?php echo Text::_('JCANCEL'); ?>
</button>
<!-- post the batch values if process -->
<button class="btn btn-success" type="submit" onclick="Joomla.submitbutton('unit.batch');">
	<?php echo Text::_('JGLOBAL_BATCH_PROCESS'); ?>
</button>