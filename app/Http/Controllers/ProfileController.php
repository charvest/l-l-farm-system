<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        $ordersQuery = Order::query()
            ->where('user_id', $user->id)
            ->latest();

        $orders = $ordersQuery->take(25)->get();

        $statusCounts = $ordersQuery
            ->clone()
            ->selectRaw('LOWER(COALESCE(status, "pending")) as s, COUNT(*) as c')
            ->groupBy('s')
            ->pluck('c', 's')
            ->all();

        $get = static fn (string $k): int => (int) ($statusCounts[$k] ?? 0);

        return view('profile.show', [
            'user' => $user,
            'orders' => $orders,
            'orderCount' => (int) array_sum($statusCounts),
            'pendingCount' => $get('pending'),
            'processingCount' => $get('processing'),
            'shippedCount' => $get('shipped'),
            'deliveredCount' => $get('delivered') + $get('completed'),
            'wishlistCount' => 0,
            'couponCount' => 0,
            'points' => 0,
        ]);
    }

    public function edit(Request $request): View
    {
        $user = $request->user();

        $orders = Order::query()
            ->where('user_id', $user->id)
            ->latest()
            ->take(25)
            ->get();

        return view('profile.edit', [
            'user' => $user,
            'orders' => $orders,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return back()->with('success', 'Profile updated.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        return redirect()->route('home');
    }
}

