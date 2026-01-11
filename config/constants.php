<?php

$defaultPath = public_path() . '/uploads/';
$viewPath = '/uploads/';

//$monthNameList = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

return [

    'perPage' => 20,

    'uploadFilePath' => [
        'companyDocument'       => ['default' => $defaultPath. 'companyprofile/',         'view' => $viewPath. 'companyprofile/'],
        'purchaseOrderDocument' => ['default' => $defaultPath. 'purchaseorder/',          'view' => $viewPath. 'purchaseorder/'],
        'recivePoDocument'      => ['default' => $defaultPath. 'recivepo/',               'view' => $viewPath. 'recivepo/'],
        'challanDocument'       => ['default' => $defaultPath. 'challan/',                'view' => $viewPath. 'challan/'],
        'quotationDocument'     => ['default' => $defaultPath. 'quotation/',              'view' => $viewPath. 'quotation/'],
        'orderDocument'         => ['default' => $defaultPath. 'order/',                  'view' => $viewPath. 'order/'],
        'piDocument'            => ['default' => $defaultPath. 'ProformaInvoice/',         'view' => $viewPath. 'ProformaInvoice/'],
        'Invoice'               => ['default' => $defaultPath. 'invoice/',                'view' => $viewPath. 'invoice/'],
        'ServiceReports'        => ['default' => $defaultPath. 'service-reports/',        'view' => $viewPath. 'service-reports/'],
        'productImages'         => ['default' => $defaultPath. 'product-images/',         'view' => $viewPath. 'product-images/'],
        'maintenanceDocument'   => ['default' => $defaultPath. 'maintenancecontract/',    'view' => $viewPath. 'maintenancecontract/'],
        'jobcardDocument'       => ['default' => $defaultPath. 'jobcard/',                'view' => $viewPath. 'jobcard/'],
    ],

    'occupationType' => [
        [ "id" => "Dr.",        "name" => "Dr."],
        [ "id" => "Hospital",   "name" => "Hospital"],
        [ "id" => "Clinic",     "name" => "Clinic"],
        [ "id" => "Dealer",     "name" => "Dealer"],
        [ "id" => "Pharmacy",   "name" => "Pharmacy"],
    ],

    'customerType' => [
        [ "id" => "Retail", "name" => "Retail"],
        [ "id" => "Tax",    "name" => "Tax"]
    ],

    'invoiceType' => [
        [ "id" => null,     "name" => "ALL"],
        [ "id" => "Retail", "name" => "RETAIL"],
        [ "id" => "Tax",    "name" => "TAX"]
    ],

    'paymentType' => [
        [ "id" => "cr", "name" => "Receipt"],
        [ "id" => "dr", "name" => "Payment"]
    ],

    'reportType' => [
        [ "id" => "Service Report",         "name" => "Service Report"],
        [ "id" => "Installation Report",    "name" => "Installation Report"],
        [ "id" => "Preventive Maintenance", "name" => "Preventive Maintenance"]
    ],

    'gstType' => [
        [ "id" => "IGST",       "name" => "IGST"],
        [ "id" => "CGST/SGST",  "name" => "CGST/SGST"]
    ],

    'productCategoryList' => [
        [ "id" => 'Sales',   "name" => "Sales"],
        [ "id" => 'Service', "name" => "Service"],
    ],

    'purchaseTypeList' => [
        [ "id" => null,     "name" => "ALL"],
        [ "id" => 'invoice',   "name" => "INVOICE"],
        [ "id" => 'challan', "name" => "CHALLAN"],
    ],

    'ordersStatus' => [
        [ "id" => null,         "name" => "ALL"],
        [ "id" => 'Pending',    "name" => "PENDING"],
        [ "id" => 'In Process', "name" => "IN PROCESS"],
        [ "id" => 'Completed',  "name" => "COMPLETED"],
        [ "id" => 'Cancelled',  "name" => "CANCELLED"],
    ],

    'proformaStatus' => [
        [ "id" => null,    "name" => "ALL"],
        [ "id" => 'Open',  "name" => "OPEN"],
        [ "id" => 'Close', "name" => "CLOSE"],
    ],

    'quotationList' => [
        [ "id" => null,      "name" => "ALL"],
        [ "id" => 'Sales',   "name" => "SALES"],
        [ "id" => 'Service', "name" => "SERVICE"],
    ],

    'challanCategoryList' => [
        [ "id" => 'Sales',   "name" => "Sales"],
        [ "id" => 'Service', "name" => "Service"],
        [ "id" => 'Demo',    "name" => "Demo"],
    ],

    'challanCategory' => [
        [ "id" => null,      "name" => "ALL"],
        [ "id" => 'Sales',   "name" => "SALES"],
        [ "id" => 'Service', "name" => "SERVICE"],
        [ "id" => 'Demo',    "name" => "DEMO"],
    ],

    'jobStatus' => [
        [ "id" => 'Repaired',        "name" => "Repaired"],
        [ "id" => 'Not Repaired',    "name" => "Not Repaired"],
        [ "id" => 'Return As It Is', "name" => "Return As It Is"],
    ],

    'jobFilterStatus' => [
        [ "id" => 'null',    "name" => "ALL"],
        [ "id" => 'Open',  "name" => "OPEN"],
        [ "id" => 'Close', "name" => "CLOSE"],
    ],
    'maintenanceType' => [
        [ "id" => 'AMC',    "name" => "AMC"],
        [ "id" => 'CMC', "name" => "CMC"],
    ],

    'termsAndConditions' => [
        "validity"      => '30 days from the date of quotation',
        "delivery"      => 'Six to Eight weeks after valid PO',
        "payment_terms" => '50% advance and 50% against PI',
        "warranty"      => 'One year from the date of installation'
    ],

    'invoiceTermsAndConditions' => [
        "term1"      => 'Goods once sold will not be taken back or exchanged.',
        "term2"      => 'Bill not paid due date will attract 24% interest.',
        "term3" => 'All disputes subject to AHMEDABAD Jurisdication only.',
    ],

    'quotationBankinfo' => [
        "bank_name"     => 'Kotak Mahindra Bank',
        "branch_name"   => 'Sola Road, Ahmedabad',
        "account_no"    => '6113250578',
        "ifsc_code"     => 'KKBK0002576'
    ],

    'quotationHealthCareBankinfo' => [
        "bank_name"     => 'Kotak Mahindra Bank',
        "branch_name"   => 'Sola Road, Ahmedabad',
        "account_no"    => '9412449803',
        "ifsc_code"     => 'KKBK0002576'
    ],

    'quotationNoxBankinfo' => [
        "bank_name"     => 'HDFC Bank',
        "branch_name"   => 'Ghatlodiya, Ahmedabad',
        "account_no"    => '50200076823714',
        "ifsc_code"     => 'HDFC0001337'
    ],

    'invoiceMCBankinfo' => [
        "bank_name"     => 'KOTAK MAHINDRA BANK',
        "branch_name"   => 'SOLA ROAD, AHMEDABAD',
        "account_no"    => '6113250578',
        "ifsc_code"     => 'KKBK0002576'
    ],

    'invoiceHCBankinfo' => [
        "bank_name"     => 'KOTAK MAHINDRA BANK',
        "branch_name"   => 'SATYASURYA COMPLEX,SATADHAR,SOLA ROAD.',
        "account_no"    => '9412449803',
        "ifsc_code"     => 'KKBK0002576'
    ],

    'invoiceNOXBankinfo' => [
        "bank_name"     => 'HDFC BANK',
        "branch_name"   => 'GHATLODIYA, AHMEDABAD.',
        "account_no"    => '50200076823714',
        "ifsc_code"     => 'HDFC0001337'
    ],

    'isOrganization' => [
        [ "id" => 'yes', "name" => "Yes"],
        [ "id" => 'no',  "name" => "No"],
    ],

    'funnelStatus' => [
        [ "id" => 'Hot',  "name" => "Hot"],
        [ "id" => 'Warm', "name" => "Warm"],
        [ "id" => 'Cold', "name" => "Cold"]
    ],

    'inquiryTypes' => [
        [ "id" => null,     "name" => "ALL"],
        [ "id" => 'Hot',    "name" => "HOT"],
        [ "id" => 'Warm',   "name" => "WARM"],
        [ "id" => 'Cold',   "name" => "COLD"]
    ],

    //'months' => array_map(fn($month) => ['id' => $month, 'name' => $month], $monthNameList),
    'months' => [
        [ "id"=> "January",     "name"=> "January"],
        [ "id"=> "February",    "name"=> "February"],
        [ "id"=> "March",       "name"=> "March"],
        [ "id"=> "April",       "name"=> "April"],
        [ "id"=> "May",         "name"=> "May"],
        [ "id"=> "June",        "name"=> "June"],
        [ "id"=> "July",        "name"=> "July"],
        [ "id"=> "August",      "name"=> "August"],
        [ "id"=> "September",   "name"=> "September"],
        [ "id"=> "October",     "name"=> "October"],
        [ "id"=> "November",    "name"=> "November"],
        [ "id"=> "December",    "name"=> "December"]
    ],

    'paymentTypes' => [
        ['id' => 'cash', 'name' => 'Cash'],
        ['id' => 'check', 'name' => 'Cheque'],
        ['id' => 'NEFT', 'name' => 'NEFT']
    ],

    'pageTypes' => [
        ['id' => 'portrait', 'name' => 'Portrait'],
        ['id' => 'landscape', 'name' => 'Landscape']
    ]

];
