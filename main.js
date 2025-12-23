

const ctx = document.getElementById('financeChart').getContext('2d');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Incomes', 'Expenses', 'Balance'],
        datasets: [{
            label: 'Amount (DH)',
            data: [totalIncomes, totalExpenses, totalBalance],
            backgroundColor: [
                'rgba(34, 197, 94, 0.8)',
                'rgba(239, 68, 68, 0.8)',
                totalBalance >= 0 ? 'rgba(59, 130, 246, 0.8)' : 'rgba(239, 68, 68, 0.8)'
            ],
            borderColor: [
                'rgb(34, 197, 94)',
                'rgb(239, 68, 68)',
                totalBalance >= 0 ? 'rgb(59, 130, 246)' : 'rgb(239, 68, 68)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function (context) {
                        return context.parsed.y.toFixed(2) + ' DH';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function (value) {
                        return value.toFixed(0) + ' DH';
                    }
                }
            }
        }
    }
});

// Pie Chart
const ctxPie = document.getElementById('pieChart').getContext('2d');

new Chart(ctxPie, {
    type: 'doughnut',
    data: {
        labels: ['Incomes', 'Expenses'],
        datasets: [{
            data: [totalIncomes, totalExpenses],
            backgroundColor: [
                'rgba(34, 197, 94, 0.8)',
                'rgba(239, 68, 68, 0.8)'
            ],
            borderColor: [
                'rgb(34, 197, 94)',
                'rgb(239, 68, 68)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
            },
            tooltip: {
                callbacks: {
                    label: function (context) {
                        const label = context.label || '';
                        const value = context.parsed;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return label + ': ' + value.toFixed(2) + ' DH (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// DOM Elements
const UPDATE_INCOME_BUTTONS = document.querySelectorAll('.update_income');
const DELETE_INCOME_BUTTONS = document.querySelectorAll('.delete_income');
const UPDATE_EXPENCE_BUTTONS = document.querySelectorAll('.update_expense');
const DELETE_EXPENCE_BUTTONS = document.querySelectorAll('.delete_expense');
const CREATE_INCOME = document.querySelector('.create_income');
const CREATE_EXPENCE = document.querySelector('.create_expence');

// Functions
function CREATE_ITEM(item) {
    const FORM = document.createElement('form');
    FORM.setAttribute('action', `./${item}/create.php`);
    FORM.setAttribute('method', "POST");

    const style = item == 'expenses' ? 'bg-red-50 border-red-300' : 'bg-green-50 border-green-300';
    FORM.className = `fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 ${style} border-2 rounded-lg shadow-2xl p-6 w-[90vw] max-w-md z-50`;
    FORM.innerHTML = `
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Create ${item.slice(0, -1)}</h2>
                    <button type="button" id='remove_form' class='bg-red-500 hover:bg-red-600 w-8 h-8 rounded-full text-white font-bold flex items-center justify-center cursor-pointer transition duration-200'>×</button>
                </div>

                <div class="space-y-4">
                    <label class="block">
                        <span class="text-gray-700 font-medium">Montant (DH):</span>
                        <input type="number" name="montant" placeholder="Enter amount" required class="mt-1 w-full p-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </label>

                    <label class="block">
                        <span class="text-gray-700 font-medium">Date:</span>
                        <input type="date" name="created_at" required class="mt-1 w-full p-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </label>

                    <label class="block">
                        <span class="text-gray-700 font-medium">Description:</span>
                        <textarea name="description" placeholder="Enter description" rows="4" class="mt-1 w-full p-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    </label>

                    <button type="submit" class='w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg cursor-pointer transition duration-200 mt-6'>Create</button>
                </div>
            `;

    // Backdrop
    const backdrop = document.createElement('div');
    backdrop.className = 'fixed inset-0 bg-black bg-opacity-50 z-40';
    backdrop.id = 'modal-backdrop';

    FORM.querySelector('#remove_form').addEventListener('click', () => {
        FORM.remove();
        backdrop.remove();
    });

    document.body.appendChild(backdrop);
    document.body.appendChild(FORM);
}

function DELETE_ITEM(type, element) {
    const FORM = document.createElement('form');
    FORM.setAttribute('action', `./${type}/delete.php`);
    FORM.setAttribute('method', "POST");

    FORM.className = 'fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-2xl p-6 w-[90vw] max-w-md z-50 border-2 border-red-300';
    FORM.innerHTML = `
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Delete ${type.slice(0, -1)}</h3>
                    <p class="text-gray-600 mb-6">Are you sure you want to delete this item? This action cannot be undone.</p>
                    
                    <input type="hidden" name="method" value="DELETE">
                    <input type="hidden" name="id" value="${element.getAttribute('name')}">

                    <div class="flex gap-3 justify-center">
                        <button type="submit" class='bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg cursor-pointer transition duration-200'>Delete</button>
                        <button type="button" class='bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg cursor-pointer transition duration-200' id='remove_form'>Cancel</button>
                    </div>
                </div>
            `;

    const backdrop = document.createElement('div');
    backdrop.className = 'fixed inset-0 bg-black bg-opacity-50 z-40';
    backdrop.id = 'modal-backdrop';

    FORM.querySelector('#remove_form').addEventListener('click', () => {
        FORM.remove();
        backdrop.remove();
    });

    document.body.appendChild(backdrop);
    document.body.appendChild(FORM);
}

function UPDATE_ITEM(type, element) {
    const FORM = document.createElement('form');
    FORM.setAttribute('action', `./${type}/update.php`);
    FORM.setAttribute('method', "POST");

    const style = type == 'expenses' ? 'bg-red-50 border-red-300' : 'bg-green-50 border-green-300';
    FORM.className = `fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 ${style} border-2 rounded-lg shadow-2xl p-6 w-[90vw] max-w-md z-50`;
    FORM.innerHTML = `
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Update ${type.slice(0, -1)}</h2>
                    <button type="button" id='remove_form' class='bg-red-500 hover:bg-red-600 w-8 h-8 rounded-full text-white font-bold flex items-center justify-center cursor-pointer transition duration-200'>×</button>
                </div>

                <input type="hidden" name="method" value="PUT">
                <input type="hidden" name="id" value="${element.getAttribute('name')}">
                
                <div class="space-y-4">
                    <label class="block">
                        <span class="text-gray-700 font-medium">Montant (DH):</span>
                        <input type="number" name="montant" placeholder="Enter amount" required class="mt-1 w-full p-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </label>

                    <label class="block">
                        <span class="text-gray-700 font-medium">Date:</span>
                        <input type="date" name="created_at" required class="mt-1 w-full p-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </label>

                    <label class="block">
                        <span class="text-gray-700 font-medium">Description:</span>
                        <textarea name="description" placeholder="Enter description" rows="4" class="mt-1 w-full p-3 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    </label>

                    <button type="submit" class='w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg cursor-pointer transition duration-200 mt-6'>Update</button>
                </div>
            `;

    const backdrop = document.createElement('div');
    backdrop.className = 'fixed inset-0 bg-black bg-opacity-50 z-40';
    backdrop.id = 'modal-backdrop';

    FORM.querySelector('#remove_form').addEventListener('click', () => {
        FORM.remove();
        backdrop.remove();
    });

    document.body.appendChild(backdrop);
    document.body.appendChild(FORM);
}

// Event Listeners
CREATE_INCOME.addEventListener('click', () => CREATE_ITEM('incomes'));
CREATE_EXPENCE.addEventListener('click', () => CREATE_ITEM('expenses'));

UPDATE_INCOME_BUTTONS.forEach(element => {
    element.addEventListener('click', () => UPDATE_ITEM('incomes', element));
});

UPDATE_EXPENCE_BUTTONS.forEach(element => {
    element.addEventListener('click', () => UPDATE_ITEM('expenses', element));
});

DELETE_INCOME_BUTTONS.forEach(element => {
    element.addEventListener('click', () => DELETE_ITEM('incomes', element));
});

DELETE_EXPENCE_BUTTONS.forEach(element => {
    element.addEventListener('click', () => DELETE_ITEM('expenses', element));
});



// git commit -m 'finish expences crud validation '
