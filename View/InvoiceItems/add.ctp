<?php echo $this->Form->create('InvoiceItem'); ?>
<?php echo $this->Form->input('InvoiceItem.is_reusable', array('type' => 'hidden', 'value' => 1)); ?>
<?php echo $this->Form->input('InvoiceItem.parent_id', array('empty' => '-- Select --')); ?>
<?php echo $this->Form->input('InvoiceItem.name'); ?>
<?php echo $this->Form->input('InvoiceItem.description'); ?>
<?php echo $this->Form->input('InvoiceItem.unit_cost'); ?>
<?php echo $this->Form->end('Add Item'); ?>