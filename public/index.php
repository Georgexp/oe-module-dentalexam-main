<?php
/**
 * Main entry point for Dental Chart Module.
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Your Name <your.email@example.com>
 * @copyright Copyright (c) 2025
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

// Ensure this file is accessed from OpenEMR context
$ignoreAuth = false;
require_once dirname(__FILE__, 5) . '/globals.php';

use OpenEMR\Common\Acl\AclMain;
use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Common\Twig\TwigContainer;
use OpenEMR\Core\Header;
use OpenEMR\Modules\DentalChart\DentalChartService;

// Check access control
if (!AclMain::aclCheckCore('patients', 'dental')) {
    echo xlt('Access denied');
    exit;
}

// Initialize service
$dentalChartService = new DentalChartService();

// Create database tables if they don't exist
$dentalChartService->createDatabaseTables();

// Get patient ID from request
$pid = $_GET['pid'] ?? $pid ?? null;

// If no patient is selected, prompt for selection
if (empty($pid)) {
    // Display patient selector or message
    echo xlt('Please select a patient');
    exit;
}

// Process form submission
$message = '';
$savedChartData = null;

// Get patient information
$patientService = new \OpenEMR\Services\PatientService();
$patient = $patientService->findByPid($pid);

if (!empty($patient[0])) {
    $patientData = $patient[0];
    $patientName = $patientData['fname'] . ' ' . $patientData['lname'];
} else {
    $patientName = xlt("Unknown Patient");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_chart'])) {
    // Verify CSRF
    if (!CsrfUtils::verifyCsrfToken($_POST["csrf_token_form"])) {
        CsrfUtils::csrfNotVerified();
    }
    
    // Prepare data for saving
    $chartData = [
        'patient_id' => $pid,
        'chief_complaint' => $_POST['chiefComplaint'] ?? '',
        'primary_diagnosis' => $_POST['primaryDiagnosis'] ?? '',
        'recommended_treatment' => $_POST['recommendedTreatment'] ?? '',
        'procedures_performed' => $_POST['proceduresPerformed'] ?? '',
        'medications_prescribed' => $_POST['medicationsPrescribed'] ?? '',
        'follow_up_next_visit' => $_POST['followUpNextVisit'] ?? '',
        'grid_data' => $_POST['gridData'] ?? '',
        'user_id' => $_SESSION['authUserID']
    ];
    
    // Save the chart
    $result = $dentalChartService->saveDentalChart($chartData);
    
    if ($result) {
        $message = xlt('Dental chart saved successfully!');
    } else {
        $message = xlt('Error saving dental chart');
    }
}

// Load existing dental chart data
$existingChart = $dentalChartService->getDentalChartByPatientId($pid);

if (!empty($existingChart)) {
    $savedChartData = $existingChart[0];
}

// Prepare data for the template
$templateData = [
    'title' => xlt('Dental Chart'),
    'patient_id' => $pid,
    'patient_name' => $patientName,
    'csrf_token_form' => CsrfUtils::collectCsrfToken(),
    'chart_data' => $savedChartData,
    'message' => $message
];

// Output the HTML header
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo xlt('Dental Chart'); ?></title>
    <?php Header::setupHeader(['jquery', 'bootstrap', 'toastr']); ?>
    <link rel="stylesheet" href="assets/css/dental-chart.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h2><?php echo xlt('Dental Chart'); ?> - <?php echo text($patientName); ?></h2>
                <?php if (!empty($message)) : ?>
                <div class="alert alert-success" role="alert">
                    <?php echo text($message); ?>
                </div>
                <?php endif; ?>

                <form method="post" id="dental-chart-form">
                    <input type="hidden" name="csrf_token_form" value="<?php echo attr($templateData['csrf_token_form']); ?>" />
                    <input type="hidden" name="gridData" id="gridData" value="<?php echo attr($savedChartData['grid_data'] ?? ''); ?>" />

                    <!-- Dental Chart Grid -->
                    <div class="card mb-3">
                        <div class="card-header"><?php echo xlt('Tooth Chart'); ?></div>
                        <div class="card-body">
                            <div class="grid" id="grid"></div>

                            <!-- Legend -->
                            <div class="legend">
                                <h5><?php echo xlt('Legend'); ?></h5>
                                <ul class="list-inline">
                                    <li class="list-inline-item"><span class="color-box" style="background-color: white;"></span> <?php echo xlt('Healthy'); ?></li>
                                    <li class="list-inline-item"><span class="color-box" style="background-color: red;"></span> <?php echo xlt('Caries'); ?></li>
                                    <li class="list-inline-item"><span class="color-box" style="background-color: yellow;"></span> <?php echo xlt('Filling'); ?></li>
                                    <li class="list-inline-item"><span class="color-box" style="background-color: gray;"></span> <?php echo xlt('Missing'); ?></li>
                                    <li class="list-inline-item"><span class="color-box" style="background-color: black;"></span> <?php echo xlt('Extraction'); ?></li>
                                    <li class="list-inline-item"><span class="color-box" style="background-color: #90EE90;"></span> <?php echo xlt('Crown'); ?></li>
                                    <li class="list-inline-item"><span class="color-box" style="background-color: #87CEFA;"></span> <?php echo xlt('Implant'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Clinical Notes -->
                    <div class="card mb-3">
                        <div class="card-header"><?php echo xlt('Clinical Notes'); ?></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="chiefComplaint"><?php echo xlt('Chief Complaint'); ?></label>
                                <textarea class="form-control" id="chiefComplaint" name="chiefComplaint" rows="2"><?php echo text($savedChartData['chief_complaint'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="primaryDiagnosis"><?php echo xlt('Primary Diagnosis'); ?></label>
                                <textarea class="form-control" id="primaryDiagnosis" name="primaryDiagnosis" rows="2"><?php echo text($savedChartData['primary_diagnosis'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="recommendedTreatment"><?php echo xlt('Recommended Treatment'); ?></label>
                                <textarea class="form-control" id="recommendedTreatment" name="recommendedTreatment" rows="2"><?php echo text($savedChartData['recommended_treatment'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="proceduresPerformed"><?php echo xlt('Procedures Performed'); ?></label>
                                <textarea class="form-control" id="proceduresPerformed" name="proceduresPerformed" rows="2"><?php echo text($savedChartData['procedures_performed'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="medicationsPrescribed"><?php echo xlt('Medications Prescribed'); ?></label>
                                <textarea class="form-control" id="medicationsPrescribed" name="medicationsPrescribed" rows="2"><?php echo text($savedChartData['medications_prescribed'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="followUpNextVisit"><?php echo xlt('Follow-up & Next Visit'); ?></label>
                                <textarea class="form-control" id="followUpNextVisit" name="followUpNextVisit" rows="2"><?php echo text($savedChartData['follow_up_next_visit'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group">
                        <button type="submit" name="save_chart" class="btn btn-primary"><?php echo xlt('Save Dental Chart'); ?></button>
                        <button type="button" class="btn btn-secondary" id="clear-form"><?php echo xlt('Clear Form'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include the JavaScript for dental chart functionality -->
    <script src="assets/js/dental-chart.js"></script>
</body>
</html>