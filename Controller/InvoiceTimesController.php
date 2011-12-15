<?php
class InvoiceTimesController extends AppController {

	public $name = 'InvoiceTimes';
	public $uses = 'Invoices.InvoiceTime';

	function index() {
		$this->InvoiceTime->recursive = 0;
		$this->set('invoiceTimes', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid invoice time', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('invoiceTime', $this->InvoiceTime->read(null, $id));
	}

	function add() {
		if (!empty($this->request->data)) {
			$this->InvoiceTime->create();
			if ($this->InvoiceTime->save($this->request->data)) {
				$this->Session->setFlash(__('The invoice time has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The invoice time could not be saved. Please, try again.', true));
			}
		}
		$projects = $this->InvoiceTime->Project->find('list');
		$tasks = $this->InvoiceTime->Task->find('list');
		$invoices = $this->InvoiceTime->Invoice->find('list');
		$times = $this->InvoiceTime->Time->find('list');
		$this->set(compact('projects', 'tasks', 'invoices', 'times'));
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid invoice time', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->InvoiceTime->save($this->request->data)) {
				$this->Session->setFlash(__('The invoice time has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The invoice time could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->InvoiceTime->read(null, $id);
		}
		$projects = $this->InvoiceTime->Project->find('list');
		$tasks = $this->InvoiceTime->Task->find('list');
		$invoices = $this->InvoiceTime->Invoice->find('list');
		$times = $this->InvoiceTime->Time->find('list');
		$this->set(compact('projects', 'tasks', 'invoices', 'times'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for invoice time', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->InvoiceTime->delete($id)) {
			$this->Session->setFlash(__('Invoice time deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Invoice time was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>