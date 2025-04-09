<?php
/**
 * Dental Chart Template for OpenEMR
 * 
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Your Name <your.email@example.com>
 * @copyright Copyright (c) 2025
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

// Ensure this script is being called from OpenEMR
if (!defined('ABSPATH')) {
    exit;
}

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Core\Header;

// Retrieve the patient data
$pid = $GLOBALS['pid'] ?? null;

// Check if we have a patient
if (empty($pid)) {
    echo "<div class='alert alert-warning'>" . xlt("No patient selected") . "</div>";
    return;
}

// Get dental chart data for this patient
$dental_chart_data = (new \OpenEMR\Modules\DentalChart\DentalChartService())->getPatientChartData($pid);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php Header::setupHeader(['jquery', 'bootstrap']); ?>
    <link rel="stylesheet" href="<?php echo $GLOBALS['webroot']; ?>/interface/modules/custom_modules/oe-module-dentalchart/public/assets/css/dental-chart.css">
    <title><?php echo xlt('Dental Chart'); ?></title>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h2><?php echo xlt('Dental Chart'); ?></h2>
                <hr>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="grid" id="dental-grid"></div>
            </div>
        </div>

        <!-- Legend Section -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><?php echo xlt('Legend'); ?></div>
                    <div class="card-body">
                        <div class="legend">
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: white;"></span>
                                <span class="legend-text"><?php echo xlt('Healthy'); ?></span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: red;"></span>
                                <span class="legend-text"><?php echo xlt('Caries'); ?></span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: yellow;"></span>
                                <span class="legend-text"><?php echo xlt('Filling'); ?></span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: gray;"></span>
                                <span class="legend-text"><?php echo xlt('Missing'); ?></span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: black;"></span>
                                <span class="legend-text"><?php echo xlt('Extraction'); ?></span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: #90EE90;"></span>
                                <span class="legend-text"><?php echo xlt('Crown'); ?></span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: #87CEFA;"></span>
                                <span class="legend-text"><?php echo xlt('Implant'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Text Input Fields -->
        <div class="row mt-4">
            <div class="col-md-12">
                <form id="dental-chart-form">
                    <input type="hidden" name="csrf_token_form" value="<?php echo attr(CsrfUtils::collectCsrfToken()); ?>" />
                    <input type="hidden" name="pid" value="<?php echo attr($pid); ?>" />
                    <input type="hidden" name="dental_chart_data" id="dental_chart_data" value="" />

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="chiefComplaint"><?php echo xlt('Chief Complaint'); ?>:</label>
                                <textarea id="chiefComplaint" name="chiefComplaint" class="form-control"><?php echo text($dental_chart_data['chief_complaint'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="primaryDiagnosis"><?php echo xlt('Primary Diagnosis'); ?>:</label>
                                <textarea id="primaryDiagnosis" name="primaryDiagnosis" class="form-control"><?php echo text($dental_chart_data['primary_diagnosis'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recommendedTreatment"><?php echo xlt('Recommended Treatment'); ?>:</label>
                                <textarea id="recommendedTreatment" name="recommendedTreatment" class="form-control"><?php echo text($dental_chart_data['recommended_treatment'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="proceduresPerformed"><?php echo xlt('Procedures Performed'); ?>:</label>
                                <textarea id="proceduresPerformed" name="proceduresPerformed" class="form-control"><?php echo text($dental_chart_data['procedures_performed'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="medicationsPrescribed"><?php echo xlt('Medications Prescribed'); ?>:</label>
                                <textarea id="medicationsPrescribed" name="medicationsPrescribed" class="form-control"><?php echo text($dental_chart_data['medications_prescribed'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="followUpNextVisit"><?php echo xlt('Follow-up & Next Visit'); ?>:</label>
                                <textarea id="followUpNextVisit" name="followUpNextVisit" class="form-control"><?php echo text($dental_chart_data['follow_up_next_visit'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12 text-center">
                            <button type="button" id="saveDentalChart" class="btn btn-primary"><?php echo xlt('Save'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="<?php echo $GLOBALS['webroot']; ?>/interface/modules/custom_modules/oe-module-dentalchart/public/assets/js/dental-chart.js"></script>
    <script>
        $(function() {
            // Initialize the dental chart with saved data
            if (<?php echo !empty($dental_chart_data['chart_data']) ? 'true' : 'false'; ?>) {
                initDentalChart(<?php echo $dental_chart_data['chart_data'] ?? '{}'; ?>);
            } else {
                initDentalChart({});
            }

            // Save button handler
            $('#saveDentalChart').on('click', function() {
                // Get chart data from the UI
                const chartData = collectChartData();
                
                // Update the hidden input with chart data
                $('#dental_chart_data').val(JSON.stringify(chartData));
                
                // Submit the form via AJAX
                $.ajax({
                    url: '<?php echo $GLOBALS['webroot']; ?>/interface/modules/custom_modules/oe-module-dentalchart/public/index.php',
                    type: 'POST',
                    data: $('#dental-chart-form').serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(<?php echo xlj('Dental chart saved successfully'); ?>);
                        } else {
                            alert(<?php echo xlj('Error saving dental chart'); ?> + ': ' + response.message);
                        }
                    },
                    error: function() {
                        alert(<?php echo xlj('Server error while saving dental chart'); ?>);
                    }
                });
            });
        });
    </script>
</body>
</html>