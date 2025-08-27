# E-commerce Activity Log for Laravel & Filament

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hamzaabaila/ecom-activitylog.svg?style=flat-square)](https://packagist.org/packages/hamzaabaila/ecom-activitylog)
[![Total Downloads](https://img.shields.io/packagist/dt/hamzaabaila/ecom-activitylog.svg?style=flat-square)](https://packagist.org/packages/hamzaabaila/ecom-activitylog)

A powerful Laravel package that extends Spatie's Activity Log with enhanced e-commerce features and beautiful Filament v4 timeline components.

## Features

- 🎯 **E-commerce focused**: Pre-built event types for orders, shipments, payments, and commissions
- 🎨 **Beautiful Filament v4 integration**: Timeline components and modal actions
- 🏷️ **Enum support**: Automatic label resolution for enum fields
- 💰 **Money formatting**: Built-in currency formatting for financial fields
- 🌍 **Multi-language**: Support for English, French, and Arabic
- ⚡ **Performance optimized**: Efficient querying and caching
- 🎭 **Customizable**: Flexible formatters and display options
- 📱 **Responsive**: Mobile-friendly timeline components

## Requirements

- PHP 8.2+
- Laravel 11.0+ or 12.0+
- Filament 4.0+
- Spatie Laravel Activity Log 4.10+

## Installation

Install the package via Composer:

```bash
composer require hamzaabaila/ecom-activitylog
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag="ecom-activitylog-config"
```

Optionally, publish the views and language files:

```bash
php artisan vendor:publish --tag="ecom-activitylog-views"
php artisan vendor:publish --tag="ecom-activitylog-lang"
```

## Quick Start

### 1. Add Activity Logging to Your Models

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HamzaAabila\EcomActivitylog\Traits\EcomLogsActivity;

class Order extends Model
{
    use EcomLogsActivity;

    // Your model code...
    
    public function getActivityDisplayName(): string
    {
        return "#{$this->id} - {$this->customer_name}";
    }
}
```

### 2. Add Timeline Action to Filament Table

```php
use HamzaAabila\EcomActivitylog\Filament\Table\Actions\TimelineAction;

public function table(Table $table): Table
{
    return $table
        ->actions([
            TimelineAction::make()->limit(100),
            // Other actions...
        ]);
}
```

### 3. Create Enhanced Activity Observer

```php
<?php

namespace App\Observers;

use App\Models\Order;
use HamzaAabila\EcomActivitylog\Traits\EcomLogsActivity;

class OrderObserver
{
    public function updated(Order $order): void
    {
        // Log status changes with enum support
        if ($order->wasChanged('status')) {
            $order->logStatusChange(
                'status',
                $order->getOriginal('status'),
                $order->status,
                'Order status updated'
            );
        }

        // Log money field changes
        if ($order->wasChanged('amount_collected')) {
            $order->logMoneyChange(
                'amount_collected',
                $order->getOriginal('amount_collected'),
                $order->amount_collected,
                $order->currency_code
            );
        }

        // Log relationship changes
        if ($order->wasChanged('delivery_provider_id')) {
            $order->logRelationshipChange(
                'delivery_provider',
                $order->getOriginal('delivery_provider_id'),
                $order->delivery_provider_id,
                fn($id) => \App\Models\DeliveryProvider::find($id)?->name
            );
        }
    }
}
```

## Advanced Usage

### Custom Event Logging

```php
// Log custom e-commerce events
$order->logEcomEvent('payment_failed', 'Payment attempt failed', [
    'payment_method' => 'credit_card',
    'error_code' => 'DECLINED',
    'amount' => 99.99
]);

// Log commission events
$order->logEcomEvent('commission_created', 'Commission awarded to agent', [
    'agent_id' => $agent->id,
    'commission_amount' => 10.00,
    'commission_rate' => '5%'
]);
```

### Custom Timeline Formatter

```php
<?php

namespace App\Services;

use HamzaAabila\EcomActivitylog\Services\DefaultTimelineFormatter;

class CustomTimelineFormatter extends DefaultTimelineFormatter
{
    public function getFieldMetadata(string $field): array
    {
        return match ($field) {
            'payment_status' => [
                'label' => 'Payment Status',
                'color' => 'success',
                'icon' => 'heroicon-m-credit-card'
            ],
            default => parent::getFieldMetadata($field),
        };
    }
}
```

Register your custom formatter in `AppServiceProvider`:

```php
$this->app->bind(
    \HamzaAabila\EcomActivitylog\Contracts\TimelineFormatter::class,
    \App\Services\CustomTimelineFormatter::class
);
```

### Configuration

The configuration file allows you to customize:

```php
return [
    // Hide sensitive fields
    'hide_fields' => ['password', 'remember_token'],
    
    // Event type styling
    'event_types' => [
        'payment_processed' => [
            'icon' => 'heroicon-m-credit-card',
            'color' => 'success',
            'label' => 'Payment Processed',
        ],
    ],
    
    // Money formatting
    'field_formatters' => [
        'money' => [
            'decimal_places' => 2,
            'decimal_separator' => ',',
            'thousands_separator' => ' ',
        ],
    ],
    
    // Timeline display
    'timeline' => [
        'default_limit' => 50,
        'group_by_date' => true,
        'show_relative_time' => true,
    ],
];
```

## Real-World Example

This package provides comprehensive activity logging for e-commerce platforms, handling:

- **Order Lifecycle**: Status changes, confirmations, shipping updates
- **Customer Management**: Contact attempts, address changes, phone updates  
- **Commission Tracking**: Agent assignments, commission calculations, payouts
- **Shipment Monitoring**: Provider changes, tracking updates, delivery status
- **Financial Events**: Payment collections, fee calculations, refunds

```php
// Example OrderObserver implementation
public function updated(Order $order): void
{
    // Multi-field city change with custom properties
    if ($order->wasChanged('city') || $order->wasChanged('carrier_city_id')) {
        $order->logEcomEvent('city_changed', 'City and carrier updated', [
            'attributes' => [
                'city' => $order->city,
                'carrier_city_id' => $order->carrier_city_id,
            ],
            'old' => [
                'city' => $order->getOriginal('city'),
                'carrier_city_id' => $order->getOriginal('carrier_city_id'),
            ],
            'human' => [
                'summary' => $this->createCityChangeSummary($order),
            ],
        ]);
    }
}
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Hamza Aabila](https://github.com/hamzaabaila)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.# ecom-activitylog
