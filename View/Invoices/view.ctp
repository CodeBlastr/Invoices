<div class="invoice view">
	<div id="n1" class="info-block">
		<div class="col-md-12">
			<span class="label label-info"><?php echo __('Status %s', $invoice['Invoice']['status']); ?></span>
			<span class="label label-info"><?php echo __('Due %s', ZuhaInflector::datify($invoice['Invoice']['due_date'])); ?></span>
			<span class="label label-info"><?php echo __('Sent Count %s', $invoice['Invoice']['is_sent']); ?></span>
		</div>
   		<div class="col-md-12">
	        <table>
	          	<?php if (!empty($invoice['InvoiceTime'][0])) : ?>
	        	<tr>
		            <th>Task</th>
		            <th>Time Entry Notes</th>
		            <th>Rate</th>
		            <th>Hours</th>
		            <th>Line Total</th>
	          	</tr>
		          	<?php foreach ($invoice['InvoiceTime'] as $invoiceTime) : ?>
		          	<tr>
			            <td><?php echo $invoiceTime['name']; ?></td>
			            <td><?php echo $invoiceTime['notes']; ?></td>
			            <td><?php echo $invoiceTime['rate']; ?></td>
			            <td><?php echo $invoiceTime['hours']; ?></td>
			            <td><?php echo ZuhaInflector::pricify($invoiceTime['rate'] * $invoiceTime['hours']); ?></td>
		          	</tr>
		          	<?php endforeach; ?>
	          	<?php endif; ?>
	          	<?php if (!empty($invoice['InvoiceItem'][0])) : ?>
	          	<tr>
		            <th>Item</th>
		            <th>Description</th>
		            <th>Unit Cost</th>
		            <th>Qty</th>
		            <th>Line Total</th>
	          	</tr>
		          	<?php foreach ($invoice['InvoiceItem'] as $invoiceItem) : ?>
		          	<tr>
			            <td><?php echo $invoiceItem['name']; ?></td>
			            <td><?php echo $invoiceItem['description']; ?></td>
			            <td><?php echo $invoiceItem['unit_cost']; ?></td>
			            <td><?php echo $invoiceItem['quantity']; ?></td>
			            <td class="text-right"><?php echo number_format($invoiceItem['unit_cost'] * $invoiceItem['quantity'], 2, '.', ','); ?></td>
		          	</tr>
		          	<?php endforeach; ?>
		      	<?php endif; ?>
	          	<tr>
		            <th>Notes</th>
		            <th>Terms</th>
		            <th>Totals</th>
		            <th>&nbsp;</th>
		            <th>&nbsp;</th>
	          	</tr>
	          	<tr id="totals">
		            <td class="invoiceIntroduction"><?php echo $invoice['Invoice']['introduction']; ?></td>
		            <td class="invoiceConclusion"><?php echo $invoice['Invoice']['conclusion']; ?></td>
		            <td><p>Total</p>
		              <p>Paid</p>
		              <p>Balance</p></td>
		            <td>&nbsp;</td>
		            <td>
		            	<p class="text-right">$<?php echo number_format($invoice['Invoice']['total'], 2, '.', ','); ?></p>
		              	<p class="text-right">$<?php echo number_format($invoice['Invoice']['total'] - $invoice['Invoice']['balance'], '2', '.', ','); ?></p>
		              	<p class="text-right">$<?php echo number_format($invoice['Invoice']['balance'], 2, '.', ','); ?></p>
					</td>
	          	</tr>
			</table>
		</div>
		
		<?php if ( $invoice['Invoice']['status'] !== 'paid' ) { ?>
        <div id="invoicePayButton" class="text-right">
			<?php
			echo $this->Form->create(null, array('url' => '/transactions/transaction_items/add', 'class' => 'form-inline'));
			// echo $this->Form->input('TransactionItem.price', array(
				// 'label' => false,
				// 'value' => number_format($invoice['Invoice']['balance'], 2, '.', ''),
				// 'class' => 'span2',
				// 'div' => false,
				// 'disabled' => 'disabled'
			// ));
			
			echo $this->Form->hidden('TransactionItem.model', array('value' => 'Invoice'));
			echo $this->Form->hidden('TransactionItem.foreign_key', array('value' => $invoice['Invoice']['id']));
			//echo $this->Form->hidden('TransactionItem.arb_settings.PaymentAmount',
			// array('value' => $task['Task']['assignee_id']));
			//			$arbSettingsValues = array(
			//				array(
			//					'name' => 'ExecutionFrequencyType',
			//					'desc' => 'The frequency to execute the schedule.'
			//								.'<br />"Daily", "Weekly", "BiWeekly", "FirstofMonth",
			// "SpecificDayofMonth", "LastofMonth", "Quarterly", "SemiAnnually", "Annually"',
			//					),
			//				array(
			//					'name' => 'ExecutionFrequencyParameter',
			//					'desc' => 'The execution frequency parameter specifies the day of month
			// for a SpecificDayOfMonth frequency or specifies day of week for Weekly or
			// BiWeekly schedule.'
			//					.'<br />It is required when ExecutionFrequncyType is SpecificDayofMonth,
			// Weekly or BiWeekly.'
			//					.'<br />"Sunday" ... "Saturday"',
			//					),
			//			);
			//
			//			echo
			// $this->Form->input('TransactionItem.arb_settings.ExecutionFrequencyType',
			// array(
			//				'label' => 'Repetition of Payment',
			//				'options' => array("Weekly"=>"Weekly", "BiWeekly"=>"Bi-Weekly",
			// "FirstofMonth"=>"First of the Month", "LastofMonth"=>"Last of the Month",
			// "Quarterly"=>"Quarterly", "SemiAnnually"=>"Semi-Annually",
			// "Annually"=>"Annually"),
			//				'empty' => 'One-Time'
			//			));
			//			echo
			// $this->Form->input('TransactionItem.arb_settings.ExecutionFrequencyParameter',
			// array(
			//				'label' => 'Day of Payment',
			//				'options' => array("Sunday"=>"Sunday", "Monday"=>"Monday",
			// "Tuesday"=>"Tuesday", "Wednesday"=>"Wednesday", "Thursday"=>"Thursday",
			// "Friday"=>"Friday", "Saturday"=>"Saturday"),
			//				'empty' => '(choose one)'
			//			));
			if ($_SERVER['HTTP_HOST'] == 'mega.buildrr.com') {
				echo $this->Html->link('Enter Payment on Invoice', 'http://buildrr.com/checkout/?a=' . number_format($invoice['Invoice']['balance'], 2, '.', ''), array('class' => 'btn btn-primary'));
			} else {
				echo $this->Form->submit('Enter Payment on Invoice', array('class' => 'button btn btn-primary'));
			}
			echo $this->Form->end();
			?>
			
		</div>
		<?php } ?>
		
    </div>
  </div>
  <!-- /info-block end -->
