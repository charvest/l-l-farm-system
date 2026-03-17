@extends('layouts.app')

@section('content')


<div class="container" style="padding:40px;">

    <h2>Farm Management Dashboard</h2>

    
    <h3 style="margin-top:30px;">Sales Summary</h3>

    <div style="display:flex; gap:20px; margin-top:20px;">
        <div class="card">
            <h4>Total Revenue</h4>
            <p>₱ {{ number_format($totalRevenue, 2) }}</p>
        </div>

        <div class="card">
            <h4>Total Orders</h4>
            <p>{{ $totalOrders }}</p>
        </div>

        <div class="card">
            <h4>Pending Orders</h4>
            <p>{{ $pendingOrders }}</p>
        </div>

        <div class="card">
            <h4>Completed Orders</h4>
            <p>{{ $completedOrders }}</p>
        </div>
    </div>


    <!-- PRODUCT SUMMARY -->
    <h3 style="margin-top:50px;">Product Summary</h3>

    <div style="display:flex; gap:20px; margin-top:20px;">
        <div class="card">
            <h4>Total Products</h4>
            <p>{{ $totalProducts }}</p>
        </div>

        <div class="card">
            <h4>Available</h4>
            <p>{{ $availableProducts }}</p>
        </div>

        <div class="card">
            <h4>Out of Stock</h4>
            <p>{{ $outOfStock }}</p>
        </div>
    </div>



    <h3 style="margin-top:50px;">Recent Orders</h3>

    <table border="1" cellpadding="10" cellspacing="0" width="100%" style="margin-top:15px; background:white;">
        <tr>
            <th>ID</th>
            <th>Status</th>
            <th>Date</th>
        </tr>

        @forelse($recentOrders as $order)
        <tr>
            <td>{{ $order->id }}</td>
            <td>{{ $order->status }}</td>
            <td>{{ $order->created_at }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="3">No orders yet</td>
        </tr>
        @endforelse
    </table>

</div>

@endsection