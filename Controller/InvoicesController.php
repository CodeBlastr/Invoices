<?php
App::uses('InvoicesAppController', 'Invoices.Controller');

class AppInvoicesController extends InvoicesAppController {

/**
 * Name
 * 
 * var string
 */
	public $name = 'Invoices';

	public $uses = 'Invoices.Invoice';

	public $order = array('number', 'due_date');

	public function index() {
		$this->paginate['order'] = array('created' => 'DESC');
		$this->set('invoices', $this->paginate());
	}


	public function owned() {
		$this->paginate['conditions']['Invoice.owner_id'] = $this->Session->read('Auth.User.id');
		$this->view = 'index';
		return $this->index();
	}


	public function received() {
		$this->paginate['conditions']['Invoice.recipient_id'] = $this->Session->read('Auth.User.id');
		$this->view = 'index';
		return $this->index();
	}

/**
 * View method
 * 
 * @param uuid
 * @return array
 */
	public function view($id = null) {
		$this->Invoice->id = $id;
		if (!$this->Invoice->exists()) {
			throw new NotFoundException(__('Invoice not found'));
		}
		$invoice = $this->Invoice->find('first', array(
			'conditions' => array(
				'Invoice.id' => $id
				),
			'contain' => array(
				'InvoiceTime' => array(
					'order' => 'created'
					),
				'InvoiceItem',
				'Recipient',
				'Owner'
				),
			));
		
		$this->set('invoice', $this->request->data = $invoice);
		$this->set('title_for_layout',  strip_tags($invoice['Invoice']['name']));
		$this->set('page_title_for_layout',  strip_tags($invoice['Invoice']['name']));
	}

	public function add() {
		if ($this->request->is('post')) {
			try {
				$this->Invoice->saveAll($this->request->data);
				$this->Session->setFlash(__('The invoice has been saved', true));
				$this->redirect(array('action' => 'view', $this->Invoice->id));
			} catch(Exception $e) {
				$this->Session->setFlash($e->getMessage());
			}
		}
		$contacts = $this->Invoice->Contact->findCompaniesWithRegisteredUsers('list');
		$invoiceNumber = $this->Invoice->generateInvoiceNumber();
		$dueDate = date('Y-m-d');
		$defaultIntroduction = defined('__INVOICES_DEFAULT_INTRODUCTION') ? __INVOICES_DEFAULT_INTRODUCTION : '';
		$defaultConclusion = defined('__INVOICES_DEFAULT_CONCLUSION') ? __INVOICES_DEFAULT_CONCLUSION : '';
		$this->set('invoiceItems', $invoiceItems = $this->Invoice->InvoiceItem->generateTreeList(array('InvoiceItem.is_reusable' => 1), '{n}.InvoiceItem.name', null, '-- '));
		$this->set(compact('contacts', 'invoiceNumber', 'dueDate', 'defaultIntroduction', 'defaultConclusion'));
	}

	public function edit($id = null) {
		$this->Invoice->id = $id;
		if (!$this->Invoice->exists()) {
			throw new NotFoundException(__('Invoice not found'));
		}
		if (!empty($this->request->data)) {
			try {
				$this->Invoice->saveAll($this->request->data);
				$this->Session->setFlash(__('The invoice has been saved', true));
				$this->redirect(array('action' => 'view', $this->Invoice->id));
			} catch(Exception $e) {
				$this->Session->setFlash($e->getMessage());
			}
		}

		$this->request->data = $this->Invoice->find('first', array(
			'conditions' => array(
				'Invoice.id' => $id
				),
			'contain' => array(
				'InvoiceTime' => array(
					'order' => 'created'
					),
				'InvoiceItem',
				),
			));		
		$this->set( 'reusableItems', $this->Invoice->InvoiceItem->find('all', array('conditions' => array('is_reusable'=>1))) );
		$this->request->data['Invoice']['balance'] = !empty($this->request->data['Invoice']['balance']) ? ZuhaInflector::pricify($this->request->data['Invoice']['balance']) : null;
		$contacts = $this->Invoice->Contact->findCompaniesWithRegisteredUsers('list');
		$this->set(compact('contacts'));
		$this->set('invoiceItems', $invoiceItems = $this->Invoice->InvoiceItem->generateTreeList(array('InvoiceItem.is_reusable' => 1), '{n}.InvoiceItem.name', null, '-- '));
		$this->set('page_title_for_layout', __('Edit %s', $this->request->data['Invoice']['name']));
	}

