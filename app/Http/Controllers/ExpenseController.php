<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Category;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a responsive data listing table of all logged items.
     */
    public function index()
    {
        $userId = Auth::id();

        $expenses = Expense::with(['category', 'paymentMethod'])
                            ->where('user_id', $userId)
                            ->orderBy('date', 'desc')
                            ->get();

        $categories = Category::whereNull('user_id')
                            ->orWhere('user_id', $userId)
                            ->orderBy('name', 'asc')
                            ->get();

        $paymentMethods = PaymentMethod::whereNull('user_id')
                            ->orWhere('user_id', $userId)
                            ->orderBy('name', 'asc')
                            ->get();

        return view('expenses', compact('expenses', 'categories', 'paymentMethods'));
    }

    /**
     * Process and save a newly scanned form record item log.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'location'          => 'required|string|max:255',
            'date'              => 'required|date',
            'amount'            => 'required|numeric|min:0.01',
            'category_id'       => 'required|exists:categories,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        $expense = new Expense();
        $expense->user_id           = Auth::id(); 
        $expense->location          = $validatedData['location'];
        $expense->date              = $validatedData['date'];
        $expense->amount            = $validatedData['amount'];
        $expense->category_id       = $validatedData['category_id'];
        $expense->payment_method_id = $validatedData['payment_method_id'];
        $expense->save();

        return redirect()->route('expenses.index')->with('success', 'Expense entry logged successfully!');
    }

    // Dynamic Custom Categories CRUD Action Handlers
    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:50', 'icon' => 'nullable|string|max:50', 'color_theme' => 'required|string|max:20']);
        Category::create(['user_id' => Auth::id(), 'name' => $request->name, 'icon' => $request->icon ?? 'bi-tag', 'color_theme' => $request->color_theme]);
        return back()->with('success', 'Custom category registered.');
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:50', 'icon' => 'nullable|string|max:50', 'color_theme' => 'required|string|max:20']);
        $category = Category::where('user_id', Auth::id())->findOrFail($id);
        $category->update($request->only('name', 'icon', 'color_theme'));
        return back()->with('success', 'Custom category updated successfully!');
    }

    public function destroyCategory($id)
    {
        Category::where('user_id', Auth::id())->findOrFail($id)->delete();
        return back()->with('success', 'Custom category entry cleared.');
    }

    // Dynamic Custom Payment Methods CRUD Action Handlers
    public function storePaymentMethod(Request $request)
    {
        $request->validate(['name' => 'required|string|max:50']);
        PaymentMethod::create(['user_id' => Auth::id(), 'name' => $request->name]);
        return back()->with('success', 'Custom payment channel registered.');
    }

    public function updatePaymentMethod(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:50']);
        PaymentMethod::where('user_id', Auth::id())->findOrFail($id)->update($request->only('name'));
        return back()->with('success', 'Payment method updated successfully!');
    }

    public function destroyPaymentMethod($id)
    {
        PaymentMethod::where('user_id', Auth::id())->findOrFail($id)->delete();
        return back()->with('success', 'Custom payment entry cleared.');
    }
}
