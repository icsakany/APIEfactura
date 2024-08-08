<?php

class UBLStructure
{
    function AnafExport($invoiceId, $invoice): void
    {


        $x=new XMLWriter();
        $x->openURI('php://stdout');
        //$x->openURI($invoiceId. '.xml');
        $x->startDocument('1.0','UTF-8','yes');
        $x->setIndent(true);
        $x->startElement('Invoice');
        $x->writeAttribute(
            'xmlns',
            'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2'
        );
        $x->writeAttribute(
            'xmlns:cbc',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2'
        );
        $x->writeAttribute(
            'xmlns:cac',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2'
        );
        $x->writeAttribute(
            'xmlns:ns4',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2'
        );
        $x->writeAttribute(
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance'
        );
        $x->writeAttribute(
            'xsi:schemaLocation',
            'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2 http://docs.oasis-open.org/ubl/os-UBL-2.1/xsd/maindoc/UBL-Invoice-2.1.xsd'
        );
        $x->startElement('cbc:CustomizationID');
        $x->text('urn:cen.eu:en16931:2017#compliant#urn:efactura.mfinante.ro:CIUS-RO:1.0.1');
        $x->endElement();
        $x->startElement('cbc:ID');
        $x->text($invoice['DisplayedNumber']); //?
        $x->endElement();
        $x->startElement('cbc:IssueDate');$x->text(date("Y-m-d H:i:s", strtotime($invoice['Created'])));$x->endElement();
        $x->startElement('cbc:DueDate');$x->text($invoice['DueDate']);$x->endElement();
        $x->startElement('cbc:InvoiceTypeCode');$x->text($invoice['invoice_type_code']);$x->endElement(); // accepted ones are 380,384,389,751
        $x->startElement('cbc:DocumentCurrencyCode');$x->text($invoice['Currency']);$x->endElement();//?
        $x->startElement('cac:AccountingSupplierParty');
        $x->startElement('cac:Party');
        $x->startElement('cac:PostalAddress');
        $x->startElement('cbc:StreetName');$x->text($invoice['ProviderStreet']);$x->endElement();
        $x->startElement('cbc:CityName');$x->text($invoice['ProviderCity']);$x->endElement();
        $x->startElement('cbc:PostalZone');$x->text($invoice['ProviderZip']);$x->endElement();
        $x->startElement('cbc:CountrySubentity');$x->text($invoice[0]['code_of_county']);$x->endElement(); //? ex: RO-B RO-TM
        $x->startElement('cac:Country');
        $x->startElement('cbc:IdentificationCode');$x->text('RO');$x->endElement(); //harcoded since it's only used for romanian providers, can be changed later
        $x->endElement();
        $x->endElement();

        $x->startElement('cac:PartyLegalEntity');
        $x->startElement('cbc:RegistrationName');$x->text( mb_substr($invoice['ProviderName'], 0, 100));$x->endElement();
        $x->startElement('cbc:CompanyID');$x->text( $invoice['ProviderVATNumber']);$x->endElement();
        $x->endElement();
        $x->endElement();
        $x->endElement();

        $x->startElement('cac:AccountingCustomerParty');
        $x->startElement('cac:Party');
        $x->startElement('cac:PostalAddress');
        $x->startElement('cbc:StreetName');$x->text($invoice['BillingStreet']);$x->endElement();
        $x->startElement('cbc:CityName');$x->text($invoice['BillingCity']);$x->endElement();
        $x->startElement('cbc:PostalZone');$x->text($invoice['BillingZip']);$x->endElement();
        $x->startElement('cbc:CountrySubentity');$x->text($invoice['client_county']);$x->endElement();//?
        $x->startElement('cac:Country');
        $x->startElement('cbc:IdentificationCode');$x->text($invoice['BilingCountry']);$x->endElement(); //country code!
        $x->endElement();
        $x->endElement();
        if ($invoice['BillingType'] != 'client') {
            $x->startElement('cac:PartyLegalEntity');
            $x->startElement('cbc:RegistrationName');
            $x->text($invoice['BillingCompanyName']);
            $x->endElement();
            if (!empty($invoice['BillingCompanyTaxID'])) {
                $x->startElement('cbc:CompanyID');
                $x->text($invoice['BillingCompanyTaxID']);
                $x->endElement();
            }
            $x->endElement();
        }
            $x->endElement();
            $x->endElement();
        $x->startElement('cac:PaymentMeans');
        $x->startElement('cbc:PaymentMeansCode');$x->text('1');$x->endElement(); //10,48,etc Mixed?
        $x->startElement('cac:PayeeFinancialAccount');
        $x->startElement('cbc:ID');$x->text($invoice['BillingCompanyTaxID']);$x->endElement();
        $x->endElement();
        $x->endElement();
        $x->startElement('cac:TaxTotal');
        $x->startElement('cbc:TaxAmount');
        $x->writeAttribute(
            'currencyID',
            'RON'
        );
        $x->text($invoice['total_tva']);
        $x->endElement();
        $x->startElement('cac:TaxSubtotal');
        $x->startElement('cbc:TaxableAmount');
        $x->writeAttribute(
            'currencyID',
            'RON'
        );
        $x->text($invoice['total_fara_tva']);
        $x->endElement();
        $x->startElement('cbc:TaxAmount');
        $x->writeAttribute(
            'currencyID',
            'RON'
        );
        $x->text($total_tva);
        $x->endElement();
        $x->startElement('cac:TaxCategory');
        $x->startElement('cbc:ID');$x->text($taxcateg);$x->endElement();
        $x->startElement('cbc:Percent');$x->text($tva);$x->endElement();
        if (!$is_firma_tva){
            $x->startElement('cbc:TaxExemptionReasonCode');$x->text('VATEX-EU-O');$x->endElement();
        }
        $x->startElement('cac:TaxScheme');
        $x->startElement('cbc:ID');$x->text('VAT');$x->endElement();
        $x->endElement();
        $x->endElement();
        $x->endElement();
        $x->endElement();
        $x->startElement('cac:LegalMonetaryTotal');
        $x->startElement('cbc:LineExtensionAmount');
        $x->writeAttribute(
            'currencyID',
            'RON'
        );
        $x->text($total_fara_tva);
        $x->endElement();
        $x->startElement('cbc:TaxExclusiveAmount');
        $x->writeAttribute(
            'currencyID',
            'RON'
        );
        $x->text($total_fara_tva);
        $x->endElement();
        $x->startElement('cbc:TaxInclusiveAmount');
        $x->writeAttribute(
            'currencyID',
            'RON'
        );
        $x->text($total_cu_tva);
        $x->endElement();
        $x->startElement('cbc:PayableAmount');
        $x->writeAttribute(
            'currencyID',
            'RON'
        );
        $x->text($total_cu_tva);
        $x->endElement();
        $x->endElement();

        $count = count($invoice['factura_randuri']);
        $ii=1;
        for ($i = 0; $i < $count; $i++) {
            $x->startElement('cac:InvoiceLine');
            $x->startElement('cbc:ID');$x->text($ii);$x->endElement();
            $x->startElement('cbc:InvoicedQuantity');
            $x->writeAttribute(
                'unitCode',
                'H87'
            );
            $x->text($invoice['factura_randuri'][$i]['cantitate']);
            $x->endElement();
            $x->startElement('cbc:LineExtensionAmount');
            $x->writeAttribute(
                'currencyID',
                'RON'
            );
            $x->text($invoice[0]['factura_randuri'][$i]['valoare']);
            $x->endElement();
            $x->startElement('cac:Item');
            $x->startElement('cbc:Name');$x->text($invoice[0]['factura_randuri'][$i]['denumire']);$x->endElement();

            $x->startElement('cac:ClassifiedTaxCategory');
            $x->startElement('cbc:ID');$x->text($taxcateg);$x->endElement();
            if ($is_firma_tva){
                $x->startElement('cbc:Percent');$x->text($tva);$x->endElement();
            }
            $x->startElement('cac:TaxScheme');
            $x->startElement('cbc:ID');$x->text('VAT');$x->endElement();
            $x->endElement();
            $x->endElement();
            $x->endElement();
            $x->startElement('cac:Price');
            $x->startElement('cbc:PriceAmount');
            $x->writeAttribute(
                'currencyID',
                'RON'
            );
            $x->text($invoice[0]['factura_randuri'][$i]['valoare']);
            $x->endElement();
            $x->endElement();
            $x->endElement();
            $ii++;
        }
        $x->endElement(); // root
        $x->endDocument();

    }



}