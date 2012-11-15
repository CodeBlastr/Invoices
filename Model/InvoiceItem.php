<?php
class InvoiceItem extends AppModel {
	
	public $name = 'InvoiceItem';
	
	public $displayField = 'name';
	
	public $validate = array();
	
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	public $belongsTo = array(
		'Invoice' => array(
			'className' => 'Invoices.Invoice',
			'foreignKey' => 'invoice_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Product' => array(
			'className' => 'Products.Product',
			'foreignKey' => 'foreign_key',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}