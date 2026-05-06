<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ReceiveSession;

class ValidateReceiveSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->route('token');

        // Find session by token
        $session = ReceiveSession::where('session_token', $token)->first();

        // Token not found
        if (!$session) {
            return response()->view('receive.invalid', [
                'message' => 'This receive session does not exist.',
                'code' => 'INVALID_TOKEN'
            ], 404);
        }

        // Session cancelled
        if ($session->status === 'cancelled') {
            return response()->view('receive.invalid', [
                'message' => 'This receive session has been cancelled.',
                'code' => 'CANCELLED'
            ], 410);
        }

        // Session completed
        if ($session->status === 'completed') {
            return response()->view('receive.invalid', [
                'message' => 'This receiving session has already been completed.',
                'code' => 'COMPLETED',
                'event' => $session->event
            ], 410);
        }

        // Session expired
        if ($session->isExpired()) {
            $session->update(['status' => 'expired']);
            return response()->view('receive.invalid', [
                'message' => 'This receive session has expired. Ask your coordinator to extend or restart.',
                'code' => 'EXPIRED',
                'session' => $session
            ], 410);
        }

        // User not authenticated — store intended URL and redirect to login
        if (!auth()->check()) {
            session(['url.intended' => $request->url()]);
            return redirect()->route('login')
                ->with('scan_redirect', true)
                ->with('event_name', $session->event->name);
        }

        // Authenticated but wrong role — only Admin, Manager, Staff, Warehouse can access
        $allowedRoles = ['admin', 'manager', 'staff', 'warehouse'];
        $userRoles = auth()->user()->getRoleNames()->map(fn($r) => strtolower($r))->toArray();
        if (empty(array_intersect($allowedRoles, $userRoles))) {
            return response()->view('receive.invalid', [
                'message' => 'You do not have permission to access receive sessions.',
                'code' => 'UNAUTHORIZED'
            ], 403);
        }

        // All checks passed — load event relationship and bind session to request for use in controller
        $session->load('event');
        $request->attributes->set('receive_session', $session);

        return $next($request);
    }
}
