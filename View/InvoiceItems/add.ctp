<?php echo $this->Form->create('Invoice'); ?>
<?php echo $this->Form->input('Invoice.is_reusable', array('type' => 'hidden', 'value' => 1)); ?>
<?php echo $this->Form->input('Invoice.parent_id', array('empty' => '-- Select --')); ?>
<?php echo $this->Form->input('Invoice.name'); ?>
<?php echo $this->Form->input('Invoice.description'); ?>
<?php echo $this->Form->input('Invoice.unit_cost'); ?>
<?php echo $this->Form->end('Add Item'); ?>