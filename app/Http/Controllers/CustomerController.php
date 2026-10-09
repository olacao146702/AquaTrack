<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $term = trim((string) $request->query('q', ''));

        return view('customers.index', [
            'customers' => Customer::search($term)->orderBy('customer_name')->paginate(15)->withQueryString(),
            'term'      => $term,
        ]);
    }

    public function create()
    {
        return view('customers.form', ['customer' => new Customer(['customer_type' => 'Household'])]);
    }

    public function store(CustomerRequest $request)
    {
        $customer = Customer::create($request->validated());

        return redirect()->route('customers.index')->with('ok', "Customer {$customer->customer_name} added.");
    }

    public function edit(Customer $customer)
    {
        return view('customers.form', compact('customer'));
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return redirect()->route('customers.index')->with('ok', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        // Past sales and deliveries must keep their customer, so those customers cannot be deleted.
        if ($customer->sales()->exists() || $customer->deliveries()->exists()) {
            return back()->withErrors(['customer' => "{$customer->customer_name} has sales or deliveries on record and cannot be deleted."]);
        }
        $customer->delete();

        return redirect()->route('customers.index')->with('ok', 'Customer deleted.');
    }
}
