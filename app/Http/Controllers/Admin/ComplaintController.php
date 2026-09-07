<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrxComplaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'open');

        $query = TrxComplaint::with(['customer', 'order'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $complaints = $query->paginate(20)->withQueryString();

        $counts = [
            'open' => TrxComplaint::where('status', 'open')->count(),
            'in_progress' => TrxComplaint::where('status', 'in_progress')->count(),
            'resolved' => TrxComplaint::where('status', 'resolved')->count(),
        ];

        return view('admin.complaints.index', compact('complaints', 'status', 'counts'));
    }

    public function update(Request $request, TrxComplaint $complaint)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved',
            'resolution_note' => 'nullable|string|max:1000',
        ]);

        $complaint->update([
            'status' => $request->status,
            'resolution_note' => $request->resolution_note,
            'resolved_by' => $request->status === 'resolved' ? auth()->id() : $complaint->resolved_by,
            'resolved_at' => $request->status === 'resolved' ? now() : null,
        ]);

        return back()->with('success', "Komplain #{$complaint->id} diperbarui.");
    }
}
