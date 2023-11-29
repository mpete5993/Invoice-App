<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Counter;
use App\Models\Product;
use App\Models\InvoiceItem;


class InvoiceController extends Controller
{
    public function get_all_invoice()
    {
        // code...
        $invoices = Invoice::with('customer')->orderBy('id', 'DESC')->get();

        return response()->json([
            'invoices' => $invoices
        ], 200);
    }

    public function search_invoice(Request $request)
    {
        // code...
        $search = $request->get('s');

        if ($search != null ) {
            // code...
            $invoices = Invoice::with('customer')
            ->where('id', 'like', "%$search%")
            ->get();

            return response()->json([
                'invoices' => $invoices
            ], 200);
        }else {
            return $this->get_all_invoice();
        }
    }

    public function create_invoice(Request $request) {

        $counter = Counter::where('key', 'invoice')->first();

        $random = Counter::where('key', 'invoice')->first();

        $invoice = Invoice::orderBy('id', 'DESC')->first();

        if($invoice){
            $invoice = $invoice->id+1;
            $counters = $counter->value + $invoice;
        }else {
            $counters = $counter->value;
        }

        $formData = [
            'number' => $counter->prefix.$counter,
            'customer' => null,
            'customer_id' => null,
            'date' => date('Y-m-d'),
            'due_date' => null,
            'reference' => null,
            'discount' => 0,
            'term_and_condition' => "deafult Terms and conditions",
            'items' => [
                [
                    'product_id' => null,
                    'product' => null,
                    'unit_price' => 0,
                    'quantity' => 1
                ]
            ]
        ];

        return response()->json($formData);
    }
    
    public function addInvoice(Request $request)
    {
        $invoiceItems = $request->input("invoice_item");

        $invoiceData['sub_total'] = $request->input("subtotal");
        $invoiceData['total'] = $request->input("total");
        $invoiceData['customer_id'] = $request->input("customer_id");
        $invoiceData['number'] = $request->input("number");
        $invoiceData['date'] = $request->input("date");
        $invoiceData['due_date'] = $request->input("due_date");
        $invoiceData['discount'] = $request->input("discount");
        $invoiceData['reference'] = $request->input("reference");
        $invoiceData['term_and_condition'] = $request->input("term_and_condition");
        $invoice = Invoice::create($invoiceData);

        foreach (json_decode($invoiceItems) as $item) 
        {
            $itemdata['product_id'] = $item->id;
            $itemdata['invoice_id'] = $invoice->id;
            $itemdata['quantity'] = $item->quality;
            $itemdata['unit_price'] = $item->unit_price;

            InvoiceItem::create($itemdata);
        }
    }


    public function showInvoice($id)
    {
        $invoice = Invoice::with(['customer', 'invoice_items.product'])->find($id);

        return response()->json([
            'invoice' => $invoice
        ], 200);
    }
}
