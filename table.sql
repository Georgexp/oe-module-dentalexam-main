-- 
-- Table structure for the dental chart module
-- 

-- Create the dental chart records table
CREATE TABLE IF NOT EXISTS `dental_chart` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pid` bigint(20) NOT NULL COMMENT 'Patient ID',
  `encounter` bigint(20) DEFAULT NULL COMMENT 'Optional encounter link',
  `chart_data` longtext COMMENT 'JSON data for the dental chart',
  `chief_complaint` text COMMENT 'Chief complaint',
  `primary_diagnosis` text COMMENT 'Primary diagnosis',
  `recommended_treatment` text COMMENT 'Recommended treatment',
  `procedures_performed` text COMMENT 'Procedures performed',
  `medications_prescribed` text COMMENT 'Medications prescribed',
  `follow_up_next_visit` text COMMENT 'Follow-up and next visit info',
  `created_by` bigint(20) NOT NULL COMMENT 'User ID who created the record',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'User ID who updated the record',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `encounter` (`encounter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create an audit log table for tracking changes
CREATE TABLE IF NOT EXISTS `dental_chart_audit` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `dental_chart_id` bigint(20) NOT NULL,
  `pid` bigint(20) NOT NULL,
  `action` varchar(10) NOT NULL COMMENT 'CREATE, UPDATE, DELETE',
  `chart_data` longtext COMMENT 'JSON data that was changed',
  `chief_complaint` text COMMENT 'Changed chief complaint',
  `primary_diagnosis` text COMMENT 'Changed primary diagnosis',
  `recommended_treatment` text COMMENT 'Changed recommended treatment',
  `procedures_performed` text COMMENT 'Changed procedures performed',
  `medications_prescribed` text COMMENT 'Changed medications prescribed',
  `follow_up_next_visit` text COMMENT 'Changed follow-up info',
  `action_by` bigint(20) NOT NULL COMMENT 'User ID who performed action',
  `action_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `dental_chart_id` (`dental_chart_id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Add foreign key constraints
ALTER TABLE `dental_chart`
  ADD CONSTRAINT `dental_chart_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `patient_data` (`pid`) ON DELETE CASCADE,
  ADD CONSTRAINT `dental_chart_ibfk_2` FOREIGN KEY (`encounter`) REFERENCES `form_encounter` (`encounter`) ON DELETE SET NULL;

ALTER TABLE `dental_chart_audit`
  ADD CONSTRAINT `dental_chart_audit_ibfk_1` FOREIGN KEY (`dental_chart_id`) REFERENCES `dental_chart` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dental_chart_audit_ibfk_2` FOREIGN KEY (`pid`) REFERENCES `patient_data` (`pid`) ON DELETE CASCADE;