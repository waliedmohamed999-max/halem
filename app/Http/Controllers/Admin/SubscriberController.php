<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscriberController extends Controller
{
    public function index()
    {
        $subscribers = Subscriber::query()->latest()->paginate(30);

        return view('admin.subscribers.index', compact('subscribers'));
    }

    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();

        return redirect()->route('admin.subscribers.index')->with('success', 'Deleted successfully');
    }

    public function exportCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="subscribers.csv"',
        ];

        return response()->stream(function (): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['email', 'created_at']);

            Subscriber::query()->orderBy('id')->chunk(500, function ($rows) use ($handle): void {
                foreach ($rows as $row) {
                    fputcsv($handle, [$row->email, $row->created_at]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
