<?php

/**
 * Dental Chart Service
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Your Name <your.email@example.com>
 * @copyright Copyright (c) 2025
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace OpenEMR\Modules\DentalChart;

use OpenEMR\Common\Database\QueryUtils;
use OpenEMR\Common\Logging\SystemLogger;
use OpenEMR\Common\Acl\AclMain;

/**
 * Service class for the Dental Chart module
 */
class DentalChartService
{
    /**
     * @var SystemLogger
     */
    private $logger;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logger = new SystemLogger();
    }

    /**
     * Get dental chart data for a patient
     *
     * @param int $pid Patient ID
     * @return array Dental chart data
     */
    public function getPatientChartData($pid)
    {
        try {
            // Validate patient ID
            if (empty($pid)) {
                throw new \InvalidArgumentException("Patient ID is required");
            }
            
            // Validate permissions
            if (!AclMain::aclCheckCore('patients', 'med')) {
                throw new \Exception("Access denied. User does not have sufficient permissions.");
            }
            
            $sql = "SELECT * FROM dental_chart WHERE pid = ? ORDER BY created_at DESC LIMIT 1";
            $result = QueryUtils::fetchRecords($sql, [$pid]);
            
            if (!empty($result[0])) {
                $data = $result[0];
                // Parse JSON data if needed
                if (!empty($data['chart_data'])) {
                    $data['chart_data'] = json_decode($data['chart_data'], true);
                }
                return $data;
            }
            
            // Return empty result if no data found
            return [
                'pid' => $pid,
                'chart_data' => null,
                'chief_complaint' => '',
                'primary_diagnosis' => '',
                'recommended_treatment' => '',
                'procedures_performed' => '',
                'medications_prescribed' => '',
                'follow_up_next_visit' => ''
            ];
        } catch (\Exception $e) {
            $this->logger->errorLogCaller("Error retrieving dental chart data: " . $e->getMessage(), ['pid' => $pid]);
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Save dental chart data
     *
     * @param array $data Chart data array
     * @return array Result array with success/error information
     */
    public function saveChartData($data)
    {
        try {
            // Validate required fields
            if (empty($data['pid'])) {
                throw new \InvalidArgumentException("Patient ID is required");
            }
            
            // Validate permissions
            if (!AclMain::aclCheckCore('patients', 'med')) {
                throw new \Exception("Access denied. User does not have sufficient permissions.");
            }
            
            $pid = $data['pid'];
            $encounter = $data['encounter'] ?? null;
            $chartData = $data['dental_chart_data'] ?? '{}';
            $userId = $_SESSION['authUserID'] ?? null;
            
            // Check if record exists
            $sql = "SELECT id FROM dental_chart WHERE pid = ?";
            $existingRecord = QueryUtils::fetchRecords($sql, [$pid]);
            
            if (!empty($existingRecord[0])) {