	public function delete($id = null) {
		$this->Invoice->id = $id;
		if (!$this->Invoice->exists()) {
			throw new NotFoundException(__('Invoice not found'));
		}
		if ($this->Invoice->delete($id)) {
			$this->Session->setFlash(__('Invoice deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Invoice was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}


/** 
 * Generate an invoice from data gathered in another plugin
 * 
 * @param {string}		The plugin identifier generating the plugin
 */
	public function generate($type = 'project') {
		if (!empty($this->request->data)) {

			switch ($type) {
				case 'project':
				case 'timesheet':
					$data = $this->Invoice->generateTimeBasedInvoice($this->request->data);
					break;

				case 'transaction':
					$data = $this->Invoice->generateTransactionInvoice($this->request->data);
					break;
			}
						
			if ($this->Invoice->save($data)) {
				$this->Session->setFlash(__('Invoice generated', true));
				$this->redirect(array('action' => 'edit', $this->Invoice->id));
			} else { 
				$this->Session->setFlash(__('Invoice generation failed.', true));
			}

		}

		switch ($type) {
			case 'project':
				$contacts = $this->Invoice->Project->findContactsWithProjects('list');
				$projects = $this->Invoice->Project->find('all', array(
					'contain' => array(
						'Invoice' => array(
							'fields' => array('Invoice.id', 'Invoice.created', 'Invoice.project_id', 'Invoice.contact_id'),
							'order' => array('created' => 'DESC'),
							'limit' => 1,
							),
						),
					'fields' => array('Project.id', 'Project.name', 'Project.contact_id'),
					));
				$this->set(compact('contacts', 'projects'));
				$this->set('element', 'generate/project');
				break;
			case 'timesheet':
				$this->set('element', 'generate/timesheet');
				break;

			case 'transaction':
				$this->set('element', 'generate/transaction');
				break;
		}
	}
	
	
/**
 * Find all the email address associated with an invoice, and list them in an editable page before sending.
 *
 * @param {int}		The invoice id
 */
	public function email($id = null) {
		$this->Invoice->id = $id;
		if (!$this->Invoice->exists()) {
			throw new NotFoundException(__('Invalid invoice.'));
		}
  		
		if(!empty($this->request->data['Invoice']['recipient'])) {
			$recipients = explode(',', str_replace(' ', '', $this->request->data['Invoice']['recipient']));
			$subject = $this->request->data['Invoice']['subject'];
			$message = $this->request->data['Invoice']['message'];
			foreach ($recipients as $recipient) {
				$this->__sendMail(trim($recipient), $subject, $message, $template = 'default');
			}
			$this->request->data['Invoice']['is_sent'] = $this->request->data['Invoice']['is_sent'] + 1;
			try {
				$this->Invoice->save($this->request->data);
				$this->Session->setFlash(__('Invoice Emailed', true));
				$this->redirect(array('action' => 'view', $this->request->data['Invoice']['id']));
			} catch (Exception $e) {
				$this->Session->setFlash($e->getMessage() . 'Invoice count not updated');
			}
		}
		
		$invoice = $this->Invoice->find('first', array(
			'conditions' => array(
				'Invoice.id' => $id,
				),
			'contain' => array(
				'Contact' => array(
					'Employee' => array(
						'User',
						),
					),
				'Creator',
				),
			));
		if (!empty($invoice['Contact']['Employee'][0])) : 
			foreach ($invoice['Contact']['Employee'] as $employee) :
				$recipients[] = $employee['User']['email'];
			endforeach;
		endif;
		
		if (!empty($invoice['Creator']['email'])) :
			$recipients[] = $invoice['Creator']['email'];
		endif;
		
		$this->set('page_title_for_layout', $invoice['Invoice']['name']);
		$this->set(compact('invoice'));		
		$this->request->data['Invoice']['is_sent'] = $invoice['Invoice']['is_sent'];
		$this->request->data['Invoice']['recipient'] = implode(', ', $recipients);
		$this->request->data['Invoice']['subject'] = 'Invoice : ' . $invoice['Invoice']['name'];
		$url = '<a href="http://' . $_SERVER['HTTP_HOST'] . '/invoices/invoices/view/' . $invoice['Invoice']['id'] .'">' . $_SERVER['HTTP_HOST'] . '/invoices/invoices/view/' . $invoice['Invoice']['id'] . '</a>';
		
		$message = defined('__INVOICES_EMAIL_TEMPLATES') ? unserialize(__INVOICES_EMAIL_TEMPLATES) : '<p>You have an invoice: ' . $url;
		$this->request->data['Invoice']['message'] =  str_replace('{viewLink}', $url, $message['template'][0]); // temporary till we setup multi templates
	}
	
	
	public function dashboard() {
	}
	
	public function pay($id, $amount = null) {
		
		if ( !empty($this->request->data) ) {
			App::uses('TransactionItem', 'TransactionItem.Model');
			$TransactionItem = new TransactionItem;
			//derr...  To Be Continued!
			
		}
		
		$this->set( 'invoice', $this->Invoice->findById($id) );
	}
}

if (!isset($refuseInit)) {
	class InvoicesController extends AppInvoicesController {}
}
