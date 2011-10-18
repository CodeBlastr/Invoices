<div class="invoice form"> <?php echo $this->Form->create('Invoice');?>
  <fieldset>
    <legend>
    <p>New Invoice</p>
    </legend>
    <?php
		echo $this->Form->hidden('Invoice.id');
		echo $this->Form->hidden('Invoice.status');
		echo $this->Form->hidden('Invoice.project_id');
		if (!empty($this->request->params['named']['contact_id'])) {
			echo $this->Form->input('Invoice.contact_id', array('type' => 'hidden', 'value' => $this->request->params['named']['contact_id']));
		} else {
			echo $this->Form->input('Invoice.contact_id', array('empty' => '-- Optional --', 'label' => 'Which client? <small>(or '.$this->Html->link('create a new company', array('plugin' => 'contacts', 'controller' => 'contacts', 'controller' => 'contacts', 'action' => 'add', 'company')).')</small>', 'after' => '<br /><br /><br />'.$this->Form->checkbox('Invoice.contact_all_access', array('checked' => 'checked')).' Give everyone at this company access to this invoice?'));
		}
		echo $this->Form->input('Invoice.number', array('label' => 'Invoice Number'));
		#echo $this->Form->input('Invoice.po_number', array('label' => 'PO Number'));
		echo $this->Form->input('Invoice.due_date');
	?>
  </fieldset>
  <table class="invoiceTimeLines">
    <tr>
      <th>&nbsp;</th>
      <th>Task</th>
      <th>Time Entry Notes</th>
      <th>Rate</th>
      <th>Hours</th>
      <th>Line Total</th>
    </tr>
    <?php $i=0; if(!empty($this->request->data['InvoiceTime'][0])) : ?>
    <tbody class="invoiceTimes">
      <?php foreach($this->request->data['InvoiceTime'] as $invoiceTime): ?>
      <tr id="line<?php echo $i; ?>Time" class="invoiceTimeLine timeRow">
	    <?php echo $this->Form->hidden('InvoiceTime.'.$i.'.id'); ?>
	    <?php echo $this->Form->hidden('InvoiceTime.'.$i.'.project_id'); ?>
	    <?php echo $this->Form->hidden('InvoiceTime.'.$i.'.task_id'); ?>
	    <?php echo $this->Form->hidden('InvoiceTime.'.$i.'.time_id'); ?>
	    <?php echo $this->Form->hidden('InvoiceTime.'.$i.'.creator_id'); ?>
	    <?php echo $this->Form->hidden('InvoiceTime.'.$i.'.created'); ?>
      	<td><a class="newTimelink" href="#">New</a>, <a href="#" id="line<?php echo $i; ?>Time" class="deleteTimeLink">Delete</a></td>
        <td><?php echo $this->Form->input('InvoiceTime.'.$i.'.name', array('label' => false)); ?></td>
        <td ><?php echo $this->Form->input('InvoiceTime.'.$i.'.notes', array('type' => 'text', 'label' => false)); ?></td>
        <td><?php echo $this->Form->input('InvoiceTime.'.$i.'.rate', array('label' => false)); ?></td>
        <td><?php echo $this->Form->input('InvoiceTime.'.$i.'.hours', array('label' => false)); ?></td>
        <td id="lineTime<?php echo $i; ?>Total" class="lineTotal"> 0.00 </td>
        <td></td>
      </tr>
      <?php $i++; endforeach; ?>
    </tbody>
    <?php endif; ?>
    <tbody class="invoiceTimes">
      <tr id="line<?php echo $i; ?>Time" class="invoiceTimeLine timeRow">
      	<td><a class="newTimelink" href="#">New</a>, <a href="#" id="line<?php echo $i; ?>Time" class="deleteTimeLink">Delete</a></td>
        <td><?php echo $this->Form->input('InvoiceTime.'.$i.'.name', array('label' => false)); ?></td>
        <td ><?php echo $this->Form->input('InvoiceTime.'.$i.'.notes', array('type' => 'text', 'label' => false)); ?></td>
        <td><?php echo $this->Form->input('InvoiceTime.'.$i.'.rate', array('label' => false)); ?></td>
        <td><?php echo $this->Form->input('InvoiceTime.'.$i.'.hours', array('label' => false)); ?></td>
        <td id="lineTime<?php echo $i; ?>Total" class="lineTotal"> 0.00 </td>
        <td></td>
      </tr>
    </tbody>
  </table>
  <table class="invoiceItemLines">
    <tr>
      <th>&nbsp;</th>
      <th>Item</th>
      <th>Description</th>
      <th>Unit Cost</th>
      <th>Qty</th>
      <th>Line Total</th>
    </tr>
    <?php $i=0; if(!empty($this->request->data['InvoiceItem'][0])) : ?>
    <tbody class="invoiceItems">
      <?php foreach($this->request->data['InvoiceItem'] as $invoiceTime): ?>
      <tr id="line<?php echo $i; ?>Item" class="invoiceItemLine itemRow">
	    <?php echo $this->Form->hidden('InvoiceItem.'.$i.'.id'); ?>
	    <?php echo $this->Form->hidden('InvoiceItem.'.$i.'.catalog_item_id'); ?>
	    <?php echo $this->Form->hidden('InvoiceItem.'.$i.'.creator_id'); ?>
	    <?php echo $this->Form->hidden('InvoiceItem.'.$i.'.created'); ?>
      	<td><a class="newItemlink" href="#">New</a>, <a href="#" id="line<?php echo $i; ?>Item" class="deleteItemLink">Delete</a></td>
        <td><?php echo $this->Form->input('InvoiceItem.'.$i.'.name', array('label' => false)); ?></td>
        <td><?php echo $this->Form->input('InvoiceItem.'.$i.'.description', array('type' => 'text', 'label' => false)); ?></td>
        <td><?php echo $this->Form->input('InvoiceItem.'.$i.'.unit_cost', array('label' => false)); ?></td>
        <td><?php echo $this->Form->input('InvoiceItem.'.$i.'.quantity', array('label' => false)); ?></td>
        <td id="lineItem<?php echo $i; ?>Total" class="lineTotal"> 0.00 </td>
      </tr>
      <?php $i = $i + 1; endforeach; ?>
    </tbody>
    <?php endif;  ?>
    <tbody class="invoiceItems">
      <tr id="line<?php echo $i; ?>Item" class="invoiceItemLine itemRow">
      	<td><a class="newItemlink" href="#">New</a>, <a href="#" id="line<?php echo $i; ?>Item" class="deleteItemLink">Delete</a></td>
        <td><?php echo $this->Form->input('InvoiceItem.'.$i.'.name', array('label' => false)); ?></td>
        <td><?php echo $this->Form->input('InvoiceItem.'.$i.'.description', array('type' => 'text', 'label' => false)); ?></td>
        <td><?php echo $this->Form->input('InvoiceItem.'.$i.'.unit_cost', array('label' => false)); ?></td>
        <td><?php echo $this->Form->input('InvoiceItem.'.$i.'.quantity', array('label' => false)); ?></td>
        <td id="lineItem<?php echo $i; ?>Total" class="lineTotal"> 0.00 </td>
      </tr>
    </tbody>
  </table>
  <div class="invoiceTotal">
    <ul id="totals">
      <li><?php echo $this->Form->input('Invoice.total', array('label' => 'Invoice Total', 'div' => false)); ?></li>
      <li><?php echo $this->Form->input('Invoice.balance', array('label' => 'Invoice Balance', 'div' => false)); ?></li>
    </ul>
  </div>
  <fieldset>
    <legend>
    <?php echo __('Notes &amp; Terms'); ?>
    </legend>
    <?php
		echo $this->Form->input('Invoice.introduction', array('label' => 'Notes to client'));
		echo $this->Form->input('Invoice.conclusion', array('label' => 'Terms'));
	?>
  </fieldset>
  <?php echo $this->Form->end('Submit');?> </div>
<?php 
// set the contextual menu items
echo $this->Element('context_menu', array('menus' => array(
	array(
		'heading' => 'Invoices',
		'items' => array(
			$this->Html->link(__('View Invoice', true), array('controller' => 'invoices', 'action' => 'view', $this->request->data['Invoice']['id'])),
			$this->Html->link(__('List Invoices', true), array('controller' => 'invoices', 'action' => 'index')),
			$this->Html->link(__('New Invoice', true), array('controller' => 'invoices', 'action' => 'add')),
			$this->Html->link(__('Delete Invoice', true), array('controller' => 'invoices', 'action' => 'delete', $this->request->data['Invoice']['id'])),
			)
		),
	)));
?>
<?php echo $this->Html->script('/js/jquery.formmodifier.js');?>
<?php echo $this->Html->script('/invoices/invoice.js');?>
