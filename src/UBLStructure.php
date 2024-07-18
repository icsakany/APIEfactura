<?php

class UBLStructure
{
    function CreateUBLFromData($ubl_file_path,$fact_data,$is_firma_tva,$total_fara_tva,$total_tva,$total_cu_tva,$tva){

        if ($is_firma_tva){
            if ($total_tva > 0){
                $taxcateg='S';
            }else{
                $taxcateg='Z';
            }
        }else{
            $taxcateg='O';
        }

        $x=new XMLWriter();
        $filename=$ubl_file_path;
        $x->openURI($filename);
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
        $x->text($fact_data[0]['numar_factura']);
        $x->endElement();
        $x->startElement('cbc:IssueDate');$x->text($fact_data[0]['data_factura']);$x->endElement();
        $x->startElement('cbc:DueDate');$x->text($fact_data[0]['data_valabilitate_factura']);$x->endElement();
        $x->startElement('cbc:InvoiceTypeCode');$x->text('380');$x->endElement();
        $x->startElement('cbc:DocumentCurrencyCode');$x->text('RON');$x->endElement();
        $x->startElement('cac:AccountingSupplierParty');
        $x->startElement('cac:Party');
        $x->startElement('cac:PostalAddress');
        $x->startElement('cbc:StreetName');$x->text($fact_data[0]['adresa_firma']);$x->endElement();
        $x->startElement('cbc:CityName');$x->text($fact_data[0]['oras_firma']);$x->endElement();
        $x->startElement('cbc:PostalZone');$x->text($fact_data[0]['codul_postal_firma']);$x->endElement();
        $x->startElement('cbc:CountrySubentity');$x->text($fact_data[0]['cod_judet_firma']);$x->endElement();
        $x->startElement('cac:Country');
        $x->startElement('cbc:IdentificationCode');$x->text('RO');$x->endElement();
        $x->endElement();
        $x->endElement();

        $x->startElement('cac:PartyLegalEntity');
        $x->startElement('cbc:RegistrationName');$x->text($fact_data[0]['nume_firma']);$x->endElement();
        $x->startElement('cbc:CompanyID');$x->text($fact_data[0]['codul_fiscal_firma']);$x->endElement();
        $x->endElement();
        $x->endElement();
        $x->endElement();

        $x->startElement('cac:AccountingCustomerParty');
        $x->startElement('cac:Party');
        $x->startElement('cac:PostalAddress');
        $x->startElement('cbc:StreetName');$x->text($fact_data[0]['adresa_client']);$x->endElement();
        $x->startElement('cbc:CityName');$x->text($fact_data[0]['oras_client']);$x->endElement();
        $x->startElement('cbc:PostalZone');$x->text($fact_data[0]['codul_postal_client']);$x->endElement();
        $x->startElement('cbc:CountrySubentity');$x->text($fact_data[0]['cod_judet_client']);$x->endElement();
        $x->startElement('cac:Country');
        $x->startElement('cbc:IdentificationCode');$x->text('RO');$x->endElement();
        $x->endElement();
        $x->endElement();
        $x->startElement('cac:PartyLegalEntity');
        $x->startElement('cbc:RegistrationName');$x->text($fact_data[0]['numefirma_client']);$x->endElement();
        $x->startElement('cbc:CompanyID');$x->text($fact_data[0]['codul_fiscal_client']);$x->endElement();
        $x->endElement();
        $x->endElement();
        $x->endElement();
        $x->startElement('cac:PaymentMeans');
        $x->startElement('cbc:PaymentMeansCode');$x->text('1');$x->endElement();
        $x->startElement('cac:PayeeFinancialAccount');
        $x->startElement('cbc:ID');$x->text($fact_data[0]['cod_iban_firma']);$x->endElement();
        $x->endElement();
        $x->endElement();
        $x->startElement('cac:TaxTotal');
        $x->startElement('cbc:TaxAmount');
        $x->writeAttribute(
            'currencyID',
            'RON'
        );
        $x->text($total_tva);
        $x->endElement();
        $x->startElement('cac:TaxSubtotal');
        $x->startElement('cbc:TaxableAmount');
        $x->writeAttribute(
            'currencyID',
            'RON'
        );
        $x->text($total_fara_tva);
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

        $count = count($fact_data[0]['factura_randuri']);
        $ii=1;
        for ($i = 0; $i < $count; $i++) {
            $x->startElement('cac:InvoiceLine');
            $x->startElement('cbc:ID');$x->text($ii);$x->endElement();
            $x->startElement('cbc:InvoicedQuantity');
            $x->writeAttribute(
                'unitCode',
                'H87'
            );
            $x->text($fact_data[0]['factura_randuri'][$i]['cantitate']);
            $x->endElement();
            $x->startElement('cbc:LineExtensionAmount');
            $x->writeAttribute(
                'currencyID',
                'RON'
            );
            $x->text($fact_data[0]['factura_randuri'][$i]['valoare']);
            $x->endElement();
            $x->startElement('cac:Item');
            $x->startElement('cbc:Name');$x->text($fact_data[0]['factura_randuri'][$i]['denumire']);$x->endElement();

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
            $x->text($fact_data[0]['factura_randuri'][$i]['valoare']);
            $x->endElement();
            $x->endElement();
            $x->endElement();
            $ii++;
        }
        $x->endElement(); // root
        $x->endDocument();
    }



}