# Movie Viewer and Watchlist Recommendation System

A complete web application inspired by Letterboxd and IMDb, built with PHP, MySQL, HTML5, and CSS3 for a Web Application Development course.

![Movie Viewer](assets/images/placeholder.jpg)

## 🎬 Features

### User Features
- **User Authentication**: Secure registration and login with password hashing
- **Browse Movies**: Search and filter movies by title and genre
- **Movie Details**: View comprehensive information about each movie
- **Watchlist Management**: Add and remove movies from your personal watchlist
- **Rating System**: Rate movies from 1 to 5 stars
- **Personalized Dashboard**: View your activity and statistics
- **Smart Recommendations**: Get movie suggestions based on your watchlist and ratings

### Admin Features
- **Admin Dashboard**: View site statistics and analytics
- **Movie Management**: Add, edit, and delete movies
- **User Management**: View and manage registered users
- **Statistics**: Track popular movies and user engagement

## 🚀 Tech Stack

- **Frontend**: HTML5, CSS3 (responsive, dark theme)
- **Backend**: Pure PHP (no frameworks)
- **Database**: MySQL with normalized schema
- **Security**: Password hashing, prepared statements, XSS protection

## 📋 Prerequisites

- XAMPP, WAMP, or similar PHP development environment
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web browser (Chrome, Firefox, Safari, Edge)

## 🛠️ Installation

### 1. Clone or Download the Project

```bash
# Navigate to your web server directory
cd /path/to/htdocs  # For XAMPP
# or
cd /path/to/www     # For WAMP

# Copy the movie-viewer folder here
```

### 2. Database Setup

1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `movie_viewer_db`
3. Import the database schema:
   - Click on the `movie_viewer_db` database
   - Go to the "Import" tab
   - Choose `database/schema.sql`
   - Click "Go"
4. Import sample data:
   - Go to the "Import" tab again
   - Choose `database/sample_data.sql`
   - Click "Go"

### 3. Configure Database Connection

Edit `includes/db.php` and update the database credentials if needed:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Your MySQL password
define('DB_NAME', 'movie_viewer_db');
```

### 4. Start the Application

1. Start Apache and MySQL in XAMPP/WAMP
2. Open your browser and navigate to:
   ```
   http://localhost/movie-viewer/
   ```

## 👤 Default Credentials

### Regular User
- **Username**: `john_doe`
- **Password**: `password123`

### Admin User
- **Username**: `admin`
- **Password**: `admin123`

**⚠️ Important**: Change the admin password after first login!

## 📁 Project Structure

```
movie-viewer/
├── admin/                      # Admin panel files
│   ├── admin_dashboard.php     # Admin dashboard
│   ├── admin_login.php         # Admin login
│   ├── manage_movies.php       # Movie management
│   ├── add_movie.php           # Add new movie
│   ├── edit_movie.php          # Edit movie
│   └── manage_users.php        # User management
├── assets/                     # Static assets
│   ├── css/
│   │   ├── style.css           # Main stylesheet
│   │   └── admin.css           # Admin styles
│   ├── images/                 # Movie posters
│   └── js/
│       └── main.js             # JavaScript
├── database/                   # Database files
│   ├── schema.sql              # Database schema
│   └── sample_data.sql         # Sample data
├── includes/                   # Shared PHP files
│   ├── db.php                  # Database connection
│   ├── auth.php                # Authentication helpers
│   ├── header.php              # Common header
│   └── footer.php              # Common footer
├── user/                       # User panel files
│   ├── movies.php              # Browse movies
│   ├── movie_details.php       # Movie details
│   ├── watchlist.php           # User watchlist
│   ├── dashboard.php           # User dashboard
│   ├── recommendations.php     # Recommendations
│   ├── toggle_watchlist.php    # AJAX: Toggle watchlist
│   └── rate_movie.php          # AJAX: Rate movie
├── index.php                   # Landing page
├── login.php                   # User login
├── register.php                # User registration
├── logout.php                  # Logout handler
└── README.md                   # This file
```

## 🎨 Design Features

- **Dark Theme**: Inspired by Letterboxd with a modern, sleek design
- **Responsive Layout**: Works on desktop, tablet, and mobile devices
- **Interactive UI**: Smooth transitions and hover effects
- **Clean Typography**: Using Inter font family
- **Star Ratings**: Interactive 5-star rating system
- **Movie Cards**: Beautiful grid layout with poster images

## 🔒 Security Features

- **Password Hashing**: Using PHP's `password_hash()` and `password_verify()`
- **Prepared Statements**: Protection against SQL injection
- **XSS Protection**: Input sanitization with `htmlspecialchars()`
- **Session Management**: Secure session handling with regeneration
- **Role-Based Access**: Separate admin and user permissions
- **CSRF Protection**: Token-based CSRF prevention

## 📊 Database Schema

### Tables

1. **users**: User accounts (id, username, email, password, role)
2. **movies**: Movie catalog (id, title, genre, description, year, poster, rating)
3. **watchlist**: User watchlists (id, user_id, movie_id)
4. **ratings**: User ratings (id, user_id, movie_id, rating)

### Features
- Foreign key constraints for data integrity
- Indexes for optimized queries
- Triggers for automatic rating calculations
- Normalized structure (3NF)

## 🎯 Usage Guide

### For Users

1. **Register**: Create a new account on the registration page
2. **Browse**: Explore movies using search and genre filters
3. **Add to Watchlist**: Click the "+" button on any movie card
4. **Rate Movies**: View movie details and rate from 1-5 stars
5. **Get Recommendations**: Visit the recommendations page for personalized suggestions

### For Admins

1. **Login**: Use admin credentials at `/admin/admin_login.php`
2. **View Dashboard**: See site statistics and popular movies
3. **Manage Movies**: Add, edit, or delete movies
4. **Manage Users**: View user statistics and manage accounts

## 🐛 Troubleshooting

### Database Connection Error
- Check if MySQL is running
- Verify database credentials in `includes/db.php`
- Ensure `movie_viewer_db` database exists

### Images Not Loading
- Check if images exist in `assets/images/`
- Verify file permissions
- Check browser console for 404 errors

### Session Issues
- Clear browser cookies
- Check PHP session configuration
- Ensure write permissions on session directory

## 📝 Code Quality

- **Well-commented**: All files include explanatory comments
- **Clean Code**: Following PHP best practices
- **Separation of Concerns**: Logic separated from presentation
- **Reusable Components**: Common header/footer includes
- **Error Handling**: Comprehensive validation and error messages

## 🎓 Learning Objectives

This project demonstrates:
- PHP procedural programming
- MySQL database design and queries
- User authentication and authorization
- CRUD operations
- AJAX for dynamic interactions
- Responsive web design
- Security best practices
- Session management

## 📄 License

This project is created for educational purposes as part of a Web Application Development course.

## 👨‍💻 Author

Built for Web Application Development Course

## 🙏 Acknowledgments

- Design inspired by Letterboxd and IMDb
- Sample movie data for demonstration purposes
- Inter font family from Google Fonts

---

**Enjoy using Movie Viewer! 🎬🍿**
