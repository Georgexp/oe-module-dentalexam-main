<?php
// save.php
require_once(dirname(__FILE__) . '/../../globals.php');

if (!acl_check('patients', 'write')) {
    die("Unauthorized access");
}

$encounterId = $_POST['encounter_id'] ?? '';
$patientId = $_POST['patient_id'] ?? '';
$dentalData = $_POST['dental_data'] ?? '';
$chiefComplaint = $_POST['chiefComplaint'] ?? '';
$primaryDiagnosis = $_POST['primaryDiagnosis'] ?? '';
$recommendedTreatment = $_POST['recommendedTreatment'] ?? '';
$proceduresPerformed = $_POST['proceduresPerformed'] ?? '';
$medicationsPrescribed = $_POST['medicationsPrescribed'] ?? '';
$followUpNextVisit = $_POST['followUpNextVisit'] ?? '';

if ($encounterId && $patientId && $dentalData) {
    // Sanitize inputs as needed
    $sql = "INSERT INTO dental_exams (encounter_id, patient_id, dental_data, chief_complaint, primary_diagnosis, recommended_treatment, procedures_performed, medications_prescribed, follow_up_next_visit, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE 
            dental_data = VALUES(dental_data),
            chief_complaint = VALUES(chief_complaint),
            primary_diagnosis = VALUES(primary_diagnosis),
            recommended_treatment = VALUES(recommended_treatment),
            procedures_performed = VALUES(procedures_performed),
            medications_prescribed = VALUES(medications_prescribed),
            follow_up_next_visit = VALUES(follow_up_next_visit),
            created_at = VALUES(created_at)";
    sqlStatement($sql, [
        $encounterId,
        $patientId,
        $dentalData,
        $chiefComplaint,
        $primaryDiagnosis,
        $recommendedTreatment,
        $proceduresPerformed,
        $medicationsPrescribed,
        $followUpNextVisit
    ]);

    // Redirect back to patient chart or encounter
    header("Location: $web_root/patient_file/encounter/encounter_top.php");
    exit;
} else {
    die("Missing required fields");
}