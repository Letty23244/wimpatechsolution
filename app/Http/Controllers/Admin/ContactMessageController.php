<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:viewAny,App\Models\ContactMessage');
    }

    public function index(Request $request)
    {
        $messages = ContactMessage::when(
                $request->status,
                fn($q) => $q->where('status', $request->status)
            )
            ->latest()
            ->paginate(15);

        return view('admin.messages.index', compact('messages'));
    }

    public function show(ContactMessage $contactMessage)
    {
        if ($contactMessage->status === 'unread') {
            $contactMessage->update(['status' => 'read']);
        }

        return view('admin.messages.show', compact('contactMessage'));
    }

    public function markAsReplied(ContactMessage $contactMessage)
    {
        $contactMessage->update(['status' => 'replied']);

        return back()->with('success', 'Message marked as replied.');
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message deleted successfully.');
    }
}