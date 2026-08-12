<div class="table-responsive py-4" id="items-table-ajax">
    <table id="datatables2"  data-name="stock_table_ajax_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
        <thead>
            <tr>
                <th>Megnevezés</th>
                <th>Leírás</th>
                <th>Raktár</th>
                <th>Mennyiség</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Megnevezés</th>
                <th>Leírás</th>
                <th>Raktár</th>
                <th>Mennyiség</th>
            </tr>
        </tfoot>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product['product_name'] }}</td>
                    <td>{{ $product['short_description'] }}</td>
                    <td>{{ $product['warehouse_name'] }}</td>
                    <td>{{ $product['stock'] }} db</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
                  