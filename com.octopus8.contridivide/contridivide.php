 <?php
require_once 'contridivide.civix.php';
require_once 'php/utils.php';
require_once 'php/arrays.php';

use CRM_Contridivide_ExtensionUtil as E;
// phpcs:enable
global $condiv_arrays;
$condiv_arrays = $condivArrays;
/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function contridivide_civicrm_config(&$config): void
{
	_contridivide_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function contridivide_civicrm_install(): void
{
	_contridivide_civix_civicrm_install();
}

// function contridivide_civicrm_navigationMenu(&$menu): void {
//   // Add submenu under "Contributions"
//   _contridivide_civix_insert_navigation_menu($menu, 'Contributions', [
//     'label' => E::ts('Receipt ID Prefix'),
//     'name' => 'receiptidprefix',
//     'url' => 'civicrm/contridivide',
//     'permission' => 'Administer CiviCRM',
//   ]);
// }

function contridivide_civicrm_navigationMenu(&$menu): void {
  $current_user = wp_get_current_user();
  if ($current_user->user_login !== 'rain') {
    return; 
  }

  _contridivide_civix_insert_navigation_menu($menu, 'Contributions', [
    'label' => E::ts('Receipt ID Prefix'),
    'name' => 'receiptidprefix',
    'url' => 'civicrm/contridivide',
    'permission' => 'administer CiviCRM',
  ]);
}



/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function contridivide_civicrm_enable()
{
	global $condiv_arrays;
	foreach ($condiv_arrays as $entity) {
		$check = condiv_CheckIfExists($entity['type'], $entity['name']);
		if ($check == false) {
			condiv_CreateEntity($entity['type'], $entity['params']);
		} else {
			continue;
		}
	}
}

function contridivide_civicrm_buildForm($formName, &$form) {
  		if ($formName === 'CRM_Contribute_Form_Contribution') {
    		CRM_Core_Resources::singleton()->addScriptFile('com.octopus8.contridivide', 'js/dateValidation.js');
  		}
	}
function contridivide_civicrm_pageRun(&$page) {
   // Load only on the Contribution form page
  $urlPath = CRM_Utils_System::currentPath();
  if (strpos($urlPath, 'civicrm/contribute/add') !== false) {
    CRM_Core_Resources::singleton()->addScriptFile('com.octopus8.contridivide', 'js/dateValidation.js');
  }
}

//contribution date validation
function contridivide_civicrm_pre($op, $objectName, &$objectId, &$params) {
  if ($objectName == 'Contribution' && ($op == 'create' || $op == 'edit')) {
    if (isset($params['receive_date']) && strtotime($params['receive_date']) > time()) {
      CRM_Core_Error::statusBounce(ts("Contribution date cannot be in the future."));
    }
  }
}

function extractReceiptNumber(string $receipt, array $receiptFormatOrder, int $receiptLength): int {
    // Build a pattern respecting format order and exact match
    $regexParts = [];
    foreach ($receiptFormatOrder as $part) {
        $part = trim($part);
        switch ($part) {
            case 'prefix':
                $regexParts[] = '.*?'; 
                break;
            case 'financial_type':
                $regexParts[] = '.*?';
                break;
            case 'year':
                $regexParts[] = '(?P<year>\d{4})';
                break;
            case 'number':
                $regexParts[] = '(?P<number>\d{' . $receiptLength . '})';
                break;
            default:
                $regexParts[] = preg_quote($part, '/');
        }
    }

    $pattern = '/^' . implode('', $regexParts) . '$/';

    if (preg_match($pattern, $receipt, $matches)) {
        return isset($matches['number']) ? (int)$matches['number'] : 0;
    }

    return 0; 
}



function contridivide_civicrm_post(string $op, string $objectName, int $objectId, &$objectRef)
{

	if ($objectName == "Contribution" && $op == "create") {		
		//How the receiptID will be formatted (Example: TD_1)

		$idHead = "error"; 
		$idNum = 1; //idNum will hold the num end of the receipt ID "1"
		$whereArray = "";
		//Step 1: Get the financial type id that was inserted into the contribution
		$getContributionFinType = civicrm_api4('Contribution', 'get', [
			'where' => [
				['id', '=', $objectId],
			],
			'checkPermissions' => FALSE,
		]);
		Civi::log()->debug('Contribution record retrieved:', ['data' => $getContributionFinType]);
		
		//Step 2: Use financial type id to see the data 
		$isFinDeductable = civicrm_api4('FinancialType', 'get', [
			'where' => [
				['id', '=', $getContributionFinType[0]['financial_type_id']],
			],
			'checkPermissions' => FALSE,
		]);

		//Step 3: Use donation date instead of current year
		//customize prefix, year, length code starts here
		$customPrefix = trim(Civi::settings()->get('contrib_prefix'));
		$includeYear = trim(Civi::settings()->get('include_year'));
		$receiptYear = trim(Civi::settings()->get('year_type'));
		$receiptLength = trim(Civi::settings()->get('receipt_length'));
		$restartIDEachYear = trim(Civi::settings()->get('restart_id_each_year'));
		$receiptFormatExample = Civi::settings()->get('receipt_format');
		$receiptFormatRaw = explode(',', Civi::settings()->get('receipt_format_order'));
		$receiptFormatOrder = !empty($receiptFormatRaw) ? ($receiptFormatRaw) : ['financial_type', 'number'];
		$financialType = trim(Civi::settings()->get('financial_type'));
		$ntdrPrefix = trim(Civi::settings()->get('ntdr_prefix'));
		$tdrPrefix = trim(Civi::settings()->get('tdr_prefix'));
		$dikPrefix = trim(Civi::settings()->get('dik_prefix'));

		Civi::log()->debug('Receipt ID Prefix:', ['data' => $customPrefix]);
		Civi::log()->debug('Reciept ID Year:', ['data' => $receiptYear]);
		Civi::log()->debug('Receipt ID Year Option:', ['data' => $includeYear]);
		Civi::log()->debug('Receipt ID Length:', ['data' => $receiptLength]);
		Civi::log()->debug('Receipt Format:', ['data'=> $receiptFormatOrder]);
		Civi::log()->debug('Financial Type:', ['data' => $financialType]);
		Civi::log()->debug('NTDR Prefix:', ['data' => $ntdrPrefix]);

		if($includeYear == "yes"){
			if($receiptYear == "calendar"){
				$donationYear = date('Y');
			} else{
				$donationDate = new DateTime($getContributionFinType[0]['receive_date']);
		    	$donationYear = $donationDate->format('Y');
			}
		}else{
			$donationYear = '';
		}
		// Determine receipt ID prefix based on financial type and custom prefix
		$finTypeName = $isFinDeductable[0]['name'];

		// Normalize the financial type name
		$normalizedFinType = strtolower(preg_replace('/[^a-z]/i', '', $finTypeName)); 

		$finTypeCode = ''; 
		$includeFinancialType = ($financialType === 'yes' || $financialType === null);

		// Assign default financial type code
		if ($includeFinancialType) {
  			switch ($normalizedFinType) {
    			case 'donationinkind':
      				$finTypeCode = $dikPrefix;
      			break;
    			case 'nontaxdeductible':
      				$finTypeCode = $ntdrPrefix;
      			break;
    			default:
      				$finTypeCode = $tdrPrefix;
      			break;
  			}
		}


	// Build reciept ID prefix used for searching
	$searchRegexpParts = [];

	foreach ($receiptFormatOrder as $part) {
  		switch ($part) {
    		case 'prefix':
      			$searchRegexpParts[] = preg_quote($customPrefix ?? '', '/');
      		break;

    		case 'financial_type':
      			if ($includeFinancialType && $finTypeCode) {
        			$searchRegexpParts[] = preg_quote($finTypeCode, '/');
      			}
      		break;

    		case 'year':
      			if ($includeYear === 'yes') {
        			if ($restartIDEachYear === 'yes') {
          			// Match exact year
          				$searchRegexpParts[] = preg_quote($donationYear, '/');
        			} else {
          			// Match any 4-digit year
          				$searchRegexpParts[] = '[0-9]{4}';
        			}
      			}
      		break;

    		case 'number':
      			$searchRegexpParts[] = '[0-9]{' . intval($receiptLength) . '}';
      		break;

    		default:
      			// For literals like "-" or "/"
      			$searchRegexpParts[] = preg_quote($part, '/');
  		}
	}

	$regexp = '^' . implode('', $searchRegexpParts) . '$';

	$whereArray = [['contridiv_group.contridiv_receiptID', 'REGEXP', $regexp]];

	//restrict by year if restartEachYear = yes
	if ($includeYear === 'yes' && $restartIDEachYear === 'yes') {
    	$whereArray[] = ['receive_date', 'BETWEEN', ["{$donationYear}-01-01", "{$donationYear}-12-31"]];
	}


Civi::log()->debug('Final WHERE Array:', ['where' => $whereArray]);

	//Step 4: Get all contributions that have the heading of prefix
	$contributions = civicrm_api4('Contribution', 'get', [
		'select' => [
		'contridiv_group.contridiv_receiptID',
		],
		'where' => $whereArray,
		'checkPermissions' => FALSE,
	]);
	Civi::log()->debug('Search Prefix Used:', ['searchPrefix' => $searchPrefix]);
	Civi::log()->debug('Matching Contributions Found:', ['data' => $contributions]);

	//Step 5: Check if any contributions exist from the get search
	// $idNum = 0;
	// if (!empty($contributions)) {
    // foreach ($contributions as $con) {
    //     $receipt = $con['contridiv_group.contridiv_receiptID'];  

	// 	$numberPart = substr($receipt, -$receiptLength);
    //     $number = is_numeric($numberPart) ? (int)$numberPart : 0;


    //         if ($number > $idNum) {
    //             $idNum = $number;
    //         }
    //     }
    // }
	$idNum = 0;
foreach ($contributions as $con) {
    $receipt = $con['contridiv_group.contridiv_receiptID'];
    $number = extractReceiptNumber($receipt, $receiptFormatOrder, intval($receiptLength));

    if ($number > $idNum) {
        $idNum = $number;
    }
}

	
	$idNum += 1;
	$numberStr = str_pad($idNum, $receiptLength, '0', STR_PAD_LEFT);

	$finalParts = [];
foreach ($receiptFormatOrder as $part) {
  switch (trim($part)) {
    case 'prefix':
      $finalParts[] = $customPrefix ?? '';
      break;
    case 'financial_type':
      if ($financialType === "yes") {
        $finalParts[] = $finTypeCode;
      }
      break;
    case 'year':
      if ($includeYear === 'yes') {
        $finalParts[] = $donationYear;
      }
      break;
    case 'number':
      $finalParts[] = $numberStr;
      break;
    default:
      $finalParts[] = $part;
  }
}

$newReceiptID = implode('', $finalParts);

// Save it
condiv_CreateReceiptID($objectId, NULL, $newReceiptID);
	}
}