# Blog Backend

A simple PHP backend for managing blog posts with password protection.

## Features

- Secure login system to protect the blog backend
- Add new blog posts with title, excerpt, content, image, and tags
- Automatic slug generation from title
- Simple post management interface
- Toggle publish status of posts
- Delete unwanted posts
- Easily configurable database connection

## Setup

1. Place all files in your web server directory (e.g., htdocs, www, or public_html)
2. Configure your database connection in `config.php`:
   ```php
   $db_host = 'localhost'; // Your database host
   $db_name = 'blog';      // Your database name
   $db_user = 'root';      // Your database username
   $db_pass = '';          // Your database password
   ```
3. Run the setup script by accessing `setup.php` in your browser to create the database and table
4. Access the blog backend by opening `login.php` (default password is "admin")
5. (Optional) Generate a new password hash using `utils/generate_hash.php` and update it in `login.php`

## Usage

1. **Login**: Enter your password on the login page
2. **Add New Post**: Fill out the form on the main page (`index.php`)
3. **Manage Posts**: View all posts on the management page (`posts.php`)
4. **Toggle Status**: Click the "Publish" or "Set to Draft" button to change post status
5. **Delete Post**: Click the "Delete" button to remove a post
6. **Logout**: Click the "Logout" button when finished

## File Structure

- `login.php` - Authentication page
- `auth.php` - Authentication verification script
- `logout.php` - Session termination script
- `index.php` - Main page with the form to add new posts
- `posts.php` - Page to view and manage existing posts
- `save_post.php` - Script that processes the form submission
- `toggle_status.php` - Script that toggles post publish status
- `delete_post.php` - Script that deletes posts
- `config.php` - Database configuration
- `setup.php` - Database and table setup script
- `utils/generate_hash.php` - Utility to generate password hashes
- `README.md` - This documentation file

## Image Format

The featured image URL follows this format:
```
https://ik.imagekit.io/mywine/andiblog/tr:w-800,h-400/[image_name].jpg
```

Only enter the image name (without the .jpg extension) in the form, and the full URL will be generated automatically.

## Security

The backend is protected by a password-based authentication system. By default, the password is "admin". It is strongly recommended to:

1. Generate a new password hash using the `utils/generate_hash.php` tool
2. Replace the default password hash in `login.php` with your new hash 