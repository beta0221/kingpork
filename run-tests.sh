#!/bin/bash

# Laravel KingPork 測試執行腳本

echo "🧪 執行 Laravel KingPork 單元測試套件"
echo "=================================="

# 檢查 vendor/bin/phpunit 是否存在
if [ ! -f "vendor/bin/phpunit" ]; then
    echo "❌ PHPUnit 未安裝，請先執行 composer install"
    exit 1
fi

# 清除快取
echo "🧹 清除快取..."
php artisan cache:clear > /dev/null 2>&1
php artisan config:clear > /dev/null 2>&1

# 執行簡單測試（不需要資料庫）
echo ""
echo "🚀 執行簡單測試 (無資料庫依賴)..."
echo "================================"
vendor/bin/phpunit tests/Unit/SimpleUserTest.php --testdox

# 檢查測試資料庫是否存在
echo ""
echo "🔍 檢查測試環境..."
echo "=================="

# 嘗試執行一個需要資料庫的測試來檢查環境
if vendor/bin/phpunit tests/Unit/UserTest.php --filter testUpdateBonusDecrease > /dev/null 2>&1; then
    echo "✅ 測試資料庫環境正常"
    
    # 執行 Unit Tests
    echo ""
    echo "📊 執行 Unit Tests..."
    echo "====================="
    vendor/bin/phpunit tests/Unit --testdox
else
    echo "⚠️  測試資料庫環境未設定，跳過需要資料庫的測試"
    echo "💡 請參考 TESTING_SETUP.md 設定測試資料庫"
    echo ""
    echo "📊 執行可用的 Unit Tests..."
    echo "=========================="
    vendor/bin/phpunit tests/Unit/SimpleUserTest.php --testdox
fi

# 執行 Feature Tests  
echo ""
echo "🎯 執行 Feature Tests..."
echo "======================="
vendor/bin/phpunit tests/Feature --testdox

# 執行完整測試套件並生成覆蓋率報告（如果安裝了 xdebug）
echo ""
echo "📈 執行完整測試套件..."
echo "===================="
if php -m | grep -q xdebug; then
    echo "🔍 包含程式碼覆蓋率分析..."
    vendor/bin/phpunit --coverage-text --coverage-html coverage-report
    echo "📋 覆蓋率報告已生成至 coverage-report/ 目錄"
else
    vendor/bin/phpunit --testdox
    echo "💡 提示：安裝 xdebug 擴充套件可生成程式碼覆蓋率報告"
fi

echo ""
echo "✅ 測試完成！"