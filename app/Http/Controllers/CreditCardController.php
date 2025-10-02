<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserCreditCard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreditCardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $creditCards = Auth::user()->creditCards()
            ->active()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('creditCard.index', compact('creditCards'));
    }

    public function create()
    {
        return view('creditCard.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'card_alias' => 'required|string|max:50',
            'card_number' => 'required|string|min:13|max:19',
            'card_holder_name' => 'required|string|max:100',
            'expiry_month' => 'required|integer|between:1,12',
            'expiry_year' => 'required|integer|min:' . date('Y'),
            'card_brand' => 'required|in:VISA,MASTERCARD,JCB,UNIONPAY',
            'is_default' => 'boolean'
        ]);

        $userId = Auth::id();
        
        $maskedCardNumber = $this->maskCardNumber($request->card_number);

        if ($request->is_default) {
            UserCreditCard::setUserDefaultCard($userId, null);
        }

        $creditCard = UserCreditCard::create([
            'user_id' => $userId,
            'card_alias' => $request->card_alias,
            'masked_card_number' => $maskedCardNumber,
            'card_holder_name' => $request->card_holder_name,
            'expiry_month' => $request->expiry_month,
            'expiry_year' => $request->expiry_year,
            'card_brand' => $request->card_brand,
            'is_default' => $request->is_default ?? false,
        ]);

        return redirect()->route('creditCard.index')->with('success', '信用卡已成功新增！');
    }

    public function edit($id)
    {
        $creditCard = UserCreditCard::forUser(Auth::id())->findOrFail($id);
        return view('creditCard.edit', compact('creditCard'));
    }

    public function update(Request $request, $id)
    {
        $creditCard = UserCreditCard::forUser(Auth::id())->findOrFail($id);

        $this->validate($request, [
            'card_alias' => 'required|string|max:50',
            'card_holder_name' => 'required|string|max:100',
            'expiry_month' => 'required|integer|between:1,12',
            'expiry_year' => 'required|integer|min:' . date('Y'),
            'is_default' => 'boolean'
        ]);

        if ($request->is_default && !$creditCard->is_default) {
            UserCreditCard::setUserDefaultCard(Auth::id(), $id);
        }

        $creditCard->update([
            'card_alias' => $request->card_alias,
            'card_holder_name' => $request->card_holder_name,
            'expiry_month' => $request->expiry_month,
            'expiry_year' => $request->expiry_year,
            'is_default' => $request->is_default ?? false,
        ]);

        return redirect()->route('creditCard.index')->with('success', '信用卡資訊已更新！');
    }

    public function destroy($id)
    {
        $creditCard = UserCreditCard::forUser(Auth::id())->findOrFail($id);
        
        $wasDefault = $creditCard->is_default;
        $creditCard->update(['is_active' => false]);

        if ($wasDefault) {
            $nextCard = UserCreditCard::forUser(Auth::id())
                ->active()
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($nextCard) {
                $nextCard->update(['is_default' => true]);
            }
        }

        return redirect()->route('creditCard.index')->with('success', '信用卡已刪除！');
    }

    public function setDefault($id)
    {
        $creditCard = UserCreditCard::forUser(Auth::id())->active()->findOrFail($id);
        
        UserCreditCard::setUserDefaultCard(Auth::id(), $id);

        return redirect()->route('creditCard.index')->with('success', '預設信用卡已設定！');
    }

    private function maskCardNumber($cardNumber)
    {
        $cleaned = preg_replace('/\D/', '', $cardNumber);
        $length = strlen($cleaned);
        
        if ($length < 13) {
            return $cleaned;
        }

        $firstFour = substr($cleaned, 0, 4);
        $lastFour = substr($cleaned, -4);
        $middle = str_repeat('*', $length - 8);

        return $firstFour . $middle . $lastFour;
    }
}