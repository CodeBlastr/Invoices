<div class="invoice form"> <?php echo $this->Form->create('Invoice');?>
  <fieldset>
    <?php
		echo $this->Form->hidden('Invoice.id');
		echo $this->Form->hidden('Invoice.status');
		echo $this->Form->hidden('Invoice.project_id');
		if (!empty($this->request->params['named']['contact_id'])) {
			echo $this->Form->input('Invoice.contact_id', array('type' => 'hidden', 'value' => $this->request->params['named']['contact_id']));
		} else {
			//echo $this->Form->input('Invoice.contact_id', array('empty' => '-- Optional --', 'label' => 'Which client? <small>(or '.$this->Html->link('create a new company', array('plugin' => 'contacts', 'controller' => 'contacts', 'controller' => 'contacts', 'action' => 'add', 'company')).')</small>', 'after' => '<br /><br /><br />'.$this->Form->checkbox('Invoice.contact_all_access', array('checked' => 'checked')).' Give everyone at this company access to this invoice?'));
			echo $this->Form->input('Invoice.contact_id', array(
				'empty' => '-- Optional --',
				'label' => 'Which client? <small>( or '.$this->Html->link('create a new company', array('plugin' => 'contacts', 'controller' => 'contacts', 'controller' => 'contacts', 'action' => 'add', 'company'), array('class' => 'toggleClick', 'data-target' => '#ContactNameDiv')).')</small>',
				//'after' => '&nbsp;'.$this->Form->checkbox('Project.contact_all_access', array('checked' => 'checked')).' Give everyone at this company access to this project?'
				));
		}
		echo $this->Form->input('Invoice.number', array('label' => 'Invoice Number'));
		#echo $this->Form->input('Invoice.po_number', array('label' => 'PO Number'));
		echo $this->Form->input('Invoice.due_date');
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
    <?php $i=0; if(!empty($this->request->data['InvoiceTime'][0])) : ?>
    <tbody class="invoiceTimes">
      <?php foreach($this->request->data['InvoiceTime'] as $invoiceTime): ?>
      <tr id="line<?php echo $i; ?>Time" class="invoiceTimeLine timeRow">
	    <?php echo $this->Form->hidden('InvoiceTime.'.$i.'.id'); ?>
      	<td>
			<a class="newTimelink btn-mini btn-primary" href="#">New</a>
			<a href="#" id="line<?php echo $i; ?>Time" class="deleteTimeLink btn-mini btn-danger">Delete</a>
		</td>
        <td><?php echo $this->Form->input('InvoiceTime.'.$i.'.name', array('label' => false)); ?></td>
        <td ><?php echo $this->Form->input('InvoiceTime.'.$i.'.notes', array('type' => 'text', 'label' => false)); ?></td>
        <td><?php echo $this->Form->input('InvoiceTime.'.$i.'.rate', array('label' => false, 'class' => 'span1')); ?></td>
        <td><?php echo $this->Form->input('InvoiceTime.'.$i.'.hours', array('label' => false, 'class' => 'span1')); ?></td>
        <td id="lineTime<?php echo $i; ?>Total" class="lineTotal"> 0.00 </td>
        <td></td>
      </tr>
      <?php $i++; endforeach; ?>
    </tbody>
    <?php endif; ?>
    <tbody class="invoiceTimes">
      <tr id="line<?php echo $i; ?>Time" class="invoiceTimeLine timeRow">
      	<td>
			<a class="newTimelink btn-mini btn-primary" href="#">New</a>
			<a href="#" id="line<?php echo $i; ?>Time" class="deleteTimeLink btn-mini btn-danger">Delete</a>
		</td>
        <td><?php echo $this->Form->input('InvoiceTime.'.$i.'.name', array('label' => false)); ?></td>
        <td ><?php echo $this->Form->input('InvoiceTime.'.$i.'.notes', array('type' => 'text', 'label' => false)); ?></td>
        <td><?php echo $this->Form->input('InvoiceTime.'.$i.'.rate', array('label' => false, 'class' => 'span1')); ?></td>
        <td><?php echo $this->Form->input('InvoiceTime.'.$i.'.hours', array('label' => false, 'class' => 'span1')); ?></td>
        <td id="lineTime<?php echo $i; ?>Total" class="lineTotal"> 0.00 </td>
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
    <?php $i=0; if(!empty($this->request->data['InvoiceItem'][0])) : ?>
    <tbody class="invoiceItems">
      <?php foreach($this->request->data['InvoiceItem'] as $invoiceTime): ?>
      <tr id="line<?php echo $i; ?>Item" class="invoiceItemLine itemRow">
	    <?php echo $this->Form->hidden('InvoiceItem.'.$i.'.id'); ?>
      	<td>
			<a class="newItemlink btn-mini btn-primary" href="#">New</a>
			<a href="#" id="line<?php echo $i; ?>Item" class="deleteItemLink btn-mini btn-danger">Delete</a>
			<?php echo $this->Form->input('InvoiceItem.'.$i.'.is_reusable', array('type' => 'checkbox', 'checked' => false)) ?>
		</td>
        <td><?php echo $this->Form->input('InvoiceItem.'.$i.'.name', array('label' => false)); ?></td>
        <td><?php echo $this->Form->input('InvoiceItem.'.$i.'.description', array('type' => 'text', 'label' => false)); ?></td>
        <td><?php echo $this->Form->input('InvoiceItem.'.$i.'.unit_cost', array('label' => false, 'class' => 'span1')); ?></td>
        <td><?php echo $this->Form->input('InvoiceItem.'.$i.'.quantity', array('label' => false, 'class' => 'span1')); ?></td>
        <td id="lineItem<?php echo $i; ?>Total" class="lineTotal"> 0.00 </td>
      </tr>
      <?php $i = $i + 1; endforeach; ?>
    </tbody>
    <?php endif;  ?>
    <tbody class="invoiceItems">
      <tr id="line<?php echo $i; ?>Item" class="invoiceItemLine itemRow">
      	<td>
			<a class="newItemlink btn-mini btn-primary" href="#">New</a>
			<a href="#" id="line<?php echo $i; ?>Item" class="deleteItemLink btn-mini btn-danger">Delete</a>
			<?php echo $this->Form->input('InvoiceItem.'.$i.'.is_reusable', array('type' => 'checkbox', 'checked' => false)) ?>
		</td>
        <td><?php echo $this->Form->input('InvoiceItem.'.$i.'.name', array('label' => false)); ?></td>
        <td><?php echo $this->Form->input('InvoiceItem.'.$i.'.description', array('type' => 'text', 'label' => false)); ?></td>
        <td><?php echo $this->Form->input('InvoiceItem.'.$i.'.unit_cost', array('label' => false, 'class' => 'span1')); ?></td>
        <td><?php echo $this->Form->input('InvoiceItem.'.$i.'.quantity', array('label' => false, 'class' => 'span1')); ?></td>
        <td id="lineItem<?php echo $i; ?>Total" class="lineTotal"> 0.00 </td>
      </tr>
	  
    </tbody>
    <tbody class="reusableItems">
	  <?php
	  if ( !empty($reusableItems) ) {
		  ++$i;
	  ?>
      <tr id="line<?php echo $i; ?>Item" class="reusableItemLine itemRow">
		  <td>
			<a class="newReuseItemlink btn-mini btn-primary" href="#">New</a>
			<a href="#" id="line<?php echo $i; ?>Item" class="deleteReuseItemLink btn-mini btn-danger">Delete</a>
		  </td>
		  <td>
			  <?php
				  $reusableItemsOptions = array();
				  foreach ( $reusableItems as $reusableItem ) {
					  $reusableItemsOptions[ $reusableItem['InvoiceItem']['name'] ] = $reusableItem['InvoiceItem']['name'];
					  echo $this->Html->div('hidden', $reusableItem['InvoiceItem']['description'], array('id' => 'reusableDesc_'.$i));
					  echo $this->Html->div('hidden', $reusableItem['InvoiceItem']['unit_cost'], array('id' => 'reusableUnit_'.$i));
				  }
				  echo $this->Form->input('InvoiceItem.'.$i.'.name', array(
					  'options' => $reusableItemsOptions, 'empty' => '(Add Reusable Item)',
					  'label' => false,
					  'class' => 'reusableSelect'
					  ));
			?>
		  </td>
			<td><?php echo $this->Form->input('InvoiceItem.'.$i.'.description', array('type' => 'text', 'label' => false)); ?></td>
			<td><?php echo $this->Form->input('InvoiceItem.'.$i.'.unit_cost', array('label' => false, 'class' => 'span1')); ?></td>
			<td><?php echo $this->Form->input('InvoiceItem.'.$i.'.quantity', array('label' => false, 'class' => 'span1')); ?></td>
			<td id="lineItem<?php echo $i; ?>Total" class="lineTotal"> 0.00 </td>
      </tr>
	  <?php
	  }
	  ?>
	  
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
		echo $this->Form->input('Invoice.introduction', array('label' => 'Notes to client', 'class'=>'span5', 'div' => array('class'=>'span5')));
		echo $this->Form->input('Invoice.conclusion', array('label' => 'Terms', 'class'=>'span5', 'div' => array('class'=>'span5')));
	?>
  </fieldset>
  <?php echo $this->Form->end('Submit');?> </div>
<?php 
// set the contextual menu items
$this->set('context_menu', array('menus' => array(
	array(
		'heading' => 'Invoices',
		'items' => array(
			$this->Html->link(__('View'), array('controller' => 'invoices', 'action' => 'view', $this->request->data['Invoice']['id'])),
			$this->Html->link(__('List'), array('controller' => 'invoices', 'action' => 'index')),
			$this->Html->link(__('Add'), array('controller' => 'invoices', 'action' => 'add')),
    		$this->Html->link(__('Delete'), array('controller' => 'invoices', 'action' => 'delete', $this->request->data['Invoice']['id']), null, __('Are you sure you want to delete %s', strip_tags($this->request->data['Invoice']['name']))),
			)
		),
	))); ?>
<?php echo $this->Html->script('/js/plugins/jquery.formmodifier.js');?>
<?php echo $this->Html->script('/invoices/invoice.js');?>
