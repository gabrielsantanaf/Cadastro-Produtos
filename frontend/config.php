<?php
// Configuração para ambiente Docker
define('API_BASE_URL', 'http://backend:8000');

class ApiClient {
    private $baseUrl;

    public function __construct($baseUrl = API_BASE_URL) {
        $this->baseUrl = $baseUrl;
    }

    private function makeRequest($method, $endpoint, $data = null) {
        $url = $this->baseUrl . $endpoint;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        switch($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_error($ch)) {
            curl_close($ch);
            return [
                'data' => ['error' => 'Connection failed: ' . curl_error($ch)],
                'status' => 500
            ];
        }

        curl_close($ch);

        return [
            'data' => json_decode($response, true),
            'status' => $httpCode
        ];
    }

    public function getProjects() {
        return $this->makeRequest('GET', 'cadastro-de-projetos');
    }

    public function getProject($id) {
        return $this->makeRequest('GET', "cadastro-de-projetos/{id}");
    }

    public function createProject($data) {
        return $this->makeRequest('POST', 'cadastro-de-projetos', $data);
    }

    public function updateProject($id, $data) {
        return $this->makeRequest('PUT', "cadastro-de-projetos/{id}", $data);
    }

    public function deleteProject($id) {
        return $this->makeRequest('DELETE', "cadastro-de-projetos/{id}");
    }
}
?>