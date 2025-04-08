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
use OpenEMR\Services\BaseService;

class DentalChartService extends BaseService
{
    private $logger;
    
    /**
     * @var string
     */
    const TABLE_NAME = 'dental_chart';
    
    public function __construct()
    {
        parent::__construct(self::TABLE_NAME);
        $this->logger = new SystemLogger();
    }
    
    /**
     * Create the dental chart database table if it doesn't exist
     */
    public function createDatabaseTables()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . self::TABLE_NAME . "` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `patient_id` bigint(20) NOT NULL,
            `chief_complaint` text,
            `primary_diagnosis` text,
            `recommended_treatment` text,
            `procedures_performed` text,
            `medications_prescribed` text,
            `follow_up_next_visit` text,
            `grid_data` text,
            `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
            `date_updated` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `created_by` bigint(20) DEFAULT NULL,
            `updated_by` bigint(20) DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `patient_id` (`patient_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        
        QueryUtils::sqlStatementThrowException($sql);
    }
    
    /**
     * Save dental chart data
     *
     * @param array $data
     * @return int|bool
     */
    public function saveDentalChart($data)
    {
        $sql = "INSERT INTO " . self::TABLE_NAME . " (
                patient_id,
                chief_complaint,
                primary_diagnosis,
                recommended_treatment,
                procedures_performed,
                medications_prescribed,
                follow_up_next_visit,
                grid_data,
                created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                chief_complaint = VALUES(chief_complaint),
                primary_diagnosis = VALUES(primary_diagnosis),
                recommended_treatment = VALUES(recommended_treatment),
                procedures_performed = VALUES(procedures_performed),
                medications_prescribed = VALUES(medications_prescribed),
                follow_up_next_visit = VALUES(follow_up_next_visit),
                grid_data = VALUES(grid_data),
                updated_by = VALUES(created_by)";
            
        $params = [
            $data['patient_id'],
            $data['chief_complaint'] ?? null,
            $data['primary_diagnosis'] ?? null,
            $data['recommended_treatment'] ?? null,
            $data['procedures_performed'] ?? null,
            $data['medications_prescribed'] ?? null,
            $data['follow_up_next_visit'] ?? null,
            $data['grid_data'] ?? null,
            $data['user_id']
        ];
        
        return QueryUtils::sqlInsert($sql, $params);
    }
    
    /**
     * Get dental chart data for a patient
     *
     * @param int $patientId
     * @return array|null
     */
    public function getDentalChartByPatientId($patientId)
    {
        $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE patient_id = ? ORDER BY date_updated DESC LIMIT 1";
        return QueryUtils::fetchRecords($sql, [$patientId]);
    }
    
    /**
     * Check if the patient has a dental chart
     *
     * @param int $patientId
     * @return bool
     */
    public function hasDentalChart($patientId)
    {
        $sql = "SELECT COUNT(*) AS count FROM " . self::TABLE_NAME . " WHERE patient_id = ?";
        $result = QueryUtils::fetchRecords($sql, [$patientId]);
        return $result[0]['count'] > 0;
    }
}