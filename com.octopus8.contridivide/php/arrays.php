<?php 

$condivArrays = array(
	//Receipt Details Custom Group'
	array(
		'type' => 'CustomGroup',
		'name' => 'contridiv_group',
		'params' => array(
			'name' => 'contridiv_group',
			'title' => 'Receipt Details',
			'extends' => 'Contribution',
			'style' => 'Inline',
			'is_active' => TRUE,
		),
	),
	//Receipt ID Custom Field
	array(
		'type' => 'CustomField',
		'name' => 'contridiv_receiptID',
		'params' => array(
			'custom_group_id.name' => 'contridiv_group',
			'name' => 'contridiv_receiptID',
			'label' => 'Receipt ID',
			'data_type' => 'String',
			'html_type' => 'Text',
			'is_searchable' => TRUE,
			'is_view' => TRUE,
		),
	),
);