<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function index(Request $request, ReportService $reports)
    {
        [$from, $to, $problems] = $this->range($request);

        return view('reports.index', [
            'from' => $from, 'to' => $to, 'problems' => $problems,
            'r'    => $reports->summary($from, $to),
        ]);
    }

    public function export(Request $request)
    {
        [$from, $to] = $this->range($request);
        $name = "sales_{$from->toDateString()}_to_{$to->toDateString()}.csv";

        return response()->streamDownload(function () use ($from, $to) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // lets Excel read UTF-8 (peso sign, names) correctly
            fputcsv($out, ['Sale #', 'Date', 'Customer', 'Type', 'Recorded by', 'Payment', 'Total'], ',', '"', '');

            Sale::with('customer', 'cashier')
                ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
                ->lazyById(500)
                ->each(fn ($s) => fputcsv($out, [
                    $s->id, $s->created_at->format('Y-m-d H:i'), $this->safe($s->customer?->customer_name ?? 'Walk-in'),
                    $s->sale_type, $this->safe($s->cashier->full_name), $s->payment_status, number_format($s->total_amount, 2, '.', ''),
                ], ',', '"', ''));

            fclose($out);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** @return array{0:Carbon,1:Carbon,2:array<int,string>} */
    private function range(Request $request): array
    {
        $default = [now()->startOfMonth(), now()->startOfDay()];
        $v = Validator::make($request->query(), [
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to'   => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
        ], [
            'from.date_format' => 'Enter the start date as YYYY-MM-DD.',
            'to.date_format'   => 'Enter the end date as YYYY-MM-DD.',
            'to.after_or_equal' => 'The end date cannot be before the start date.',
        ]);

        if ($v->fails()) {
            return [...$default, $v->errors()->all()];
        }

        $from = $request->query('from') ? Carbon::createFromFormat('Y-m-d', $request->query('from'))->startOfDay() : $default[0];
        $to = $request->query('to') ? Carbon::createFromFormat('Y-m-d', $request->query('to'))->startOfDay() : $default[1];

        if (abs($from->diffInDays($to)) > 366) {
            return [...$default, ['Choose a range of one year or less.']];
        }

        return [$from, $to, []];
    }

    /** Stops spreadsheet formula injection from names that start with = + - or @. */
    private function safe(string $value): string
    {
        return preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value;
    }
}
