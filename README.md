# API E-factura


E-factura integration for SabeeApp

Official documentation for Endpoints:
https://mfinante.gov.ro/static/10/eFactura/prezentare%20api%20efactura.pdf
https://mfinante.gov.ro/web/efactura/informatii-tehnice

Done so far: 

    Authorization
    Generate token
    Upload XML : For type UBL only atm, supported ones are: UBL, CN, CII and RASP
    Check status of an uploaded XML (based on ID of invoice);
    Download a specific XML (based on ID of invoice);
    Check statuses for the last X days (List w pagination);

To do next: 

1. create Client ID and Client secret for the testing URL (is it possible to host this on a test page for until I test it through? )
2. check for possible issues w future integration
3. Data for XML 
4. XML validation


Decision Backlog:

1. Drop the Card de Vacanta (Szep kartya) option entirely as it is now supported as payment method in Sabeeapp at the moment
2. Do we need the official XML to PDF ?
3. Do we need their XML validator or is something already in house? (also theirs seem to not work all the time)
4. Storing app related info: CLIENT ID , CLIENT SECRET, CALLBACK URL
5. Storing provider related info: CIF (Vat nr)
6. Do we need a different type of XML for upload? should we make it dynamic?