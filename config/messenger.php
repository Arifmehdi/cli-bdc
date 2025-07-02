<?php

return [

    // 'user_model' => App\Models\User::class,

    'message_model' => Cmgmyr\Messenger\Models\AdminDealerMessage::class,

    'participant_model' => Cmgmyr\Messenger\Models\Participant::class,

    'thread_model' => Cmgmyr\Messenger\Models\Thread::class,

    /**
     * Define custom database table names - without prefixes.
     */
    'messages_table' => 'admin_dealer_messages',

    'participants_table' => null,

    'threads_table' => null,
];
