{* CustomReceiptPrefix.tpl *}

<div class="crm-block crm-form-block crm-custom-receipt-prefix-form-block">
  <h3>{ts}Custom Receipt ID Prefix Settings{/ts}</h3>
  <form id="CustomReceiptPrefix" action="{$action}" method="post">
    <div class="form-item">
      <label for="contrib_prefix">{ts}Receipt Prefix (e.g., AWWA or HCSA){/ts}</label>
      <input type="text" id="contrib_prefix" name="contrib_prefix" value="{$form.contrib_prefix.value}" />
    </div>

    <div class="form-item">
      <em>{ts}This prefix will be used in your receipt IDs (e.g., AWWA2025, HCSADIK2025).{/ts}</em>
    </div>

    <div class="form-item">
      <label for="receipt_length">{ts}Receipt Number Length (e.g., 6 for 000001){/ts}</label>
      <input type="number" id="receipt_length" name="receipt_length" min="1" max="10" oninput="updatePreview()" required value="{$form.receipt_length.value}">
    </div>

    <div class="form-item">
      <label for="include_year">{ts}Do you want to include Year in Receipt ID?{/ts}</label>
      <select id="include_year" name="include_year" onchange="updatePreview()" required>
        <option value="yes" {if $form.include_year.value eq "yes"}selected="selected"{/if}>{ts}Yes{/ts}</option>
        <option value="no" {if $form.include_year.value eq "no"}selected="selected"{/if}>{ts}No{/ts}</option>
      </select>
    </div>

    <div class="form-item" id="year_type_section" style="display: none;">
      <label for="year_type">{ts}Use Which Year?{/ts}</label>
      <select id="year_type" name="year_type" onchange="updatePreview()">
        <option value="calendar" {if $form.year_type.value|@reset eq "calendar"}selected="selected"{/if}>{ts}Current Calendar Year{/ts}</option>
        <option value="donation_date" {if $form.year_type.value|@reset eq "donation_date"}selected="selected"{/if}>{ts}Donation Date Year{/ts}</option>
      </select>
    </div>

     <div class="form-item" id="restart_id_section" style="display: none;">
  <label for="restart_id_each_year">{ts}Do you want to restart Receipt ID Each Year?{/ts}</label>
  <select id="restart_id_each_year" name="restart_id_each_year" onchange="updatePreview()" required>
    <option value="yes" {if $form.restart_id_each_year.value|@reset eq "yes"}selected="selected"{/if}>{ts}Yes – Start new id each year (e.g., 2024001, 2025001){/ts}</option>
    <option value="no" {if $form.restart_id_each_year.value|@reset eq "no"}selected="selected"{/if}>{ts}No – Continue sequentially across years (e.g., 2024001, 2025002){/ts}</option>
  </select>
</div>

{* New option*}
<div class="form-item" id="financial_type_wrapper">
  <label for="financial_type">{ts}Do you want to include financial type in your receipt ID?{/ts}</label>
  <select id="financial_type" name="financial_type" onchange="updatePreview()">
    <option value="yes" {if $form.financial_type.value eq "yes"}selected="selected"{/if}>{ts}Yes{/ts}</option>
    <option value="no" {if $form.financial_type.value eq "no"}selected="selected"{/if}>{ts}No{/ts}</option>
  </select>
</div>

<!-- Wrapped in a container to control visibility -->
<div id="financial_type_fields" style="display: none;">
  <div class="form-item">
    <label for="ntdr_prefix">{ts}For NTDR: {/ts}</label>
    <input type="text" id="ntdr_prefix" name="ntdr_prefix" value="{$form.ntdr_prefix.value}" />
  </div>

  <div class="form-item">
    <label for="tdr_prefix">{ts}For TDR: {/ts}</label>
    <input type="text" id="tdr_prefix" name="tdr_prefix" value="{$form.tdr_prefix.value}" />
  </div>

  <div class="form-item">
    <label for="dik_prefix">{ts}For DIK: {/ts}</label>
    <input type="text" id="dik_prefix" name="dik_prefix" value="{$form.dik_prefix.value}" />
  </div>
</div>

{* New Item *}
<h4>{ts}Reorder your receipt{/ts}</h4>
<div id="formatBuilder" class="drop-zone" data-saved-order="{$receiptFormatOrder|escape:'html'}"></div>


<div class="form-item">
  <label>{ts}Preview:{/ts}</label>
  <div id="receipt_example" class="readonly-box">Example: APPLE-2025-00001</div>
 {* Safely render the hidden preview field if it exists *}
  <input type="hidden" id="receipt_format_hidden" name="receipt_format_hidden" />
