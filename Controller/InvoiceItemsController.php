<?php
class InvoiceItemsController extends AppController {

	public $name = 'InvoiceItems';
	public $uses = 'Invoices.InvoiceItem';

	function index() {
		$this->InvoiceItem->recursive = 0;
		$this->set('invoiceItems', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid invoice item', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('invoiceItem', $this->InvoiceItem->read(null, $id));
	}

	function add() {
		if (!empty($this->request->data)) {
			$this->InvoiceItem->create();
			if ($this->InvoiceItem->save($this->request->data)) {
				$this->Session->setFlash(__('The invoice item has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The invoice item could not be saved. Please, try again.', true));
			}
		}
		$invoices = $this->InvoiceItem->Invoice->find('list');
		$catalogItems = $this->InvoiceItem->CatalogItem->find('list');
		$this->set(compact('invoices', 'catalogItems'));
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid invoice item', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->InvoiceItem->save($this->request->data)) {
				$this->Session->setFlash(__('The invoice item has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The invoice item could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->InvoiceItem->read(null, $id);
		}
		$invoices = $this->InvoiceItem->Invoice->find('list');
		$catalogItems = $this->InvoiceItem->CatalogItem->find('list');
		$this->set(compact('invoices', 'catalogItems'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for invoice item', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->InvoiceItem->delete($id)) {
			$this->Session->setFlash(__('Invoice item deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Invoice item was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>