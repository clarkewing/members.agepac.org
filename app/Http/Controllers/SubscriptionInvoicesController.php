<?php

namespace App\Http\Controllers;

use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;

class SubscriptionInvoicesController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $invoiceId
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $invoiceId)
    {
        $invoice = $request->user()->findInvoiceOrFail($invoiceId);

        $pdf = SnappyPdf::loadHTML($invoice->view([]));

        return $pdf->setPaper(config('cashier.paper'))
            ->inline('Facture AGEPAC ' . $invoice->date()->format('Y-m-d') . '.pdf');
    }
}
