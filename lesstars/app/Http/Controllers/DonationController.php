<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DonationController extends Controller
{
    public function create(): View
    {
        return view('donate');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'country' => ['nullable', 'string', 'max:100'],
            'currency' => ['required', 'in:RWF,USD'],
            'amount' => ['required', 'numeric', 'min:1', 'max:999999999'],
            'payment_method' => ['required', 'in:mobile_money,bank_transfer,card,other'],
            'message' => ['nullable', 'string', 'max:1000'],
            'privacy_consent' => ['accepted'],
        ]);

        unset($data['privacy_consent']);
        Donation::create($data + ['status' => 'pending']);

        return back()->with('success', 'Thank you. Your donation request has been received. Our team will contact you with the next steps.');
    }

    public function index(): View
    {
        return view('admin.donations.index', [
            'donations' => Donation::latest()->paginate(20),
        ]);
    }

    public function updateStatus(Request $request, Donation $donation): RedirectResponse
    {
        $donation->update($request->validate([
            'status' => ['required', 'in:pending,contacted,received,cancelled'],
        ]));

        return back()->with('success', 'Donation status updated.');
    }
}
