{{--  <link href="{{ asset('bootstrap.min.css')}}" rel="stylesheet">
<link href="{{ asset('pdf.css')}}" rel="stylesheet">  --}}
<table autosize="1" style="table-layout: fixed; border-spacing:1em; ">
@foreach ($barcodes->chunk(5) as $chunk)
    @if(count($chunk) < 5)
        <tr style="text-align:center">
            @foreach($chunk as $barcode)
                <td style="width:20%; padding-top: 3px;padding-bottom: 3px;text-align:center;border: 1px solid #3e3c3c; border-radius: 10px;">
                        <h3>Diganta</h3>
                        <h4>{{ $barcode->name }}</h4>
                        <barcode code="{{ $barcode->barcode }}" type="C128A"/>
                        <h4>{{ $barcode->barcode }}</h4>
                        <h2>Price : {{ $barcode->price }}/-</h2>
                </td>
            @endforeach
            <?php $v = 5 - count($chunk); ?>
                <td colspan="{$v}"></td>
        </tr>
    @else
        <tr style="text-align:center">
            @foreach($chunk as $barcode)
                <td style="padding-top: 3px;padding-bottom: 3px;text-align:center;border: 1px solid #3e3c3c; border-radius: 10px;">
                        <h3>Diganta</h3>
                        <h4>{{ $barcode->name }}</h4>
                        <barcode code="{{ $barcode->barcode }}" type="C128A"/>
                        <h4>{{ $barcode->barcode }}</h4>
                        <h2>Price : {{ $barcode->price }}/-</h2>
                </td>
            @endforeach
        </tr>
    @endif
@endforeach
</table>
