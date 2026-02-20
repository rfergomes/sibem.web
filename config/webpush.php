<?php

return [

    /*
     * These are the keys for authentication.
     */
    'vapid' => [
        'subject' => env('VAPID_SUBJECT', 'mailto:rfergomes@gmail.com'),
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
        'pem_file' => env('VAPID_PEM_FILE'),
    ],

    /*
     * This is the name of the table that will be created by the migration and
     * used by the model to store push subscriptions.
     */
    'table_name' => env('WEBPUSH_DB_TABLE', 'push_subscriptions'),

    /*
     * This is the name of the model that will be used to store push subscriptions.
     */
    'model' => \NotificationChannels\WebPush\PushSubscription::class,

    /*
     * This is the name of the column that will be used to store the push subscription
     * endpoint.
     */
    'subscriber_column' => env('WEBPUSH_DB_SUBSCRIBER_COLUMN', 'subscribable'),

    /*
     * This is the database connection that will be used.
     */
    'database_connection' => env('WEBPUSH_DB_CONNECTION', env('DB_CONNECTION', 'mysql')),

    /*
     * The GCM project identifier.
     */
    'gcm' => [
        'key' => env('GCM_KEY'),
        'sender_id' => env('GCM_SENDER_ID'),
    ],

    /*
     * The client options.
     */
    'client_options' => [],

];
