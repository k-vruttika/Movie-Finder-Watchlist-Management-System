# Quick Setup Guide

## Step-by-Step Installation

### 1. Prerequisites
- Install XAMPP or WAMP
- Ensure Apache and MySQL are installed

### 2. Copy Files
1. Copy the `movie-viewer` folder to:
   - XAMPP: `C:\xampp\htdocs\` (Windows) or `/opt/lampp/htdocs/` (Linux)
   - WAMP: `C:\wamp64\www\`

### 3. Database Setup
1. Start Apache and MySQL in XAMPP/WAMP Control Panel
2. Open browser and go to: `http://localhost/phpmyadmin`
3. Click "New" to create a database
4. Name it: `movie_viewer_db`
5. Click "Create"
6. Select the `movie_viewer_db` database
7. Click "Import" tab
8. Click "Choose File" and select: `movie-viewer/database/schema.sql`
9. Click "Go" at the bottom
10. Click "Import" tab again
11. Click "Choose File" and select: `movie-viewer/database/sample_data.sql`
12. Click "Go"

### 4. Configure Database (if needed)
If your MySQL has a password:
1. Open `movie-viewer/includes/db.php`
2. Change line 8:
   ```php
   define('DB_PASS', 'your_mysql_password');
   ```

### 5. Access the Application
Open your browser and go to:
```
http://localhost/movie-viewer/
```

### 6. Login Credentials

**Regular User:**
- Username: `john_doe`
- Password: `password123`

**Admin:**
- Username: `admin`
- Password: `admin123`
- Admin URL: `http://localhost/movie-viewer/admin/admin_login.php`

## Troubleshooting

### "Connection failed" error
- Make sure MySQL is running in XAMPP/WAMP
- Check database credentials in `includes/db.php`
- Verify database `movie_viewer_db` exists

### "Table doesn't exist" error
- Import `schema.sql` first, then `sample_data.sql`
- Make sure you selected the correct database before importing

### Images not showing
- Check if files exist in `assets/images/` folder
- Clear browser cache (Ctrl+F5)

### Page not found (404)
- Verify the folder is in the correct location (htdocs or www)
- Check the URL: `http://localhost/movie-viewer/`

## File Structure
```
movie-viewer/
├── admin/              # Admin pages
├── assets/             # CSS, JS, Images
├── database/           # SQL files
├── includes/           # PHP includes
├── user/               # User pages
├── index.php           # Landing page
├── login.php           # Login
├── register.php        # Registration
└── README.md           # Full documentation
```

## Next Steps
1. Login with demo credentials
2. Browse movies and add to watchlist
3. Rate some movies
4. Check recommendations
5. Login as admin to manage content

## Support
For detailed documentation, see `README.md`

---
**Happy Movie Tracking! 🎬**
