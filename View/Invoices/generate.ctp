<?php echo $this->Element($element); ?>

<?php
// set the contextual menu items
$this->set('context_menu', array('menus' => array(
	array(
		'heading' => 'Invoice',
		'items' => array(
			$this->Html->link(__('List', true), array('controller' => 'invoices', 'action' => 'index')),
			)
		),
	))); ?>