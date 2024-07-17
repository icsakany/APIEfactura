<?php
const CLIENT_ID = 'XXXXXXXY'; //from developer auth on ANAF
const CLIENT_SECRET = 'XYYYYYYY'; //from developer auth on ANAF
const CLIENT_REDIRECT_URI = 'https://www.sabeeapp.com/'; //we need to register one callback URL for handling response from the server
const PROVIDER_VAT_NUMBER ='111118'; // without the RO -> this is the only info need from the provider
const AUTHORIZE_URL = 'https://logincert.anaf.ro/anaf-oauth2/v1/authorize';
const TOKEN_URL = 'https://logincert.anaf.ro/anaf-oauth2/v1/token';
const SANDBOX_UPLOAD_URL = 'https://api.anaf.ro/test/FCTEL/rest/upload?standard=UBL&cif='.PROVIDER_VAT_NUMBER;
const SANDBOX_STATUS_URL = 'https://api.anaf.ro/test/FCTEL/rest/stareMesaj?id_incarcare=';
const SANDBOX_DOWNLOAD_URL = 'https://api.anaf.ro/test/FCTEL/rest/descarcare?id=';
const SANDBOX_LIST_URL = 'https://api.anaf.ro/test/FCTEL/rest/listaMesajeFactura?zile=5&cif='.PROVIDER_VAT_NUMBER;
const PROD_UPLOAD_URL = 'https://api.anaf.ro/prod/FCTEL/rest/upload?standard=UBL&cif='.PROVIDER_VAT_NUMBER;
const PROD_STATUS_URL = 'https://api.anaf.ro/prod/FCTEL/rest/stareMesaj?id_incarcare=';
const PROD_DOWNLOAD_URL = 'https://api.anaf.ro/prod/FCTEL/rest/descarcare?id=';
const PROD_LIST_URL = 'https://api.anaf.ro/prod/FCTEL/rest/listaMesajeFactura?zile=5&cif='.PROVIDER_VAT_NUMBER;
