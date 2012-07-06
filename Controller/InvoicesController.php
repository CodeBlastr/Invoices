<?php
class InvoicesController extends AppController {

	public $name = 'Invoices';
	public $uses = 'Invoices.Invoice';
	public $order = array('number', 'due_date');

	function index($status = array('unpaid', 'partpaid')) {
		$this->paginate = array(
			'conditions' => array(
				'Invoice.status' => $status,
				),
			'fields' => array(
				'id',
				'name',
				'status',
				'total',
				'balance',
				'due_date',
				),
			'order' => array(
				'created DESC',
				),
			);
		$this->set('invoices', $this->paginate());
		
		$this->set('displayName', 'name');
		$this->set('displayDescription', ''); 
		$pageActions = array(array(
			'linkText' => 'Add',
			'linkUrl' => array(
				'plugin' => 'invoices',
				'controller' => 'invoices',
				'action' => 'add',
				),
			),array(
			'linkText' => 'Paid',
			'linkUrl' => array(
				'plugin' => 'invoices',
				'controller' => 'invoices',
				'action' => 'index',
				'paid',
				),
			),array(
			'linkText' => 'Generate',
			'linkUrl' => array(
				'plugin' => 'invoices',
				'controller' => 'invoices',
				'action' => 'generate',
				),
			));
		$this->set(compact('pageActions')); 
	}

