// DOM Elements
const userTableBody = document.getElementById('userTableBody');
const searchInput = document.getElementById('searchInput');
const clearSearchBtn = document.getElementById('clearSearchBtn');
const refreshBtn = document.getElementById('refreshBtn');
const loadingIndicator = document.getElementById('loadingIndicator');
const errorMessage = document.getElementById('errorMessage');
const errorText = document.getElementById('errorText');
const emptyState = document.getElementById('emptyState');
const userCount = document.getElementById('userCount');
const pagination = document.querySelector('.pagination');

// App State
let users = [];
let filteredUsers = [];
let currentPage = 1;
let totalPages = 1;
let sortColumn = 'id';
let sortDirection = 'asc';
const usersPerPage = 6;

// Initialize the application
async function init() {
    setupEventListeners();
    await fetchUsers();
}

// Set up event listeners
function setupEventListeners() {
    // Search functionality
    searchInput.addEventListener('input', handleSearch);
    clearSearchBtn.addEventListener('click', clearSearch);
    
    // Refresh data
    refreshBtn.addEventListener('click', handleRefresh);
    
    // Sorting
    document.querySelectorAll('.sortable').forEach(th => {
        th.addEventListener('click', () => {
            handleSort(th.dataset.sort);
        });
    });
}

// Fetch users from the API
async function fetchUsers() {
    showLoading();
    hideError();
    hideEmptyState();
    
    try {
        // Fetch all users from multiple pages to have more data to work with
        const allUsers = [];
        
        // First, get page 1 to determine total pages
        const response = await fetch('https://reqres.in/api/users?page=1&per_page=12');
        const data = await response.json();
        
        allUsers.push(...data.data);
        totalPages = data.total_pages;
        
        // If there are more pages, fetch them too
        if (totalPages > 1) {
            for (let page = 2; page <= totalPages; page++) {
                const nextResponse = await fetch(`https://reqres.in/api/users?page=${page}&per_page=12`);
                const nextData = await nextResponse.json();
                allUsers.push(...nextData.data);
            }
        }
        
        // Process the user data
        users = allUsers.map(user => ({
            id: user.id,
            avatar: user.avatar,
            name: `${user.first_name} ${user.last_name}`,
            firstName: user.first_name,
            lastName: user.last_name,
            email: user.email
        }));
        
        // Apply initial sorting
        sortUsers(sortColumn, sortDirection);
        
        // Update the UI
        hideLoading();
        renderUsers();
        renderPagination();
        
    } catch (error) {
        console.error('Error fetching users:', error);
        hideLoading();
        showError(error.message || 'Failed to load user data. Please try again.');
    }
}

// Handle search input
function handleSearch() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    
    if (searchTerm === '') {
        filteredUsers = [...users];
    } else {
        filteredUsers = users.filter(user => 
            user.name.toLowerCase().includes(searchTerm) || 
            user.email.toLowerCase().includes(searchTerm)
        );
    }
    
    // Reset to first page when searching
    currentPage = 1;
    
    renderUsers();
    renderPagination();
}

// Clear search
function clearSearch() {
    searchInput.value = '';
    filteredUsers = [...users];
    currentPage = 1;
    renderUsers();
    renderPagination();
    searchInput.focus();
}

// Handle refresh button click
async function handleRefresh() {
    const refreshIcon = refreshBtn.querySelector('i');
    refreshIcon.classList.add('refreshing');
    refreshBtn.disabled = true;
    
    await fetchUsers();
    
    refreshIcon.classList.remove('refreshing');
    refreshBtn.disabled = false;
}

// Handle sorting
function handleSort(column) {
    // If clicking the same column, toggle direction
    if (sortColumn === column) {
        sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        sortColumn = column;
        sortDirection = 'asc';
    }
    
    // Update sort indicators in the UI
    updateSortIndicators();
    
    // Sort the users
    sortUsers(sortColumn, sortDirection);
    
    // Re-render the table
    renderUsers();
}

// Sort users based on column and direction
function sortUsers(column, direction) {
    const compareFn = (a, b) => {
        let valueA, valueB;
        
        // Skip sorting for avatar column
        if (column === 'avatar') return 0;
        
        if (column === 'id') {
            valueA = a.id;
            valueB = b.id;
        } else if (column === 'name') {
            valueA = a.name.toLowerCase();
            valueB = b.name.toLowerCase();
        } else if (column === 'email') {
            valueA = a.email.toLowerCase();
            valueB = b.email.toLowerCase();
        } else {
            return 0;
        }
        
        // Compare based on direction
        if (direction === 'asc') {
            return valueA > valueB ? 1 : -1;
        } else {
            return valueA < valueB ? 1 : -1;
        }
    };
    
    // Sort both the full list and filtered list
    users.sort(compareFn);
    filteredUsers = [...users];
    
    // Apply current search filter
    handleSearch();
}

// Update sort indicators in the table headers
function updateSortIndicators() {
    // Reset all headers
    document.querySelectorAll('.sortable').forEach(th => {
        th.classList.remove('sort-asc', 'sort-desc');
    });
    
    // Set the active sort header
    const activeHeader = document.querySelector(`.sortable[data-sort="${sortColumn}"]`);
    if (activeHeader) {
        activeHeader.classList.add(sortDirection === 'asc' ? 'sort-asc' : 'sort-desc');
    }
}

// Render users to the table
function renderUsers() {
    // Calculate pagination
    const startIndex = (currentPage - 1) * usersPerPage;
    const endIndex = startIndex + usersPerPage;
    const paginatedUsers = filteredUsers.slice(startIndex, endIndex);
    
    // Update user count
    userCount.textContent = filteredUsers.length;
    
    // Check if we have users to display
    if (filteredUsers.length === 0) {
        userTableBody.innerHTML = '';
        showEmptyState();
        return;
    }
    
    hideEmptyState();
    
    // Generate table rows
    userTableBody.innerHTML = paginatedUsers.map(user => `
        <tr>
            <td>${user.id}</td>
            <td>
                <img src="${user.avatar}" alt="${user.name}" class="avatar" 
                     title="${user.name}">
            </td>
            <td>${escapeHTML(user.name)}</td>
            <td>
                <a href="mailto:${user.email}" class="text-decoration-none">
                    ${escapeHTML(user.email)}
                </a>
            </td>
        </tr>
    `).join('');
}

// Render pagination controls
function renderPagination() {
    const totalPages = Math.ceil(filteredUsers.length / usersPerPage);
    
    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }
    
    let paginationHTML = `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
    `;
    
    // Generate page numbers
    for (let i = 1; i <= totalPages; i++) {
        paginationHTML += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>
        `;
    }
    
    paginationHTML += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    `;
    
    pagination.innerHTML = paginationHTML;
    
    // Add event listeners to pagination links
    document.querySelectorAll('.page-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const page = parseInt(link.dataset.page);
            if (page >= 1 && page <= totalPages) {
                currentPage = page;
                renderUsers();
                renderPagination();
                // Scroll to top of table
                document.querySelector('.table').scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}

// Helper function to escape HTML
function escapeHTML(str) {
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// UI state management functions
function showLoading() {
    loadingIndicator.classList.remove('d-none');
}

function hideLoading() {
    loadingIndicator.classList.add('d-none');
}

function showError(message) {
    errorText.textContent = message;
    errorMessage.classList.remove('d-none');
}

function hideError() {
    errorMessage.classList.add('d-none');
}

function showEmptyState() {
    emptyState.classList.remove('d-none');
}

function hideEmptyState() {
    emptyState.classList.add('d-none');
}

// Initialize the app when the DOM is loaded
document.addEventListener('DOMContentLoaded', init);