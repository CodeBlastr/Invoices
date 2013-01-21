<div class="invoice form"> <?php echo $this->Form->create('Invoice');?>
  <fieldset>
    <legend>
    <p>New Invoice</p>
    </legend>
    <?php
		echo $this->Form->hidden('Invoice.status', array('value' => 'unpaid'));
		echo $this->Form->hidden('Invoice.project_id');
		if (!empty($this->request->params['named']['contact_id'])) {
			echo $this->Form->input('Invoice.contact_id', array('type' => 'hidden', 'value' => $this->request->params['named']['contact_id']));
		} else {
			//echo $this->Form->input('Invoice.contact_id', array('empty' => '-- Optional --', 'label' => 'Which client? <small>(or '.$this->Html->link('create a new company', array('plugin' => 'contacts', 'controller' => 'contacts', 'controller' => 'contacts', 'action' => 'add', 'company')).')</small>', 'after' => '<br /><br /><br />'.$this->Form->checkbox('Invoice.contact_all_access', array('checked' => 'checked')).' Give everyone at this company access to this invoice?'));
			echo $this->Form->input('Invoice.contact_id', array(
				'empty' => '-- Optional --',
				'label' => 'Which client? <small>( or '.$this->Html->link('create a new company', array('plugin' => 'contacts', 'controller' => 'contacts', 'controller' => 'contacts', 'action' => 'add', 'company'), array('class' => 'toggleClick', 'data-target' => '#ContactNameDiv')).')</small>',
				'after' => '&nbsp;'.$this->Form->checkbox('Invoice.contact_all_access', array('checked' => 'checked')).' Give everyone at this company access to this invoice?'
				));
			//echo $this->Form->checkbox('Invoice.contact_all_access', array('checked' => 'checked', 'label'=>'Give everyone at this company access to this invoice?'));
		}
		echo $this->Form->input('Contact.name', array('label' => 'Company Name', 'div' => array('id' => 'ContactNameDiv')));
		echo $this->Form->input('Contact.is_company', array('type' => 'hidden', 'value' => 1));
		
		echo $this->Form->input('Invoice.number', array('value' => $invoiceNumber, 'label' => 'Invoice Number'));
		#echo $this->Form->input('Invoice.po_number', array('label' => 'PO Number'));
		echo $this->Form->input('Invoice.due_date', array('value' => $dueDate));
	?>
  </fieldset>
  <table class="invoiceTimeLines">
	  <tr>
		  <th colspan="6">Hourly Items</th>
	  </tr>
    <tr>
      <th>&nbsp;</th>
      <th>Task</th>
      <th>Time Entry Notes</th>
      <th>Rate</th>
      <th>Hours</th>
      <th>Line Total</th>
    </tr>
    <tbody class="invoiceTimes">
      <tr id="line0Time" class="invoiceTimeLine timeRow">
      	<td>
			<a class="newTimelink btn-mini btn-primary" href="#">New</a>
			<a href="#" id="line0Time" class="deleteTimeLink btn-mini btn-danger">Delete</a>
			<?php //echo $this->Form->input('InvoiceTime.0.reusable', array('type' => 'checkbox')) ?>
		</td>
        <td><?php echo $this->Form->input('InvoiceTime.0.name', array('label' => false)); ?></td>
        <td ><?php echo $this->Form->input('InvoiceTime.0.notes', array('type' => 'text', 'label' => false)); ?></td>
        <td><?php echo $this->Form->input('InvoiceTime.0.rate', array('label' => false, 'class' => 'span1')); ?></td>
        <td><?php echo $this->Form->input('InvoiceTime.0.hours', array('label' => false, 'class' => 'span1')); ?></td>
        <td id="lineTime0Total" class="lineTotal"> 0.00 </td>
        <td></td>
      </tr>
    </tbody>
  </table>
  <table class="invoiceItemLines">
	  <tr>
		  <th colspan="6">Flat-Rate Items</th>
	  </tr>
    <tr>
      <th>&nbsp;</th>
      <th>Item</th>
      <th>Description</th>
      <th>Unit Cost</th>
      <th>Qty</th>
      <th>Line Total</th>
    </tr>
    <tbody class="invoiceItems">
      <tr id="line0Item" class="invoiceItemLine itemRow">
      	<td>
			<a class="newItemlink btn-mini btn-primary" href="#">New</a>
			<a href="#" id="line0Item" class="deleteItemLink btn-mini btn-danger">Delete</a>
			<?php echo $this->Form->input('InvoiceItem.0.reusable', array('type' => 'checkbox')) ?>
		</td>
        <td><?php echo $this->Form->input('InvoiceItem.0.name', array('label' => false)); ?></td>
        <td><?php echo $this->Form->input('InvoiceItem.0.description', array('type' => 'text', 'label' => false)); ?></td>
        <td><?php echo $this->Form->input('InvoiceItem.0.unit_cost', array('label' => false, 'class' => 'span1')); ?></td>
        <td><?php echo $this->Form->input('InvoiceItem.0.quantity', array('label' => false, 'class' => 'span1')); ?></td>
        <td id="lineItem0Total" class="lineTotal"> 0.00 </td>
      </tr>
    </tbody>
	
  </table>
  <div class="invoiceTotal">
    <ul id="totals">
      <li><?php echo $this->Form->input('Invoice.total', array('label' => 'Invoice Total', 'div' => false)); ?></li>
    </ul>
  </div>
  <fieldset>
    <legend>
    <?php echo __('Notes &amp; Terms'); ?>
    </legend>
    <?php
		echo $this->Form->input('Invoice.introduction', array('value' => $defaultIntroduction, 'label' => 'Notes to client'));
		echo $this->Form->input('Invoice.conclusion', array('value' => $defaultConclusion, 'label' => 'Terms'));
	?>
  </fieldset>
  <?php echo $this->Form->end('Submit');?> </div>
<?php 
// set the contextual menu items
$this->set('context_menu', array('menus' => array(
	array(
		'heading' => 'Invoices',
		'items' => array(
			$this->Html->link(__('List', true), array('controller' => 'invoices', 'action' => 'index'), array('class' => 'index')),
			)
		),
	))); ?>
<?php echo $this->Html->script('/js/plugins/jquery.formmodifier.js');?>
<?php echo $this->Html->script('/invoices/invoice.js');?>