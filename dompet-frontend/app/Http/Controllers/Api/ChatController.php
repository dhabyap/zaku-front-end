<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = $request->input('message');

        $backendUrl = env('VITE_API_BASE_URL', 'http://127.0.0.1:8001/api');

        try {
            $response = Http::timeout(30)->post($backendUrl . '/ai/chat', [
                'message' => $message,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            // Backend not available, use local parser
        }

        return $this->localParse($message);
    }

    private function localParse(string $message): array
    {
        $lower = strtolower($message);

        // Detect transaction patterns
        $patterns = [
            'makan|beli.*(?:makan|nasi|ayam|soto|bakso|warteg)|kopi|starbucks' => [
                'category' => 'MAKANAN',
                'emoji' => '🍜',
                'type' => 'expense',
                'label' => 'Makanan & Minuman',
            ],
            'grab|gojek|ojek|taksi|bensin|bbm|transport|parkir|tol' => [
                'category' => 'TRANSPORTASI',
                'emoji' => '🚗',
                'type' => 'expense',
                'label' => 'Transportasi',
            ],
            'listrik|pln|tagihan|pdam|air|pulsa|internet|telpon|bpjs' => [
                'category' => 'TAGIHAN',
                'emoji' => '⚡',
                'type' => 'expense',
                'label' => 'Tagihan',
            ],
            'gaji|salary|honor|pendapatan|freelance|project.*dapat|dibayar' => [
                'category' => 'PEMASUKAN',
                'emoji' => '💰',
                'type' => 'income',
                'label' => 'Pemasukan',
            ],
            'belanja|shopee|tokped|baju|sepatu|online|marketplace' => [
                'category' => 'BELANJA',
                'emoji' => '🛍️',
                'type' => 'expense',
                'label' => 'Belanja',
            ],
            'obat|apotek|dokter|rumah sakit|kesehatan|vitamin|rs|klinik' => [
                'category' => 'KESEHATAN',
                'emoji' => '💊',
                'type' => 'expense',
                'label' => 'Kesehatan',
            ],
        ];

        $category = null;
        foreach ($patterns as $pattern => $cat) {
            if (preg_match('/' . $pattern . '/i', $message)) {
                $category = $cat;
                break;
            }
        }

        if (!$category) {
            $category = [
                'category' => 'UMUM',
                'emoji' => '📄',
                'type' => 'expense',
                'label' => 'Lainnya',
            ];
        }

        // Extract amount
        $amount = null;

        // Pattern: "35rb", "35 ribu", "35.000"
        if (preg_match('/(\d+[\d,.]*)\s*(rb|ribu|jt|juta|k\b)?/i', $message, $m)) {
            $num = str_replace(['.', ','], '', $m[1]);
            $num = (int) $num;

            $multiplier = isset($m[2]) ? strtolower($m[2]) : '';
            if ($multiplier === 'rb' || $multiplier === 'ribu' || $multiplier === 'k') {
                $num *= 1000;
            } elseif ($multiplier === 'jt' || $multiplier === 'juta') {
                $num *= 1000000;
            }

            $amount = $num;
        }

        // Determine type from message
        $isIncome = $category['type'] === 'income' ||
                    preg_match('/gajian|dapat|terima|pendapatan|masuk|dibayar|freelance/i', $message);

        $type = $isIncome ? 'income' : 'expense';

        // Extract description
        $description = $this->extractDescription($message, $category['label']);

        if ($amount && $amount > 0) {
            $formattedAmount = 'Rp ' . number_format($amount, 0, ',', '.');

            if ($isIncome) {
                $response = $category['emoji'] . ' Mantap, ' . $description . ' tercatat! 🤑';
            } else {
                $response = $category['emoji'] . ' Oke, ' . $description . ' udah dicatat ya! 📝';
            }

            return [
                'success' => true,
                'data' => [
                    'response' => $response,
                    'description' => $description,
                    'amount' => $amount,
                    'amount_formatted' => ($isIncome ? '+' : '-') . $formattedAmount,
                    'category' => $category['emoji'] . ' ' . $category['label'],
                    'type' => $type,
                ],
            ];
        }

        // No amount found - ask for clarification
        $greetings = ['halo', 'hai', 'hi', 'hey', 'pagi', 'siang', 'sore', 'malam', 'test', 'tes'];
        $isGreeting = false;
        foreach ($greetings as $g) {
            if (str_contains($lower, $g)) {
                $isGreeting = true;
                break;
            }
        }

        if ($isGreeting) {
            return [
                'success' => true,
                'data' => [
                    'response' => 'Halo! 👋 Ceritain aja transaksi kamu ke saya. Misalnya: "Beli makan siang 35rb" atau "Gajian 5 juta"',
                    'description' => null,
                    'amount' => null,
                    'amount_formatted' => null,
                    'category' => null,
                    'type' => null,
                ],
            ];
        }

        // Financial advice queries
        $advicePatterns = [
            'hemat' => 'Coba catat pengeluaran harian dan bedakan kebutuhan vs keinginan. Budgeting tiap bulan bisa bantu banget! 💪',
            'nabung|menabung|tabungan|invest|reksadana|saham' => 'Mulai nabung dari sekarang! Sisihin minimal 10% dari pemasukan tiap bulan. Konsisten itu kuncinya! 🎯',
            'utang|hutang|kredit|cicilan' => 'Prioritasin bayar utang dengan bunga tertinggi dulu. Jangan lupa catat semua cicilan biar ke-track! 📊',
        ];

        foreach ($advicePatterns as $key => $advice) {
            if (preg_match('/' . $key . '/i', $message)) {
                return [
                    'success' => true,
                    'data' => [
                        'response' => $advice,
                        'description' => null,
                        'amount' => null,
                        'amount_formatted' => null,
                        'category' => null,
                        'type' => null,
                    ],
                ];
            }
        }

        return [
            'success' => true,
            'data' => [
                'response' => 'Bisa kamu jelasin lagi? Minimal harus ada nominalnya biar bisa saya catat. Contoh: "Makan siang 25rb" 😊',
                'description' => null,
                'amount' => null,
                'amount_formatted' => null,
                'category' => null,
                'type' => null,
            ],
        ];
    }

    private function extractDescription(string $message, string $defaultCategory): string
    {
        // Remove amount patterns
        $text = preg_replace('/\d+[\d,.]*\s*(rb|ribu|jt|juta|k\b)?/i', '', $message);
        // Remove common words
        $text = preg_replace('/\b(beli|bayar|tadi|sudah|udah|saya|gue|aku|hari\s*ini)\b/i', '', $text);
        $text = trim($text);

        if (strlen($text) < 3) {
            return $defaultCategory;
        }

        return ucfirst(trim($text));
    }
}
