@extends('layouts.app')
@section('title', $customer->exists ? 'Edit customer' : 'Add customer')
@section('content')
<div class="card" style="max-width:560px">
  <h2 style="margin-top:0">{{ $customer->exists ? 'Edit customer' : 'Add customer' }}</h2>
  <form method="post" action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">@csrf @if($customer->exists) @method('PUT') @endif
    <label for="customer_name">Name</label><input id="customer_name" name="customer_name" value="{{ old('customer_name', $customer->customer_name) }}" maxlength="100" required>
    <label for="contact_number">Contact number <small>(optional)</small></label><input id="contact_number" name="contact_number" value="{{ old('contact_number', $customer->contact_number) }}" maxlength="15" inputmode="tel">
    <label for="customer_type">Type</label><select id="customer_type" name="customer_type">@foreach(\App\Models\Customer::TYPES as $t)<option @selected(old('customer_type', $customer->customer_type) === $t)>{{ $t }}</option>@endforeach</select>
    <label for="address">Address <small>(optional)</small></label><input id="address" name="address" value="{{ old('address', $customer->address) }}" maxlength="255">
    <p class="actions"><button>Save customer</button><a class="btn alt" href="{{ route('customers.index') }}">Cancel</a></p>
  </form>
</div>
@endsection
