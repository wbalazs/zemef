<?php

namespace App\Http\Controllers;

use App\Services\Print\Sam4sPrinterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Sam4sController extends Controller
{
    protected $printerService;

    public function __construct(Sam4sPrinterService $printerService)
    {
        $this->printerService = $printerService;
    }

    /**
     * Send a receipt transaction via QR string
     */
    public function sendReceipt(Request $request)
    {
        $request->validate([
            'qr_string' => 'required|string',
            'port' => 'sometimes|string',
            'baud_rate' => 'sometimes|integer',
        ]);

        $qrString = $request->input('qr_string');
        $port = $request->input('port', '/dev/ttyUSB0');
        $baudRate = $request->input('baud_rate', 9600);

        // Log the attempt
        Log::info('Sending QR to Sam4s', [
            'qr_string' => $qrString,
            'port' => $port,
            'baud_rate' => $baudRate
        ]);

        try {
            $this->printerService->sendQrToPrinter($qrString, $port, $baudRate);
            
            Log::info('QR sent successfully');
            return response()->json([
                'success' => true,
                'message' => 'QR string sent successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send QR to Sam4s', [
                'error' => $e->getMessage(),
                'port' => $port
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Example endpoint to create and send a simple sale receipt
     */
    public function sendSimpleSale(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:99',
            'price' => 'required|integer',
            'quantity' => 'required|integer',
            'vat_code' => 'required|in:A00,B00,C00,D00,E00',
            'comment' => 'sometimes|string|max:50',
        ]);
        
        // Build QR string according to Sam4s documentation
        $productName = $request->input('product_name');
        $price = $request->input('price');
        $quantity = $request->input('quantity');
        $vatCode = $request->input('vat_code');
        $comment = $request->input('comment', '');
        
        $totalValue = $price * $quantity;
        
        // Format: PTG|1\1|árunév||ár|mennyiség|db|teljes érték|ÁFA||megjegyzés
        $qrString = sprintf(
            'PTG|1\1|%s||%d|%d|db|%d|%s||%s',
            $productName,
            $price,
            $quantity,
            $totalValue,
            $vatCode,
            $comment
        );
        
        // Send to printer
        return $this->sendReceipt(new Request([
            'qr_string' => $qrString
        ]));
    }
}