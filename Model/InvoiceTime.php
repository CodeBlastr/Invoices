<?php
class InvoiceTime extends AppModel {
	
	public $name = 'InvoiceTime';
	
	public $displayField = 'name';
	
	public $validate = array();
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	public $belongsTo = array(
		'Invoice' => array(
			'className' => 'Invoices.Invoice',
			'foreignKey' => 'invoice_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
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
		if (CakePlugin::loaded('Tasks')) {
			$this->belongsTo['Task'] = array(
				'className' => 'Tasks.Task',
				'foreignKey' => 'task_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
				);
		}
		if (CakePlugin::loaded('Timesheets')) {
			$this->belongsTo['Time'] = array(
				'className' => 'Timesheets.TimesheetTime',
				'foreignKey' => 'time_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
				);
		}
	}
}