# Laravel KingPork 測試文件

## 測試架構概覽

本專案已建立完整的單元測試和功能測試套件，涵蓋核心業務邏輯和關鍵功能。

### 測試分類

#### 1. Unit Tests (單元測試)
- **UserTest.php** - User Model 測試
  - 紅利點數計算邏輯
  - 購物車關聯查詢
  - 信用卡管理功能
  
- **ProductsTest.php** - Products Model 測試
  - 加價購邏輯處理
  - 商品綁定機制
  - 庫存計算功能
  - 違規檢查機制
  
- **BillTest.php** - Bill Model 測試
  - 訂單狀態管理
  - 出貨流程控制
  - 付款方式處理
  - 紅利系統整合
  
- **ECPayTest.php** - ECPay Helper 測試
  - 金流串接邏輯
  - 加密解密功能
  - 付款資訊處理

#### 2. Feature Tests (功能測試)
- **BillControllerTest.php** - 訂單控制器測試
  - 訂單建立流程
  - 付款處理邏輯
  - 狀態更新機制
  
- **KartControllerTest.php** - 購物車控制器測試
  - 購物車 CRUD 操作
  - 會員/訪客購物車管理
  - 數量計算邏輯
  
- **ECPayIntegrationTest.php** - ECPay 整合測試
  - 完整金流流程
  - 回調處理機制
  - 錯誤處理邏輯
  
- **BonusSystemTest.php** - 紅利系統測試
  - 紅利獲得機制
  - 紅利使用折抵
  - 退款回補處理

## 測試環境設定

### 必要軟體
- PHP >= 7.0
- Composer
- SQLite (測試資料庫)

### 環境變數
測試環境已在 `phpunit.xml` 中設定：
```xml
<env name="APP_ENV" value="testing"/>
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

## 執行測試

### 方法一：使用測試腳本
```bash
./run-tests.sh
```

### 方法二：直接使用 PHPUnit
```bash
# 執行所有測試
vendor/bin/phpunit

# 執行單元測試
vendor/bin/phpunit tests/Unit

# 執行功能測試
vendor/bin/phpunit tests/Feature

# 執行特定測試類別
vendor/bin/phpunit tests/Unit/UserTest.php

# 執行測試並生成覆蓋率報告
vendor/bin/phpunit --coverage-html coverage-report
```

## 測試資料工廠

已建立完整的測試資料工廠 (`database/factories/ModelFactory.php`)：
- User 工廠
- Products 工廠
- Bill 工廠
- BillItem 工廠
- Kart 工廠
- UserCreditCard 工廠
- PaymentLog 工廠
- 其他相關模型工廠

## 測試最佳實踐

### 1. 資料庫事務
所有測試都使用 `DatabaseTransactions` Trait，確保測試間的資料隔離。

### 2. 工廠使用
```php
// 建立測試用戶
$user = factory(User::class)->create(['bonus' => 500]);

// 建立測試商品
$product = factory(Products::class)->create(['price' => 1000]);
```

### 3. 測試輔助
使用 `TestHelper` Trait 提供常用的測試輔助方法。

### 4. 模擬外部服務
ECPay 等外部服務已進行適當的模擬和測試。

## 測試覆蓋範圍

### 核心業務邏輯
- ✅ 用戶管理與紅利系統
- ✅ 商品管理與庫存計算
- ✅ 訂單處理與狀態管理
- ✅ 購物車功能
- ✅ 金流整合 (ECPay)
- ✅ 紅利獲得/使用/退款

### 邊界條件測試
- ✅ 紅利點數不足處理
- ✅ 庫存不足檢查
- ✅ 無效資料處理
- ✅ 權限驗證
- ✅ 錯誤回應處理

## 持續整合建議

1. **Git Hooks**
   建議在 pre-commit hook 中執行測試：
   ```bash
   #!/bin/sh
   vendor/bin/phpunit --stop-on-failure
   ```

2. **CI/CD 流程**
   在部署前確保所有測試通過：
   ```bash
   composer install --no-dev --optimize-autoloader
   vendor/bin/phpunit
   ```

## 故障排除

### 常見問題

1. **記憶體不足**
   ```bash
   php -d memory_limit=512M vendor/bin/phpunit
   ```

2. **資料庫連線問題**
   確認 SQLite 擴充套件已安裝：
   ```bash
   php -m | grep sqlite
   ```

3. **工廠相依性問題**
   檢查 ModelFactory.php 中的模型關聯設定。

## 擴展測試

### 新增測試類別
1. 在適當的目錄 (`tests/Unit` 或 `tests/Feature`) 建立測試檔案
2. 繼承 `TestCase` 類別
3. 使用 `DatabaseTransactions` Trait
4. 遵循現有的命名慣例

### 測試範例
```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NewFeatureTest extends TestCase
{
    use DatabaseTransactions;

    public function testNewFeature()
    {
        // 測試邏輯
        $this->assertTrue(true);
    }
}
```

---

**注意事項：**
- 所有測試都應該是獨立的，不依賴其他測試的執行順序
- 使用有意義的測試名稱，清楚描述測試目的
- 定期更新測試以反映業務邏輯變更
- 保持測試簡潔，每個測試方法只測試一個功能點