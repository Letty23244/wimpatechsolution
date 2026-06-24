<?php

namespace App\Services;

use App\Models\ContactMessage;

class ContactService
{
    public function create(array $data, string $ip): ContactMessage
    {
        return ContactMessage::create([
            ...$data,
            'ip_address' => $ip,
        ]);
    }

    public function markAsRead(ContactMessage $message): void
    {
        if ($message->status === 'unread') {
            $message->update(['status' => 'read']);
        }
    }

    public function markAsReplied(ContactMessage $message): void
    {
        $message->update(['status' => 'replied']);
    }

    public function getUnreadCount(): int
    {
        return ContactMessage::unread()->count();
    }
}