<div>
<?php
echo $this->Html->link($transactionItem['name'],
	'/invoices/invoices/view/'.$transactionItem['foreign_key'],
	array('style' => 'text-decoration: underline;'),
	'Are you sure you want to leave this page?'
	);
?>
</div>
<?php
//echo $this->element('thumb', array(
//	    'model' => 'Task',
//	    'foreignKey' => $transactionItem['foreign_key'],
//	    'thumbSize' => 'small',
//	    'thumbWidth' => 75,
//	    'thumbHeight' => 75,
//	    //'thumbLink' => '/tasks/tasks/view/'.$transactionItem['foreign_key']
//	    'thumbLink' => '#'
//	    ),
//	array('plugin' => 'galleries')
//	);
echo $this->Form->hidden("TransactionItem.{$i}.quantity", array(
    'label' => 'Qty.',
    'div' => array('style' => 'display:inline-block'),
    'value' => 1,
    'size' => 1
    ));
?>

<div style="display: inline-block; float: right; font-weight: bold; padding: 22px 0;" id="">
    $<?php echo number_format($transactionItem['price'], 2); ?>
</div>
