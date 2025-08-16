# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### Laravel Artisan Commands
```bash
# Database operations
php artisan migrate                    # Run database migrations
php artisan migrate:refresh            # Reset and re-run all migrations
php artisan migrate:status             # Show migration status

# Application
php artisan tinker                     # Interactive PHP shell
php artisan serve                      # Start development server

# Cache management
php artisan cache:clear                # Clear application cache
php artisan config:clear               # Clear configuration cache
php artisan route:clear                # Clear route cache

# Custom commands
php artisan test:job                   # Test job command
```

### Frontend Build Commands
```bash
# Development
npm run dev                            # Build assets for development
npm run watch                          # Watch files and rebuild on changes

# Production
npm run production                     # Build optimized assets for production
```

### Testing
```bash
vendor/bin/phpunit                     # Run all tests
vendor/bin/phpunit tests/Unit          # Run unit tests only
vendor/bin/phpunit tests/Feature       # Run feature tests only
```

## Architecture Overview

### Core Business Logic
This is an e-commerce platform specialized for group buying (團購), built on Laravel 5.4. The system handles both individual purchases and group buying scenarios.

**Key Models Relationship:**
- `User` → `Kart` (shopping cart items)
- `User` → `Group` (as dealer/organizer)
- `Group` → `GroupMember` → `GroupMembersBill` (group purchase flow)
- `Products` → `ProductCategory` (product organization)
- `Bill` → `BillItem` (order details)
- `Products` ↔ `Inventory` (stock management with pivot table)

### Group Buying Flow
1. **Group Creation**: Users with dealer status create groups via `GroupController`
2. **Member Participation**: Members join groups and place orders
3. **Order Processing**: Individual orders are tracked through `GroupMembersBill`
4. **Inventory Management**: Stock is managed through `PSIController` (進銷存)

### Payment Integration
- **ECPay Integration**: Payment processing through ECPay (綠界科技)
- **Multiple Payment Methods**: Credit card, ATM transfer
- **Invoice System**: Electronic invoice generation via `InvoiceLog`

### Key Controllers Architecture
- `GroupBuyController`: Main group buying interface (routes/web.php:142)
- `ProductController`: Product management (admin middleware)
- `BillController`: Order processing and payment
- `kartController`: Shopping cart functionality
- `OrderManagementController`: Backend order management
- `PSIController`: Inventory management system

### Database Architecture Notes
- **Bonus System**: Users earn/spend bonus points on purchases
- **Product Binding**: Products can be bound together for bundled sales
- **Additional Products**: Special category (ID=12) for add-on purchases
- **Carrier Restrictions**: Products have shipping method limitations
- **Favorite Addresses**: Users can save multiple delivery addresses

### Frontend Structure
- **Blade Templates**: Traditional server-side rendering
- **jQuery**: Client-side interactivity
- **Bootstrap 3**: UI framework
- **Custom CSS**: Module-specific stylesheets in public/css/

### Important Configuration
- **ECPay Settings**: Configure in `.env` with ECPAY_MERCHANT_ID, ECPAY_HASH_KEY, etc.
- **Google Analytics**: GTM_ID and GA_ID for tracking
- **Image Processing**: Uses Intervention/Image for product images
- **Excel Export**: Maatwebsite/Excel for order reports

### Custom Business Logic
- **Additional Purchase Threshold**: Products over 500 NT can add special items
- **Product Violation Check**: Validates bound products are purchased together
- **Inventory Calculation**: Complex inventory deduction across multiple warehouses
- **Bonus Point System**: Automatic calculation and user balance updates