<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SatisfactionRating;
use App\Models\Tickets;
use App\Models\TicketsResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX — Antrean semua tiket diurutkan berdasarkan prioritas
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = Tickets::with(['reporter', 'operator'])
            ->byPriority(); // Scope: High > Mid > Low, lalu terlama

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by kategori
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $tickets    = $query->paginate(15)->withQueryString();
        $categories = array_keys(Tickets::getAutoReplySuggestions());

        // Hitung badge jumlah per status untuk header
        $counts = [
            'open'        => Tickets::where('status', 'Open')->count(),
            'in_progress' => Tickets::where('status', 'In-Progress')->count(),
            'closed'      => Tickets::where('status', 'Closed')->count(),
        ];

        return view('admin.tickets.index', compact('tickets', 'categories', 'counts'));
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW — Detail tiket + thread percakapan lengkap + auto-reply suggestion
    |--------------------------------------------------------------------------
    */
    public function show(Tickets $ticket)
    {
        $ticket->load(['reporter', 'operator', 'responses.responder', 'rating']);

        // Ambil saran jawaban otomatis berdasarkan kategori tiket
        $autoReply = $ticket->auto_reply;

        return view('admin.tickets.show', compact('ticket', 'autoReply'));
    }

    /*
    |--------------------------------------------------------------------------
    | REPLY — Admin membalas tiket + otomatis catat SLA & assign operator
    |--------------------------------------------------------------------------
    */
    public function reply(Request $request, Tickets $ticket)
    {
        $request->validate([
            'message'       => 'required|string|min:5',
            'is_auto_reply' => 'boolean',
            'new_status'    => 'nullable|in:Open,In-Progress,Closed',
        ]);

        // Cek apakah ini balasan pertama dari admin (untuk SLA Response Time)
        $isFirstResponse = TicketsResponse::where('ticket_id', $ticket->id)
            ->whereHas('responder', fn($q) => $q->where('role', 'admin'))
            ->doesntExist();

        // Catat first_response_at untuk SLA jika ini balasan pertama
        if ($isFirstResponse && !$ticket->first_response_at) {
            $ticket->first_response_at = now();
        }

        // Auto-assign operator jika tiket belum ada yang handle
        if (!$ticket->operator_id) {
            $ticket->operator_id = Auth::id();
        }

        // Update status tiket
        $newStatus      = $request->new_status ?? 'In-Progress';
        $ticket->status = $newStatus;

        // Catat resolved_at untuk SLA Resolution Time jika di-close
        if ($newStatus === 'Closed' && !$ticket->resolved_at) {
            $ticket->resolved_at = now();
        }

        $ticket->save();

        // Simpan balasan
        TicketsResponse::create([
            'ticket_id'     => $ticket->id,
            'responder_id'  => Auth::id(),
            'message'       => $request->message,
            'is_auto_reply' => $request->boolean('is_auto_reply'),
        ]);

        return back()->with('success', 'Balasan berhasil dikirim. Status tiket: ' . $ticket->status);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS — Ubah status tiket tanpa perlu reply
    |--------------------------------------------------------------------------
    */
    public function updateStatus(Request $request, Tickets $ticket)
    {
        $request->validate([
            'status' => 'required|in:Open,In-Progress,Closed',
        ]);

        $oldStatus      = $ticket->status;
        $ticket->status = $request->status;

        // Catat resolved_at jika di-close
        if ($request->status === 'Closed' && !$ticket->resolved_at) {
            $ticket->resolved_at = now();
        }

        $ticket->save();

        // Catat perubahan status sebagai log otomatis di thread
        TicketsResponse::create([
            'ticket_id'     => $ticket->id,
            'responder_id'  => Auth::id(),
            'message'       => '[LOG] Status diubah dari ' . $oldStatus . ' menjadi ' . $request->status . ' oleh ' . Auth::user()->nama,
            'is_auto_reply' => false,
        ]);

        return back()->with('success', 'Status tiket berhasil diubah menjadi ' . $request->status);
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD — Analitik performa operator & laporan kendala
    |--------------------------------------------------------------------------
    */
    public function dashboard()
    {
        // Statistik umum tiket
        $stats = [
            'total'       => Tickets::count(),
            'open'        => Tickets::where('status', 'Open')->count(),
            'in_progress' => Tickets::where('status', 'In-Progress')->count(),
            'closed'      => Tickets::where('status', 'Closed')->count(),
        ];

        // Performa tiap admin/operator
        $operatorPerformance = User::where('role', 'admin')
            ->get()
            ->map(function ($operator) {

                // Rata-rata Response Time (menit)
                $avgResponse = Tickets::where('operator_id', $operator->id)
                    ->whereNotNull('first_response_at')
                    ->get()
                    ->avg(fn($t) => $t->response_time_minutes);

                // Rata-rata Resolution Time (menit)
                $avgResolution = Tickets::where('operator_id', $operator->id)
                    ->whereNotNull('resolved_at')
                    ->get()
                    ->avg(fn($t) => $t->resolution_time_minutes);

                // Rata-rata rating kepuasan
                $avgRating = SatisfactionRating::whereHas(
                    'ticket',
                    fn($q) => $q->where('operator_id', $operator->id)
                )->avg('score');

                // Total tiket yang ditangani
                $totalHandled = Tickets::where('operator_id', $operator->id)->count();

                return [
                    'nama'           => $operator->nama,
                    'total_handled'  => $totalHandled,
                    'avg_response'   => round($avgResponse ?? 0),
                    'avg_resolution' => round($avgResolution ?? 0),
                    'avg_rating'     => round($avgRating ?? 0, 1),
                ];
            });

        // Jumlah tiket per kategori
        $ticketsByCategory = Tickets::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->get();

        // Tiket masuk per hari (7 hari terakhir) untuk grafik
        $ticketsPerDay = Tickets::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Rata-rata rating keseluruhan
        $avgRatingOverall = round(SatisfactionRating::avg('score') ?? 0, 1);

        // Feedback/Rating terbaru (5 teratas yang memiliki komentar)
        $recentFeedbacks = SatisfactionRating::with('ticket.reporter')
            ->whereNotNull('feedback')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.tickets.dashboard', compact(
            'stats',
            'operatorPerformance',
            'ticketsByCategory',
            'ticketsPerDay',
            'avgRatingOverall',
            'recentFeedbacks'
        ));
    }
}