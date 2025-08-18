<?php
// config.php (substitua a classe ApiClient existente por esta)

define('API_BASE_URL', 'http://backend:8000/');

class ApiClient {
    private $baseUrl;

    public function __construct($baseUrl = API_BASE_URL) {
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
    }

    private function makeRequest($method, $endpoint, $data = null) {
        $url = $this->baseUrl . ltrim($endpoint, '/');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // segue redirects (útil para barra final)
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $headers = ['Accept: application/json'];

        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                $headers[] = 'Content-Type: application/json';
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                $headers[] = 'Content-Type: application/json';
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case 'GET':
            default:
                // GET padrão
                break;
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $err = curl_error($ch);
            curl_close($ch);
            return [
                'data' => ['error' => 'Connection failed: ' . $err],
                'status' => 500
            ];
        }

        curl_close($ch);

        $decoded = json_decode($response, true);

       if (is_array($decoded) && array_key_exists('status', $decoded) && array_key_exists('data', $decoded)) {
            $apiStatus = $decoded['status'];
            $apiData = $decoded['data'];
            return [
                'status' => $apiStatus,
                'data' => $apiData,
                'raw' => $decoded // opcional para debug
            ];
        }

        // Caso contrário, retorna o HTTP code e o corpo decodificado (ou raw)
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            // resposta não-JSON
            return [
                'status' => $httpCode,
                'data' => ['raw' => $response, 'json_error' => json_last_error_msg()]
            ];
        }

        return [
            'status' => $httpCode,
            'data' => $decoded
        ];
    }

    public function getProjects() {
        return $this->makeRequest('GET', 'cadastro-de-projetos/'); // com slash final para evitar redirect
    }

    public function getProject($id) {
        return $this->makeRequest('GET', "cadastro-de-projetos/{$id}");
    }

    public function createProject($data) {
        return $this->makeRequest('POST', 'cadastro-de-projetos/', $data);
    }

    public function updateProject($id, $data) {
        return $this->makeRequest('PUT', "cadastro-de-projetos/{$id}", $data);
    }

    public function deleteProject($id) {
        return $this->makeRequest('DELETE', "cadastro-de-projetos/{$id}");
    }
}