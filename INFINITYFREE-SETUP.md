# InfinityFree Deployment Guide for SMM Panel

This project is ready to be uploaded to InfinityFree with a small hosting adjustment.

## 1) Upload the project to public_html
Upload the whole project folder (or all project files) into your InfinityFree public_html directory.

Your folder structure should look like this:

public_html/
├── .htaccess
├── app/
├── public/
├── vendor/
├── composer.json
├── .env
├── README.md

Important:
- Do not move the project into a subfolder if you want the website to open at the root domain.
- The .htaccess file already redirects requests to the public/ folder.

## 2) Install dependencies locally before upload
Run this locally before uploading the project:

```bash
composer install
```

This creates the vendor/ folder.

## 3) Put your environment variables in .env
Create .env file in the root of the upload with values like:

```env
SMM_API_BASE_URL=https://api.example.com
SMM_API_KEY=your_api_key_here
SMM_API_USERNAME=your_username_here
SMM_API_TIMEOUT=20
DB_HOST=localhost
DB_NAME=your_database_name
DB_USER=your_database_user
DB_PASS=your_database_password
DB_PORT=3306
```

## 4) Open the website
Open in browser:

- Main site: https://yourdomain.com/
- Admin: https://yourdomain.com/admin/login.php

## 5) Important path notes
The project already uses the correct paths for a root-hosted deployment:

- public/index.php uses: ../vendor/autoload.php
- public/admin/login.php uses: ../../vendor/autoload.php

They are designed to work when the project is uploaded directly under public_html and the .htaccess redirects to public/.

## 6) If the site shows a blank page
Check the following:

- vendor/ folder exists
- .htaccess uploaded correctly
- PHP version is 8.1+ on InfinityFree
- app/ and public/ files are in the correct root

## 7) Admin login
The admin form is at:

```text
https://yourdomain.com/admin/login.php
```

If your MySQL database is not configured, the admin session will not work properly. Add your database credentials to .env and make sure the database exists in cPanel.

## 8) If your host does not allow Composer install on server
Then simply install locally before upload, as shown above. That is the recommended approach for InfinityFree.

## 9) Files to upload
You should upload all of these from your project:

- .htaccess
- app/
- public/
- vendor/
- composer.json
- composer.lock
- .env
- README.md

## 10) Quick upload summary
1. Run composer install locally
2. Upload project files to public_html
3. Make sure .htaccess is present
4. Make sure vendor/ exists
5. Open the website and admin pages

If you need help with database configuration or API integration, I can also prepare the exact MySQL-ready version for InfinityFree.
