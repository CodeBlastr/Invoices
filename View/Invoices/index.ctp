<div class="invoices index">
	<div class="list-group">
		<?php foreach ($invoices as $invoice) : ?>
		<div class="list-group-item">
			<?php echo $this->Html->link($invoice['Invoice']['name'], array('action' => 'view', $invoice['Invoice']['id'])); ?>
			<span class="badge">Due <?php echo ZuhaInflector::datify($invoice['Invoice']['due_date']); ?></span>
			<span class="badge">Balance <?php echo ZuhaInflector::pricify($invoice['Invoice']['balance'], array('currency' => 'USD')); ?></span>
			<span class="badge">Total <?php echo ZuhaInflector::pricify($invoice['Invoice']['total'], array('currency' => 'USD')); ?></span>
			<span class="badge"><?php echo $invoice['Invoice']['status']; ?></span>
		</div>
		<?php endforeach; ?>
	</div>
</div>

<?php echo $this->element('paging'); ?>


<?php
// set the contextual breadcrumb items
$this->set('context_crumbs', array('crumbs' => array(
	$this->Html->link(__('Admin Dashboard'), '/admin'),
	'Invoices',
)));

// set the contextual menu items
$this->set('context_menu', array('menus' => array(
	array(
		'heading' => 'Invoices',
		'items' => array(
			$this->Html->link(__('List Paid'), array('controller' => 'invoices', 'action' => 'index', 'paid')),
			$this->Html->link(__('Add'), array('controller' => 'invoices', 'action' => 'add')),
			$this->Html->link(__('Generate'), array('controller' => 'invoices', 'action' => 'generate'))
			)
		)
	)));
