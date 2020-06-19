<h5 class="font-weight-bold pb-2 border-bottom mb-4">Factures</h5>

<div class="d-flex px-lg-4 mb-5">
    <table class="table table-hover">
        <thead>
        <tr>
            <th scope="col">Date</th>
            <th scope="col">Montant</th>
            <th scope="col"></th>
        </tr>
        </thead>

        <tbody>
        @foreach (Auth::user()->invoices() as $invoice)
            <tr>
                <td>{{ $invoice->date()->format('j F Y') }}</td>
                <td>{{ $invoice->total() }}</td>
                <td style="width: 1px; white-space: nowrap;">
                    <a href="{{ route('subscription.invoices.show', $invoice->id) }}" target="_blank">
                        <svg class="bi bi-file-earmark-spreadsheet" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M13 9H3V8h10v1zm0 3H3v-1h10v1z"/>
                            <path fill-rule="evenodd" d="M5 14V9h1v5H5zm4 0V9h1v5H9z"/>
                            <path d="M4 1h5v1H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V6h1v7a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2z"/>
                            <path d="M9 4.5V1l5 5h-3.5A1.5 1.5 0 0 1 9 4.5z"/>
                        </svg>
                        Télécharger
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
