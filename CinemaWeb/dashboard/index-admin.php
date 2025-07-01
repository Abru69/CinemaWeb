<div class="header">
    <h1>Admin Panel</h1>
</div>

<div class="stats">
    <h2>Statistics</h2>
    <div class="stat-item">
        <span>Movies: 10</span>
    </div>
    <div class="stat-item">
        <span>Theaters: 5</span>
    </div>
    <div class="stat-item">
        <span>Functions: 20</span>
    </div>
    <div class="stat-item">
        <span>Users: 100</span>
    </div>
</div>

<div class="quick-actions">
    <h2>Quick Actions</h2>
    <div class="action-item">
        <h3>Movies</h3>
        <a href="catalogos/peliculas/">View Movies</a>
        <a href="catalogos/peliculas/crear.php">Add Movie</a>
    </div>
    <div class="action-item">
        <h3>Theaters</h3>
        <a href="catalogos/salas/">View Theaters</a>
        <a href="catalogos/salas/crear.php">Add Theater</a>
    </div>
    <div class="action-item">
        <h3>Functions</h3>
        <a href="catalogos/funciones/">View Functions</a>
        <a href="catalogos/funciones/crear.php">Schedule Function</a>
    </div>
    <div class="action-item">
        <h3>Users</h3>
        <a href="catalogos/usuarios/">View Users</a>
        <a href="catalogos/usuarios/crear.php">Add User</a>
    </div>
</div>

<div class="recent-activity">
    <h2>Recent Activity</h2>
    <ul>
        <li>New movie added</li>
        <li>Theater maintenance scheduled</li>
        <li>Function times updated</li>
    </ul>
</div>

<div class="logout">
    <a href="logout.php">Logout</a>
</div>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }
    .header {
        text-align: center;
    }
    .stats, .quick-actions, .recent-activity {
        margin: 20px 0;
        padding: 10px;
        border: 1px solid #ccc;
    }
    .stat-item, .action-item {
        margin: 10px 0;
    }
    .logout {
        text-align: right;
    }
</style>