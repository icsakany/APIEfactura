<?php

include_once 'Constants.php';

class ClientErrorException extends Exception{};
class ServerErrorException extends Exception{};
 class AnafInvoice

{
    //dev info
    private string $client_id;
    private string $client_secret;
    private string $redirect_uri;

    private string $authorize_url;
    private string $token_url;
    private string $upload_url;
    private string $status_url;
    private string $download_url;
    private string $list_url;

    private string $ubi_file_path;
    private string $vat_number;
    private string $fact_id;
    private string $download_id;
    private string $token;

     /**
      * ANAF Elektronikus Számla beküldő osztály
      * @param $filepath_UBI
      * @param $debug 0 - prod, 1 - sandbox (default 1) az ANAF teszt endpoinjainak kapcsolatához és képernyőn való debugoláshoz
      */

    public function __construct($filepath_UBI, $debug)
    {
        $this->client_id = CLIENT_ID;
        $this->client_secret = CLIENT_SECRET;
        $this->ubi_file_path = $filepath_UBI;
        $this->redirect_uri =  CLIENT_REDIRECT_URI;
        $this->vat_number = PROVIDER_VAT_NUMBER;
        $this->authorize_url = AUTHORIZE_URL;
        $this->token_url = TOKEN_URL;
        $this->token = '';
        $this->fact_id = '';
        $this->download_id = '';
        if ($debug == '1') {
            $this->upload_url = SANDBOX_UPLOAD_URL;
            $this->download_url = SANDBOX_DOWNLOAD_URL;
            $this->list_url = SANDBOX_LIST_URL;
            $this->status_url = SANDBOX_STATUS_URL;
        }
        else{
            $this->upload_url = PROD_UPLOAD_URL;
            $this->download_url = PROD_DOWNLOAD_URL;
            $this->list_url = PROD_LIST_URL;
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
        $link = $this->authorize_url."?response_type=code&client_id=".CLIENT_ID."&redirect_uri=".CLIENT_REDIRECT_URI."&token_content_type=jwt";
        header("Location: $link");
    }


     /**
      * @throws Exception
      */
     public function getTokenAnaf(): void
    {
        $code=$_GET['code'];
        if (isset($_GET['op']) && $_GET['op']=="gettoken" && empty($code)){
            $this->authorizeAnaf();
        }
        if (!empty($code)) {
            $postData = "grant_type=authorization_code&code=" . $code . "&client_id=" . $this->client_id . "&client_secret=" . $this->client_secret . "&redirect_uri=" . $this->redirect_uri;
            $response = $this->executeCurlRequest($this->token_url, 'POST', [], [CURLOPT_POSTFIELDS => $postData]);
            $outputJson = json_decode($response, true);
            var_dump($outputJson);
            $this->token = $outputJson["access_token"];
        }

    }

     /**
      * @throws Exception
      */
     public function uploadUBIAnaf() :array
    {
        try {
            $url = $this->upload_url;
            $xmlContent = file_get_contents($this->ubi_file_path);
            $response = $this->executeCurlRequest($this->upload_url, 'POST', [
                'Content-Type: application/xml',
                'Authorization: Bearer ' . $this->token
            ], [CURLOPT_POSTFIELDS => $xmlContent]);
            $xml = new SimpleXMLElement($response);
            $array = (array)$xml;
            $this->fact_id = $array['fact_id'];
            return $array;
        } catch (ClientErrorException $e) {
            // Handle client error (4xx)
            echo 'Client error: ' . $e->getMessage();
        } catch (ServerErrorException $e) {
            // Handle server error (5xx)
            echo 'Server error: ' . $e->getMessage();
        } catch (Exception $e) {
            // Handle other errors
            echo 'Error: ' . $e->getMessage();
        }
        return [];

    }

     /**
      * @throws Exception
      */
     public function statusUBIAnaf(): string
    {

        $response = $this->executeCurlRequest($this->status_url . $this->fact_id, 'GET', [
            'Authorization: Bearer ' . $this->token
        ]);
        $xml = new SimpleXMLElement($response);
        $array = (array)$xml;
        $this->download_id = $array["@attributes"]["id_descarcare"];
        return $array["@attributes"]["stare"];

    }
    public function downloadUBIAnaf(): bool|string
    {

        $filepath = $this->download_id . ".zip";
        $fp = fopen($filepath, 'w+');
        $response = $this->executeCurlRequest($this->download_url . $this->download_id, 'GET', [
            'Authorization: Bearer ' . $this->token
        ], [CURLOPT_FILE => $fp]);
        fclose($fp);
        return $response;
    }


    public function getLastMsgAnaf(): bool|string
    {
        try {
            return $this->executeCurlRequest($this->list_url . $this->vat_number, 'GET', [
                'Authorization: Bearer ' . $this->token
            ]);
        } catch (ClientErrorException $e) {
            // Handle client error (4xx)
            echo 'Client error: ' . $e->getMessage();
        } catch (ServerErrorException $e) {
            // Handle server error (5xx)
            echo 'Server error: ' . $e->getMessage();
        } catch (Exception $e) {
            // Handle other errors
            echo 'Error: ' . $e->getMessage();
        }
        return false;
    }



     function executeCurlRequest(string $url, string $method, array $headers = [], array $options = []): string
     {
         $curl = curl_init();
         $defaultOptions = [
             CURLOPT_URL => $url,
             CURLOPT_RETURNTRANSFER => true,
             CURLOPT_CUSTOMREQUEST => $method,
             CURLOPT_HTTPHEADER => $headers,
         ];
         curl_setopt_array($curl, $defaultOptions + $options);
         $response = curl_exec($curl);
         if (curl_errno($curl)) {
             throw new Exception('Curl error: ' . curl_error($curl));
         }
         $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
         $header = substr($response, 0, $headerSize);
         $body = substr($response, $headerSize);

         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
         if ($httpCode >= 400 && $httpCode < 500) {
             throw new ClientErrorException('Client error: ' . $httpCode . ' - ' . $body);
         } elseif ($httpCode >= 500) {
             throw new ServerErrorException('Server error: ' . $httpCode . ' - ' . $body);
         }
         curl_close($curl);
         return $response;
     }
}