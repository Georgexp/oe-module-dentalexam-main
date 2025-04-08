// assets/js/dental_exam.js
const colors = ['white', 'red', 'yellow', 'gray', 'black', '#90EE90', '#87CEFA'];
const grid = document.getElementById('grid');

function createContainer(label) {
    const wrapper = document.createElement('div');
    wrapper.className = 'container-wrapper';

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

    const sections = [1, 2, 3, 4, 5].map(i => {
        const section = document.createElement('div');
        section.className = `section section${i}`;
        section.setAttribute('data-tooth', label);
        section.setAttribute('data-section', i);
        section.style.backgroundColor = 'white'; // Default healthy
        section.setAttribute('data-state', 'white');
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

// Generate grid
const row1Labels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
const row2Labels = Array.from({ length: 16 }, (_, i) => (i + 1).toString());
const row3Labels = Array.from({ length: 16 }, (_, i) => (32 - i).toString());
const row4Labels = ['T', 'S', 'R', 'Q', 'P', 'O', 'N', 'M', 'L', 'K'];
const allRows = [row1Labels, row2Labels, row3Labels, row4Labels];

allRows.forEach(labels => {
    const row = document.createElement('div');
    row.className = 'row';
    labels.forEach(label => row.appendChild(createContainer(label)));
    grid.appendChild(row);
});

// Add interactivity
document.querySelectorAll('.section').forEach(section => {
    section.addEventListener('click', function() {
        const currentState = this.getAttribute('data-state');
        const currentIndex = colors.indexOf(currentState);
        const nextIndex = (currentIndex + 1) % colors.length;
        const nextColor = colors[nextIndex];
        this.style.backgroundColor = nextColor;
        this.setAttribute('data-state', nextColor);
    });
});

// Load existing data
if (initialDentalData && Object.keys(initialDentalData).length > 0) {
    document.querySelectorAll('.section').forEach(section => {
        const tooth = section.getAttribute('data-tooth');
        const sectionNum = section.getAttribute('data-section');
        if (initialDentalData[tooth] && initialDentalData[tooth][sectionNum]) {
            const state = initialDentalData[tooth][sectionNum];
            section.style.backgroundColor = state;
            section.setAttribute('data-state', state);
        }
    });
}

// Handle form submission
document.getElementById('dentalExamForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const dentalData = {};
    document.querySelectorAll('.section').forEach(section => {
        const tooth = section.getAttribute('data-tooth');
        const sectionNum = section.getAttribute('data-section');
        const state = section.getAttribute('data-state');
        if (!dentalData[tooth]) dentalData[tooth] = {};
        dentalData[tooth][sectionNum] = state;
    });
    document.getElementById('dental_data').value = JSON.stringify(dentalData);
    this.submit();
});