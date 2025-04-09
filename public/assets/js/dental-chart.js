/**
 * Dental Chart JavaScript for OpenEMR
 * 
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Your Name <your.email@example.com>
 * @copyright Copyright (c) 2025
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

// Available tooth colors
const TOOTH_COLORS = ['white', 'red', 'yellow', 'gray', 'black', '#90EE90', '#87CEFA'];
const COLOR_MEANINGS = {
    'white': 'healthy',
    'red': 'caries',
    'yellow': 'filling',
    'gray': 'missing',
    'black': 'extraction',
    '#90EE90': 'crown',
    '#87CEFA': 'implant'
};

// Track the chart data
let dentalChartData = {};

/**
 * Initialize the dental chart
 * @param {Object} savedData - Previously saved chart data
 */
function initDentalChart(savedData) {
    const grid = document.getElementById('dental-grid');
    
    // Define labels for each row
    const row1Labels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
    const row2Labels = Array.from({ length: 16 }, (_, i) => (i + 1).toString()); // 1-16
    const row3Labels = Array.from({ length: 16 }, (_, i) => (32 - i).toString()); // 32-17
    const row4Labels = ['T', 'S', 'R', 'Q', 'P', 'O', 'N', 'M', 'L', 'K']; // T-K descending

    const allRows = [row1Labels, row2Labels, row3Labels, row4Labels];
    
    // Clear the grid if needed
    grid.innerHTML = '';

    // Create 4 rows with their respective labels
    allRows.forEach((labels, rowIndex) => {
        const row = document.createElement('div');
        row.className = 'row';
        
        labels.forEach((label, colIndex) => {
            const container = createToothContainer(label, rowIndex, colIndex);
            row.appendChild(container);
        });
        
        grid.appendChild(row);
    });

    // Load saved data if available
    if (savedData && Object.keys(savedData).length > 0) {
        dentalChartData = savedData;
        applyChartData();
    }
}

/**
 * Create a single tooth container
 * @param {string} label - Tooth label
 * @param {number} rowIndex - Row index
 * @param {number} colIndex - Column index
 * @returns {HTMLElement} - The container element
 */
function createToothContainer(label, rowIndex, colIndex) {
    const wrapper = document.createElement('div');
    wrapper.className = 'container-wrapper';
    wrapper.dataset.tooth = label;

    const labelDiv = document.createElement('div');
    labelDiv.className = 'label';
    labelDiv.textContent = label;
    wrapper.appendChild(labelDiv);

    const container = document.createElement('div');
    container.className = 'container';

    const outerSquare = document.createElement('div');
    outerSquare.className = 'outer-square';

    const innerSquare = document.createElement('div');
    innerSquare.className = 'inner-square';

    // Create the 5 sections (4 trapezoids and center square)
    const sections = [];
    for (let i = 1; i <= 5; i++) {
        const section = document.createElement('div');
        section.className = `section section${i}`;
        section.dataset.section = i;
        section.dataset.tooth = label;
        
        // Add click event listener
        section.addEventListener('click', function() {
            const tooth = this.dataset.tooth;
            const section = this.dataset.section;
            
            // Get current color or default to white
            const currentColor = dentalChartData[tooth]?.[section] || 'white';
            const currentIndex = TOOTH_COLORS.indexOf(currentColor);
            
            // Move to next color in rotation
            const nextIndex = (currentIndex + 1) % TOOTH_COLORS.length;
            const nextColor = TOOTH_COLORS[nextIndex];
            
            // Update the UI
            this.style.backgroundColor = nextColor;
            
            // Store the data
            if (!dentalChartData[tooth]) {
                dentalChartData[tooth] = {};
            }
            dentalChartData[tooth][section] = nextColor;
        });
        
        sections.push(section);
        container.appendChild(section);
    }

    // Add diagonal lines for clearer section separation
    const diagonalStyles = [
        'top: 0; left: 0; transform: rotate(45deg); transform-origin: 0 0;',
        'top: 0; right: 0; transform: rotate(-45deg); transform-origin: 100% 0;',
        'bottom: 0; right: 0; transform: rotate(45deg); transform-origin: 100% 100%;',
        'bottom: 0; left: 0; transform: rotate(-45deg); transform-origin: 0 100%;'
    ];
    
    diagonalStyles.forEach(style => {
        const diagonal = document.createElement('div');
        diagonal.className = 'diagonal-line';
        diagonal.style.cssText = style;
        container.appendChild(diagonal);
    });

    container.appendChild(outerSquare);
    container.appendChild(innerSquare);
    wrapper.appendChild(container);
    
    return wrapper;
}

/**
 * Apply saved chart data to the UI
 */
function applyChartData() {
    // For each tooth
    Object.keys(dentalChartData).forEach(tooth => {
        // For each section of the tooth
        Object.keys(dentalChartData[tooth]).forEach(section => {
            const color = dentalChartData[tooth][section];
            const selector = `.section[data-tooth="${tooth}"][data-section="${section}"]`;
            const element = document.querySelector(selector);
            
            if (element) {
                element.style.backgroundColor = color;
            }
        });
    });
}

/**
 * Collect chart data from UI
 * @returns {Object} - The chart data
 */
function collectChartData() {
    // We're already maintaining the dentalChartData object with each click
    return dentalChartData;
}