<?php
function CreateInvoiceXml ( $invoiceID, $isSystemInvoice = false)
{

}
function getInvoiceData($invoiceID, $isSystemInvoice = false){
    return array (
        'InvoiceDetails' => $invoice[0],
        'SupplierData' => $suplierData,
        'CustomerData' => $customerData,
        'InvoiceItmes' => $invoiceItems,
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

        default :
            return 'ZZZ';
    }
}