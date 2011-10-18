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
            <td><?php echo formatPrice($invoiceTime['rate'] * $invoiceTime['hours']); ?></td>
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
            <td><?php echo formatPrice($invoiceItem['unit_cost'] * $invoiceItem['quantity']); ?></td>
          </tr>
          <?php endforeach; endif; ?>
          <tr id="totals">
            <td class="invoiceIntroduction"><?php echo $invoice['Invoice']['introduction']; ?></td>
            <td class="invoiceConclusion"><?php echo $invoice['Invoice']['conclusion']; ?></td>
            <td><p>Total</p>
              <p>Paid</p>
              <p>Balance</p></td>
            <td>&nbsp;</td>
            <td><p><?php echo formatPrice($invoice['Invoice']['total']); ?></p>
              <p><?php echo formatPrice($invoice['Invoice']['total'] - $invoice['Invoice']['total']); ?></p>
              <p><?php echo formatPrice($invoice['Invoice']['balance']); ?></p></td>
          </tr>
        </table>
        <div class="invoiceEmail form">
			<?php echo $this->Form->create('Invoice');?>
            <?php echo $this->Form->hidden('Invoice.id', array('value' => $invoice['Invoice']['id'])); ?>
            <?php echo $this->Form->textarea('Invoice.recipient', array('value' => $recipients)); ?>
            <?php echo $this->Form->end('Email Invoice'); ?>
        </div>
      </div>
    </div>
  </div>
  <!-- /info-block end -->
</div>
<?php
// set the contextual menu items
echo $this->Element('context_menu', array('menus' => array(
	array(
		'heading' => 'Invoice',
		'items' => array(
			$this->Html->link(__('New Invoice', true), array('controller' => 'invoices', 'action' => 'add')),
			$this->Html->link(__('List Invoices', true), array('controller' => 'invoices', 'action' => 'index')),
			$this->Html->link(__('Edit Invoice', true), array('controller' => 'invoices', 'action' => 'edit', $invoice['Invoice']['id'])),
			$this->Html->link(__('Email Invoice', true), array('controller' => 'invoices', 'action' => 'email', $invoice['Invoice']['id'])),
			)
		),
	))); 
?>
<?php $this->set('page_title_for_layout', $invoice['Invoice']['name']); ?>
