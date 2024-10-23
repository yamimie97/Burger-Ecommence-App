<div class="col-md-3 sidebar bg-light">
    <div class="card">
        <!-- Wrap the card header with a clickable link -->
        <a href="dashboard.php">
            <div class="card-header text-center bg-teal text-white">
                Admin Dashboard
            </div>
        </a>
        <div class="card-body">
            <!-- Menu Management Dropdown -->
            <div class="dropdown mb-3">
                <button class="btn btn-teal btn-block dropdown-toggle" type="button" data-toggle="collapse" data-target="#menuSubcategories" aria-expanded="false">
                    Menu Management
                </button>
                <div class="collapse" id="menuSubcategories">
                    <a class="dropdown-item" href="menu.php">Menu</a>
                    <a class="dropdown-item" href="menugroup.php">Menu Group</a>
                    <a class="dropdown-item" href="menuitem.php">Menu Item</a>
                </div>
            </div>

            <!-- Order Management Dropdown -->
            <div class="dropdown mb-3">
                <button class="btn btn-teal btn-block dropdown-toggle" type="button" data-toggle="collapse" data-target="#orderSubcategories" aria-expanded="false">
                    Order Management
                </button>
                <div class="collapse" id="orderSubcategories">
                    <a class="dropdown-item" href="burgerorder.php">Burger Order</a>
                    <a class="dropdown-item" href="orderdetails.php">Order Details</a>
                </div>
            </div>

            <!-- Customer Management Dropdown -->
            <div class="dropdown mb-3">
                <button class="btn btn-teal btn-block dropdown-toggle" type="button" data-toggle="collapse" data-target="#customerSubcategories" aria-expanded="false">
                    Customer Management
                </button>
                <div class="collapse" id="customerSubcategories">
                    <a class="dropdown-item" href="customer.php">Customer</a>
                    <a class="dropdown-item" href="feedback.php">Feedback</a>
                </div>
            </div>

            <!-- Promotion Management Dropdown -->
            <div class="dropdown mb-3">
                <button class="btn btn-teal btn-block dropdown-toggle" type="button" data-toggle="collapse" data-target="#promotionSubcategories" aria-expanded="false">
                    Promotion Management
                </button>
                <div class="collapse" id="promotionSubcategories">
                    <a class="dropdown-item" href="promotion.php">Promotion</a>
                    <a class="dropdown-item" href="promotionitem.php">Promotion Item</a>
                </div>
            </div>

            <!-- Employee Management Dropdown -->
            <div class="dropdown mb-3">
                <button class="btn btn-teal btn-block dropdown-toggle" type="button" data-toggle="collapse" data-target="#employeeSubcategories" aria-expanded="false">
                    Employee Management
                </button>
                <div class="collapse" id="employeeSubcategories">
                    <a class="dropdown-item" href="employee.php">Employee</a>
                </div>
            </div>
        </div>
    </div>
</div>
