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
	@subpackage		default.php
	@author			Oh Martin <https://github.com/namibia/eHealth-Portal>
	@copyright		Copyright (C) 2020 Vast Development Method. All rights reserved.
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

	Portal for mobile health clinics

/-----------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
?>
<?php if ($this->canDo->get('patient_queue.access')): ?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task === 'patient_queue.back') {
			parent.history.back();
			return false;
		} else {
			var form = document.getElementById('adminForm');
			form.task.value = task;
			form.submit();
		}
	}
</script>
<?php $urlId = (isset($this->item->id)) ? '&id='. (int) $this->item->id : ''; ?>
<form action="<?php echo JRoute::_('index.php?option=com_ehealth_portal&view=patient_queue' . $urlId); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

<?php
$q = new \SplPriorityQueue();

$q->insert(1, 0);
$q->insert(2, 0);
$q->insert(3, 0);
$q->insert(4, 0);

while (!$q->isEmpty()) {
    echo $q->extract() . " ";
}
?>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Document</title>
<style>
   body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
   }
   .result {
      font-size: 18px;
      font-weight: 500;
      color: blueviolet;
   }
   button {
      padding: 6px;
      margin: 4px;
   }
</style>
</head>
<body>
<h1>Implementation of queue in JavaScript.</h1>
<div class="result"></div>
<br />
<input type="text" class="enqueueVal" /><button class="enqueueBtn">
Enqueue
</button>
<button class="dequeueBtn">Dequeue</button>
<button class="Btn">Display</button>
<h3>Click on the above buttons to perform queue operations</h3>
<script>
   let resEle = document.querySelector(".result");
   let BtnEle = document.querySelector(".Btn");
   let enqueueBtnEle = document.querySelector(".enqueueBtn");
   let dequeueBtnEle = document.querySelector(".dequeueBtn");
   class Queue {
      constructor() {
         this.items = [];
         this.length = 0;
      }
   }
   Queue.prototype.enqueue = function (ele) {
      this.items[this.length] = ele;
      this.length += 1;
   };
   Queue.prototype.dequeue = function () {
      debugger;
      if (this.length === 0) {
         return "Underflow: no more elements to remove";
      }
      tempNum = this.items[0];
      this.length -= 1;
      return tempNum;
   };
   Queue.prototype.display = function () {
      debugger;
      if (this.length == 0) {
         return "Stack is empty";
      }
      for (let i = 0; i < this.length; i++) {
         resEle.innerHTML += this.items[i] + " , ";
      }
   };
   let queue1 = new Queue();
   BtnEle.addEventListener("click", () => {
      resEle.innerHTML = "";
      queue1.display();
   });
   enqueueBtnEle.addEventListener("click", () => {
      let ele = document.querySelector(".enqueueVal").value;
      resEle.innerHTML = ele + " is added to the back of the queue";
      queue1.enqueue(ele);
   });
   dequeueBtnEle.addEventListener("click", () => {
      resEle.innerHTML =
      queue1.dequeue() + " is removed from the front of queue";
   });
</script>
</body>
</html>
<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>
<?php else: ?>
        <h1><?php echo JText::_('COM_EHEALTH_PORTAL_NO_ACCESS_GRANTED'); ?></h1>
<?php endif; ?>

