
<div class="invoice-items edit form">
    <?php
    $this->Tree->addTypeAttribute('data-identifier', $this->request->data['InvoiceItem']['id'], null, 'previous');
    echo $this->Tree->generate($this->request->data, array(
        	'model' => 'InvoiceItem', 
			'alias' => 'name', 
			'class' => 'sortable sortableMenu', 
			'id' => 'menu' . $this->request->data['InvoiceItem']['id'], 
			'element' => 'item', 
			'elementPlugin' => 'invoices')); ?>
    <h5>
    <?php echo $this->Html->link(__('Save'), '', array('class' => 'btn btn-default', 'onClick' => 'history.go(-1);return false;')); ?>
    <?php echo $this->Html->link(__('Save & Continue Editing'), '', array('class' => 'btn btn-default', 'onClick' => 'window.location.reload()')); ?>
    </h5>
</div>

<?php echo $this->Html->css('/css/jquery-ui/jquery-ui-1.9.2.custom.min'); ?>
<?php echo $this->Html->css('/webpages/menus/css/nestedSortable'); ?>
<?php echo $this->Html->script('/js/jquery-ui/jquery-ui-1.9.2.custom.min'); ?>
<?php echo $this->Html->script('/webpages/menus/js/jquery.ui.nestedSortable'); ?>

<script type="text/javascript">
$(function() {
    // maybe this is for editing item values???
	$('.sortableMenu a').click(function(e) {
		e.preventDefault();
	});

	$('.sortableMenu').nestedSortable({
		forcePlaceholderSize: true,
		listType: 'ul',
		handle: 'div',
		helper: 'clone',
		opacity: .6,
    	placeholder: 'placeholder',
        rootID: '<?php echo $this->request->data['InvoiceItem']['id']; ?>',
		items: "li",
		delay: 100,
		tolerance: 'pointer',
		toleranceElement: '> div',
		update: function(event, ui) {
			//$('#loadingimg').show();
		 	var order = $('ul.sortableMenu').nestedSortable('toArray');
			$.post('/invoices/invoice_items/sort.json', {order:order}, 
				   function(data){
					  	var n = 1;
						$.each(data, function(i, item) {
							$('td.'+item).html(n);
							n++;
						});	
						//$('#loadingimg').hide()
				   }
			);
		}
	});
});
</script>


<?php
// set the contextual breadcrumb items
$this->set('context_crumbs', array('crumbs' => array(
	$this->Html->link(__('Admin Dashboard'), '/admin'),
	$this->Html->link(__('Invoices'), '/admin/invoices'),
	'Reusable Items',
)));
// set the contextual menu items
$this->set('context_menu', array('menus' => array(
	array(
		'heading' => 'Invoice Items',
		'items' => array(
			$this->Html->link(__('Add'), array('controller' => 'invoice_items', 'action' => 'add'))
			)
		)
	)));