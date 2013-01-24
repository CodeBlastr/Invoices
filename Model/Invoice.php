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
			foreach ($this->invalidFields() as $models) {
				if (is_array($models)) {
					foreach ($models as $err) {
						$error .= $err . ', ';
					}
				} else {
					$error .= $models;
				}
			}
			throw new Exception($error);
		}
	}
	
	
	public function cleanData($data) {
		
		// clear Invoice Item if its empty
		$i = 0;
		if (!empty($data['InvoiceItem'])) {
			$data['InvoiceItem'] = array_values($data['InvoiceItem']);  
			foreach ($data['InvoiceItem'] as $invoiceItem) {
				if (empty($invoiceItem['quantity'])) {
					unset($data['InvoiceItem'][$i]);
				}
				$i++;
			}
		}
		
		if (empty($data['InvoiceItem'])) {
			unset($data['InvoiceItem']);
		}
		
		// clear Invoice time if its empty
		$i = 0;
		if (!empty($data['InvoiceTime'])) {
			$data['InvoiceTime'] = array_values($data['InvoiceTime']); 
			foreach ($data['InvoiceTime'] as $invoiceTime) {
				if (empty($invoiceTime['hours'])) {
					unset($data['InvoiceTime'][$i]);
				}
				$i++;
			}
		}
		
		if (empty($data['InvoiceTime'])) {
			unset($data['InvoiceTime']);
		}
		
//		// Duplicate and format Reusable Items.
//		// We are making a template from the Item they submitted as reusable.
//		if ( !empty($data['InvoiceItem']) ) {
//			$i = 0;
//			foreach ($data['InvoiceItem'] as $invoiceItem) {
//				if ( $invoiceItem['is_reusable'] ) {
//					$reusableInvoiceItem = $invoiceItem;
//					$data['InvoiceItem'][] = $reusableInvoiceItem; // this one will be the template
//					$data['InvoiceItem'][$i]['is_reusable'] = 0;
//				}
//				++$i;
//			}
//		}
		
		// make a name for the invoice
		if (empty($data['Invoice']['name']) && !empty($data['Invoice']['contact_id'])) {
			$contact = $this->Contact->find('first', array('conditions' => array('Contact.id' => $data['Invoice']['contact_id'])));
			$data['Invoice']['name'] = !empty($contact['Contact']['name']) ? $contact['Contact']['name'] . ' ' . $data['Invoice']['number'] : $data['Invoice']['number'];
		}
		
		// auto set the invoice status
		if (empty($data['Invoice']['balance']) && intval($data['Invoice']['balance']) === 0) {
			$data['Invoice']['status'] = 'paid';
		} elseif ($data['Invoice']['balance'] == $data['Invoice']['total']) {
			$data['Invoice']['status'] = 'unpaid';
		} elseif (empty($data['Invoice']['balance']) && !empty($data['Invoice']['total'])){
			$data['Invoice']['balance'] = $data['Invoice']['total'];
			$data['Invoice']['status'] = 'unpaid';
		} elseif ($data['Invoice']['balance'] < $data['Invoice']['total']) {
			$data['Invoice']['status'] = 'partpaid';
		}
		
		
		// for updates you have to submit all hasMany model records or they get deleted
		if (!empty($data['Invoice']['id'])) {
			$currentItems = $this->InvoiceItem->find('all', array(
				'conditions' => array('InvoiceItem.invoice_id' => $data['Invoice']['id'])));
			$currentItems = Set::extract('/InvoiceItem/id', $currentItems);
			$this->InvoiceItem->deleteAll(array('InvoiceItem.id' => $currentItems));
		}
		
		if (!empty($data['Invoice']['id'])) {
			$currentTimes = $this->InvoiceTime->find('all', array(
				'conditions' => array('InvoiceTime.invoice_id' => $data['Invoice']['id'])));
			$currentTimes = Set::extract('/InvoiceTime/id', $currentTimes);
			$this->InvoiceTime->deleteAll(array('InvoiceTime.id' => $currentTimes));
		}
		
		return $data;
	}

	
/**
 * This trims an object, formats it's values if you need to, and returns the data to be merged with the Transaction data.
 * It is a required function for models that will be for sale via the Transactions Plugin.
 * 
 * @param string $foreignKey
 * @return array The necessary fields to add a Transaction Item
 */
	public function mapTransactionItem( $foreignKey ) {
	    
	    $itemData = $this->find('first', array('conditions' => array('id' => $foreignKey)));
	    
	    $fieldsToCopyDirectly = array('name');
	    
	    foreach($itemData['Invoice'] as $k => $v) {
			if(in_array($k, $fieldsToCopyDirectly)) {
				$return['TransactionItem'][$k] = $v;
			}
	    }
	    
	    return $return;
	}
	
/**
 * Duplicate and format Reusable Items.
 * We are making a template from the Item they submitted as reusable.
 * @param array $data
 */
//	public function disassociateReusableItems($data) {
//		if ( !empty($data['InvoiceItem']) ) {
//			$i = 0;
//			foreach ($data['InvoiceItem'] as $invoiceItem) {
//				if ( $invoiceItem['is_reusable'] ) {
//					$data['InvoiceItem'][$i]['invoice_id'] = null;
//				}
//				++$i;
//			}
//			$this->InvoiceItem->saveAll($data);
//		}
//	}
	
}