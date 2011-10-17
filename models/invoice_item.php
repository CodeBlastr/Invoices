<?php
class InvoiceItem extends AppModel {
	var $name = 'InvoiceItem';
	var $displayField = 'name';
	var $validate = array();
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Invoice' => array(
			'className' => 'Invoices.Invoice',
			'foreignKey' => 'invoice_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CatalogItem' => array(
			'className' => 'Catalogs.CatalogItem',
			'foreignKey' => 'catalog_item_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
?>