@php
    $invoice = $getRecord();
    $lines = $invoice->lines;
@endphp

@if($lines->isEmpty())
    <span class="text-gray-400 text-sm">Aucune ligne</span>
@else
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700">
                <th class="text-left py-2 font-semibold text-gray-600 dark:text-gray-400">Description</th>
                <th class="text-right py-2 font-semibold text-gray-600 dark:text-gray-400 w-32">Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lines as $line)
                <tr class="{{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                    <td class="py-2.5">{{ $line->description }}</td>
                    <td class="py-2.5 text-right whitespace-nowrap">CHF {{ number_format($line->amount, 2, '.', '') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                <td class="py-2.5 font-bold">Total</td>
                <td class="py-2.5 text-right font-bold whitespace-nowrap">CHF {{ number_format($invoice->amount, 2, '.', '') }}</td>
            </tr>
        </tfoot>
    </table>
@endif
