<?php

const invoice_items = [
[
'ProviderName' => 'Supplier Inc.',
'ProviderCountry' => 'US',
'ProviderZip' => '12345',
'ProviderCity' => 'New York',
'ProviderStreet' => '5th Avenue',
'ProviderStreetNature' => 'Avenue',
'ProviderStreetNumber' => '10',
'ProviderBuilding' => 'Building A',
'ProviderStaircase' => '1',
'ProviderLevel' => '2',
'ProviderDoor' => '3',
'BillingVatNumber' => 'US123456789',
'BillingLocalVATNumber' => 'US987654321',
'BillingCountryID' => '1',
'BillingCompanyName' => 'Customer LLC',
'BillingCountry' => 'US',
'BillingZip' => '54321',
'BillingCity' => 'Los Angeles',
'BillingStreet' => 'Sunset Boulevard',
'BillingStreetNature' => 'Boulevard',
'BillingStreetNumber' => '20',
'BillingBuilding' => 'Building B',
'BillingStaircase' => '2',
'BillingLevel' => '3',
'BillingDoor' => '4',
'BillingCompanyTaxID' => 'US1122334455',
'ShowIncludedExtrasInInvoicePdf' => true
]
];

$supplierVAT = 'US123456789';
$providerData = ['Currency' => 'USD'];
$customerVatNumber = 'US987654321';
$customerVatStatus = 'Active';
$invoiceItems = [
[
'Description' => 'Item 1',
'Quantity' => 1,
'UnitPrice' => 100.00
],
[
'Description' => 'Item 2',
'Quantity' => 2,
'UnitPrice' => 50.00
]
];