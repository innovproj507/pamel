# 📦 ECOMMERCE PLUGIN - COMPLETE DEPLOYMENT CHECKLIST

## 🎯 Quick Summary

**Missing in Production:** Entire `plugins/ecommerce/Controllers/` directory (6 files)

**Recommended:** Upload ALL ecommerce plugin files to ensure nothing else is missing

---

## 📂 Complete File Structure to Upload

### 1. Controllers (6 files) ⚠️ **MISSING - CRITICAL**

```
plugins/ecommerce/Controllers/
├── AdminCategoryController.php (4.3 KB)
├── AdminOrderController.php (1.0 KB)
├── AdminProductController.php (10.3 KB)
├── CartController.php (1.7 KB)
├── OrderController.php (2.6 KB)
└── ProductController.php (2.2 KB)
```

### 2. Models (5 files) ✓ Verify these exist

```
plugins/ecommerce/Models/
├── Cart.php (2.7 KB)
├── Category.php (2.4 KB)
├── Order.php (1.7 KB)
├── Product.php (6.7 KB)
└── ProductReview.php (2.3 KB)
```

### 3. Views (11 items) ✓ Verify these exist

```
plugins/ecommerce/views/
├── admin/ (subdirectory with 6 files)
│   ├── categories.php
│   ├── category-form.php
│   ├── orders.php
│   ├── order-detail.php
│   ├── products.php
│   └── product-form.php
├── cart.php (5.0 KB)
├── checkout.php (3.3 KB)
├── product.php (13.7 KB)
├── shop.php (8.4 KB)
└── success.php (1.9 KB)
```

### 4. Main Plugin File ✓ Verify exists

```
plugins/ecommerce/
└── ecommerce.php (5.6 KB)
```

---

## 🚀 Deployment Steps

### Option A: Upload Only Missing Files (Quick Fix)

**Upload just the Controllers directory:**

```
Source: c:\laragon\www\cms\plugins\ecommerce\Controllers\
Target: /var/www/html/pamel_edu_pa/plugins/ecommerce/Controllers/
```

Then run:
```bash
cd /var/www/html/pamel_edu_pa
composer dump-autoload --optimize
```

### Option B: Upload Entire Plugin (Recommended)

**Upload the complete ecommerce directory to be safe:**

```
Source: c:\laragon\www\cms\plugins\ecommerce\
Target: /var/www/html/pamel_edu_pa/plugins/ecommerce/
```

This ensures:
- ✅ All controllers present
- ✅ All models present
- ✅ All views present
- ✅ Main plugin file present

Then run:
```bash
cd /var/www/html/pamel_edu_pa
composer dump-autoload --optimize
```

---

## ✅ Verification Checklist

After uploading, SSH into server and verify:

```bash
# Check Controllers exist (should show 6 files)
ls -la /var/www/html/pamel_edu_pa/plugins/ecommerce/Controllers/

# Check Models exist (should show 5 files)
ls -la /var/www/html/pamel_edu_pa/plugins/ecommerce/Models/

# Check views exist (should show 5 files + 1 admin directory)
ls -la /var/www/html/pamel_edu_pa/plugins/ecommerce/views/

# Check admin views (should show 6 files)
ls -la /var/www/html/pamel_edu_pa/plugins/ecommerce/views/admin/

# Verify main plugin file exists
ls -la /var/www/html/pamel_edu_pa/plugins/ecommerce/ecommerce.php
```

---

## 🧪 Test Routes After Deployment

### Public Routes
1. ✅ Shop page: `http://your-domain.com/shop`
2. ✅ Product detail: `http://your-domain.com/shop/some-product-slug`
3. ✅ Cart: `http://your-domain.com/cart`
4. ✅ Checkout: `http://your-domain.com/checkout`

### Admin Routes
1. ✅ Products list: `http://your-domain.com/admin/products`
2. ✅ Create product: `http://your-domain.com/admin/products/create`
3. ✅ Categories: `http://your-domain.com/admin/categories`
4. ✅ Orders: `http://your-domain.com/admin/orders`

---

## 📊 File Count Summary

| Directory | Files | Status |
|-----------|-------|--------|
| Controllers | 6 | ⚠️ **MISSING** |
| Models | 5 | ✓ Verify |
| views/ | 5 | ✓ Verify |
| views/admin/ | 6 | ✓ Verify |
| Root | 1 (ecommerce.php) | ✓ Verify |
| **TOTAL** | **23 files** | Upload all to be safe |

---

## 🔍 Check Other Plugins Too

While you're at it, verify these plugins are complete:

```bash
# Check PayPal plugin
ls -la /var/www/html/pamel_edu_pa/plugins/paypal/

# Check SEO plugin  
ls -la /var/www/html/pamel_edu_pa/plugins/seo/
```

---

## ⚡ Quick Command Reference

```bash
# Navigate to project
cd /var/www/html/pamel_edu_pa

# Regenerate autoloader
composer dump-autoload --optimize

# Check error logs
tail -f logs/nginx_error.log

# Set permissions if needed
sudo chown -R www-data:www-data plugins/
chmod -R 755 plugins/
```

---

**Status:** Ready for deployment  
**Priority:** HIGH - Site functionality broken without these files
