<?php

include_once 'Constants.php';
 class AnafRest

{
    private $client_id;
    private $client_secret;
    private $redirect_uri;
    private $authorize_url;
    private $token_url;
    private $upload_url;
    private $status_url;
    private $download_url;
    private $ubi_file_path;
    private $mess_url;
    private $vat_number;
    private $testmode;
    private $fact_id;
    private $token;



    public function __construct($filepath_UBI, $testmode)
    {
        $this->client_id = CLIENT_ID;
        $this->client_secret = CLIENT_SECRET;
        $this->ubi_file_path = $filepath_UBI;
        $this->redirect_uri =  CLIENT_REDIRECT_URI;
        $this->vat_number = PROVIDER_VAT_NUMBER;
        $this->authorize_url = AUTHORIZE_URL;
        $this->token_url = TOKEN_URL;
        $this->token = '';
        $this->fact_id= '';
        $this->testmode = $testmode;
        if ($testmode) {
            $this->upload_url = SANDBOX_UPLOAD_URL;
            $this->download_url = SANDBOX_DOWNLOAD_URL;
            $this->mess_url = SANDBOX_LIST_URL;
            $this->status_url = SANDBOX_STATUS_URL;
        }
        else{
            $this->upload_url = PROD_UPLOAD_URL;
            $this->download_url = PROD_DOWNLOAD_URL;
            $this->mess_url = PROD_LIST_URL;
            $this->status_url = PROD_STATUS_URL;
        }

    }
    function getVat(): string
    {
        return $this->vat_number;
    }
    function getClientId(): string
    {
        return $this->client_id;
    }
    function getRedirectUri(): string
    {
        return $this->redirect_uri;
    }

    public function authorizeAnaf(): void
    {
        $link = "https://logincert.anaf.ro/anaf-oauth2/v1/authorize?response_type=code&client_id=".CLIENT_ID."&redirect_uri=".CLIENT_REDIRECT_URI."&token_content_type=jwt";
        header("Location: $link");
    }
    public function getTokenAnaf($code): void
    {
        $code=$_GET['code'];
        if (isset($_GET['op']) && $_GET['op']=="gettoken" && empty($code)){
            $this->authorizeAnaf();
        }
        if (!empty($code)) {
            $test = "grant_type=authorization_code&code=".$code."&client_id=".$this->client_id."&client_secret=".$this->client_secret."&redirect_uri=".$this->redirect_uri;
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->token_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $test);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);
            $outputJson = json_decode($server_output, true);

            $this->token = $outputJson["access_token"];
        }

    }

    public function uploadUBIAnaf() :array
    {
        $url = $this->upload_url;
        $xmlContent = file_get_contents($this->ubi_file_path);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $xmlContent,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/xml',
                'Authorization: Bearer '.$this->token
            ),
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            echo 'Curl error: ' . curl_error($curl);
        }
        curl_close($curl);

        $xml = new SimpleXMLElement($response);
        $array = (array)$xml;
        $this->fact_id= $array['fact_id'];
        return $array;

    }
   public function statusUBIAnaf(): string
    {

        $url = $this->status_url.$this->fact_id;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$this->token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $xml = new SimpleXMLElement($response);
        $array = (array)$xml;

        return $array["@attributes"]["stare"];
        //echo $array["@attributes"]["id_descarcare"];

    }
    public function downloadUBIAnaf(): bool|string
    {

    }
    public function getLastMsgAnaf(): array
    {

    }



}