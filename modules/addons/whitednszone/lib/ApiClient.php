<?php

namespace WhiteDNSZone;

/**
 * WhiteDNSZone API Client
 */
class ApiClient
{
    private $apiUrl;
    private $apiKey;
    private $lastError;

    /**
     * Constructor
     *
     * @param string $apiUrl
     * @param string $apiKey
     */
    public function __construct($apiUrl, $apiKey)
    {
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->apiKey = $apiKey;
        $this->lastError = null;
    }

    /**
     * Make API request
     *
     * @param string $endpoint
     * @param string $method
     * @param array $data
     * @return array|false
     */
    private function request($endpoint, $method = 'GET', $data = [])
    {
        $url = $this->apiUrl . $endpoint;
        
        $ch = curl_init();
        
        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json',
        ];
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        if ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif (!empty($data)) {
            $url .= '?' . http_build_query($data);
            curl_setopt($ch, CURLOPT_URL, $url);
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            $this->lastError = 'cURL Error: ' . $error;
            return false;
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode >= 400) {
            $this->lastError = isset($result['message']) ? $result['message'] : 'API Error: HTTP ' . $httpCode;
            return false;
        }
        
        return $result;
    }

    /**
     * Get last error message
     *
     * @return string|null
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * List all zones
     *
     * @return array|false
     */
    public function listZones()
    {
        return $this->request('/zones');
    }

    /**
     * Get zone details
     *
     * @param string $zoneId
     * @return array|false
     */
    public function getZone($zoneId)
    {
        return $this->request('/zones/' . $zoneId);
    }

    /**
     * Create new zone
     *
     * @param string $domain
     * @param array $nameservers
     * @return array|false
     */
    public function createZone($domain, $nameservers = [])
    {
        $data = [
            'domain' => $domain,
            'nameservers' => $nameservers,
        ];
        return $this->request('/zones', 'POST', $data);
    }

    /**
     * Delete zone
     *
     * @param string $zoneId
     * @return array|false
     */
    public function deleteZone($zoneId)
    {
        return $this->request('/zones/' . $zoneId, 'DELETE');
    }

    /**
     * List records for a zone
     *
     * @param string $zoneId
     * @return array|false
     */
    public function listRecords($zoneId)
    {
        return $this->request('/zones/' . $zoneId . '/records');
    }

    /**
     * Get record details
     *
     * @param string $zoneId
     * @param string $recordId
     * @return array|false
     */
    public function getRecord($zoneId, $recordId)
    {
        return $this->request('/zones/' . $zoneId . '/records/' . $recordId);
    }

    /**
     * Create DNS record
     *
     * @param string $zoneId
     * @param array $recordData
     * @return array|false
     */
    public function createRecord($zoneId, $recordData)
    {
        return $this->request('/zones/' . $zoneId . '/records', 'POST', $recordData);
    }

    /**
     * Update DNS record
     *
     * @param string $zoneId
     * @param string $recordId
     * @param array $recordData
     * @return array|false
     */
    public function updateRecord($zoneId, $recordId, $recordData)
    {
        return $this->request('/zones/' . $zoneId . '/records/' . $recordId, 'PUT', $recordData);
    }

    /**
     * Delete DNS record
     *
     * @param string $zoneId
     * @param string $recordId
     * @return array|false
     */
    public function deleteRecord($zoneId, $recordId)
    {
        return $this->request('/zones/' . $zoneId . '/records/' . $recordId, 'DELETE');
    }

    /**
     * Bulk create records
     *
     * @param string $zoneId
     * @param array $records
     * @return array|false
     */
    public function bulkCreateRecords($zoneId, $records)
    {
        return $this->request('/zones/' . $zoneId . '/records/bulk', 'POST', ['records' => $records]);
    }

    /**
     * Bulk delete records
     *
     * @param string $zoneId
     * @param array $recordIds
     * @return array|false
     */
    public function bulkDeleteRecords($zoneId, $recordIds)
    {
        return $this->request('/zones/' . $zoneId . '/records/bulk', 'DELETE', ['record_ids' => $recordIds]);
    }

    /**
     * Get DNSSEC details
     *
     * @param string $zoneId
     * @return array|false
     */
    public function getDNSSEC($zoneId)
    {
        return $this->request('/zones/' . $zoneId . '/dnssec');
    }

    /**
     * Enable DNSSEC
     *
     * @param string $zoneId
     * @return array|false
     */
    public function enableDNSSEC($zoneId)
    {
        return $this->request('/zones/' . $zoneId . '/dnssec', 'POST');
    }

    /**
     * Disable DNSSEC
     *
     * @param string $zoneId
     * @return array|false
     */
    public function disableDNSSEC($zoneId)
    {
        return $this->request('/zones/' . $zoneId . '/dnssec', 'DELETE');
    }

    /**
     * Get zone audit log
     *
     * @param string $zoneId
     * @param array $filters
     * @return array|false
     */
    public function getAuditLog($zoneId, $filters = [])
    {
        return $this->request('/zones/' . $zoneId . '/audit', 'GET', $filters);
    }

    /**
     * Check DNS propagation
     *
     * @param string $domain
     * @param string $recordType
     * @return array|false
     */
    public function checkPropagation($domain, $recordType = 'A')
    {
        return $this->request('/propagation/check', 'GET', [
            'domain' => $domain,
            'type' => $recordType,
        ]);
    }
}
