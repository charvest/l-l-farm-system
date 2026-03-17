@extends('layouts.app')

@section('content')

<div class="py-12">

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
<div class="max-w-xl">
@include('profile.partials.update-profile-information-form')
</div>
</div>

<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
<div class="max-w-xl">
@include('profile.partials.update-password-form')
</div>
</div>

<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
<div class="max-w-xl">
@include('profile.partials.delete-user-form')
</div>
</div>

{{-- ORDER HISTORY --}}

<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

<h2 class="text-xl font-bold mb-4">My Orders</h2>

@if($orders->count() > 0)

<table class="w-full border">

<thead>
<tr class="bg-green-100">
<th class="p-2">Order ID</th>
<th class="p-2">Date</th>
<th class="p-2">Status</th>
</tr>
</thead>

<tbody>

@foreach($orders as $order)

<tr class="text-center border-t">
<td class="p-2">#{{ $order->id }}</td>
<td class="p-2">{{ $order->created_at->format('M d, Y') }}</td>
<td class="p-2">{{ $order->status ?? 'Pending' }}</td>
</tr>

@endforeach

</tbody>

</table>

@else

<p>No orders yet.</p>

@endif

</div>

</div>
</div>

@endsection