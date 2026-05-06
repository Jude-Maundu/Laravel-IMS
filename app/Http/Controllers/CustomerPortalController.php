<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CustomerPortalController extends Controller
{
    public function showLoginForm()
    {
        return view('portal.login');
    }

    public function showBookingForm()
    {
        return view('portal.book');
    }

    public function submitBookingRequest(Request $request)
    {
        $validated = $request->validate([
            'client_name'    => 'required|string|max:255',
            'customer_phone' => 'required|string|max:15',
            'name'           => 'required|string|max:255',
            'venue'          => 'required|string|max:255',
            'event_date'     => 'required|date|after:today',
            'notes'          => 'required|string',
        ]);

        $validated['status'] = 'Draft';
        $validated['plan_ref'] = Event::generatePlanRef();
        
        $event = Event::create($validated);

        return redirect()->route('portal.login')->with('success', 'Booking request submitted! Use your phone number and Booking ID ' . $event->plan_ref . ' to track progress.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'plan_ref' => 'required|string',
            'phone'    => 'required|string',
        ]);

        $event = Event::where('plan_ref', $request->plan_ref)
            ->where('customer_phone', $request->phone)
            ->first();

        if (!$event) {
            return back()->with('error', 'Invalid Booking Number or Phone Number.')->withInput();
        }

        Session::put('customer_event_id', $event->id);
        Session::put('customer_phone', $event->customer_phone);

        return redirect()->route('portal.dashboard');
    }

    public function logout()
    {
        Session::forget(['customer_event_id', 'customer_phone']);
        return redirect()->route('portal.login')->with('success', 'You have been logged out.');
    }

    public function dashboard()
    {
        $eventId = Session::get('customer_event_id');
        $event = Event::with('payments', 'eventItems.item')->findOrFail($eventId);

        return view('portal.dashboard', compact('event'));
    }

    public function showEvent(Event $event)
    {
        // Ensure customer can only view their own event
        if ($event->id !== Session::get('customer_event_id')) {
            abort(403);
        }

        return view('portal.event_show', compact('event'));
    }

    public function initiatePayment(Request $request, Event $event)
    {
        if ($event->id !== Session::get('customer_event_id')) {
            abort(403);
        }

        $request->validate([
            'phone' => 'required|string|regex:/^254[17][0-9]{8}$/',
        ]);

        // Logic for triggering M-Pesa STK Push via MpesaController or Service
        $mpesa = new MpesaController();
        return $mpesa->stkPush($event, $request->phone);
    }

    public function checkPaymentStatus(Event $event)
    {
        if ($event->id !== Session::get('customer_event_id')) {
            abort(403);
        }

        return response()->json([
            'payment_status' => $event->payment_status,
            'status' => $event->status,
        ]);
    }

    public function downloadReceipt(Event $event)
    {
        if ($event->id !== Session::get('customer_event_id')) {
            abort(403);
        }

        if ($event->payment_status !== 'paid') {
            return back()->with('error', 'Receipt is only available for paid events.');
        }

        $reportsController = new ReportsController();
        return $reportsController->generateReceiptPdf($event);
    }
}
