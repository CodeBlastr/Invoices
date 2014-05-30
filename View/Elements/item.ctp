<?php

//debug($data);
//debug($depth);
//debug($hasChildren);
//debug($numberOfDirectChildren);
//debug($numberOfTotalChildren);
//debug($firstChild);
//debug($lastChild);
//debug($hasVisibleChildren);
//debug($plugin); 

$class = $this->request->params['controller'] == 'invoice_items'  && $this->request->params['action'] == 'view' ? $param['class'] = 'showClick' : '';

$name = $this->request->params['controller'] == 'invoice_items'  && $this->request->params['action'] == 'view' ? $param['name'] = 'InvoiceItemForm' : '';

$this->Tree->addItemAttribute('id', false, 'li_' . $data['InvoiceItem']['id']);
$this->Tree->addItemAttribute('data-identifier', false, $data['InvoiceItem']['id']);

echo '<div class="item">';
echo $this->Html->link($data['InvoiceItem']['name'], $data['InvoiceItem']['name'], array('class' => 'toggleClick', 'data-target' => '#form' . $data['InvoiceItem']['id'])); 

    echo '<div id="form' . $data['InvoiceItem']['id'] . '">';
    echo $this->Form->create('InvoiceItem', array('url' => array('plugin' => 'invoices', 'controller' => 'invoice_items','action' => 'edit'), 'class' => 'form-inline'));
    echo $this->Form->input('InvoiceItem.id', array('type' => 'hidden', 'value' => $data['InvoiceItem']['id']));
    echo $this->Form->input('InvoiceItem.parent_id', array('type' => 'hidden', 'value' => $data['InvoiceItem']['parent_id']));
    echo $this->Form->input('InvoiceItem.name', array('value' => $data['InvoiceItem']['name']));
    echo $this->Form->input('InvoiceItem.description', array('type' => 'text', 'value' => $data['InvoiceItem']['description']));
	echo $this->Form->input('InvoiceItem.unit_cost', array('value' => $data['InvoiceItem']['unit_cost']));
    echo $this->Form->end(__('Save'));
    echo $this->Form->postlink(__('Delete'), array('plugin' => 'invoices', 'controller' => 'invoice_items', 'action' => 'delete', $data['InvoiceItem']['id']), array('class' => 'btn btn-danger'), __('Are you sure you want to delete the %s menu item?', $data['InvoiceItem']['item_text']));
    
    echo '</div>';

echo '</div>';