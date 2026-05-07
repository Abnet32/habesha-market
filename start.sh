#!/bin/bash
set -e

DB_NAME="habesha_market"
DB_USER="habesha_market_user"
DB_PASSWORD="habesha_market_password"
SQL_FILE="database/habesha_market.sql"

echo "🚀 Starting Habesha Market..."

# 1. Start MariaDB service

echo "🔧 Starting MariaDB..."
sudo systemctl start mariadb

sleep 2

# 2. Clean reset database (prevents duplicate errors)

echo "🗄️ Resetting database..."
sudo mariadb -e "DROP DATABASE IF EXISTS $DB_NAME;"
sudo mariadb -e "CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mariadb -e "DROP USER IF EXISTS '$DB_USER'@'localhost';"
sudo mariadb -e "DROP USER IF EXISTS '$DB_USER'@'127.0.0.1';"
sudo mariadb -e "CREATE USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';"
sudo mariadb -e "CREATE USER '$DB_USER'@'127.0.0.1' IDENTIFIED BY '$DB_PASSWORD';"
sudo mariadb -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
sudo mariadb -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'127.0.0.1';"
sudo mariadb -e "FLUSH PRIVILEGES;"

export DB_HOST="127.0.0.1"
export DB_USER
export DB_PASSWORD
export DB_NAME
export DB_PORT="3306"

# 3. Import schema + data

if [ -f "$SQL_FILE" ]; then
echo "📦 Importing database schema + data..."
sudo mariadb $DB_NAME < "$SQL_FILE"
else
echo "⚠️ SQL file not found: $SQL_FILE"
fi

echo "✅ Database ready!"

# 4. Start PHP server

echo "🌐 Starting PHP server at http://localhost:5000 ..."
php -S 0.0.0.0:5000 -t . router.php