</div>
<?php
// set the contextual breadcrumb items
$this->set('context_crumbs', array('crumbs' => array(
	$this->Html->link(__('Admin Dashboard'), '/admin'),
	$this->Html->link(__('Invoices'), '/admin/invoices'),
	'Invoice ' . $invoice['Invoice']['name'],
)));
// set the contextual menu items
$this->set('context_menu', array('menus' => array( array(
			'heading' => 'Invoice',
			'items' => array(
				$this->Html->link(__('Add'), array(
					'controller' => 'invoices',
					'action' => 'add'
				)),
				$this->Html->link(__('List'), array(
					'controller' => 'invoices',
					'action' => 'index'
				)),
				$this->Html->link(__('Edit'), array(
					'controller' => 'invoices',
					'action' => 'edit',
					$invoice['Invoice']['id']
				)),
				$this->Html->link(__('Email'), array(
					'controller' => 'invoices',
					'action' => 'email',
					$invoice['Invoice']['id']
				)),
				$this->Html->link(__('Delete'), array(
					'controller' => 'invoices',
					'action' => 'delete',
					$invoice['Invoice']['id']
				), null, __('Are you sure you want to delete %s', strip_tags($invoice['Invoice']['name']))),
			)
		))));
?>
<?php $this->set('page_title_for_layout', $invoice['Invoice']['name']); ?>