	function view($id = null) {
		$invoice = $this->Invoice->find('first', array(
			'contain' => array(
				'InvoiceTime',
				'InvoiceItem',
				),
			'conditions' => array(
				'Invoice.id' => $id
				)
			)
		);
		
		if (!empty($invoice)) {
			$this->set(compact('invoice', 'trackedHoursSum', 'percentComplete'));
		} else {
			$this->Session->setFlash(__('Invalid Invoice.', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('title_for_layout',  strip_tags($invoice['Invoice']['name']));
	}

	function add() {
		if (!empty($this->request->data)) {
			try {
				$this->Invoice->add($this->request->data);
				$this->Session->setFlash(__('The invoice has been saved', true));
				$this->redirect(array('action' => 'view', $this->Invoice->id));
			} catch(Exception $e) {
				$this->Session->setFlash($e->getMessage());
			}
		}
		
		$contacts = $this->Invoice->Contact->findCompaniesWithRegisteredUsers('list');
		$invoiceNumber = $this->_generateInvoiceNumber();
		$dueDate = date('Y-m-d');
		$defaultIntroduction = defined('__INVOICES_DEFAULT_INTRODUCTION') ? __INVOICES_DEFAULT_INTRODUCTION : '';
		$defaultConclusion = defined('__INVOICES_DEFAULT_CONCLUSION') ? __INVOICES_DEFAULT_CONCLUSION : '';
		$this->set(compact('contacts', 'invoiceNumber', 'dueDate', 'defaultIntroduction', 'defaultConclusion'));
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid invoice', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			try {
				$this->Invoice->add($this->request->data);
				$this->Session->setFlash(__('The invoice has been saved', true));
				$this->redirect(array('action' => 'view', $this->Invoice->id));
			} catch(Exception $e) {
				$this->Session->setFlash($e->getMessage());
			}
		}
		if (empty($this->request->data)) {
			$this->Invoice->contain(array('InvoiceTime', 'InvoiceItem'));
			$this->request->data = $this->Invoice->read(null, $id);
			$this->request->data['Invoice']['balance'] = !empty($this->request->data['Invoice']['balance']) ? ZuhaInflector::pricify($this->request->data['Invoice']['balance']) : null;
		}
		$contacts = $this->Invoice->Contact->findCompaniesWithRegisteredUsers('list');
		$this->set(compact('contacts'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for invoice', true));
			$this->redirect(array('action'=>'index'));
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
	function generate($type = 'project') {
		if (!empty($this->request->data)) :
			$projectIds = array_values(array_filter($this->request->data['Invoice']['project_id'])); //reindex & filter zero values
			$project = $this->Invoice->Project->find('first', array('conditions' => array('Project.id' => $projectIds[0])));
			$conditions['TimesheetTime.project_id'] = $projectIds;
			$conditions['TimesheetTime.started_on >='] = !empty($this->request->data['Invoice']['start_date']) ? $this->request->data['Invoice']['start_date'] : '0000-00-00 00:00:00';
			$conditions['TimesheetTime.started_on <='] = !empty($this->request->data['Invoice']['end_date']) ? date('Y-m-d 99:99:99', strtotime($this->request->data['Invoice']['end_date'])) : '9999-99-99 99:99:99';
			$times = $this->Invoice->Project->TimesheetTime->find('all', array(
				'conditions' => $conditions,
				'contain' => array(
					'Task',
					'ProjectIssue',
					),
				'order' => array(
					'TimesheetTime.created DESC',
					),
				));
			
			$data['Invoice']['name'] = strip_tags($project['Project']['displayName']) . ' ' . $this->_generateInvoiceNumber();
			$data['Invoice']['number'] = $this->_generateInvoiceNumber();
			$data['Invoice']['status'] = 'unpaid';
			$data['Invoice']['introduction'] = defined('__INVOICES_DEFAULT_INTRODUCTION') ? __INVOICES_DEFAULT_INTRODUCTION : '';
			$data['Invoice']['conclusion'] = defined('__INVOICES_DEFAULT_CONCLUSION') ? __INVOICES_DEFAULT_CONCLUSION : '';
			$data['Invoice']['due_date'] = date('Y-m-d');
			$data['Invoice']['contact_id'] = $this->request->data['Invoice']['contact_id'];
			$data['Invoice']['project_id'] = $projectIds[0]; // didn't think ahead for having an invoice relate to multiple projects (but I really don't want to create another new habtm db table in order to just relate invoices to projects)
			$rate = defined('__INVOICES_DEFAULT_RATE') ? __INVOICES_DEFAULT_RATE : '0';
			$rate = !empty($this->request->data['Invoice']['rate']) ? $this->request->data['Invoice']['rate'] : '0'; // over write default if provided
			
			$i=0; $total=0; foreach ($times as $invTime) :
				$data['InvoiceTime'][$i]['name'] = !empty($invTime['Task']['name']) ? $invTime['Task']['name'] : $invTime['ProjectIssue']['name'];  // support the deprecated project_issues table
				$data['InvoiceTime'][$i]['notes'] = date('M j, Y', strtotime($invTime['TimesheetTime']['created'])) . ', ' .$invTime['TimesheetTime']['comments'];
				$data['InvoiceTime'][$i]['rate'] = $rate;
				$data['InvoiceTime'][$i]['hours'] = $invTime['TimesheetTime']['hours'];
				$data['InvoiceTime'][$i]['project_id'] = $projectIds[0];
				$data['InvoiceTime'][$i]['task_id'] = $invTime['Task']['id'];
				$data['InvoiceTime'][$i]['time_id'] = $invTime['TimesheetTime']['id'];
				$lineTotal = $rate * $invTime['TimesheetTime']['hours'];
				$total =  $total + $lineTotal;
				$i++;
			endforeach;
			$data['Invoice']['total'] = $total;
			$data['Invoice']['balance'] = $total;
						
			if ($this->Invoice->add($data)) : 
				$this->Session->setFlash(__('Invoice generated', true));
				$this->redirect(array('action' => 'edit', $this->Invoice->id));
			else : 
				$this->Session->setFlash(__('Invoice generation failed.', true));
			endif;
		endif;
		
		if ($type == 'timesheet') {
			$this->set('element', 'generate/timesheet');
		} else {
			$contacts = $this->Invoice->Project->findContactsWithProjects('list');
			$projects = $this->Invoice->Project->find('all', array(
				'contain' => array(
					'Invoice' => array(
						'fields' => array(
							'Invoice.id',
							'Invoice.created',
							'Invoice.project_id',
							'Invoice.contact_id',
							),
						'order' => array(
							'created' => 'DESC',
							),
						'limit' => 1,
						),
					),
				'fields' => array(
					'Project.id',
					'Project.displayName',
					'Project.contact_id',
					),
				));
			$this->set(compact('contacts', 'projects'));
			$this->set('element', 'generate/project');
		}
	}
	
	
	/**
	 * Find all the email address associated with an invoice, and list them in an editable page before sending.
	 *
	 * @param {int}		The invoice id
	 */
	function email($id = null) {
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
	
	function _generateInvoiceNumber() {
		return str_pad($this->Invoice->find('count') + 1, 7, '0', STR_PAD_LEFT);
	}
	
	function dashboard() {
	}
}
?>