<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rfq;
use Illuminate\Http\Request;

class RfqController extends Controller
{
    public function index()
    {
        $rfqs = Rfq::latest()->paginate(20);

        return view('admin.rfqs.index', compact('rfqs'));
    }

    public function show(Rfq $rfq)
    {
        return view('admin.rfqs.show', compact('rfq'));
    }

    public function updateStatus(Request $request, Rfq $rfq)
    {
        $request->validate([
            'status' => 'required|in:new,contacted,quoted,closed'
        ]);

        $rfq->update([
            'status' => $request->status
        ]);

        return redirect()
            ->back()
            ->with('success', 'RFQ status updated successfully');
    }
}
