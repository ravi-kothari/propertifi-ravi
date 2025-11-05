<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalculatorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CalculatorController extends Controller
{
    /**
     * Log calculator usage (optional - for analytics)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logUsage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'calculator_type' => 'required|in:roi,mortgage,rent_vs_buy',
            'input_data' => 'required|array',
            'result_data' => 'required|array',
            'session_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $log = CalculatorLog::create([
                'calculator_type' => $request->calculator_type,
                'input_data' => $request->input_data,
                'result_data' => $request->result_data,
                'user_id' => auth()->id(), // null if not authenticated
                'session_id' => $request->session_id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Calculator usage logged successfully',
                'log_id' => $log->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to log calculator usage',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get calculator statistics (for admin dashboard)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total_usage' => CalculatorLog::count(),
                'popular_calculators' => CalculatorLog::getPopularCalculators(),
                'recent_usage' => CalculatorLog::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
                    ->get(),
                'by_type' => CalculatorLog::selectRaw('calculator_type, COUNT(*) as count')
                    ->groupBy('calculator_type')
                    ->get(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate Rental ROI
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateRoi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_price' => 'required|numeric|min:0',
            'closing_costs' => 'required|numeric|min:0',
            'rehab_costs' => 'required|numeric|min:0',
            'monthly_rent' => 'required|numeric|min:0',
            'property_taxes' => 'required|numeric|min:0',
            'insurance' => 'required|numeric|min:0',
            'maintenance' => 'required|numeric|min:0',
            'other_expenses' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $total_investment = $request->purchase_price + $request->closing_costs + $request->rehab_costs;
        $annual_rental_income = $request->monthly_rent * 12;
        $annual_operating_expenses = ($request->property_taxes + $request->insurance + $request->maintenance + $request->other_expenses);

        if ($total_investment == 0) {
            return response()->json(['error' => 'Total investment cannot be zero.'], 422);
        }

        $roi = (($annual_rental_income - $annual_operating_expenses) / $total_investment) * 100;

        return response()->json(['roi' => round($roi, 2)]);
    }

    /**
     * Calculate Mortgage
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateMortgage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loan_amount' => 'required|numeric|min:0',
            'annual_interest_rate' => 'required|numeric|min:0',
            'loan_term_in_years' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $monthly_interest_rate = ($request->annual_interest_rate / 100) / 12;
        $number_of_months = $request->loan_term_in_years * 12;

        if ($monthly_interest_rate == 0) {
            $monthly_payment = $request->loan_amount / $number_of_months;
        } else {
            $monthly_payment = $request->loan_amount * ($monthly_interest_rate * pow(1 + $monthly_interest_rate, $number_of_months)) / (pow(1 + $monthly_interest_rate, $number_of_months) - 1);
        }

        return response()->json(['monthly_payment' => round($monthly_payment, 2)]);
    }

    /**
     * Calculate Rent vs. Buy
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateRentVsBuy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'monthly_rent' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'down_payment' => 'required|numeric|min:0',
            'annual_interest_rate' => 'required|numeric|min:0',
            'loan_term_in_years' => 'required|integer|min:1',
            'property_taxes' => 'required|numeric|min:0',
            'home_insurance' => 'required|numeric|min:0',
            'maintenance_costs' => 'required|numeric|min:0',
            'appreciation_rate' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $loan_amount = $request->purchase_price - $request->down_payment;
        $monthly_interest_rate = ($request->annual_interest_rate / 100) / 12;
        $number_of_months = $request->loan_term_in_years * 12;

        if ($monthly_interest_rate == 0) {
            $monthly_mortgage_payment = $loan_amount / $number_of_months;
        } else {
            $monthly_mortgage_payment = $loan_amount * ($monthly_interest_rate * pow(1 + $monthly_interest_rate, $number_of_months)) / (pow(1 + $monthly_interest_rate, $number_of_months) - 1);
        }

        $total_rent_cost = $request->monthly_rent * $number_of_months;

        $total_buying_cost = ($monthly_mortgage_payment * $number_of_months) + ($request->property_taxes * $request->loan_term_in_years) + ($request->home_insurance * $request->loan_term_in_years) + ($request->maintenance_costs * $request->loan_term_in_years);

        $future_home_value = $request->purchase_price * pow(1 + ($request->appreciation_rate / 100), $request->loan_term_in_years);

        $net_buying_cost = $total_buying_cost - ($future_home_value - $loan_amount);

        return response()->json([
            'total_rent_cost' => round($total_rent_cost, 2),
            'net_buying_cost' => round($net_buying_cost, 2),
        ]);
    }
}
