@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Your Cart</h2>

    @if(session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif

    @if(count($cart) > 0)

        @php $total = 0; @endphp

        @foreach($cart as $id => $item)
            @php $subtotal = $item['price'] * $item['quantity']; @endphp
            @php $total += $subtotal; @endphp

            <div style="border:1px solid #ccc;padding:10px;margin-bottom:10px;">
                <strong>{{ $item['name'] }}</strong>
                <p>Price: ₱{{ $item['price'] }}</p>
                <p>Quantity: {{ $item['quantity'] }}</p>
                <p>Subtotal: ₱{{ $subtotal }}</p>

                <form action="{{ route('cart.remove', $id) }}" method="POST">
                    @csrf
                    <button type="submit">Remove</button>
                </form>
            </div>
        @endforeach

        <h3>Total: ₱{{ $total }}</h3>

        <form action="{{ route('cart.clear') }}" method="POST">
            @csrf
            <button type="submit">Clear Cart</button>
        </form>

    @else
        <p>Cart is empty.</p>
    @endif
</div>
@endsection