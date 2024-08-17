@foreach($result as $product)
    <table>
        <thead>
            <tr>
                <th>
                    Customer Email
                </th>
                <th>
                    Product Name
                </th>
                @for($month = 0 ; $month < 12 ; $month++)
                    <th>
                        {{ $now->clone()->addMonth($month)->endOfMonth()->format('Y-m-d'); }}
                    </th>
                @endfor
                <th>
                    Total
                </th>
            </tr>
        </thead>

        <tbody>
            @foreach($product->subscriptions->data as $subscription)
                <tr>
                    <td>
                        {{ $subscription->customerEmail }}
                    </td>
                    <td>
                        {{ $product->productName }}
                    </td>
                    @foreach($subscription->prices as $price)
                        <td>
                            {{ $price }}
                        </td>
                    @endforeach
                    <td>
                        {{ $subscription->totalPrice }}
                    </td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td>Totals</td>
                <td></td>
                @foreach($product->subscriptions->monthsTotals as $price)
                    <td>
                        {{ $price }}
                    </td>
                @endforeach
                <td>
                     {{ $product->subscriptions->yearTotals }}
                </td>
            </tr>
        </tfoot>
    </table>
    <br>
@endforeach