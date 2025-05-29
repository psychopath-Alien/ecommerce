<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="#"><i class="fas fa-user me-1"></i>Admin User</a>
                <a class="nav-link" href="#"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" onclick="showSection('dashboard')">
                                <i class="fas fa-chart-bar me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showSection('products')">
                                <i class="fas fa-box me-2"></i>Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showSection('users')">
                                <i class="fas fa-users me-2"></i>Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showSection('orders')">
                                <i class="fas fa-shopping-cart me-2"></i>Orders
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Dashboard Section -->
                <div id="dashboard" class="section">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Dashboard</h1>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Products</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalProducts">0</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-box fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Users</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalUsers">0</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Orders</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalOrders">0</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Revenue</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalRevenue">$0</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Sales Overview</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="salesChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Order Status Distribution</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="orderStatusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Section -->
                <div id="products" class="section d-none">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Products Management</h1>
                        <button class="btn btn-primary" onclick="showProductModal()">
                            <i class="fas fa-plus me-1"></i>Add Product
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Category</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="productsTable">
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Users Section -->
                <div id="users" class="section d-none">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Users Management</h1>
                        <button class="btn btn-primary" onclick="showUserModal()">
                            <i class="fas fa-plus me-1"></i>Add User
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTable">
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Orders Section -->
                <div id="orders" class="section d-none">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Orders Management</h1>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="ordersTable">
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalTitle">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm">
                        <input type="hidden" id="productId">
                        <div class="mb-3">
                            <label for="productName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="productName" required>
                        </div>
                        <div class="mb-3">
                            <label for="productPrice" class="form-label">Price</label>
                            <input type="number" class="form-control" id="productPrice" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="productStock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="productStock" required>
                        </div>
                        <div class="mb-3">
                            <label for="productCategory" class="form-label">Category</label>
                            <select class="form-control" id="productCategory" required>
                                <option value="">Select Category</option>
                                <option value="Electronics">Electronics</option>
                                <option value="Clothing">Clothing</option>
                                <option value="Books">Books</option>
                                <option value="Home">Home</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveProduct()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalTitle">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm">
                        <input type="hidden" id="userId">
                        <div class="mb-3">
                            <label for="userName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="userName" required>
                        </div>
                        <div class="mb-3">
                            <label for="userEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="userEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="userRole" class="form-label">Role</label>
                            <select class="form-control" id="userRole" required>
                                <option value="">Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="customer">Customer</option>
                                <option value="manager">Manager</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="userStatus" class="form-label">Status</label>
                            <select class="form-control" id="userStatus" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveUser()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sample data storage
        let products = [
            {id: 1, name: 'Laptop', price: 999.99, stock: 50, category: 'Electronics'},
            {id: 2, name: 'T-Shirt', price: 19.99, stock: 100, category: 'Clothing'},
            {id: 3, name: 'Book', price: 12.99, stock: 75, category: 'Books'}
        ];

        let users = [
            {id: 1, name: 'John Doe', email: 'john@example.com', role: 'admin', status: 'active'},
            {id: 2, name: 'Jane Smith', email: 'jane@example.com', role: 'customer', status: 'active'},
            {id: 3, name: 'Bob Johnson', email: 'bob@example.com', role: 'customer', status: 'inactive'}
        ];

        let orders = [
            {id: 1, customer: 'Jane Smith', total: 1019.98, status: 'completed', date: '2024-01-15'},
            {id: 2, customer: 'Bob Johnson', total: 32.98, status: 'pending', date: '2024-01-16'},
            {id: 3, customer: 'Alice Brown', total: 999.99, status: 'shipped', date: '2024-01-17'}
        ];

        let nextProductId = 4;
        let nextUserId = 4;
        let nextOrderId = 4;

        // Navigation
        function showSection(sectionName) {
            document.querySelectorAll('.section').forEach(section => {
                section.classList.add('d-none');
            });
            document.getElementById(sectionName).classList.remove('d-none');
            
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            event.target.classList.add('active');

            if (sectionName === 'dashboard') {
                updateDashboard();
            } else if (sectionName === 'products') {
                renderProducts();
            } else if (sectionName === 'users') {
                renderUsers();
            } else if (sectionName === 'orders') {
                renderOrders();
            }
        }

        // Dashboard functions
        function updateDashboard() {
            document.getElementById('totalProducts').textContent = products.length;
            document.getElementById('totalUsers').textContent = users.length;
            document.getElementById('totalOrders').textContent = orders.length;
            
            const totalRevenue = orders.reduce((sum, order) => sum + order.total, 0);
            document.getElementById('totalRevenue').textContent = '$' + totalRevenue.toFixed(2);

            createCharts();
        }

        function createCharts() {
            // Sales Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Sales',
                        data: [1200, 1900, 3000, 5000, 2300, 3200],
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Order Status Chart
            const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
            const statusCounts = orders.reduce((acc, order) => {
                acc[order.status] = (acc[order.status] || 0) + 1;
                return acc;
            }, {});

            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(statusCounts),
                    datasets: [{
                        data: Object.values(statusCounts),
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0'
                        ]
                    }]
                },
                options: {
                    responsive: true
                }
            });
        }

        // Products CRUD
        function renderProducts() {
            const tbody = document.getElementById('productsTable');
            tbody.innerHTML = products.map(product => `
                <tr>
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>$${product.price}</td>
                    <td>${product.stock}</td>
                    <td>${product.category}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editProduct(${product.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function showProductModal(productId = null) {
            const modal = new bootstrap.Modal(document.getElementById('productModal'));
            const title = document.getElementById('productModalTitle');
            const form = document.getElementById('productForm');
            
            form.reset();
            document.getElementById('productId').value = '';
            
            if (productId) {
                const product = products.find(p => p.id === productId);
                title.textContent = 'Edit Product';
                document.getElementById('productId').value = product.id;
                document.getElementById('productName').value = product.name;
                document.getElementById('productPrice').value = product.price;
                document.getElementById('productStock').value = product.stock;
                document.getElementById('productCategory').value = product.category;
            } else {
                title.textContent = 'Add Product';
            }
            
            modal.show();
        }

        function saveProduct() {
            const id = document.getElementById('productId').value;
            const name = document.getElementById('productName').value;
            const price = parseFloat(document.getElementById('productPrice').value);
            const stock = parseInt(document.getElementById('productStock').value);
            const category = document.getElementById('productCategory').value;

            if (id) {
                // Edit existing product
                const index = products.findIndex(p => p.id === parseInt(id));
                products[index] = {id: parseInt(id), name, price, stock, category};
            } else {
                // Add new product
                products.push({id: nextProductId++, name, price, stock, category});
            }

            bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
            renderProducts();
        }

        function editProduct(id) {
            showProductModal(id);
        }

        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                products = products.filter(p => p.id !== id);
                renderProducts();
            }
        }

        // Users CRUD
        function renderUsers() {
            const tbody = document.getElementById('usersTable');
            tbody.innerHTML = users.map(user => `
                <tr>
                    <td>${user.id}</td>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td><span class="badge bg-info">${user.role}</span></td>
                    <td><span class="badge bg-${user.status === 'active' ? 'success' : 'secondary'}">${user.status}</span></td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editUser(${user.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function showUserModal(userId = null) {
            const modal = new bootstrap.Modal(document.getElementById('userModal'));
            const title = document.getElementById('userModalTitle');
            const form = document.getElementById('userForm');
            
            form.reset();
            document.getElementById('userId').value = '';
            
            if (userId) {
                const user = users.find(u => u.id === userId);
                title.textContent = 'Edit User';
                document.getElementById('userId').value = user.id;
                document.getElementById('userName').value = user.name;
                document.getElementById('userEmail').value = user.email;
                document.getElementById('userRole').value = user.role;
                document.getElementById('userStatus').value = user.status;
            } else {
                title.textContent = 'Add User';
            }
            
            modal.show();
        }

        function saveUser() {
            const id = document.getElementById('userId').value;
            const name = document.getElementById('userName').value;
            const email = document.getElementById('userEmail').value;
            const role = document.getElementById('userRole').value;
            const status = document.getElementById('userStatus').value;

            if (id) {
                // Edit existing user
                const index = users.findIndex(u => u.id === parseInt(id));
                users[index] = {id: parseInt(id), name, email, role, status};
            } else {
                // Add new user
                users.push({id: nextUserId++, name, email, role, status});
            }

            bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
            renderUsers();
        }

        function editUser(id) {
            showUserModal(id);
        }

        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                users = users.filter(u => u.id !== id);
                renderUsers();
            }
        }

        // Orders functions
        function renderOrders() {
            const tbody = document.getElementById('ordersTable');
            tbody.innerHTML = orders.map(order => `
                <tr>
                    <td>${order.id}</td>
                    <td>${order.customer}</td>
                    <td>$${order.total}</td>
                    <td><span class="badge bg-${getStatusColor(order.status)}">${order.status}</span></td>
                    <td>${order.date}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="viewOrder(${order.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-success" onclick="updateOrderStatus(${order.id})">
                            <i class="fas fa-check"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function getStatusColor(status) {
            switch(status) {
                case 'completed': return 'success';
                case 'pending': return 'warning';
                case 'shipped': return 'info';
                case 'cancelled': return 'danger';
                default: return 'secondary';
            }
        }

        function viewOrder(id) {
            const order = orders.find(o => o.id === id);
            alert(`Order Details:\nID: ${order.id}\nCustomer: ${order.customer}\nTotal: $${order.total}\nStatus: ${order.status}\nDate: ${order.date}`);
        }

        function updateOrderStatus(id) {
            const order = orders.find(o => o.id === id);
            const newStatus = prompt('Enter new status (pending, shipped, completed, cancelled):', order.status);
            if (newStatus && ['pending', 'shipped', 'completed', 'cancelled'].includes(newStatus)) {
                order.status = newStatus;
                renderOrders();
            }
        }

        // Initialize dashboard on load
        document.addEventListener('DOMContentLoaded', function() {
            updateDashboard();
        });
    </script>
</body>
</html>