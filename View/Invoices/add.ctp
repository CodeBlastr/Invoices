<?php //echo $this->Html->script('/js/plugins/jquery.formmodifier.js');?>
<?php echo $this->Html->script('plugins/jquery.form.utility.min'); ?>
<?php echo $this->Html->script('/invoices/invoice.js');?>

<div class="invoice form"> <?php echo $this->Form->create('Invoice');?>
	<fieldset>
    	<legend>New Invoice</legend>
    	<?php echo $this->Form->hidden('Invoice.status', array('value' => 'unpaid')); ?>
		<?php echo $this->Form->hidden('Invoice.project_id'); ?>
		<?php if (!empty($this->request->params['named']['contact_id'])) : ?>
			<?php echo $this->Form->input('Invoice.contact_id', array('type' => 'hidden', 'value' => $this->request->params['named']['contact_id'])); ?>
		<?php else : ?>
			<?php //echo $this->Form->input('Invoice.contact_id', array('empty' => '-- Optional --', 'label' => 'Which client? <small>(or '.$this->Html->link('create a new company', array('plugin' => 'contacts', 'controller' => 'contacts', 'controller' => 'contacts', 'action' => 'add', 'company')).')</small>', 'after' => '<br /><br /><br />'.$this->Form->checkbox('Invoice.contact_all_access', array('checked' => 'checked')).' Give everyone at this company access to this invoice?')); ?>
			<?php echo $this->Form->input('Invoice.contact_id', array(
				'empty' => '-- Optional --',
				'label' => 'Which client? <small>( or '.$this->Html->link('create a new company', array('plugin' => 'contacts', 'controller' => 'contacts', 'controller' => 'contacts', 'action' => 'add', 'company'), array('class' => 'toggleClick', 'data-target' => '#ContactNameDiv')).')</small>',
				'after' => '&nbsp;'.$this->Form->checkbox('Invoice.contact_all_access', array('checked' => 'checked')).' Give everyone at this company access to this invoice?'
				));
				//echo $this->Form->checkbox('Invoice.contact_all_access', array('checked' => 'checked', 'label'=>'Give everyone at this company access to this invoice?')); ?>
		<?php endif; ?>
		<?php echo $this->Form->input('Contact.name', array('label' => 'Company Name', 'div' => array('id' => 'ContactNameDiv'))); ?>
		<?php echo $this->Form->input('Contact.is_company', array('type' => 'hidden', 'value' => 1)); ?>
		<?php echo $this->Form->input('Invoice.number', array('value' => $invoiceNumber, 'label' => 'Invoice Number')); ?>
		<?php // echo $this->Form->input('Invoice.po_number', array('label' => 'PO Number')); ?>
		<?php echo $this->Form->input('Invoice.due_date', array('value' => $dueDate, 'type' => 'datepicker')); ?>
	</fieldset>
	
	
  	<table>
  		<thead>
  			<tr>
  				<th colspan="5"><h4>Hourly Items</h4></th>
  			</tr>
	    </thead>
    	<tbody class="invoiceTimes">
			<tr>
	      		<td>Task</td>
	      		<td>Time Entry Notes</td>
	      		<td>Rate</td>
	      		<td>Hours</td>
	      		<td class="text-right">Line Total</td>
	    	</tr>
    		<!-- duplicatable start -->
      		<tr class="duplicatable calc-row">
		        <td>
		        	<div class="row">
			        	<div class="col-xs-9">
			        		<?php echo $this->Form->input('InvoiceTime.0.name', array('label' => false, 'class' => 'changer', 'div' => 'false')); ?>
			        	</div>
			        	<div class="col-xs-3">
			        		<label class="checkbox-inline">
			        			<?php echo $this->Form->input('InvoiceTime.0.reusable', array('type' => 'checkbox', 'label' => false, 'div' => false)) ?>
			        			<span class="glyphicon glyphicon-repeat" title="Resuable" data-toggle="tooltip">&nbsp;</span>
			        		</label>
			        	</div>
			        </div>		        	
		        </td>
		        <td ><?php echo $this->Form->input('InvoiceTime.0.notes', array('type' => 'text', 'label' => false)); ?></td>
		        <td><?php echo $this->Form->input('InvoiceTime.0.rate', array('label' => false, 'class' => 'calc-rate')); ?></td>
		        <td><?php echo $this->Form->input('InvoiceTime.0.hours', array('label' => false, 'class' => 'calc-multiplier')); ?></td>
		        <td class="lineTotal"> <?php echo $this->Form->input('InvoiceTime.0.line_total', array('value' => '0.00', 'disabled' => 'disabled', 'label' => false, 'class' => 'calc-line-total text-right')); ?> </td>
      		</tr>
	 		<!-- duplicatable end -->
    	</tbody>
    	<thead>
    		<tr>
    			<th colspan="5"><h4>Items</h4></th>
    		</tr>
    	</thead>
    	<tbody class="invoiceItemLines">
    		<tr>
			    <td>Name</td>
			    <td>Description</td>
			    <td>Unit Cost</td>
			    <td>Qty</td>
			    <td class="text-right">Line Total</td>
    		</tr>
    		<!-- duplicatable start -->
      		<tr class="duplicatable calc-row">
		        <td>
		        	<div class="row">
			        	<div class="col-xs-9">
			        		<?php echo $this->Form->input('InvoiceItem.0.name', array('label' => false, 'class' => 'changer', 'div' => 'false')); ?>
			        	</div>
			        	<div class="col-xs-3">
			        		<label class="checkbox-inline">
			        			<?php echo $this->Form->input('InvoiceItem.0.reusable', array('type' => 'checkbox', 'label' => false, 'div' => false)) ?>
			        			<span class="glyphicon glyphicon-repeat" title="Resuable" data-toggle="tooltip">&nbsp;</span>
			        		</label>
			        	</div>
			        </div>
		        </td>
		        <td><?php echo $this->Form->input('InvoiceItem.0.description', array('type' => 'text', 'label' => false)); ?></td>
		        <td><?php echo $this->Form->input('InvoiceItem.0.unit_cost', array('label' => false, 'class' => 'calc-rate')); ?></td>
		        <td><?php echo $this->Form->input('InvoiceItem.0.quantity', array('label' => false, 'class' => 'calc-multiplier')); ?></td>
		        <td class="lineTotal"> <?php echo $this->Form->input('InvoiceItem.0.line_total', array('value' => '0.00', 'disabled' => 'disabled', 'label' => false, 'class' => 'calc-line-total text-right')); ?> </td>
	 		</tr>
	 		<!-- duplicatable end -->
	 		
      		<!--tr id="line0Item" class="invoiceItemLine itemRow">
      			<td>
					<a class="newItemlink btn btn-xs btn-primary" href="#">New</a>
					<a href="#" id="line0Item" class="deleteItemLink btn btn-xs btn-danger">Delete</a>
					<?php echo $this->Form->input('InvoiceItem.0.reusable', array('type' => 'checkbox')) ?>
				</td>
		        <td><?php echo $this->Form->input('InvoiceItem.0.name', array('label' => false)); ?></td>
		        <td><?php echo $this->Form->input('InvoiceItem.0.description', array('type' => 'text', 'label' => false)); ?></td>
		        <td><?php echo $this->Form->input('InvoiceItem.0.unit_cost', array('label' => false, 'class' => 'span1')); ?></td>
		        <td><?php echo $this->Form->input('InvoiceItem.0.quantity', array('label' => false, 'class' => 'span1')); ?></td>
		        <td id="lineItem0Total" class="lineTotal"> 0.00 </td>
      		</tr-->
	  		<tr>
	  			<td>&nbsp;</td>
	  			<td>&nbsp;</td>
	  			<td>&nbsp;</td>
	  			<td>&nbsp;</td>
	    		<td class="text-right">
	      			<?php echo $this->Form->input('Invoice.total', array('value' => '0.00', 'type' => 'text', 'disabled' => 'disabled', 'label' => 'Invoice Total', 'div' => false, 'class' => 'text-right calc-total')); ?>
	      		</td>
	      	</tr>
		</tbody>
  	</table>
  	<fieldset>
    	<legend><?php echo __('Notes &amp; Terms'); ?></legend>	
    	<?php echo $this->Form->input('Invoice.introduction', array('value' => $defaultIntroduction, 'label' => 'Notes to client')); ?>
		<?php echo $this->Form->input('Invoice.conclusion', array('value' => $defaultConclusion, 'label' => 'Terms')); ?>
  	</fieldset>
  	<?php echo $this->Form->end('Submit');?>
</div>

<?php 
// set the contextual menu items
$this->set('context_menu', array('menus' => array(
	array(
		'heading' => 'Invoices',
		'items' => array(
			$this->Html->link(__('List', true), array('controller' => 'invoices', 'action' => 'index'), array('class' => 'index')),
			)
		)
	)));