# CinemaWeb Project

## Overview
CinemaWeb is a simple web application designed for managing a cinema. It provides an admin interface for managing movies, cinema halls, functions, and users. The application is built using PHP and includes basic HTML and CSS for the user interface.

## Features
- **Admin Dashboard**: A central interface for administrators to view statistics and manage cinema operations.
- **Movie Management**: Create, view, and manage movies in the cinema catalog.
- **Hall Management**: Add and manage cinema halls for screenings.
- **Function Scheduling**: Schedule movie screenings with specific times and halls.
- **User Management**: Add and manage users with different roles in the system.

## File Structure
```
CinemaWeb
├── dashboard
│   ├── index-admin.php        # Main admin interface
│   └── styles
│       └── admin_styles.css   # CSS styles for the admin interface
├── catalogos
│   ├── peliculas
│   │   └── crear.php          # Form for creating new movies
│   ├── salas
│   │   └── crear.php          # Form for creating new cinema halls
│   ├── funciones
│   │   └── crear.php          # Form for scheduling new functions
│   └── usuarios
│       └── crear.php          # Form for adding new users
├── includes
│   ├── session.php            # Session management
│   └── db.php                 # Database connection and queries
├── logout.php                 # Handles user logout
└── README.md                  # Project documentation
```

## Setup Instructions
1. Clone the repository to your local machine.
2. Ensure you have a web server with PHP support (e.g., XAMPP, WAMP).
3. Create a database for the application and configure the `db.php` file with your database credentials.
4. Access the application through your web browser at `http://localhost/CinemaWeb/dashboard/index-admin.php`.

## Usage
- Log in as an admin to access the dashboard.
- Use the quick action buttons to manage movies, halls, functions, and users.
- Monitor recent activity and statistics on the dashboard for better management.

## Contributing
Feel free to contribute to the project by submitting issues or pull requests. Your feedback and improvements are welcome!