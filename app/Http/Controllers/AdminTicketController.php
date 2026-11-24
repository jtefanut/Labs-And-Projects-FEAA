<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SupportTicket;
use App\Notifications\PortalUpdateNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminTicketController extends Controller
{
    public function show(SupportTicket $ticket): View
    {
        $ticket->load('client.user', 'service.product', 'messages.user');

        return view('admin.tickets.show', ['ticket' => $ticket]);
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $validated = $request->validate(['message' => ['required', 'string', 'max:4000']]);
        abort_unless(in_array($ticket->status, ['open', 'in_progress', 'waiting_client'], true), 422, 'Acest tichet este închis.');

        DB::transaction(function () use ($request, $ticket, $validated) {
            $ticket->messages()->create(['user_id' => $request->user()->id, 'body' => $validated['message']]);
            $ticket->update(['status' => 'waiting_client']);
            $ticket->client->user->notify(new PortalUpdateNotification(
                'Răspuns nou la tichet',
                'Echipa a răspuns la solicitarea „'.$ticket->subject.'”.',
                route('client.tickets'),
            ));
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'Admin replied to support ticket',
                'entity_type' => 'ticket',
                'entity_id' => $ticket->id,
                'ip_address' => $request->ip(),
            ]);
        });

        return to_route('admin.tickets.show', $ticket)->with('success', 'Răspunsul a fost trimis clientului.');
    }
}
