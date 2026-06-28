<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class DashboardController extends Controller
{
    /**
     * Show the Main Home Dashboard Workspace.
     */
    public function index(){
        $userId = Auth::id();

        // 1. Calculate Monthly Accumulations
        $thisMonthSum = Expense::where('user_id', $userId)
            ->whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->sum('amount');

        $lastMonthSum = Expense::where('user_id', $userId)
            ->whereMonth('date', Carbon::now()->subMonth()->month)
            ->whereYear('date', Carbon::now()->subMonth()->year)
            ->sum('amount');

        // 2. Count Records and Calculate Statistical Averages
        $expensesCount = Expense::where('user_id', $userId)->count();
        $averageExpense = Expense::where('user_id', $userId)->avg('amount') ?? 0.00;

        // 3. Pull top 3 most recent entries loading their table relationships
        $recentExpenses = Expense::with(['category', 'paymentMethod'])
            ->where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->take(3)
            ->get();

        return view('home', compact(
            'thisMonthSum', 
            'lastMonthSum', 
            'expensesCount', 
            'averageExpense', 
            'recentExpenses'
        ));
    }

    public function scan()
    {
        // Pull global seeded options to populate selection forms
        $categories = Category::whereNull('user_id')->get();
        $paymentMethods = PaymentMethod::whereNull('user_id')->get();

        return view('scan', compact('categories', 'paymentMethods'));
    }
    /**
     * Show the Scan Receipt Form with dynamic Dropdown Selectors.
     */
    public function aiScan(Request $request)
    {
        $request->validate([
            'receipt' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        try {
            $image = $request->file('receipt');
            $imagePath = $image->getRealPath();
            $imageSize = filesize($imagePath);
            
            // Groq has image size limits
            if ($imageSize > 20 * 1024 * 1024) { // 20MB limit
                return response()->json([
                    'success' => false,
                    'message' => 'Image is too large. Maximum size is 20MB.'
                ], 422);
            }

            $base64Image = base64_encode(file_get_contents($imagePath));
            $mimeType = $image->getMimeType();

            // Ensure mimeType is in correct format
            if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unsupported image format. Use JPEG, PNG, GIF, or WebP.'
                ], 422);
            }

            // 1. Precise prompt tailored for explicit structural formatting mapping
            $prompt = <<<PROMPT
You are a receipt scanner that extracts key data fields from receipt images.
Analyze the receipt image and extract the following information:
- Store/Location name
- Transaction date (in YYYY-MM-DD format)
- Total amount spent
- Category (e.g., Food & Dining, Transport, Shopping, etc.)
- Payment method (Cash, Card, Digital, etc.)

IMPORTANT: You MUST respond ONLY with valid JSON in this exact format:
{
    "location": "Store Name",
    "date": "YYYY-MM-DD",
    "amount": 0.00,
    "category": "Food & Dining",
    "payment_method": "Cash"
}

Do not include any text before or after the JSON.
PROMPT;

            // 2. Get Groq API key
            $apiKey = config('services.groq.key', env('GROQ_API_KEY'));
            
            if (!$apiKey) {
                Log::error('GROQ_API_KEY is not configured');
                return response()->json([
                    'success' => false,
                    'message' => 'API key not configured. Please check GROQ_API_KEY in .env'
                ], 500);
            }

            // 3. Invoke Groq API with image (OpenAI-compatible format)
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => config('services.groq.model', 'meta-llama/llama-4-scout-17b-16e-instruct'),
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => $prompt
                                ],
                                [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => 'data:' . $mimeType . ';base64,' . $base64Image
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'temperature' => 0.1,
                    'max_tokens' => 500
                ]);

            if ($response->status() === 429) {
                return response()->json([
                    'success' => false,
                    'message' => 'Groq API rate limit exceeded. Try again later.'
                ], 429);
            }

            if (!$response->successful()) {
                $errorBody = $response->json();
                $errorMessage = 'Groq API request failed.';
                $statusCode = $response->status();
                
                // Parse specific error messages from Groq
                if ($statusCode === 401 || $statusCode === 403) {
                    $errorMessage = 'Invalid or expired API key. Check GROQ_API_KEY in .env';
                } elseif ($statusCode === 400) {
                    $errorMessage = 'Bad request to Groq API. Check request format.';
                    if (isset($errorBody['error']['message'])) {
                        $errorMessage .= ' Details: ' . $errorBody['error']['message'];
                    }
                } elseif ($statusCode === 429) {
                    $errorMessage = 'Groq API rate limit exceeded. Try again later.';
                } elseif ($statusCode === 500) {
                    $errorMessage = 'Groq API server error. Try again later.';
                } else {
                    if (isset($errorBody['error']['message'])) {
                        $errorMessage = 'Groq API Error: ' . $errorBody['error']['message'];
                    }
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'status' => $statusCode,
                    'error_details' => $errorBody['error'] ?? null,
                    'debug_info' => config('app.debug') ? [
                        'response' => $errorBody,
                        'api_key_preview' => 'Key starts with: ' . substr($apiKey, 0, 5) . '...'
                    ] : null
                ], $statusCode);
            }

            // 4. Safely extract message content from Groq response (OpenAI-compatible format)
            $content = $response->json('choices.0.message.content');

            Log::debug('Groq API Response', [
                'has_content' => !empty($content),
                'full_response' => $response->json()
            ]);

            if (!$content) {
                return response()->json([
                    'success' => false,
                    'message' => 'No content returned from Groq API.',
                    'response' => $response->json()
                ], 500);
            }

            // 5. Decode JSON string directly
            $receiptData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Decode Error', [
                    'json_error' => json_last_error_msg(),
                    'raw_response' => $content
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'AI returned malformed or syntactically invalid JSON.',
                    'raw_response' => $content
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $receiptData
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Network Error - Cannot reach Groq API', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Network error: Cannot reach Groq API. Check your internet connection.',
                'error' => $e->getMessage()
            ], 500);

        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('HTTP Request Error to Groq API', [
                'message' => $e->getMessage(),
                'response_status' => $e->response?->status(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'HTTP request error: ' . $e->getMessage(),
                'status' => $e->response?->status()
            ], 500);

        } catch (\Exception $e) {
            Log::error('Receipt AI Scan Error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'type' => class_basename($e),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error during image processing.',
                'error' => $e->getMessage(),
                'debug' => config('app.debug') ? [
                    'exception' => class_basename($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }
    }
    /**
     * Show the Analytics Charts view screen module panel.
     */
    public function analytics()
    {
        $userId = Auth::id();
    
        // 1. Core aggregates
        $totalSpent = Expense::where('user_id', $userId)->sum('amount');
        $averageExpense = Expense::where('user_id', $userId)->avg('amount') ?? 0.00;
        $topExpense = Expense::where('user_id', $userId)->max('amount') ?? 0.00;
    
        // 2. FIXED: Aggregate Grouped By Category Name
        $categoryCollection = Expense::where('expenses.user_id', $userId)
            ->join('categories', 'expenses.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(expenses.amount) as total'))
            ->groupBy('categories.name')
            ->pluck('total', 'name')
            ->toArray();
    
        // 3. FIXED: 6-Month Trend correctly grouped by Year and Month strings together
        $trendsRaw = Expense::where('expenses.user_id', $userId)
            ->where('date', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->select(
                DB::raw("DATE_FORMAT(date, '%b %Y') as month_label"), 
                DB::raw('SUM(amount) as total'),
                DB::raw("DATE_FORMAT(date, '%Y-%m') as month_order")
            )
            ->groupBy('month_label', 'month_order')
            ->orderBy('month_order', 'asc')
            ->pluck('total', 'month_label')
            ->toArray();
    
        return view('analytics', [
            'totalSpent'     => $totalSpent,
            'averageExpense' => $averageExpense,
            'topExpense'     => $topExpense,
            'categoryData'   => $categoryCollection,
            'trendData'      => $trendsRaw
        ]);
    }
   
    public function profile()
    {
        $userId = Auth::id();
        // Fetch both global templates (null) AND rows belonging explicitly to this authenticated user account
        $categories = Category::whereNull('user_id')
                              ->orWhere('user_id', $userId)
                              ->orderBy('id', 'asc')
                              ->get();

        $paymentMethods = PaymentMethod::whereNull('user_id')
                                       ->orWhere('user_id', $userId)
                                       ->orderBy('id', 'asc')
                                       ->get();

        return view('profile', compact('categories', 'paymentMethods'));
    }

    /**
     * DEBUG: Test Groq API Connection
     */
    public function testGroqApi()
    {
        $apiKey = config('services.groq.key', env('GROQ_API_KEY'));
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'error' => 'GROQ_API_KEY not found in config or .env'
            ]);
        }

        Log::debug('TEST: Simple text request to Groq API', [
            'api_key_exists' => !empty($apiKey),
            'api_key_length' => strlen($apiKey)
        ]);

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => config('services.groq.model', 'meta-llama/llama-4-scout-17b-16e-instruct'),
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => 'Respond with: {"message": "Test successful"}'
                        ]
                    ],
                    'temperature' => 0.1,
                    'max_tokens' => 100
                ]);

            Log::debug('API Response', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return response()->json([
                'test' => 'groq_api_simple_text',
                'status' => $response->status(),
                'success' => $response->successful(),
                'response' => $response->json()
            ]);

        } catch (\Exception $e) {
            Log::error('API Test Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'test' => 'groq_api_simple_text',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function updateInfo(Request $request)
    {
        $user = User::find(Auth::id());
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile identity updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = User::find(Auth::id());

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Account security key changed successfully.');
    }
}
