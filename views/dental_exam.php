<?php
// views/dental_exam.php
$encounterId = $_GET['encounter'] ?? '';
$patientId = $_GET['pid'] ?? '';
$existingData = []; // Fetch from controller if exists
if ($encounterId && $patientId) {
    $sql = "SELECT * FROM dental_exams WHERE encounter_id = ? AND patient_id = ?";
    $result = sqlQuery($sql, [$encounterId, $patientId]);
    if ($result) {
        $existingData = $result;
        $existingData['dental_data'] = json_decode($result['dental_data'], true);
    }
}
$webRoot = $GLOBALS['web_root'];
?>
<title>Dental Exam</title>

<form id="dentalExamForm" method="post" action="<?php echo $webRoot; ?>/modules/oe-module-dental-exam/save.php">
    <div class="grid" id="grid"></div>

    <!-- Legend -->
    <div class="legend">
        <ul>
            <li><span style="background-color: white; display: inline-block; width: 20px; height: 20px; border: 1px solid black;"></span> Healthy</li>
            <li><span style="background-color: red; display: inline-block; width: 20px; height: 20px; border: 1px solid black;"></span> Caries</li>
            <li><span style="background-color: yellow; display: inline-block; width: 20px; height: 20px; border: 1px solid black;"></span> Filling</li>
            <li><span style="background-color: rgb(128,128,128); display: inline-block; width: 20px; height: 20px; border: 1px solid black;"></span> Missing</li>
            <li><span style="background-color: black; display: inline-block; width: 20px; height: 20px; border: 1px solid black;"></span> Extraction</li>
            <li><span style="background-color: #90EE90; display: inline-block; width: 20px; height: 20px; border: 1px solid black;"></span> Crown</li>
            <li><span style="background-color: #87CEFA; display: inline-block; width: 20px; height: 20px; border: 1px solid black;"></span> Implant</li>
        </ul>
    </div>

    <!-- Text Areas -->
    <div class="text-box-section">
        <div class="text-box">
            <label for="chiefComplaint">Chief Complaint:</label>
            <textarea id="chiefComplaint" name="chiefComplaint"><?php echo htmlspecialchars($existingData['chief_complaint'] ?? ''); ?></textarea>
        </div>
        <div class="text-box">
            <label for="primaryDiagnosis">Primary Diagnosis:</label>
            <textarea id="primaryDiagnosis" name="primaryDiagnosis"><?php echo htmlspecialchars($existingData['primary_diagnosis'] ?? ''); ?></textarea>
        </div>
        <div class="text-box">
            <label for="recommendedTreatment">Recommended Treatment:</label>
            <textarea id="recommendedTreatment" name="recommendedTreatment"><?php echo htmlspecialchars($existingData['recommended_treatment'] ?? ''); ?></textarea>
        </div>
        <div class="text-box">
            <label for="proceduresPerformed">Procedures Performed:</label>
            <textarea id="proceduresPerformed" name="proceduresPerformed"><?php echo htmlspecialchars($existingData['procedures_performed'] ?? ''); ?></textarea>
        </div>
        <div class="text-box">
            <label for="medicationsPrescribed">Medications Prescribed:</label>
            <textarea id="medicationsPrescribed" name="medicationsPrescribed"><?php echo htmlspecialchars($existingData['medications_prescribed'] ?? ''); ?></textarea>
        </div>
        <div class="text-box">
            <label for="followUpNextVisit">Follow-up & Next Visit:</label>
            <textarea id="followUpNextVisit" name="followUpNextVisit"><?php echo htmlspecialchars($existingData['follow_up_next_visit'] ?? ''); ?></textarea>
        </div>
    </div>

    <!-- Hidden Inputs -->
    <input type="hidden" name="dental_data" id="dental_data">
    <input type="hidden" name="encounter_id" value="<?php echo htmlspecialchars($encounterId); ?>">
    <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($patientId); ?>">

    <button type="submit">Save</button>
</form>

<!-- Load Assets -->
<link rel="stylesheet" href="<?php echo $webRoot; ?>/interface/modules/custom_modules/oe-module-dentalexam/assets/css/dental_exam.css">
<script>
    var initialDentalData = <?php echo json_encode($existingData['dental_data'] ?? []); ?>;
</script>
<script src="<?php echo $webRoot; ?>/interface/modules/custom_modules/oe-module-dentalexam/assets/js/dental_exam.js"></script>
