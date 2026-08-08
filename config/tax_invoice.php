<?php

/*
|--------------------------------------------------------------------------
| Tax Invoice settings
|--------------------------------------------------------------------------
|
| Single source of truth for the GST tax-invoice module (manual, single
| customer). Numbering, units of measure, and the terms printed on the PDF.
|
*/

return [

    /*
     * Invoice number: "{prefix}/{running serial}/{financial year}"
     * e.g. TINV/1/26-27. Change the prefix to your own series if needed.
     */
    'number_prefix' => 'TINV',

    /*
     * Units of measure offered on the line items (product default is editable
     * per row). First entry is the fallback when a product has none set.
     */
    'units' => ['KG', 'Litre', 'Pcs', 'Box', 'Tin', 'Jar', 'Bag', 'Dozen'],

    /*
     * GST rates offered in the rate dropdown (percent).
     */
    'gst_rates' => [0, 5, 12, 18, 28],

    /*
     * Terms & conditions printed at the foot of the invoice.
     */
    'terms' => [
        'Goods once sold will not be taken back.',
        'Interest @18% p.a. will be charged if payment is not made within due date.',
        'Our risk and responsibility ceases as soon as the goods leave our premises.',
        'Subject to local jurisdiction only. E.&.O.E',
    ],
];
