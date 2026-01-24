<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Contact;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactReplyMail;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('message', 'like', "%{$request->search}%");
            });
        }

        return Inertia::render('Admin/Contacts/Index', [
            'contacts' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function markAsRead(Contact $contact)
    {
        $contact->update(['is_read' => true]);
        return back();
    }

    public function markAllAsRead()
    {
        Contact::where('is_read', false)->update(['is_read' => true]);
        return back()->with('success', 'All messages marked as read!');
    }

    public function reply(Request $request, Contact $contact)
    {
        $request->validate([
            'message' => 'required|string|min:10',
        ]);

        Mail::to($contact->email)->send(new ContactReplyMail($contact, $request->message));

        $contact->update([
            'reply_sent_at' => now(),
            'reply_content' => $request->message,
            'is_read' => true,
        ]);

        return back()->with('success', 'Reply sent successfully!');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return back()->with('success', 'Message deleted successfully!');
    }
}
