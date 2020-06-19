<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Facture</title>

    <link rel="stylesheet" href="{{ public_path('css/invoice.css') }}" media="all"/>
</head>

<body>
<header class="clearfix">
    <div id="logo">
        <img src="{{ public_path('images/logo.png') }}" alt="AGEPAC">
    </div>
    <div id="company">
        <h2 class="name">AGEPAC</h2>
        <div>7 avenue Edouard Belin, CS 54005, 31055 Toulouse Cedex 4</div>
        <div><a href="https://agepac.org">https://agepac.org</a></div>
    </div>
</header>
<main>
    <div id="details" class="clearfix">
        <div id="client">
            <div class="to">Destinataire :</div>
            <h2 class="name">{{ $owner->asStripeCustomer()->name ?? $owner->name }}</h2>
            <div class="address">{{-- Customer address --}}</div>
            <div class="email">
                <a href="{{ $owner->stripeEmail() ?? $owner->email }}">
                    {{ $owner->stripeEmail() ?? $owner->email }}
                </a>
            </div>
        </div>
        <div id="invoice">
            <h1>Facture {{ $id ?? $invoice->number }}</h1>
            <div class="date">Date de facturation: {{ $invoice->date()->format('j F Y') }}</div>
        </div>
    </div>
    <table border="0" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th class="desc">Description</th>
            <th class="qty">Quantité</th>
            @if ($invoice->hasTax())
                <th class="tax">Taxes</th>
            @endif
            <th class="amount">Montant</th>
        </tr>
        </thead>

        <tbody>
        @foreach ($invoice->invoiceItems() as $item)
            <tr>
                <td class="desc" colspan="2">
                    <h3>{{ $item->description }}</h3>
                    Creating a recognizable design solution based on the company's
                    existing visual identity
                </td>
                @if ($invoice->hasTax())
                    <td class="tax">
                        @if ($inclusiveTaxPercentage = $item->inclusiveTaxPercentage())
                            {{ $inclusiveTaxPercentage }}% compris
                        @endif

                        @if ($item->hasBothInclusiveAndExclusiveTax())
                            +
                        @endif

                        @if ($exclusiveTaxPercentage = $item->exclusiveTaxPercentage())
                            {{ $exclusiveTaxPercentage }}%
                        @endif
                    </td>
                @endif
                <td class="amount">{{ $item->total() }}</td>
            </tr>
        @endforeach

        @foreach ($invoice->subscriptions() as $subscription)
            <tr>
                <td class="desc">
                    <h3>Cotisation</h3>
                    {{ $subscription->startDateAsCarbon()->format('j F Y') }} -
                    {{ $subscription->endDateAsCarbon()->format('j F Y') }}
                </td>
                <td class="qty">{{ $subscription->quantity }}</td>
                @if ($invoice->hasTax())
                    <td class="tax">
                        @if ($inclusiveTaxPercentage = $subscription->inclusiveTaxPercentage())
                            {{ $inclusiveTaxPercentage }}% compris
                        @endif

                        @if ($subscription->hasBothInclusiveAndExclusiveTax())
                            +
                        @endif

                        @if ($exclusiveTaxPercentage = $subscription->exclusiveTaxPercentage())
                            {{ $exclusiveTaxPercentage }}%
                        @endif
                    </td>
                @endif
                <td class="amount">{{ $subscription->total() }}</td>
            </tr>
        @endforeach
        </tbody>

        <tfoot>
        <!-- Display The Subtotal -->
        @if ($invoice->hasDiscount() || $invoice->hasTax() || $invoice->hasStartingBalance())
            <tr>
                <td></td>
                <td colspan="{{ $invoice->hasTax() ? 2 : 1 }}">SOUS-TOTAL</td>
                <td>{{ $invoice->subtotal() }}</td>
            </tr>
        @endif

        <!-- Display The Discount -->
        @if ($invoice->hasDiscount())
            <tr>
                <td></td>
                <td colspan="{{ $invoice->hasTax() ? 2 : 1 }}">
                    @if ($invoice->discountIsPercentage())
                        {{ $invoice->coupon() }} (Réduction {{ $invoice->percentOff() }}%)
                    @else
                        {{ $invoice->coupon() }} (Réduction {{ $invoice->amountOff() }})
                    @endif
                </td>
                <td>-{{ $invoice->discount() }}</td>
            </tr>
        @endif

        <!-- Display The Taxes -->
        @if ($invoice->hasTax())
            @unless ($invoice->isNotTaxExempt())
                <tr>
                    <td></td>
                    <td colspan="{{ $invoice->hasTax() ? 3 : 2 }}">
                        @if ($invoice->isTaxExempt())
                            Exonéré de taxes
                        @else
                            Taxes à payer sur la base de l'autoliquidation
                        @endif
                    </td>
                </tr>
            @else
                @foreach ($invoice->taxes() as $tax)
                    <tr>
                        <td></td>
                        <td colspan="{{ $invoice->hasTax() ? 2 : 1 }}">
                            {{ $tax->display_name }} {{ $tax->jurisdiction ? ' - '.$tax->jurisdiction : '' }}
                            ({{ $tax->percentage }}%{{ $tax->isInclusive() ? ' compris' : '' }})
                        </td>
                        <td>{{ $tax->amount() }}</td>
                    </tr>
                @endforeach
            @endunless
        @endif

        <!-- Starting Balance -->
        @if ($invoice->hasStartingBalance())
            <tr>
                <td></td>
                <td colspan="{{ $invoice->hasTax() ? 2 : 1 }}">CRÉDIT RESTANT</td>
                <td>{{ $invoice->startingBalance() }}</td>
            </tr>
        @endif

        <!-- Display The Final Total -->
        <tr>
            <td></td>
            <td colspan="{{ $invoice->hasTax() ? 2 : 1 }}">GRAND TOTAL</td>
            <td>{{ $invoice->total() }}</td>
        </tr>
        </tfoot>
    </table>
    <div id="thanks">Merci !</div>
    <div id="notices">
        <div>NOTE:</div>
        <div class="notice">Cette facture ne constitue pas un reçu fiscal.</div>
    </div>
</main>
<footer>
    &copy; Association Générale des Élèves Pilotes de l'Aviation Civile<br>
    Facture créée sur ordinateur et  valide sans signature et sceau.
</footer>
</body>
</html>
