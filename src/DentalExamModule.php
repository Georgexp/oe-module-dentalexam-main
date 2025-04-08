<?php

namespace OpenEMR\Modules\DentalExam;

use OpenEMR\Common\Csrf\CsrfUtils;
use OpenEMR\Common\Logging\SystemLogger;
use OpenEMR\Core\Header;
use OpenEMR\Services\EncounterService;

class DentalExamModule
{
    private $twig;
    private $logger;
    private $encounterService;
    private $moduleDir;

    /**
     * Constructor to initialize dependencies and setup.
     */
    public function __construct()
    {
        // Initialize Twig environment
        $this->twig = $GLOBALS['kernel']->getTwigEnvironment();
        if (!$this->twig) {
            throw new \RuntimeException("Twig environment could not be initialized.");
        }

        // Initialize system logger
        $this->logger = new SystemLogger();
        $this->logger->debug("DentalExamModule initialized");

        // Initialize EncounterService for encounter-related operations
        $this->encounterService = new EncounterService();

        // Set module directory for asset paths
        $this->moduleDir = $GLOBALS['vendor_dir'] . "/custom-modules/oe-module-dental-exam";
        if (!file_exists($this->moduleDir)) {
            $this->logger->error("Module directory not found: " . $this->moduleDir);
            throw new \RuntimeException("Module directory not found.");
        }
    }

    /**
     * Get the module's display name.
     *
     * @return string
     */
    public function getModuleName()
    {
        return "Dental Exam";
    }

    /**
     * Render the dental exam form for a specific patient and encounter.
     *
     * @param int|null $formId The form ID (if editing an existing form).
     * @param int $pid Patient ID.
     * @param int $encounter Encounter ID.
     */
    public function renderForm($formId, $pid, $encounter)
    {
        try {
            // Validate input parameters
            if (empty($pid) || empty($encounter)) {
                throw new \InvalidArgumentException("Patient ID and Encounter ID are required.");
            }

            // Check if encounter exists
            $encounterData = $this->encounterService->getEncounter($encounter);
            if (!$encounterData) {
                throw new \RuntimeException("Invalid encounter ID: " . $encounter);
            }

            // Load existing data if formId is provided
            $formData = [];
            if ($formId) {
                $formData = $this->loadFormData($formId);
            }

            // Render the Twig template
            $this->twig->display(
                '@DentalExam/form.twig',
                [
                    'pid' => $pid,
                    'encounter' => $encounter,
                    'csrf_token' => CsrfUtils::collectCsrfToken(),
                    'baseUrl' => $GLOBALS['webroot'],
                    'formData' => $formData,
                ]
            );
        } catch (\Exception $e) {
            $this->logger->error("Error rendering Dental Exam form: " . $e->getMessage());
            echo "An error occurred while loading the form. Please contact support.";
        }
    }

    /**
     * Save the dental exam form data to the database.
     *
     * @param array $formData Form data from POST request.
     * @param int $pid Patient ID.
     * @param int $encounter Encounter ID.
     * @return bool Success status.
     */
    public function saveForm($formData, $pid, $encounter)
    {
        try {
            // Validate CSRF token
            if (!CsrfUtils::verifyCsrfToken($formData['csrf_token_form'] ?? '')) {
                throw new \RuntimeException("Invalid CSRF token.");
            }

            // Validate required fields
            if (empty($pid) || empty($encounter)) {
                throw new \InvalidArgumentException("Patient ID and Encounter ID are required.");
            }

            // Prepare tooth data as JSON
            $toothData = json_encode($formData['tooth_data'] ?? [], JSON_THROW_ON_ERROR);

            // SQL query to insert or update form data
            $sql = "INSERT INTO dental_exam (
                        pid, 
                        encounter, 
                        tooth_data, 
                        chief_complaint, 
                        primary_diagnosis, 
                        recommended_treatment, 
                        procedures_performed, 
                        medications_prescribed, 
                        follow_up_next_visit
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                        tooth_data = VALUES(tooth_data), 
                        chief_complaint = VALUES(chief_complaint),
                        primary_diagnosis = VALUES(primary_diagnosis),
                        recommended_treatment = VALUES(recommended_treatment),
                        procedures_performed = VALUES(procedures_performed),
                        medications_prescribed = VALUES(medications_prescribed),
                        follow_up_next_visit = VALUES(follow_up_next_visit)";

            $bind = [
                $pid,
                $encounter,
                $toothData,
                $formData['chief_complaint'] ?? '',
                $formData['primary_diagnosis'] ?? '',
                $formData['recommended_treatment'] ?? '',
                $formData['procedures_performed'] ?? '',
                $formData['medications_prescribed'] ?? '',
                $formData['follow_up_next_visit'] ?? ''
            ];

            // Execute the query
            $result = sqlStatement($sql, $bind);
            if ($result === false) {
                throw new \RuntimeException("Failed to save form data to database.");
            }

            $this->logger->info("Dental Exam form saved successfully for PID: $pid, Encounter: $encounter");
            return true;
        } catch (\Exception $e) {
            $this->logger->error("Error saving Dental Exam form: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Load existing form data from the database.
     *
     * @param int $formId The ID of the form to load.
     * @return array Form data.
     */
    private function loadFormData($formId)
    {
        $sql = "SELECT * FROM dental_exam WHERE id = ?";
        $result = sqlQuery($sql, [$formId]);

        if ($result) {
            $result['tooth_data'] = json_decode($result['tooth_data'], true);
            return $result;
        }

        return [];
    }

    /**
     * Register the module with OpenEMR hooks (e.g., menu or encounter form).
     */
    public function registerHooks()
    {
        // Example: Add to encounter form menu
        $GLOBALS['hooksArray']['encounter_form_menu'][] = [
            'name' => 'Dental Exam',
            'description' => 'Dental Examination Form',
            'callback' => [$this, 'renderForm'],
            'priority' => 10,
        ];
    }
}