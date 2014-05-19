<?php
class InvoiceItem extends AppModel {
	
	public $name = 'InvoiceItem';
	
	public $displayField = 'name';
	
	public $validate = array();
        
 /**
  * Acts as
  * 
  * @var array
  */
    public $actsAs = array('Tree');
	
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
	
	
	public function beforeSave($options = array()) {
		if (!empty($this->data['InvoiceItem']['is_reusable'])) {
			$this->reusableItem = $this->data;
			$this->data['InvoiceItem']['is_reusable'] = '0';
		}
		return parent::beforeSave($options);
	}
	
	public function afterSave($created) {
		if (!empty($this->reusableItem)) {
			$this->reusableItem['InvoiceItem']['id'] = null;
			$this->reusableItem['InvoiceItem']['invoice_id'] = null;
			$this->reusableItem['InvoiceItem']['creator_id'] = CakeSession::read('Auth.User.id');
			$this->create();
			$this->save($this->reusableItem, array('callbacks' => false)); // template version of invoice item
		}
		return parent::afterSave($created);
	}
}