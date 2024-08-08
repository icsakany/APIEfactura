<?php

include_once 'invoice_array.php';
include_once 'UBLStructure.php';

function CreateInvoiceXml ( $invoiceID, $isSystemInvoice = false) : SimpleXMLElement|bool
{
    $invoiceData = getInvoiceData();
    $UBLfile = new UBLStructure();
    $UBLfile->AnafExport($invoiceID, $invoiceData);
    $xml = simplexml_load_file($invoiceID . '.xml');
    return $xml;

}
function getInvoiceData() : array{ // $invoice

    //$invoice = Invoices::GetInvoiceByID($invoiceID);
    $invoice = invoice_items;

    $supplierData = array (
        'Name'         => $invoice[0]['ProviderName'],
        'CountryCode'  => $invoice[0]['ProviderCountry'],
        'Zip'          => $invoice[0]['ProviderZip'],
        'City'         => $invoice[0]['ProviderCity'],
        'Street'       => $invoice[0]['ProviderStreet'],
        'StreetNature' => $invoice[0]['ProviderStreetNature'],
        'StreetNumber' => $invoice[0]['ProviderStreetNumber'],
        'Building'     => $invoice[0]['ProviderBuilding'],
        'Staircase'    => $invoice[0]['ProviderStaircase'],
        'Level'        => $invoice[0]['ProviderLevel'],
        'Door'         => $invoice[0]['ProviderDoor'],
        'VATNumber'    =>  $supplierVAT,
        'Currency'     => $providerData['Currency']
    );
    $vatNumber = $invoice[0]['BillingVatNumber'];

    if (!empty($invoice[0]['BillingLocalVATNumber'])) {
        $vatNumber = $invoice[0]['BillingLocalVATNumber'];
    }

    //$countryID = Countries::GetCountryIDByCountryNameAllLanguages($invoice[0]['BillingCountry'])[0]['CountryID'];
    $countryID = $invoice[0]['BillingCountryID'];

    $customerData = array (
        'Name'        => $invoice[0]['BillingCompanyName'],
        'CountryCode' => $invoice[0]['BillingCountry'],
        //'Zip'               => StringFunctions::PregReplace("/[^A-Za-z0-9]/", "", $invoice[0]['BillingZip']),
        'Zip'         => $invoice[0]['BillingZip'],
        'City'        => $invoice[0]['BillingCity'],
        'Street'      => $invoice[0]['BillingStreet'],
        'StreetNature' => $invoice[0]['BillingStreetNature'],
        'StreetNumber' => $invoice[0]['BillingStreetNumber'],
        'Building'     => $invoice[0]['BillingBuilding'],
        'Staircase'    => $invoice[0]['BillingStaircase'],
        'Level'        => $invoice[0]['BillingLevel'],
        'Door'         => $invoice[0]['BillingDoor'],
        'VATNumberOriginal'    => $invoice[0]['BillingCompanyTaxID'],
        'VATNumber'    => $customerVatNumber,
        'VatStatus'     => $customerVatStatus

    );

    //$invoiceItems = Invoices::GetInvoiceItemsByID($invoiceID);

    if (!empty($invoice[0]['ShowIncludedExtrasInInvoicePdf'])) {
        $invoiceItems = ModifyAccomodationLinesAndAddIncludedExtrasForInvoicePdf($invoiceItems, $invoice[0]);
    }



    return array (
        'InvoiceDetails' => $invoice[0],
        'SupplierData' => $supplierData,
        'CustomerData' => $customerData,
        'InvoiceItems' => $invoiceItems
    );
}
function GetPaymentMethodType($paymentType) {
    switch ($paymentType) {
        case "Transfer":
            return '31';
        case 'Cash':
            return '10';
        case "CreditCard":
            return '48'; // 54 for Credit Card
        case "DebitCard":
            return '48'; //55 for Debit card
        case "Mixed":
            return 'ZZZ'; //Mutually defined A code assigned within a code list to be used on an interim basis and as defined among trading partners until a precise code can be assigned to the code list.
                         // there's no Pebbol BIS code for mixed payment method
            //default :
            return 'ZZZ';
    }
}