<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\SatisfactionRating;
use App\Models\Tickets;
use App\Models\TicketsResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX — Daftar tiket milik karyawan yang sedang login
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $tickets = Tickets::where('reporter_id', Auth::id())
            ->with(['operator', 'rating'])
            ->latest()
            ->paginate(10);

        return view('karyawan_fe.tickets.index', compact('tickets'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE — Form buat aduan baru
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $categories = array_keys(Tickets::getAutoReplySuggestions());

        return view('karyawan_fe.tickets.create', compact('categories'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE — Simpan aduan baru ke database
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'subject'     => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'category'    => 'required|string',
            'priority'    => 'required|in:Low,Mid,High',
        ]);

        $ticket = Tickets::create([
            'reporter_id' => Auth::id(),
            'subject'     => $request->subject,
            'description' => $request->description,
            'category'    => $request->category,
            'priority'    => $request->priority,
            'status'      => 'Open',
        ]);

        return redirect()
            ->route('karyawan.tickets.show', $ticket)
            ->with('success', 'Aduan berhasil dikirim! Nomor tiket: #' . $ticket->id);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW — Detail tiket + progress percakapan
    |--------------------------------------------------------------------------
    */
    public function show(Tickets $ticket)
    {
        // Pastikan hanya pemilik tiket yang bisa akses
        abort_if($ticket->reporter_id !== Auth::id(), 403, 'Akses ditolak.');

        $ticket->load(['operator', 'rating']);

        // Karyawan hanya lihat balasan yang bukan internal note (is_auto_reply tidak disembunyikan)
        $responses = TicketsResponse::where('ticket_id', $ticket->id)
            ->with('responder')
            ->oldest()
            ->get();

        return view('karyawan_fe.tickets.show', compact('ticket', 'responses'));
    }

    /*
    |--------------------------------------------------------------------------
    | REPLY — Karyawan mengirim follow-up / balasan ke tiket
    |--------------------------------------------------------------------------
    */
    public function reply(Request $request, Tickets $ticket)
    {
        abort_if($ticket->reporter_id !== Auth::id(), 403, 'Akses ditolak.');
        abort_if($ticket->status === 'Closed', 403, 'Tiket sudah ditutup, tidak bisa dibalas.');

        $request->validate([
            'message' => 'required|string|min:5',
        ]);

        TicketsResponse::create([
            'ticket_id'    => $ticket->id,
            'responder_id' => Auth::id(),
            'message'      => $request->message,
            'is_auto_reply' => false, // Balasan dari karyawan bukan auto-reply
        ]);

        // Jika tiket sedang In-Progress, kembalikan ke Open
        // agar admin tahu ada balasan baru dari pelapor
        if ($ticket->status === 'In-Progress') {
            $ticket->update(['status' => 'Open']);
        }

        return back()->with('success', 'Balasan berhasil dikirim.');
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH SIMILAR — AJAX Full-Text Search Anti-Duplikasi
    | Dipanggil saat karyawan mengetik subject/description di form buat aduan
    |--------------------------------------------------------------------------
    */
    public function searchSimilar(Request $request)
    {
        $request->validate(['keyword' => 'required|string|min:3']);

        $similar = Tickets::searchSimilar($request->keyword);

        return response()->json([
            'found'   => $similar->count() > 0,
            'tickets' => $similar->map(fn($t) => [
                'id'       => $t->id,
                'subject'  => $t->subject,
                'status'   => $t->status,
                'priority' => $t->priority,
                'reporter' => $t->reporter->nama, // kolom nama sesuai User model kamu
                'created'  => $t->created_at->diffForHumans(),
            ]),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | RATE — Karyawan memberikan rating setelah tiket Closed
    |--------------------------------------------------------------------------
    */
    public function rate(Request $request, Tickets $ticket)
    {
        abort_if($ticket->reporter_id !== Auth::id(), 403, 'Akses ditolak.');
        abort_if($ticket->status !== 'Closed', 403, 'Tiket belum diselesaikan.');
        abort_if($ticket->rating()->exists(), 403, 'Rating sudah pernah diberikan.');

        $request->validate([
            'score'    => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:500',
        ]);

        SatisfactionRating::create([
            'ticket_id'   => $ticket->id,
            'reporter_id' => Auth::id(),
            'score'       => $request->score,
            'feedback'    => $request->feedback,
        ]);

        return back()->with('success', 'Terima kasih atas penilaian Anda!');
    }
}