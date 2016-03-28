@extends('app')

@section('content')
    <div class="callout banner-section">
        <div class="row column text-center banner-inner">
            <p class="banner-text">Search through <span class="amount">{{ number_format($totalContractAmount) }} </span>
                Leu
                worth of contracts</p>

            <form class="search-form">
                <input type="search" placeholder="Type a contract name ...">
            </form>
        </div>
    </div>

    <div class="row chart-section-wrap" >
        {{-- ----- div for each two chart starts ------- --}}

        <div class="inner-wrap clearfix" data-equalizer="equal-chart-wrap">
            <div data-equalizer="equal-header" >

                <div class="medium-6 small-12 columns each-chart-section">
                    <div class="section-header clearfix" data-equalizer-watch="equal-header">
                        <ul class="breadcrumbs">
                            <li><span href="#" class="indicator tender">Tender</span></li>
                            <li><span href="#" class="indicator contracts">Contracts</span></li>
                        </ul>
                    </div>
                    <div class="chart-wrap" data-equalizer-watch="equal-chart-wrap">
                        <div id="linechart-homepage"></div>
                    </div>
                </div>

                <div class="medium-6 small-12 columns each-chart-section">
                    <div class="section-header clearfix" data-equalizer-watch="equal-header">
                        <h3>Top 5 procuring agencies</h3>
                        <div class="top-bar-right right-section">
                            <form>
                                <label>
                                    <select>
                                        <option value="husker">Based on value</option>
                                        <option value="starbuck">Based on count</option>
                                    </select>
                                </label>
                            </form>
                        </div>
                    </div>
                    <div class="chart-wrap" data-equalizer-watch="equal-chart-wrap">
                        <div id="barChart-procuring"></div>
                    </div>
                </div>

            </div>
        </div>
        {{-- ----- div for each two chart ends ------- --}}

        <div class="inner-wrap clearfix" data-equalizer="equal-chart-wrap">
            <div data-equalizer="equal-header" >

                <div class="medium-6 small-12 columns each-chart-section">
                    <div class="section-header clearfix" data-equalizer-watch="equal-header">
                        <h3>Top 5 contractors</h3>
                    </div>

                    <div class="chart-wrap" data-equalizer-watch="equal-chart-wrap">
                        <div id="barChart-contractors"></div>
                    </div>
                </div>

                <div class="medium-6 small-12 columns each-chart-section">
                    <div class="section-header clearfix" data-equalizer-watch="equal-header">
                        <h3>Top 5 goods & services procured</h3>
                    </div>

                    <div class="chart-wrap" data-equalizer-watch="equal-chart-wrap">
                        <div id="barChart-goods"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row table-wrapper">
        <table class="responsive hover custom-table">
            <tbody>
            <tr>
                <th>Contract Number</th>
                <th>Goods</th>
                <th>Contract Date</th>
                <th>Final Date</th>
                <th>Amount</th>
            </tr>

            @forelse($contractsList as $key => $contract)
                @if($key < 10)
                    <tr>
                        <td>{{ $contract['contractNumber'] }}</td>
                        <td>{{ $contract['goods']['mdValue'] }}</td>
                        <td class="dt">{{ $contract['contractDate'] }}</td>
                        <td class="dt">{{ $contract['finalDate'] }}</td>
                        <td>{{ number_format($contract['amount']) }}</td>
                    </tr>
                @endif
            @empty
            @endforelse


            </tbody>
        </table>
    </div>
@endsection

@section('script')
    <script src="{{url('js/vendorChart.min.js')}}"></script>
    <script src="{{url('js/customChart.min.js')}}"></script>
    <script>
        var trends = '{!! $trends  !!}';
        var procuringAgencies = '{!! $procuringAgency  !!}';
        var contractors = '{!! $contractors  !!}';
        var goodsAndServices = '{!! $goodsAndServices  !!}';

        createLineChart(JSON.parse(trends));
        createBarChartProcuring(JSON.parse(procuringAgencies), "barChart-procuring");
        createBarChartProcuring(JSON.parse(contractors), "barChart-contractors");
        createBarChartProcuring(JSON.parse(goodsAndServices), "barChart-goods");

        $('.dt').each(function () {
            var dt = $(this).text().split(".");
            dt=dt[1]+'/'+dt[0]+'/'+dt[2];
            var formatted = moment(dt).format('ll');
            $(this).text(formatted);
        });
    </script>
@endsection