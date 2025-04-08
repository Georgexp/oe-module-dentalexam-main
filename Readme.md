# OpenEMR Dental Chart Module

A comprehensive dental charting system for OpenEMR. This module allows dentists and dental staff to document, save, and retrieve dental charts for patients.

## Features

- Interactive dental chart grid with color-coded tooth status
- Patient record management
- Diagnostic and treatment documentation
- Saves patient dental records to the database
- Full integration with OpenEMR patient system

## Installation

1. Download the module
2. Extract it to the `interface/modules/custom_modules/` directory of your OpenEMR installation
3. Enable the module in the OpenEMR module configuration screen

## Usage

Navigate to the Dental Chart module from the patient dashboard or modules menu.

## License

This module is licensed under the GNU General Public License, version 3. See LICENSE file for details.

// File structure:
// dentalchart/
// ├── *README.md
// ├── *composer.json
// ├── *index.php 
// ├── src/
// │   ├── *Bootstrap.php
// │   └── *DentalChartService.php
// ├── templates/
// │   └── dental_chart.php
// ├── public/
// │   ├── assets/
// │   │   ├── css/
// │   │   │   └── *dental-chart.css
// │   │   └── js/
// │   │       └── dental-chart.js
// │   └── *index.php
// └── vendor/
