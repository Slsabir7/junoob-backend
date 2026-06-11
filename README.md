# Junoob Al Doha Trading - Backend API

## Deploy to Railway.app (Free)

### Step 1 - Upload to GitHub
1. Create new repository on github.com named "junoob-backend"
2. Upload all files from this folder

### Step 2 - Deploy on Railway
1. Go to railway.app
2. Click "New Project" → "Deploy from GitHub repo"
3. Select "junoob-backend"
4. Add MySQL database: Click "New" → "Database" → "MySQL"

### Step 3 - Set Environment Variables
In Railway dashboard → Your App → Variables:
```
DB_HOST = (copy from MySQL service)
DB_NAME = railway
DB_USER = root
DB_PASS = (copy from MySQL service)
```

### Step 4 - Run Database Setup
In Railway MySQL service → Connect → Run:
Copy and paste contents of database.sql

### Step 5 - Get Your URL
Railway gives you a URL like:
https://junoob-backend-production.up.railway.app

### Step 6 - Update Flutter App
In your app file:
D:\junoob_final2\lib\constants\app_constants.dart

Change:
static const String baseUrl = 'http://YOUR_SERVER_IP:3000/api';

To:
static const String baseUrl = 'https://junoob-backend-production.up.railway.app/api';

Then rebuild APK!

## API Endpoints
- POST /api/auth/register
- POST /api/auth/login
- GET  /api/products
- POST /api/orders
- GET  /api/orders/user/{id}
- GET  /api/settings
- GET  /api/dashboard
- POST /api/promo/validate

## Default Admin Login
Email: admin@junoob.com
Password: password
