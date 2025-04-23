// DOM Elements
const taskInput = document.getElementById('taskInput');
const addTaskBtn = document.getElementById('addTaskBtn');
const taskList = document.getElementById('taskList');
const emptyState = document.getElementById('emptyState');
const clearCompletedBtn = document.getElementById('clearCompletedBtn');
const clearAllBtn = document.getElementById('clearAllBtn');
const totalTasksElement = document.getElementById('totalTasks');
const completedTasksElement = document.getElementById('completedTasks');
const remainingTasksElement = document.getElementById('remainingTasks');
const filterButtons = document.querySelectorAll('.filter-btn');

// App State
let tasks = [];
let currentFilter = 'all';

// Initialize the app
function init() {
    loadTasksFromLocalStorage();
    renderTasks();
    updateTaskStats();
    
    // Event listeners
    addTaskBtn.addEventListener('click', addTask);
    taskInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            addTask();
        }
    });
    
    clearCompletedBtn.addEventListener('click', clearCompletedTasks);
    clearAllBtn.addEventListener('click', clearAllTasks);
    
    // Filter event listeners
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            setActiveFilter(filter);
            renderTasks();
        });
    });
}

// Load tasks from localStorage
function loadTasksFromLocalStorage() {
    const storedTasks = localStorage.getItem('tasks');
    if (storedTasks) {
        tasks = JSON.parse(storedTasks);
    }
}

// Save tasks to localStorage
function saveTasksToLocalStorage() {
    localStorage.setItem('tasks', JSON.stringify(tasks));
}

// Add a new task
function addTask() {
    const taskText = taskInput.value.trim();
    
    if (taskText === '') {
        shakeInput();
        return;
    }
    
    const newTask = {
        id: Date.now(),
        text: taskText,
        completed: false,
        createdAt: new Date()
    };
    
    tasks.unshift(newTask); // Add to the beginning of the array
    saveTasksToLocalStorage();
    taskInput.value = '';
    renderTasks();
    updateTaskStats();
}

// Toggle task completion status
function toggleTaskCompletion(taskId) {
    tasks = tasks.map(task => {
        if (task.id === taskId) {
            return { ...task, completed: !task.completed };
        }
        return task;
    });
    
    saveTasksToLocalStorage();
    renderTasks();
    updateTaskStats();
}

// Delete a task
function deleteTask(taskId) {
    tasks = tasks.filter(task => task.id !== taskId);
    saveTasksToLocalStorage();
    renderTasks();
    updateTaskStats();
}

// Clear completed tasks
function clearCompletedTasks() {
    if (confirm('Apakah Anda yakin ingin menghapus semua tugas yang telah selesai?')) {
        tasks = tasks.filter(task => !task.completed);
        saveTasksToLocalStorage();
        renderTasks();
        updateTaskStats();
    }
}

// Clear all tasks
function clearAllTasks() {
    if (confirm('Apakah Anda yakin ingin menghapus semua tugas?')) {
        tasks = [];
        saveTasksToLocalStorage();
        renderTasks();
        updateTaskStats();
    }
}

// Set active filter
function setActiveFilter(filter) {
    currentFilter = filter;
    
    // Update active button UI
    filterButtons.forEach(button => {
        if (button.getAttribute('data-filter') === filter) {
            button.classList.add('active');
        } else {
            button.classList.remove('active');
        }
    });
}

// Filter tasks based on current filter
function getFilteredTasks() {
    switch (currentFilter) {
        case 'active':
            return tasks.filter(task => !task.completed);
        case 'completed':
            return tasks.filter(task => task.completed);
        default:
            return tasks;
    }
}

// Render tasks to the DOM
function renderTasks() {
    const filteredTasks = getFilteredTasks();
    
    // Show/hide empty state
    if (filteredTasks.length === 0) {
        taskList.innerHTML = '';
        emptyState.style.display = 'flex';
        
        // Customize empty state message based on filter
        const emptyStateMessage = emptyState.querySelector('p');
        if (tasks.length === 0) {
            emptyStateMessage.textContent = 'Belum ada tugas. Tambahkan tugas baru!';
        } else if (currentFilter === 'active') {
            emptyStateMessage.textContent = 'Tidak ada tugas aktif.';
        } else if (currentFilter === 'completed') {
            emptyStateMessage.textContent = 'Tidak ada tugas yang selesai.';
        }
        
        return;
    }
    
    emptyState.style.display = 'none';
    
    // Render tasks
    taskList.innerHTML = '';
    
    filteredTasks.forEach(task => {
        const taskItem = document.createElement('li');
        taskItem.className = `task-item ${task.completed ? 'completed' : ''}`;
        taskItem.innerHTML = `
            <input type="checkbox" class="task-checkbox" ${task.completed ? 'checked' : ''}>
            <span class="task-text">${escapeHTML(task.text)}</span>
            <button class="task-delete">
                <i class="fas fa-trash"></i>
            </button>
        `;
        
        // Add event listeners to task elements
        const checkbox = taskItem.querySelector('.task-checkbox');
        checkbox.addEventListener('change', () => toggleTaskCompletion(task.id));
        
        const deleteButton = taskItem.querySelector('.task-delete');
        deleteButton.addEventListener('click', () => deleteTask(task.id));
        
        taskList.appendChild(taskItem);
    });
}

// Update task statistics
function updateTaskStats() {
    const totalTasks = tasks.length;
    const completedTasks = tasks.filter(task => task.completed).length;
    const remainingTasks = totalTasks - completedTasks;
    
    totalTasksElement.textContent = totalTasks;
    completedTasksElement.textContent = completedTasks;
    remainingTasksElement.textContent = remainingTasks;
}

// Helper function to escape HTML to prevent XSS
function escapeHTML(str) {
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// Visual feedback when trying to add empty task
function shakeInput() {
    taskInput.classList.add('shake');
    setTimeout(() => {
        taskInput.classList.remove('shake');
    }, 500);
}

// Add shake animation
const style = document.createElement('style');
style.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
    
    .shake {
        animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        border-color: #ff5a5f !important;
    }
`;
document.head.appendChild(style);

// Initialize the app when DOM is loaded
document.addEventListener('DOMContentLoaded', init);