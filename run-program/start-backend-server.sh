#!/bin/bash

echo "=========================================="
echo "      STARTING LARAVEL BACKEND SERVER     "
echo "=========================================="

REPO_URL="https://github.com/krisnadewangga/agogo-be.git"
TARGET_BRANCH="new"

# 🔹 0. Netralkan environment variable Git yang terkunci dari Windows Batch
unset GIT_DIR
unset GIT_WORK_TREE

# 🔹 Tangkap PORT dari argumen pertama ($1). Jika kosong, default ke 8000
PORT=${1:-8000}

# 1. Cek apakah folder ini sudah merupakan repositori Git
if [ ! -d ".git" ]; then
    echo "[1/5] Folder belum terhubung ke Git. Melakukan Git Clone (Branch: $TARGET_BRANCH)..."
    git clone -b $TARGET_BRANCH $REPO_URL . || exit 1
else
    echo "[1/5] Fetching update & Pull dari Git (Branch: $TARGET_BRANCH)..."
    git fetch origin
    git checkout $TARGET_BRANCH 2>/dev/null || git checkout -b $TARGET_BRANCH origin/$TARGET_BRANCH
    git pull origin $TARGET_BRANCH
fi

# 2. Composer Install
if [ ! -d "vendor" ]; then
    echo "[2/5] Running composer install..."
    composer install
else
    echo "[2/5] Folder vendor sudah ada, melewati composer install..."
fi

# 3. NPM Install
if [ -f "package.json" ]; then
    if [ ! -d "node_modules" ]; then
        echo "[3/5] Running npm install..."
        npm.cmd install || npm install
    else
        echo "[3/5] Folder node_modules sudah ada..."
    fi
fi

# 4. Setup .env & Key
if [ ! -f ".env" ]; then
    echo "[4/5] Membuat file .env..."
    cp .env.example .env
    php artisan key:generate
else
    echo "[4/5] File .env sudah ada..."
fi

# 5. Jalankan Ngrok & Server Laravel
echo "------------------------------------------"
echo "Menjalankan Ngrok & Server di Port $PORT..."

# Jalankan Ngrok di latar belakang
ngrok http $PORT >/dev/null 2>&1 &

# Jalankan Laravel Server
echo "[5/5] Running Laravel Server di http://localhost:$PORT ..."
php artisan serve --host=0.0.0.0 --port=$PORT