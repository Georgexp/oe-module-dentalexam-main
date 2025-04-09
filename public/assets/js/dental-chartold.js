// Dental Chart JavaScript

// Array of colors for tooth state
const colors = ['white', 'red', 'yellow', 'gray', 'black', '#90EE90', '#87CEFA'];
const grid = document.getElementById('grid');
const gridData = document.getElementById('gridData');

// Grid structure data
let gridState = {};

// Check if we have saved grid data
if (gridData.value) {
    try {
        gridState = JSON.parse(gridData.value);
    } catch (e) {
        console.error("Error parsing grid data:", e);
        gridState = {};
    }
}

// Function to create a single container
function createContainer(label) {
    const wrapper = document.createElement('div');
    wrapper.className = 'container-wrapper';

    const labelDiv = document.createElement('div');
    labelDiv.className = 'label';
    labelDiv.textContent = label;
    wrapper.appendChild(labelDiv);

    const container = document.createElement('div');
    container.className = 'container';
    container.dataset.label = label;

    const outerSquare = document.createElement('div');
    outerSquare.className = 'outer-square';

    const innerSquare = document.createElement('div');
    innerSquare.className = 'inner-square';

    const sections = [1, 2, 3, 4, 5].map(i => {
        const section = document.createElement('div');
        section.className = `section section${i}`;
        section.dataset.section = i;
        
        // Load saved state if exists
        const sectionKey = `${label}-${i}`;
        if (gridState[sectionKey] !== undefined) {
            section.style.backgroundColor = colors[gridState[sectionKey]];
        }
        
        return section;
    });

    const diagonalStyles = [
        'top: 0; left: 0; transform: rotate(45deg); transform-origin: 0 0;',
        'top: 0; right: 0; transform: rotate(-45deg); transform-origin: 100% 0;',
        'bottom: 0; right: 0; transform: rotate(45deg); transform-origin: 100% 100%;',
        'bottom: 0; left: 0; transform: rotate(-45deg); transform-origin: 0 100%;'
    ];
    const diagonals = diagonalStyles.map(style => {
        const diagonal = document.createElement('div');
        diagonal.className = 'diagonal-line';
        diagonal.style.cssText = style;
        return diagonal;
    });

    container.appendChild(outerSquare);
    container.appendChild(innerSquare);
    sections.forEach(section => container.appendChild(section));
    diagonals.forEach(diagonal => container.appendChild(diagonal));
    wrapper.appendChild(container);
    
    return wrapper;
}

// Define labels for each row
const row1Labels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
const row2Labels = Array.from({ length: 16 }, (_, i) => (i + 1).toString()); // 1-16
const row3Labels = Array.from({ length: 16 }, (_, i) => (32 - i).toString()); // 32-17
const row4Labels = ['T', 'S', 'R', 'Q', 'P', 'O', 'N', 'M', 'L', 'K']; // T-K descending

const allRows = [row1Labels, row2Labels, row3Labels, row4Labels];

// Create 4 rows with their respective labels
allRows.forEach(labels => {
    const row = document.createElement('div');
    row.className = 'row';
    
    labels.forEach(label => {
        const container = createContainer(label);
        row.appendChild(container);
    });
    
    grid.appendChild(row);
});

// Update grid data function
function updateGridData() {
    gridData.value = JSON.stringify(gridState);
}

// Add click event listeners to all sections
document.querySelectorAll('.section').forEach(section => {
    let currentIndex = 0;
    
    // Get container label and section number
    const container = section.closest('.container');
    const label = container.dataset.label;
    const sectionNum = section.dataset.section;
    const sectionKey = `
//Not done 