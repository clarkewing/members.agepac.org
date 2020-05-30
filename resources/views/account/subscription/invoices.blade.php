<h5 class="font-weight-bold pb-2 border-bottom mb-4">Factures</h5>

<div class="d-flex px-lg-4 mb-5">
    <table class="table table-hover">
        <thead>
        <tr>
            <th scope="col">Date</th>
            <th scope="col">Montant</th>
        </tr>
        </thead>

        <tbody>
        @foreach (Auth::user()->invoices() as $invoice)
            <tr>
                <td>{{ $invoice->date()->format('j F Y') }}</td>
                <td>{{ $invoice->total() }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
