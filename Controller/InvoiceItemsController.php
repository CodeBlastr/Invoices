<?php
class InvoiceItemsController extends InvoicesAppController {

	public $name = 'InvoiceItems';

	public $uses = 'Invoices.InvoiceItem';

	public function index() {
		$this->helpers[] = 'Utils.Tree'; 
		$invoiceItems = $this->InvoiceItem->find('threaded', array('conditions' => array('InvoiceItem.is_reusable' => 1)));
		$this->set('invoiceItems', $this->request->data = $invoiceItems);
	}

/**
 * View method
 * 
 * @param uuid || string (id or name)
 * @return array
 */
	public function view($id = null) {
		$id = Zuha::is_uuid($id) ? $id : $this->InvoiceItem->field('id', array('InvoiceItem.name' => $id)); // id or name
				
		$this->InvoiceItem->id = $id;
		if (!$this->InvoiceItem->exists()) {
			$this->Session->setFlash(__('Invalid invoice item', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('invoiceItem', $this->request->data = $this->InvoiceItem->read(null, $id));
		return $this->request->data;
	}

	public function sort() {
		// Configure::write('debug', 0);
        if (!empty($this->request->data['order'][1])) {
    		$i = 0;
    		foreach ($this->request->data['order'] as $item) {
    			if (!empty($item['item_id'])) {
    				$data['InvoiceItem']['id'] = $item['item_id'];
    				$data['InvoiceItem']['parent_id'] = $item['parent_id'];
    				if ($this->InvoiceItem->save($data, array('validate' => false))) {
        			    $output[] = $data;   
    				} else {
                        $output['brokeOn'] = $data;
        			    break;
    				}
    			}
    			$i++;
    		}
            $this->set(compact('output'));
        }
		$this->render(false);
	}

	public function add() {
		if (!empty($this->request->data)) {
			$this->InvoiceItem->create();
			if ($this->InvoiceItem->save($this->request->data)) {
				$this->Session->setFlash(__('The invoice item has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The invoice item could not be saved. Please, try again.', true));
			}
		}
		$parents = $this->InvoiceItem->generateTreeList();
		$this->set(compact('parents'));
	}

	public function edit($id = null) {
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
		$products = $this->InvoiceItem->Product->find('list');
		$this->set(compact('invoices', 'products'));
	}

	public function delete($id = null) {
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