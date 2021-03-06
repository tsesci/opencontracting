@extends('app')

@section('content')
    <div class="block header-block header-with-bg">
        <div class="row clearfix">
            <div class="full-header">
                <h2> {{ $tenderDetail['tender']['title'] }}
                    Servicii de tratament sanatorial
                </h2>
            </div>

            <div>
                <button class="small-button grey-yellow-btn"><span>Status:</span>
                    <span>{{ $tenderDetail['tender']['status'] }}</span>
                </button>
            </div>

        </div>
    </div>

    <div class="push-up-block row">
        <div class="description-summary clearfix">

            <div class="name-value-section each-row clearfix">
                <div class="medium-6 small-12 columns each-detail-wrap">
                    <span class="icon procuring-agency">icon</span>
                     <span class="each-detail">
                         <div class="name columns">Procuring agency</div>
                        <div class="value columns">
                            <a href="{{ route('procuring-agency.show',['name'=>$tenderDetail['buyer']['name']]) }}">
                                {{ $tenderDetail['buyer']['name'] }}
                            </a>
                        </div>
                    </span>
                </div>

                <div class="medium-6 small-12 columns each-detail-wrap">
                    <span class="icon tender-period">icon</span>
                    <span class="each-detail">
                         <div class="name columns">Tender period</div>
                        <div class="value columns"><span
                                    class="dt">{{ $tenderDetail['tender']['tenderPeriod']['startDate'] }}</span>
                            - <span class="dt">{{ $tenderDetail['tender']['tenderPeriod']['endDate'] }}</span></div>
                    </span>
                </div>
            </div>

            <div class="name-value-section each-row clearfix">
                <div class="medium-6 small-12 columns each-detail-wrap end">
                    <span class="icon procurement-method">icon</span>
                    <span class="each-detail">
                         <div class="name columns">Procurement method</div>
                        <div class="value columns">{{ $tenderDetail['tender']['procuringEntity']['identifier']['scheme'] }}</div>
                    </span>
                </div>
            </div>
        </div>

    </div>
    <div class="table-with-tab">
        <div class="row">
            <ul class="tabs" data-tabs id="example-tabs">
                <li class="tabs-title is-active">
                    <a href="#panel1" aria-selected="true">
                        <span>Goods/ Services under this tender</span>
                        <span class="tab-indicator"> ({{count($tenderDetail['tender']['items'])}})</span>
                    </a>
                </li>
                <li class="tabs-title">
                    <a href="#panel2">
                        <span>Contracts related to this tender</span>
                        <span class="tab-indicator">({{count($tenderDetail['contracts'])}})</span>

                    </a>
                </li>
            </ul>
            <div class="tabs-content" data-tabs-content="example-tabs">
                <div class="tabs-panel is-active" id="panel1">
                    <div class="table-wrapper">
                        <div class="title-section">
                            <span class="title">Goods/ Services under this tender
                                <span class="tab-indicator"> ({{count($tenderDetail['tender']['items'])}})</span>
                            </span>
                        </div>
                        <a target="_blank" class="export"
                           href="{{route('tenderGoods.export',['id'=>$tenderDetail['tender']['id']])}}">Export as
                            CSV</a>
                        <table id="table_items" class="responsive hover custom-table persist-area">
                            <thead class="persist-header">
                            <tr>
                                <th>Name</th>
                                <th>CPV code</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($tenderDetail['tender']['items'] as $key => $goods)
                                {{--@if($key < 10)--}}
                                <tr href="/goods/{{ $goods['classification']['description'] }}">
                                    <td>{{ $goods['classification']['description'] }}</td>
                                    <td>{{ $goods['classification']['id'] }}</td>
                                    <td>{{ $goods['quantity']}}</td>
                                    <td>{{ $goods['unit']['name'] }}</td>
                                </tr>
                                {{--@endif--}}
                            @empty
                            @endforelse


                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tabs-panel" id="panel2">

                    <div class="table-wrapper">
                        <div class="title-section">
                            <span class="title">Contracts related to this tender
                                <span class="tab-indicator">({{count($tenderDetail['contract'])}})</span>
                            </span>
                        </div>
                        <a target="_blank" class="export"
                           href="{{route('tenderContracts.export',['id'=>$tenderDetail['tender']['id']])}}">Export as
                            CSV</a>
                        <table id="table_contracts" class="responsive hover custom-table persist-area">
                            <thead class="persist-header">
                            <tr>
                                <th>Contract number</th>
                                <th>Goods and services contracted</th>
                                <th width="150px">Contract start date</th>
                                <th width="150px">Contract end date</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($tenderDetail['contracts'] as $key => $contract)
                                {{--@if($key < 10)--}}
                                <tr href="/contracts/{{$contract['id']}}">
                                    <td>{{ getContractInfo($tenderDetail['contracts'][$key]['title'],'id') }}</td>
                                    <td>{{ ($tenderDetail['awards'][$key]['items'])?$tenderDetail['awards'][$key]['items'][0]['classification']['description']:'-' }}</td>
                                    <td class="dt">{{ (!empty($contract['period']['startDate']))?$contract['period']['startDate']:'-' }}</td>
                                    <td class="dt">{{ $contract['period']['endDate'] }}</td>
                                    <td>{{ number_format($contract['value']['amount']) }}</td>
                                </tr>
                                {{--@endif--}}
                            @empty
                            @endforelse


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>


@endsection
@section('script')
    <script>
        $(document).ready(function () {
            $('.custom-table tbody tr').click(function () {
                if ($(this).attr('href')) {
                    window.location = $(this).attr('href');
                }
                return false;
            });
        });

        $("#table_items").DataTable({"bFilter": false});
        $("#table_contracts").DataTable({"bFilter": false});
    </script>
    <style>
        .custom-table tbody tr {
            cursor: pointer;
        }
    </style>
@endsection
