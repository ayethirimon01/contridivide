<?php
use CRM_Contridivide_ExtensionUtil as E;

class CRM_Contridivide_Form_CustomReceiptPrefix extends CRM_Core_Form {
  public function buildQuickForm() {
    $this->add('text', 'contrib_prefix', ts('Receipt Prefix (e.g., AWWA or HCSA)'), [
        'size' => 20,
        'maxlength' => 10,
        'placeholder' => 'e.g., AWWA'
    ], FALSE);

    $this->addElement('static', 'prefix_note', '', ts('This prefix will be used in your receipt IDs (e.g., AWWA2025, HCSADIK2025).'));

    // Include Year?
    $this->add('select', 'include_year', ts('Do you want to include Year in Receipt ID?'), [
        'yes' => 'Yes',
        'no' => 'No',
    ], TRUE);

    // Year Type
    $this->add('select', 'year_type', ts('Type of Year to Use'), [
        'calendar' => 'Current Calendar Year',
        'donation_date' => 'Based on Donation Date',
        //'no_date' => 'No Donation Date',
    ]);

    // Regenerate ID Across Year?
    $this->add('select', 'restart_id_each_year', ts('Do you want to restart Receipt ID Each Year?'), [
        'yes' => 'Yes',
        'no' => 'No',
    ], TRUE);

    // Receipt Number Length
    $this->add('text', 'receipt_length', ts('Receipt Number Length (e.g., 6 for 000001)'), [
        'size' => 2,
        'maxlength' => 2,
        'placeholder' => 'e.g., 4',
    ], TRUE);

    //Financial Type
    $this->add('select', 'financial_type', ts('Do you want to include financial type in your receipt ID?'), [
        'yes' => 'Yes',
        'no' => 'No',
    ], TRUE);

    $this->add('text', 'ntdr_prefix', ts('For NTDR: '), [
        'size' => 20,
        'maxlength' => 10,
        'placeholder' => 'e.g., NTDR'
    ]);

     $this->add('text', 'tdr_prefix', ts('For TDR: '), [
        'size' => 20,
        'maxlength' => 10,
        'placeholder' => 'e.g., TDR'
    ]);

     $this->add('text', 'dik_prefix', ts('For DIK: '), [
        'size' => 20,
        'maxlength' => 10,
        'placeholder' => 'e.g., DIK'
    ]);

    // Add submit button:
    $this->addButtons([
      [
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ],
    ]);

   // $this->add('hidden', 'receipt_format', NULL, ['id' => 'receipt_format']);
    $this->add('hidden', 'preview', '', ['id' => 'receipt_format_hidden']);
    $this->add('hidden', 'format_order', '', ['id' => 'receipt_format_order']);


    parent::buildQuickForm();
  }
    /**
   * Load saved values to populate form fields when form is first loaded
   */
  public function setDefaultValues() {
    return [
      'contrib_prefix' => Civi::settings()->get('contrib_prefix'),
      'include_year' => Civi::settings()->get('include_year'),
      'year_type' => Civi::settings()->get('year_type'),
      'restart_id_each_year' => Civi::settings()->get('restart_id_each_year'),
      'receipt_length' => Civi::settings()->get('receipt_length'),
      'receipt_format' => Civi::settings()->get('preview'),
      'financial_type' => Civi::settings()->get('financial_type'),
      'ntdr_prefix' => Civi::settings()->get('ntdr_prefix'),
      'tdr_prefix' => Civi::settings()->get('tdr_prefix'),
      'dik_prefix' => Civi::settings()->get('dik_prefix'),
      
    ];
  }

  /**
   * Process submitted form values and save to settings
   */
  public function postProcess() {
    $values = $this->exportValues();

    Civi::settings()->set('contrib_prefix', trim($values['contrib_prefix']));
    Civi::settings()->set('include_year', $values['include_year']);
    Civi::settings()->set('restart_id_each_year', $values['restart_id_each_year']);
    Civi::settings()->set('year_type', $values['year_type']);
    Civi::settings()->set('receipt_length', $values['receipt_length']);
    Civi::settings()->set('receipt_format', $values['preview']);
    Civi::settings()->set('receipt_format_order', $values['format_order']);
    Civi::settings()->set('financial_type', $values['financial_type']);
    Civi::settings()->set('ntdr_prefix', $values['ntdr_prefix']);
    Civi::settings()->set('tdr_prefix', $values['tdr_prefix']);
    Civi::settings()->set('dik_prefix', $values['dik_prefix']);
    CRM_Core_Session::setStatus(ts('Settings saved.'), ts('Success'), 'success');
  }

  public function preProcess() {
  $receiptFormat = Civi::settings()->get('receipt_format');
  $receiptFormatOrder = Civi::settings()->get('receipt_format_order');
  $this->assign('receipt_format', $receiptFormat);

 // $this->assign('receipt_format', $receiptFormat);
  $this->assign('receiptFormatOrder', $receiptFormatOrder);
}
}