<input type="hidden" id="receipt_format_order" name="receipt_format_order" />
</div>


    {* FOOTER *}
  <div class="crm-submit-buttons">
      {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
  </form>
</div>


{* CSS *}
<style>
.drop-zone {
  min-height: 50px;
  border: 2px dashed #ccc;
  padding: 10px;
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  background: #f9f9f9;
}

.format-block {
  background: #e2e2e2;
  padding: 6px 12px;
  border-radius: 4px;
  cursor: move;
  user-select: none;
  font-weight: bold;
}

.delimiter-btns {
  margin-top: 10px;
}

.delimiter-btns button {
  margin-right: 6px;
}

.readonly-box {
  background: #f4f4f4;
  padding: 8px 12px;
  border: 1px solid #ccc;
  font-family: monospace;
  margin-top: 10px;
}
</style>



{literal}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>

function updatePreview() {
  const prefixInput = document.querySelector('[name="contrib_prefix"]');
  const receiptLengthInput = document.getElementById('receipt_length');

  const prefix = prefixInput ? prefixInput.value : 'PREFIX';
  const length = receiptLengthInput ? parseInt(receiptLengthInput.value) : 6;

  const includeYear = document.getElementById('include_year').value === 'yes';
  const yearType = document.getElementById('year_type').value;
  const includeFinancialType = document.getElementById('financial_type').value === 'yes';
  const ntdr_prefix = document.querySelector('[name="ntdr_prefix"]').value;
  const tdr_prefix = document.querySelector('[name="tdr_prefix"]').value;
  const dik_prefix = document.querySelector('[name="dik_prefix"]').value;

  // Show/hide year type field based on selection
  document.getElementById('year_type_section').style.display = includeYear ? 'block' : 'none';
  document.getElementById('restart_id_section').style.display = includeYear ? 'block' : 'none';

  // New: Show/hide financial type fields
  const financialFields = document.getElementById('financial_type_fields');
  financialFields.style.display = includeFinancialType ? 'block' : 'none';

 // const year = new Date().getFullYear();
 const previewYear = includeYear ? (yearType === 'donation_date' ? 'YYYY' : new Date().getFullYear()) : '';
  const sampleYear = includeYear ? (yearType === 'donation_date' ? 'YYYY' : year) : '';
  const number = String(1).padStart(length, '0');
  const formatBlocks = document.querySelectorAll('#formatBuilder .format-block');
  let previewParts = [];

  formatBlocks.forEach(block => {
    const value = block.dataset.value;

    if (value === 'prefix') {
      const prefixInput = document.querySelector('[name="contrib_prefix"]');
      const prefix = prefixInput ? prefixInput.value : 'PREFIX';
      previewParts.push(prefix);
    } else if (value === 'year') {
     // const yearType = document.getElementById('year_type').value;
      //const year = yearType === 'donation_date' ? 'YYYY' : new Date().getFullYear();
     // previewParts.push(year);
     previewParts.push(previewYear);

    } else if (value === 'number') {
      const receiptLengthInput = document.getElementById('receipt_length');
      const length = receiptLengthInput ? parseInt(receiptLengthInput.value) : 6;
      const number = String(1).padStart(length, '0');
      previewParts.push(number);
    } else if (value === 'financial_type') {
      previewParts.push('DIK');
    } else {
      // Delimiter case like "-", "/", "."
      previewParts.push(value);
    }
  });

  const preview = previewParts.join('');
  document.getElementById('receipt_example').innerText = "Your receipt IDs example (e.g., " + preview + ")";

  // value in hidden field so it gets submitted
  document.getElementById('receipt_format_hidden').value = preview;

  const formatOrderParts = [];
  formatBlocks.forEach(block => {
  const value = block.dataset.value;
  if (['prefix', 'year', 'number', 'financial_type'].includes(value)) {
    formatOrderParts.push(value);
  } else {
    formatOrderParts.push(value); // Include delimiter too
  }
});
document.getElementById('receipt_format_order').value = formatOrderParts.join(',');

}

document.getElementById('CustomReceiptPrefix').addEventListener('submit', function(e) {
  updatePreview();
});



document.addEventListener('DOMContentLoaded', () => {
  // Call once on page load
  updatePreview();
  const formatBuilder = document.getElementById('formatBuilder');

  function getFieldValues() {
  const contribInput = document.querySelector('[name="contrib_prefix"]');
  const includeYearSelect = document.getElementById('include_year');
  const financialTypeSelect = document.getElementById('financial_type');
  const receiptNumberLength = document.getElementById('receipt_length');
  const ntdrPrefix = document.getElementById('ntdr_prefix');
  const tdrPrefix = document.getElementById('tdr_prefix');
  const dikPrefix = document.getElementById('dik_prefix');

  return {
    prefix: contribInput?.value || '',
    year: includeYearSelect?.value === 'yes' ? new Date().getFullYear() : null,
    //length: receiptNumberLength,
    ntdr: ntdrPrefix?.value || '',
    tdr: tdrPrefix?.value || '',
    dik: dikPrefix?.value || '',
    number: '00001',
    financial_type: financialTypeSelect?.value === 'yes' ? 'DIK' : null

  };
}



function buildBlocks() {
  const formatBuilder = document.getElementById('formatBuilder');
  formatBuilder.innerHTML = ''; // Clear existing

  const includeYear = document.getElementById('include_year').value === 'yes';
  const includeFinancialType = document.getElementById('financial_type').value === 'yes';

  const savedOrderRaw = formatBuilder.dataset.savedOrder || window.savedFormatOrder;
  let savedOrder = savedOrderRaw && savedOrderRaw.trim() ? savedOrderRaw.split(',') : ['prefix', 'year', 'number', 'financial_type'];

  // Filter out blocks based on checkbox selections
  savedOrder = savedOrder.filter(key => {
    if (key === 'year' && !includeYear) return false;
    if (key === 'financial_type' && !includeFinancialType) return false;
    return true;
  });

  const defaultFields = getFieldValues();
  const fieldMap = {
    prefix: defaultFields.prefix,
    year: defaultFields.year,
    number: defaultFields.number,
    financial_type: defaultFields.financial_type
  };

  const usedKeys = new Set();
  const blocksToRender = savedOrder.length > 0 ? savedOrder : Object.keys(fieldMap);

  blocksToRender.forEach(key => {
    if (key && !usedKeys.has(key)) {
      const div = document.createElement('div');
      div.className = 'format-block';
      div.dataset.value = key;
      div.innerText = key.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
      formatBuilder.appendChild(div);
      usedKeys.add(key);
    }
  });

  updatePreview();
}


buildBlocks();

  // Initially hide year type section
  //document.getElementById('year_type_section').style.display = 'none';
  document.getElementById('year_type_section').addEventListener('change', buildBlocks);
  document.getElementById('include_year').addEventListener('change', buildBlocks);
  document.getElementById('financial_type').addEventListener('change', buildBlocks);

  // Attach event listener to contrib_prefix input field
  const prefixInput = document.querySelector('[name="contrib_prefix"]');
  if (prefixInput) {
    prefixInput.addEventListener('input', updatePreview);
    prefixInput.addEventListener('change', updatePreview);
    prefixInput.addEventListener('input', buildBlocks);
    prefixInput.addEventListener('change', buildBlocks);
  }

  // Allow delimiter insertions
  const delimiterPanel = document.createElement('div');
  delimiterPanel.className = 'delimiter-btns';
  delimiterPanel.innerHTML = `
    <p>Add Delimiter:</p>
    <button type="button" onclick="addDelimiter('-')">-</button>
    <button type="button" onclick="addDelimiter('/')">/</button>
    <button type="button" onclick="addDelimiter('.')">.</button>
  `;
  formatBuilder.after(delimiterPanel);

  window.addDelimiter = function(symbol) {
  const div = document.createElement('div');
  div.className = 'format-block';
  div.dataset.value = symbol;
  div.innerText = symbol;

  // Add a remove button
  const removeBtn = document.createElement('span');
  removeBtn.innerText = ' ✖';
  removeBtn.style.cursor = 'pointer';
  removeBtn.style.marginLeft = '8px';function buildBlocks() {
  const formatBuilder = document.getElementById('formatBuilder');
  formatBuilder.innerHTML = ''; // Clear existing

  const includeYear = document.getElementById('include_year').value === 'yes';
  const includeFinancialType = document.getElementById('financial_type').value === 'yes';

  const savedOrderRaw = formatBuilder.dataset.savedOrder || window.savedFormatOrder;
  let savedOrder = savedOrderRaw && savedOrderRaw.trim() ? savedOrderRaw.split(',') : ['prefix', 'year', 'number', 'financial_type'];

  // Filter out blocks based on checkbox selections
  savedOrder = savedOrder.filter(key => {
    if (key === 'year' && !includeYear) return false;
    if (key === 'financial_type' && !includeFinancialType) return false;
    return true;
  });

  const defaultFields = getFieldValues();
  const fieldMap = {
    prefix: defaultFields.prefix,
    year: defaultFields.year,
    number: defaultFields.number,
    financial_type: defaultFields.financial_type
  };

  const usedKeys = new Set();
  const blocksToRender = savedOrder.length > 0 ? savedOrder : Object.keys(fieldMap);

  blocksToRender.forEach(key => {
    if (key && !usedKeys.has(key)) {
      const div = document.createElement('div');
      div.className = 'format-block';
      div.dataset.value = key;
      div.innerText = key.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
      formatBuilder.appendChild(div);
      usedKeys.add(key);
    }
  });

  updatePreview();
}
  removeBtn.style.color = 'red';
  removeBtn.addEventListener('click', function () {
    div.remove();
    updatePreview();
  });

  div.appendChild(removeBtn);
  formatBuilder.appendChild(div);
  updatePreview();
};
  // Enable drag-and-drop reordering
  Sortable.create(formatBuilder, {
    animation: 150,
    onSort: updatePreview
  })
});
{/literal}
</script>