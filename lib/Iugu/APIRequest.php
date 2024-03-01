<?php

class Iugu_APIRequest
{
    public function __construct()
    {
    }

    private function _defaultHeaders($headers = [])
    {
        $headers[] = 'Authorization: Basic '.base64_encode(Iugu::getApiKey().':');
        $headers[] = 'Accept: application/json';
        $headers[] = 'Accept-Charset: utf-8';
        $headers[] = 'User-Agent: Iugu PHPLibrary';
        $headers[] = 'Accept-Language: pt-br;q=0.9,pt-BR';

        return $headers;
    }

    public function request($method, $url, $data = [])
    {
        global $iugu_last_api_response_code;

        if (Iugu::getApiKey() == null) {
            Iugu_Utilities::authFromEnv();
        }

        if (Iugu::getApiKey() == null) {
            throw new IuguAuthenticationException('Chave de API não configurada. Utilize Iugu::setApiKey(...) para configurar.');
        }

        $headers = $this->_defaultHeaders();

        list($response_body, $response_code) = $this->requestWithCURL($method, $url, $headers, $data);

        error_log('IUGU - Requisição executada, Response Code: '.$response_code.', Response: '.$response_body);

        $response = json_decode($response_body);
        $jsonError = json_last_error();
        if(is_null($response) || $jsonError != JSON_ERROR_NONE)
        {
            switch($jsonError)
            {
                case JSON_ERROR_NONE:           $error = 'Nenhum erro identificado';            break;
                case JSON_ERROR_DEPTH:          $error = 'Máxima profundidade de nós atingida'; break;
                case JSON_ERROR_STATE_MISMATCH: $error = 'JSON inválido ou mal formado';        break;
                case JSON_ERROR_CTRL_CHAR:      $error = 'Caractere de controle encontrado';    break;
                case JSON_ERROR_SYNTAX:         $error = 'JSON malformado';                     break;
                case JSON_ERROR_UTF8:           $error = 'Carateres UTF-8 malformados';         break;
                default:                        $error = 'Erro desconhecido ('.$jsonError.')';  break;
            }
            error_log('IUGU - Erro de parse do JSON: '.$error.
                      ', Response code: '.$response_code.', Mensagem de erro: '.json_last_error_msg().', Response: '.$response_body);
            throw new IuguObjectNotFound($response_body);
        }

        if ($response_code == 404) {
            throw new IuguObjectNotFound($response_body);
        }

        if (isset($response->errors)) {
            if ((gettype($response->errors) != 'string') && count(get_object_vars($response->errors)) == 0) {
                unset($response->errors);
            } elseif ((gettype($response->errors) != 'string') && count(get_object_vars($response->errors)) > 0) {
                $response->errors = (array) $response->errors;
            }

            if (isset($response->errors) && (gettype($response->errors) == 'string')) {
                $response->errors = $response->errors;
            }
        }

        $iugu_last_api_response_code = $response_code;

        return $response;
    }

    private function encodeParameters($method, $url, $data = [])
    {
        $method = strtolower($method);

        switch ($method) {
        case 'get':
        case 'delete':
            $paramsInURL = Iugu_Utilities::arrayToParams($data);
            $data = null;
            $url = (strpos($url, '?')) ? $url.'&'.$paramsInURL : $url.'?'.$paramsInURL;
            break;
        case 'post':
        case 'put':
            $data = Iugu_Utilities::arrayToParams($data);
            break;
        }

        return [$url, $data];
    }

    private function requestWithCURL($method, $url, $headers, $data = [])
    {
        $curl = curl_init();

        $opts = [];

        list($url, $data) = $this->encodeParameters($method, $url, $data);

        if (strtolower($method) == 'post') {
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $data;
        }
        if (strtolower($method) == 'delete') {
            $opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
        }

        if (strtolower($method) == 'put') {
            $opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
            $opts[CURLOPT_POSTFIELDS] = $data;
        }

        $opts[CURLOPT_URL] = $url;
        $opts[CURLOPT_RETURNTRANSFER] = true;
        $opts[CURLOPT_CONNECTTIMEOUT] = 30;
        $opts[CURLOPT_TIMEOUT] = 80;
        $opts[CURLOPT_RETURNTRANSFER] = true;
        $opts[CURLOPT_HTTPHEADER] = $headers;

        $opts[CURLOPT_SSL_VERIFYHOST] = 2;
        $opts[CURLOPT_SSL_VERIFYPEER] = true;

        curl_setopt_array($curl, $opts);

        $response_body = curl_exec($curl);
        $response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        return [$response_body, $response_code];
    }
}
