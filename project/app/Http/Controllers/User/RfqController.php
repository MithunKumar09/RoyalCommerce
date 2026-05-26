<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Rfq;

class RfqController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'email'      => 'required|email|max:255',
            'message'    => 'required|string|min:10',
            'attachment' => 'nullable|file|max:5120',
        ]);

        $filePath = null;

        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('rfq_attachments', 'public');
        }

        Rfq::create([
            'product_id'      => $request->product_id,
            'user_id'         => Auth::id(),
            'product_name'    => $request->product_name,
            'sku'             => $request->sku,

            'first_name'      => $request->first_name,
            'last_name'       => $request->last_name,
            'email'           => $request->email,
            'phone'           => $request->phone,
            'country'         => $request->country,
            'company_name'    => $request->company_name,

            'product_type'    => $request->product_type,
            'estimate_budget' => $request->estimate_budget,
            'message'         => $request->message,
            'attachment'      => $filePath,

            'ip_address'      => $request->ip(),
            'user_agent'      => $request->userAgent(),
        ]);

        return response()->json([
            'status'  => 1,
            'message' => 'Your quotation request has been submitted successfully.',
        ]);
    }
}
