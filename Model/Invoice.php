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
				)
			)
		);
        
 /**
  * Acts as
  * 
  * @var array
  */
    public $actsAs = array(
     	'Metable',
		);
	
	public $belongsTo = array(
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
		'Owner' => array(
			'className' => 'Users.User',
			'foreignKey' => 'owner_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Recipient' => array(
			'className' => 'Users.User',
			'foreignKey' => 'recipient_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
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
	
	
	public function __construct($id = false, $table = null, $ds = null) {
    	parent::__construct($id, $table, $ds);
		if (CakePlugin::loaded('Projects')) {
			$this->belongsTo['Project'] = array(
				'className' => 'Projects.Project',
				'foreignKey' => 'project_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
				);
		}
	}
	
	
	public function saveAll($data = null, $options = array()) {
		$data = $this->cleanData($data);
		return parent::saveAll($data, $options);
	}
	
	public function add($data) {
		return $this->saveAll($data);
		// $data = $this->cleanData($data);
// 		
		// if ($this->saveAll($data)) {
			// return true;
		// } else {
			// $error = 'Error : ';
			// foreach ($this->invalidFields() as $models) {
				// if (is_array($models)) {
					// foreach ($models as $err) {
						// $error .= $err . ', ';
					// }
				// } else {
					// $error .= $models;
				// }
			// }
			// throw new Exception($error);
		// }
	}
	
	
	public function cleanData($data) {
		
		$data = $this->_cleanItems($data);
		
		$data = $this->_calculateTotal($data);
		
		// make a name for the invoice
		if (empty($data['Invoice']['name']) && !empty($data['Invoice']['contact_id'])) {
			$contact = $this->Contact->find('first', array('conditions' => array('Contact.id' => $data['Invoice']['contact_id'])));
			$data['Invoice']['name'] = !empty($contact['Contact']['name']) ? $contact['Contact']['name'] . ' ' . $data['Invoice']['number'] : $data['Invoice']['number'];
		}
		
		// make a name for the invoice
		if (empty($data[$this->alias]['name']) && !empty($data[$this->alias]['number'])) {
			$data[$this->alias]['name'] = $data[$this->alias]['number'];
		}
		
		// auto set the invoice status
		if (empty($data['Invoice']['status'])) {
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

	public function _calculateTotal($data = array()) {
		if (empty($data[$this->alias]['total'])) {
			$data[$this->alias]['total'] = 0;
			// add up invoice items
			if (!empty($data['InvoiceItem'])) {
				$data['InvoiceItem'] = array_values($data['InvoiceItem']);  
				for ($i = 0; $i < count($data['InvoiceItem']); $i++) {
					if (!empty($data['InvoiceItem'][$i]['quantity']) && !empty($data['InvoiceItem'][$i]['unit_cost'])) {
						$subTotal = $data['InvoiceItem'][$i]['quantity'] * $data['InvoiceItem'][$i]['unit_cost'];
						$data[$this->alias]['total'] = $data[$this->alias]['total'] + $subTotal;
					}
				}
			}
			// add up invoice items
			if (!empty($data['InvoiceTime'])) {
				$data['InvoiceTime'] = array_values($data['InvoiceTime']);  
				for ($i = 0; $i < count($data['InvoiceTime']); $i++) {
					if (!empty($data['InvoiceTime'][$i]['hours']) && !empty($data['InvoiceTime'][$i]['rate'])) {
						$subTotal = $data['InvoiceTime'][$i]['hours'] * $data['InvoiceTime'][$i]['rate'];
						$data[$this->alias]['total'] = $data[$this->alias]['total'] + $subTotal;
					}
				}
			}
		}
		if (empty($data[$this->alias]['balance'])) {
			$data[$this->alias]['balance'] = $data[$this->alias]['total'];
		}
		return $data;
	}

	public function _cleanItems($data = array()) {
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
		return $data;
	}


	public function generateTransactionInvoice($requestData) {
		$data = null;

		App::uses('Transaction', 'Transactions.Model');
		$Transaction = new Transaction;
		$transaction = $Transaction->find('first', array(
			'conditions' => array('Transaction.id' => $requestData['Transaction']['id']),
			'contain' => array(
				'TransactionItem' => array(
					'order' => array('TransactionItem.name' => 'ASC')
				)
			)
		));

		App::uses('User', 'Users.Model');
		$User = new User;
		$user = $User->find('first', array(
			'conditions' => array('User.id' => $transaction['Transaction']['customer_id']),
			'contain' => array(
				'Contact' => array(
					'ContactAddress'
				)
			)
		));

		$data['Invoice']['name'] = strip_tags($project['Project']['name']) . ' ' . $this->generateInvoiceNumber();
		$data['Invoice']['number'] = $this->generateInvoiceNumber();
		$data['Invoice']['status'] = 'unpaid';
		$data['Invoice']['introduction'] = defined('__INVOICES_DEFAULT_INTRODUCTION') ? __INVOICES_DEFAULT_INTRODUCTION : '';
		$data['Invoice']['conclusion'] = defined('__INVOICES_DEFAULT_CONCLUSION') ? __INVOICES_DEFAULT_CONCLUSION : '';
		$data['Invoice']['due_date'] = date('Y-m-d');
		$data['Invoice']['contact_id'] = $user['Contact']['id'];

		foreach ( $transaction['TransactionItem'] as $transactionItem ) {
			$data['InvoiceItem'][] = array(
				'name' => $transactionItem['name'],
				'cost' => $transactionItem['price'],
				'quantity' => $transactionItem['quantity']
			);
		}

		$data['Invoice']['total'] = $data['Invoice']['balance'] = $transaction['TransactionItem']['total'];

		return $data;
	}


/**
 * Used to generate Invoice data from Projects and Timesheets
 * @param array $requestData
 * @return array Invoice data to be saved
 */
	public function generateTimeBasedInvoice($requestData) {
		$data = null;
		
		$data['Invoice']['name'] = $this->generateInvoiceNumber();
		
		if (CakePlugin::loaded('Projects')) {
			// this should be moved elsewhere 5/27/2014 RK
			$projectIds = array_values(array_filter($requestData['Invoice']['project_id'])); //reindex & filter zero values
			$project = $this->Project->find('first', array('conditions' => array('Project.id' => $projectIds[0])));
			$conditions['TimesheetTime.project_id'] = $projectIds;

			$conditions['TimesheetTime.started_on >='] = !empty($requestData['Invoice']['start_date']) ? $requestData['Invoice']['start_date'] : '0000-00-00 00:00:00';
			$conditions['TimesheetTime.started_on <='] = !empty($requestData['Invoice']['end_date']) ? date('Y-m-d 99:99:99', strtotime($requestData['Invoice']['end_date'])) : '9999-99-99 99:99:99';
			$times = $this->Project->TimesheetTime->find('all', array(
				'conditions' => $conditions,
				'contain' => array(
					'Task',
					'ProjectIssue',
				),
				'order' => array(
					'TimesheetTime.created DESC',
				),
			));
			$data['Invoice']['name'] = strip_tags($project['Project']['name']) . ' ' . $this->generateInvoiceNumber();
			$data['Invoice']['project_id'] = $projectIds[0]; // didn't think ahead for having an invoice relate to multiple projects (but I really don't want to create another new habtm db table in order to just relate invoices to projects)
		}

		$data['Invoice']['number'] = $this->generateInvoiceNumber();
		$data['Invoice']['status'] = 'unpaid';
		$data['Invoice']['introduction'] = defined('__INVOICES_DEFAULT_INTRODUCTION') ? __INVOICES_DEFAULT_INTRODUCTION : '';
		$data['Invoice']['conclusion'] = defined('__INVOICES_DEFAULT_CONCLUSION') ? __INVOICES_DEFAULT_CONCLUSION : '';
		$data['Invoice']['due_date'] = date('Y-m-d');
		$data['Invoice']['contact_id'] = $requestData['Invoice']['contact_id'];
		$rate = defined('__INVOICES_DEFAULT_RATE') ? __INVOICES_DEFAULT_RATE : '0';
		$rate = !empty($requestData['Invoice']['rate']) ? $requestData['Invoice']['rate'] : '0'; // over write default if provided

		$i = 0;
		$total = 0;
		foreach ( $times as $invTime ) {
			$data['InvoiceTime'][$i]['name'] = !empty($invTime['Task']['name']) ? $invTime['Task']['name'] : $invTime['ProjectIssue']['name'];  // support the deprecated project_issues table
			$data['InvoiceTime'][$i]['notes'] = date('M j, Y', strtotime($invTime['TimesheetTime']['created'])) . ', ' . $invTime['TimesheetTime']['comments'];
			$data['InvoiceTime'][$i]['rate'] = $rate;
			$data['InvoiceTime'][$i]['hours'] = $invTime['TimesheetTime']['hours'];
			$data['InvoiceTime'][$i]['project_id'] = $projectIds[0];
			$data['InvoiceTime'][$i]['task_id'] = $invTime['Task']['id'];
			$data['InvoiceTime'][$i]['time_id'] = $invTime['TimesheetTime']['id'];
			$data['InvoiceTime'][$i]['created'] = $invTime['TimesheetTime']['created'];
			$lineTotal = $rate * $invTime['TimesheetTime']['hours'];
			$total = $total + $lineTotal;
			$i++;
		}
		$data['Invoice']['total'] = $total;
		$data['Invoice']['balance'] = $total;

		return $data;
	}


	public function generateInvoiceNumber() {
		return str_pad($this->find('count') + 1, 7, '0', STR_PAD_LEFT);
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
