<?php //echo $this->Html->script('/js/plugins/jquery.formmodifier.js');?>
<?php echo $this->Html->script('plugins/jquery.form.utility.min'); ?>
<?php echo $this->Html->script('/invoices/invoice.js');?>

<div class="invoice form"> <?php echo $this->Form->create('Invoice');?>
	<h3>New Invoice</h3>
	<?php echo $this->Form->hidden('Invoice.status', array('value' => 'unpaid')); ?>
	<?php echo $this->Form->input('Invoice.number', array('value' => $invoiceNumber, 'label' => 'Invoice Number')); ?>
	<?php echo $this->Form->input('Invoice.po_number'); ?>
	<?php echo $this->Form->input('Invoice.terms'); ?>
	<?php echo $this->Form->input('Invoice.address'); ?>
	<?php echo $this->Form->input('Invoice.address2'); ?>
	<?php // echo $this->Form->input('Invoice.po_number', array('label' => 'PO Number')); ?>
	<?php echo $this->Form->input('Invoice.due_date', array('value' => $dueDate, 'type' => 'datepicker')); ?>

  	<table>
    	<tbody class="invoiceItemLines">
    		<tr>
			    <td>Item <small><?php echo $this->Html->link('<span class="glyphicon glyphicon-edit">&nbsp;</span>', array('controller' => 'invoice_items', 'action' => 'index'), array('target' => '_blank', 'escape' => false)); ?></small></td>
			    <td>Description</td>
			    <td>Unit Cost</td>
			    <td>Qty</td>
			    <td class="text-right">Line Total</td>
    		</tr>
    		<!-- duplicatable start -->
      		<tr class="duplicatable calc-row">
		        <td class="col-xs-3">
		        	<?php // echo $this->Form->input('InvoiceItem.0.name', array('label' => false, 'div' => 'false')); ?>
		        	<?php echo $this->Form->input('InvoiceItem.0.name', array('empty' => '-- Select --', 'options' => array_merge($invoiceItems, array('+'=>'+ Custom Item')), 'div' => false, 'label' => false, 'class' => 'combobox')); ?>
		        </td>
		        	<!-- <div class="row">
			        	<div class="col-xs-9">
			        		
			        	</div>
			        	<div class="col-xs-3">
			        		<label class="checkbox-inline">
			        			<?php //echo $this->Form->input('InvoiceItem.0.reusable', array('type' => 'checkbox', 'label' => false, 'div' => false)) ?>
			        			<span class="glyphicon glyphicon-repeat" title="Resuable" data-toggle="tooltip">&nbsp;</span>
			        		</label>
			        	</div>
			        </div> -->
		        <td><?php echo $this->Form->input('InvoiceItem.0.description', array('type' => 'text', 'label' => false)); ?></td>
		        <td><?php echo $this->Form->input('InvoiceItem.0.unit_cost', array('label' => false, 'class' => 'calc-rate')); ?></td>
		        <td><?php echo $this->Form->input('InvoiceItem.0.quantity', array('label' => false, 'class' => 'changer calc-multiplier')); ?></td>
		        <td class="lineTotal"> <?php echo $this->Form->input('InvoiceItem.0.line_total', array('value' => '0.00', 'disabled' => 'disabled', 'label' => false, 'class' => 'calc-line-total text-right')); ?> </td>
	 		</tr>
	 		<!-- duplicatable end -->
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

  	<h4><?php echo __('Notes &amp; Terms'); ?></h4>	
	<?php echo $this->Form->input('Invoice.introduction', array('value' => $defaultIntroduction, 'label' => 'Notes to client')); ?>
	<?php echo $this->Form->input('Invoice.conclusion', array('value' => $defaultConclusion, 'label' => 'Terms')); ?>
  	<?php echo $this->Form->end('Submit');?>
</div>


<?php // never got these to duplicatable and combobox to work together // echo $this->Html->css('/css/twitter-bootstrap.3/plugins/bootstrap.combobox'); ?>
<?php //echo $this->Html->script('/js/twitter-bootstrap.3/plugins/bootstrap.combobox'); ?>
<!-- <script type="text/javascript">
  $(document).ready(function(){
    $('.combobox').combobox();
  });
  // need to re-init combo boxes after we dupe the line item
  // never got this to work
  $(document.body).on('keyup', '.changer', function() {
  	$('.duplicatable .combobox').combobox();
  });
</script> -->

<?php 
// set the contextual breadcrumb items
$this->set('context_crumbs', array('crumbs' => array(
	$this->Html->link(__('Admin Dashboard'), '/admin'),
	$this->Html->link(__('Invoices'), '/admin/invoices'),
	'Add Invoice',
)));
// set the contextual menu items
$this->set('context_menu', array('menus' => array(
	array(
		'heading' => 'Invoices',
		'items' => array(
			$this->Html->link(__('Invoices'), array('controller' => 'invoices', 'action' => 'index'))
			)
		),
	array(
		'heading' => 'Invoice Items',
		'items' => array(
			$this->Html->link(__('Reusable Items'), array('controller' => 'invoice_items', 'action' => 'index'))
			)
		)
	)));