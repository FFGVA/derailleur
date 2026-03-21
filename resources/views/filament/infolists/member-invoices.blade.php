@php
    $member = $getRecord();
    $invoices = $member->invoices()
        ->whereNull('deleted_at')
        ->orderByDesc('updated_at')
        ->get();
@endphp

@if($invoices->isEmpty())
    <span class="text-gray-400 text-sm">Aucune facture</span>
@else
    <table class="w-full text-sm">
        @foreach($invoices as $invoice)
            <tr class="{{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                <td class="py-1.5 pr-2">
                    <a href="{{ \App\Filament\Resources\InvoiceResource::getUrl('view', ['record' => $invoice]) }}"
                       class="text-primary-600 hover:underline">
                        {{ $invoice->invoice_number }}
                    </a>
                </td>
                <td class="py-1.5 pr-2 text-xs text-gray-500">
                    {{ $invoice->type->getLabel() }}
                </td>
                <td class="py-1.5 text-right whitespace-nowrap" style="padding-right: 2rem;">
                    CHF {{ number_format($invoice->amount, 2, '.', '') }}
                </td>
                <td class="py-1.5 whitespace-nowrap">
                    <x-filament::badge :color="$invoice->statuscode->getColor()" size="xs">
                        {{ $invoice->statuscode->getLabel() }}
                    </x-filament::badge>
                </td>
            </tr>
        @endforeach
    </table>
@endif
