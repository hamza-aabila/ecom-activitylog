<?php

return [
    /*
    |--------------------------------------------------------------------------
    | E-commerce Activity Log Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for enhanced activity logging in e-commerce applications
    |
    */

    /**
     * Fields to hide from activity timeline display
     */
    'hide_fields' => [
        'created_at',
        'updated_at', 
        'deleted_at',
        'password',
        'remember_token',
    ],

    /**
     * Event type mapping with icons and colors for Filament display
     */
    'event_types' => [
        // Order lifecycle events
        'created' => [
            'icon' => 'heroicon-m-sparkles',
            'color' => 'success',
            'label' => 'Created',
        ],
        'updated' => [
            'icon' => 'heroicon-m-pencil-square',
            'color' => 'warning',
            'label' => 'Updated',
        ],
        'deleted' => [
            'icon' => 'heroicon-m-trash',
            'color' => 'danger',
            'label' => 'Deleted',
        ],

        // E-commerce specific events
        'status_changed' => [
            'icon' => 'heroicon-m-check-badge',
            'color' => 'info',
            'label' => 'Status Changed',
        ],
        'confirmation_changed' => [
            'icon' => 'heroicon-m-phone',
            'color' => 'warning',
            'label' => 'Confirmation Status',
        ],
        'city_changed' => [
            'icon' => 'heroicon-m-map-pin',
            'color' => 'primary',
            'label' => 'City Changed',
        ],
        'schedule_changed' => [
            'icon' => 'heroicon-m-clock',
            'color' => 'primary',
            'label' => 'Scheduled',
        ],
        'provider_changed' => [
            'icon' => 'heroicon-m-truck',
            'color' => 'info',
            'label' => 'Provider Changed',
        ],
        'order_no_changed' => [
            'icon' => 'heroicon-m-hashtag',
            'color' => 'info',
            'label' => 'Tracking Number',
        ],
        'amount_collected_changed' => [
            'icon' => 'heroicon-m-banknotes',
            'color' => 'success',
            'label' => 'Amount Collected',
        ],
        'customer_name_changed' => [
            'icon' => 'heroicon-m-user',
            'color' => 'gray',
            'label' => 'Customer Name',
        ],
        'phone_changed' => [
            'icon' => 'heroicon-m-phone',
            'color' => 'gray',
            'label' => 'Phone Number',
        ],
        'address_line_changed' => [
            'icon' => 'heroicon-m-map',
            'color' => 'gray',
            'label' => 'Address',
        ],
        'replace_changed' => [
            'icon' => 'heroicon-m-arrow-path',
            'color' => 'gray',
            'label' => 'Replacement Status',
        ],
        'order_notes_changed' => [
            'icon' => 'heroicon-m-document-text',
            'color' => 'gray',
            'label' => 'Notes',
        ],
        'related_order_changed' => [
            'icon' => 'heroicon-m-link',
            'color' => 'gray',
            'label' => 'Related Order',
        ],

        // Commission & Payment events
        'commission_created' => [
            'icon' => 'heroicon-m-currency-dollar',
            'color' => 'success',
            'label' => 'Commission Created',
        ],
        'payout_processed' => [
            'icon' => 'heroicon-m-arrow-up-circle',
            'color' => 'success',
            'label' => 'Payout Processed',
        ],

        // Shipment events
        'shipment_created' => [
            'icon' => 'heroicon-m-cube',
            'color' => 'info',
            'label' => 'Shipment Created',
        ],
        'shipment_status_changed' => [
            'icon' => 'heroicon-m-truck',
            'color' => 'primary',
            'label' => 'Shipment Status',
        ],
    ],

    /**
     * Date and time formatting
     */
    'date_format' => 'd/m/Y',
    'datetime_format' => 'd/m/Y H:i:s',
    'time_format' => 'H:i',

    /**
     * Timeline display options
     */
    'timeline' => [
        'default_limit' => 50,
        'max_limit' => 200,
        'show_empty_state' => true,
        'empty_state_message' => 'No activity recorded yet.',
        'group_by_date' => true,
        'show_relative_time' => true,
    ],

    /**
     * Field formatters for specific data types
     */
    'field_formatters' => [
        'money' => [
            'decimal_places' => 2,
            'decimal_separator' => ',',
            'thousands_separator' => ' ',
        ],
        'phone' => [
            'format' => 'international', // or 'national', 'e164'
        ],
        'enum' => [
            'show_value' => false, // Show enum value or label
        ],
    ],

    /**
     * Activity log name for e-commerce events
     */
    'log_name' => 'ecommerce',

    /**
     * Models that should use e-commerce activity logging
     */
    'models' => [
        'order' => \App\Models\Order::class,
        'shipment' => \App\Models\Shipment::class,
        'commission' => \App\Models\Commission::class,
        'payout' => \App\Models\Payout::class,
    ],
];