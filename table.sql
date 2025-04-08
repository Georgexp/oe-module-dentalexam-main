CREATE TABLE IF NOT EXISTS `dental_exams` (
    `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
    `encounter_id` BIGINT(20) NOT NULL,
    `patient_id` BIGINT(20) NOT NULL,
    `dental_data` TEXT NOT NULL,
    `chief_complaint` TEXT,
    `primary_diagnosis` TEXT,
    `recommended_treatment` TEXT,
    `procedures_performed` TEXT,
    `medications_prescribed` TEXT,
    `follow_up_next_visit` TEXT,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`encounter_id`) REFERENCES `form_encounter` (`id`),
    FOREIGN KEY (`patient_id`) REFERENCES `patient_data` (`pid`)
);