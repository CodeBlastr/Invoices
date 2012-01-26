<div class="invoice view">
  <div id="n1" class="info-block">
    <div class="viewRow">
      <ul class="metaData">
        <li><span class="metaDataLabel">
          <?php echo __('Invoice Number: '); ?>
          </span><span class="metaDataDetail"><?php echo $invoice['Invoice']['number']; ?></span></li>
        <li><span class="metaDataLabel">
          <?php echo __('Status: '); ?>
          </span><span class="metaDataDetail"><?php echo $invoice['Invoice']['status']; ?></span></li>
        <li><span class="metaDataLabel">
          <?php echo __('Date: '); ?>
          </span><span class="metaDataDetail"><?php echo $invoice['Invoice']['due_date']; ?></span></li>
        <li><span class="metaDataLabel">
          <?php echo __('Sent Count: '); ?>
          </span><span class="metaDataDetail"><?php echo $invoice['Invoice']['is_sent']; ?></span></li>
      </ul>
      <div class="recordData">
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
          <?php endforeach; endif; ?>
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
            <td><?php echo ZuhaInflector::pricify($invoiceItem['unit_cost'] * $invoiceItem['quantity']); ?></td>
          </tr>
          <?php endforeach; endif; ?>
          <tr id="totals">
            <td class="invoiceIntroduction"><?php echo $invoice['Invoice']['introduction']; ?></td>
            <td class="invoiceConclusion"><?php echo $invoice['Invoice']['conclusion']; ?></td>
            <td><p>Total</p>
              <p>Paid</p>
              <p>Balance</p></td>
            <td>&nbsp;</td>
            <td><p><?php echo ZuhaInflector::pricify($invoice['Invoice']['total']); ?></p>
              <p><?php echo ZuhaInflector::pricify($invoice['Invoice']['total'] - $invoice['Invoice']['total']); ?></p>
              <p><?php echo ZuhaInflector::pricify($invoice['Invoice']['balance']); ?></p></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  <!-- /info-block end -->
</div>
        <div class="invoiceEmail form">
			<?php echo $this->Form->create('Invoice');?>
            <fieldset>
            <legend><?php echo __('Email invoice'); ?></legend>
            <?php 
			echo $this->Form->hidden('Invoice.id', array('value' => $invoice['Invoice']['id']));
			echo $this->Form->hidden('Invoice.is_sent');
			echo $this->Form->input('Invoice.subject');
			echo $this->Form->input('Invoice.recipient');
			echo $this->Form->input('Invoice.message', array('type' => 'richtext')); ?>
            </fieldset>
            <?php echo $this->Form->end('Email Invoice'); ?>
        </div>
<?php
// set the contextual menu items
$this->set('context_menu', array('menus' => array(
	array(
		'heading' => 'Invoice',
		'items' => array(
			$this->Html->link(__('Add', true), array('controller' => 'invoices', 'action' => 'add'), array('class' => 'add')),
			$this->Html->link(__('List', true), array('controller' => 'invoices', 'action' => 'index'), array('class' => 'index')),
			$this->Html->link(__('Edit', true), array('controller' => 'invoices', 'action' => 'edit', $invoice['Invoice']['id']), array('class' => 'edit')),
			)
		),
	))); ?>
