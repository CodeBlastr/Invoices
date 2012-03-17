<?php
class Invoice extends AppModel {
	public $name = 'Invoice';
	public $displayField = 'name';
	public $validate = array(
		'invoice_status' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	
	public $belongsTo = array(
		'Project' => array(
			'className' => 'Projects.Project',
			'foreignKey' => 'project_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Contact' => array(
			'className' => 'Contacts.Contact',
			'foreignKey' => 'contact_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Creator' => array(
			'className' => 'Users.User',
			'foreignKey' => 'creator_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

	public $hasMany = array(
		'InvoiceItem' => array(
			'className' => 'Invoices.InvoiceItem',
			'foreignKey' => 'invoice_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'InvoiceTime' => array(
			'className' => 'Invoices.InvoiceTime',
			'foreignKey' => 'invoice_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
	
	public function add($data) {
		$data = $this->cleanData($data);
		
		if ($this->saveAll($data)) {
			return true;
		} else {
			$error = 'Error : ';
			foreach ($this->invalidFields() as $models) :
				if (is_array($models)) : foreach ($models as $err) :
					$error .= $err . ', ';
				endforeach; else :
					$error .= $models;
				endif;
			endforeach;
			throw new Exception($error);
		}
	}
	
	
	public function cleanData($data) {
		
		# clear Invoice Item if its empty
		$i = 0;
		if (!empty($data['InvoiceItem'])) : 
		$data['InvoiceItem'] = array_values($data['InvoiceItem']);  
		foreach ($data['InvoiceItem'] as $invoiceItem) : 
			if (empty($invoiceItem['quantity'])) :
				unset($data['InvoiceItem'][$i]);
			endif;
			$i++;
		endforeach; endif;
		
		if (empty($data['InvoiceItem'])) :
			unset($data['InvoiceItem']);
		endif;
		
		# clear Invoice time if its empty
		$i = 0;
		if (!empty($data['InvoiceTime'])) :
		$data['InvoiceTime'] = array_values($data['InvoiceTime']); 
		foreach ($data['InvoiceTime'] as $invoiceTime) : 
			if (empty($invoiceTime['hours'])) :
				unset($data['InvoiceTime'][$i]);
			endif;
			$i++;
		endforeach; endif;
		
		if (empty($data['InvoiceTime'])) :
			unset($data['InvoiceTime']);
		endif;
		
		# make a name for the invoice
		if (empty($data['Invoice']['name']) && !empty($data['Invoice']['contact_id'])) :
			$contact = $this->Contact->find('first', array('conditions' => array('Contact.id' => $data['Invoice']['contact_id'])));
			$data['Invoice']['name'] = !empty($contact['Contact']['name']) ? $contact['Contact']['name'] . ' ' . $data['Invoice']['number'] : $data['Invoice']['number'];
		endif;
		
		
		if ($data['Invoice']['balance'] < $data['Invoice']['total']) :
			$data['Invoice']['status'] = 'partpaid';
		endif;
		
		if (empty($data['Invoice']['balance']) && intval($data['Invoice']['balance']) === 0) :
			$data['Invoice']['status'] = 'paid';
		elseif (empty($data['Invoice']['balance']) && !empty($data['Invoice']['total'])) :
			$data['Invoice']['balance'] = $data['Invoice']['total'];
			$data['Invoice']['status'] = 'unpaid';
		endif;
		
		
		#for updates you have to submit all hasMany model records or they get deleted
		if (!empty($data['Invoice']['id'])) : 
			$currentItems = $this->InvoiceItem->find('all', array(
				'conditions' => array('InvoiceItem.invoice_id' => $data['Invoice']['id'])));
			$currentItems = Set::extract('/InvoiceItem/id', $currentItems);
			$this->InvoiceItem->deleteAll(array('InvoiceItem.id' => $currentItems));
		endif;
		if (!empty($data['Invoice']['id'])) : 
			$currentTimes = $this->InvoiceTime->find('all', array(
				'conditions' => array('InvoiceTime.invoice_id' => $data['Invoice']['id'])));
			$currentTimes = Set::extract('/InvoiceTime/id', $currentTimes);
			$this->InvoiceTime->deleteAll(array('InvoiceTime.id' => $currentTimes));
		endif;
		
		return $data;
	}

}
?>