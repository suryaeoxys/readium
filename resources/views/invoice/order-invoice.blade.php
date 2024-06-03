<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
    /> -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Crimson+Text&display=swap" rel="stylesheet"> -->
    <title>{{ config("app.name", "Laravel") }}</title>
    <style>
      body, .table th, .table td{
        font-family: "Manrope", sans-serif;
      }
      .row {
        --bs-gutter-x: 30px;
        --bs-gutter-y: 0;
        display: flex;
        flex-wrap: wrap;
        margin-top: calc(-1 * var(--bs-gutter-y));
        margin-right: calc(-0.5 * var(--bs-gutter-x));
        margin-left: calc(-0.5 * var(--bs-gutter-x));
      }
      .mb-2 {
        margin-bottom: 0.5rem !important;
      }
      .mb-3 {
        margin-bottom: 1rem !important;
      }
      .mb-0 {
        margin-bottom: 0 !important;
      }
      .mt-2 {
        margin-top: 0.5rem !important;
      }
      .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }
      .table.table-bordered {
        border-top: 1px solid #dee2e6;
      }
      .table {
        margin-bottom: 0;
      }
      .text-nowrap {
        white-space: nowrap !important;
      }
      .w-100 {
        width: 100% !important;
      }
      .w-50 {
        width: 50% !important;
      }
      .float-end {
        float: right !important;
      }
      .table {
        --bs-table-bg: transparent;
        --bs-table-accent-bg: transparent;
        --bs-table-striped-color: #212529;
        --bs-table-striped-bg: rgba(0, 0, 0, 0.05);
        --bs-table-active-color: #212529;
        --bs-table-active-bg: rgba(0, 0, 0, 0.1);
        --bs-table-hover-color: #212529;
        --bs-table-hover-bg: #eaeaf1;
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
        vertical-align: top;
        border-color: #dee2e6;
      }
      table {
        caption-side: bottom;
        border-collapse: collapse;
      }
      .table > thead {
        vertical-align: bottom;
      }
      thead,
      tbody,
      tfoot,
      tr,
      td,
      th {
        border-color: inherit;
        border-style: solid;
        border-width: 0;
      }
      .table > :not(:first-child),
      .jsgrid .jsgrid-table > :not(:first-child) {
        border-top: none;
      }
      .table-bordered > :not(caption) > * {
        border-width: 1px 0;
      }
      .table-striped > tbody > tr:nth-of-type(odd) > * {
        --bs-table-accent-bg: var(--bs-table-striped-bg);
        color: var(--bs-table-striped-color);
      }
      /* table.no-action td {
        padding-top: 12px;
        padding-bottom: 12px;
      } */
      .table td {
        white-space: unset;
      }
      .table td {
        padding: 0.45rem 0.5rem;
      }
      /* .table td {
        font-size: 0.812rem;
      } */
      .table th,
      .table td {
        vertical-align: middle;
        /* white-space: nowrap; */
        padding: 0.5rem 0.5rem;
      }
      .table-bordered > :not(caption) > * > * {
        border-width: 0 1px;
      }
      .table > :not(:last-child) > :last-child > *,
      .jsgrid .jsgrid-table > :not(:last-child) > :last-child > * {
        border-bottom-color: #dee2e6;
      }
      .table thead th {
        border-top: 0;
        border-bottom-width: 1px;
        font-weight: 600;
        /* font-size: 0.875rem; */
      }
      .table th,
      .table td {
        vertical-align: middle;
        /* white-space: nowrap; */
        padding: 0.5rem 0.5rem;
      }
      .table-bordered > :not(caption) > * > * {
        border-width: 0 1px;
      }
      th {
        text-align: inherit;
        text-align: -webkit-match-parent;
      }
      /* .order-detail th {
        font-size: 14px;
      } */
      .table th {
        font-size: 14px;
      }
      .table td {
        font-size: 12px;
      }
      h4 {
        font-size: 14px;
      }
      h6 {
        font-size: 14px;
      }
      .fw-bolder {
        font-weight: bolder !important;
      }
      span.never_out {
        color: #4169e2;
        font-size: 21px;
        font-weight: bold;
        padding-top: 10px !important;
      }
      .justify-content-center {
        text-align: center;
        margin-top: 6px;
        margin-bottom: 6px;
      }
      .opicity {
        opacity: 0.7;
      }
      /* .serive_section {
        margin-bottom: 10px;
      } */
    </style>
    <style>                                     
      .gridtable {
          font-family: "Verdana";
          font-size:8px;                       
          color:#333333;            
          border-width: 1px;
          border-color: #000000;
          border-collapse: collapse;
          
      }
      
      
      .gridtable th {
          border-width: 1px;
          padding:8px;
          border-style: solid;
          border-color: #000000;
         // font-weight:bold;
      }
      
      .gridtable td {
          border-width: 1px;
          padding: 8px;
          border-style: solid;
          border-color: #000000;
          background-color: #ffffff;
      }
      
      
    
      </style> 
  </head>
  <body>
    <div class="content-wrapper">
      <!-- Content -->
      <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header border-bottom">
                <img src="{{ $logo }}" /> <br />
                <span class="never_out"> Never Out of Stock-NOOS</span>
              </div>

              <div class="card-body">
                <div class="mb-2">
                  <div class="row">
                    <div class="row">
                      <div class="col-12">
                        <div class="d-flex justify-content-center">
                          <b><span>Tax Invoice</span></b> <br />
                          <span class="opicity">ORIGINAL For Recipient</span>
                        </div>
                        <!-- <h2>
                          #INV-{{ $data->id }}
                          <small class="float-end"
                            >Date:{{ date('d F Y', strtotime($data->created_at)) }}</small
                          >
                        </h2> -->
                      </div>
                    </div>

                    <div class="row invoice-info mt-2 mb-3">
                      <div class="col-sm-4 invoice-col edit_shipping1">
                        {{-- <h6>Tax Invoice on behalf of-</h6> --}}
                        <span class="mb-0">Tax Invoice on behalf of</span><br><br><br><br>
                        <span class="mb-0"> Legal Entity Name : {{ isset($data->vendor) && isset($data->vendor->name) ? $data->vendor->name : '' }} </span>
                        <br />
                        <span class="mb-0">
                           Store/Restaurant Name : {{ isset($data->vendor) && isset($data->vendor->vendor) && isset($data->vendor->vendor->store_name) ? $data->vendor->vendor->store_name : ''}}
                        </span>
                        <br />
                        <span class="mb-0">
                          Address : {{ $data->vendor->vendor->address ?? ''}}
                        </span>
                        <br />
                        <span class="mb-0">
                           Store/Restaurant GSTIN :{{ $data->vendor->vendor->gst_no ?? ''}}
                        </span>
                        <br />
                        <span class="mb-0" >
                           Store/Restaurant FSSAI : {{ $data->vendor->vendor->fssai_no ?? ''}}
                        </span>
                        <br />
                        <span class="mb-0" >
                          Invoice No. : {{ $data->id ?? '' }}
                        </span>
                        <br />
                        <span class="mb-0">
                          Invoice Date : {{ date('d F Y', strtotime($data->created_at)) }}
                        </span>
                        <br />
                        <br />
                        <span class="mb-0">
                          Customer Name : {{ $data->user->name ?? '' }}
                        </span>
                        <br />
                        <span class="mb-0">
                          Delivery Address : {{ isset($deliveyAddress) ? $deliveyAddress : '' }}
                        </span>
                        <br />
                        <span class="mb-0">
                          State name & Place of Supply : {{ !empty($address_array[count($address_array)-2]) ? $address_array[count($address_array)-2] : '' }}
                        </span>
                        <!-- <br />
                        <br />
                        <span class="mb-0">
                          HNS Code : {{$data->orderItem->first()->products->Category->hsn_code ?? ''}}
                        </span>
                        <br />
                        <span class="serive_section">
                          Service Description : Restaurant Serive 
                        </span> -->
                      </div>
                    </div>
                    <div class="table-responsive">
                    @php
                      $taxDataTax = json_decode($data->tax_id_1,true); 
                      $taxDataTax1 = json_decode($data->tax_id_2,true); 
                    @endphp
                    <table width="100%" class= "gridtable table" >
                      <tr >
                        <th class="wd-15p">Particulars</th>
                        <th class="wd-15p">Gross Value</th>
                        <th class="wd-15p">Save Coupon </th>
                        <th class="wd-15p">Net Value</th>
                        @if(!empty($taxDataTax))  <th class="wd-15p">{{ $taxDataTax["type"] }}</th> @endif
                        @if(!empty($taxDataTax1))  <th class="wd-15p">{{ $taxDataTax1["type"] }}</th> @endif
                        @if(($data->surcharge)>0)  <th class="wd-15p">Surge Charge</th> @endif
                        @if(($data->packing_fee)>0)  <th class="wd-15p">Packing Fee</th> @endif
                        @if(($data->delivery_charges)>0)  <th class="wd-15p">Delivery Partner Fee</th> @endif
                        @if(($data->tip_amount)>0)  <th class="wd-15p">Tip Amount</th> @endif
                        <th class="wd-15p">Total</th>
                      </tr>
                      <tbody>
                        @php $i = 1; $total = 0; $subtotal = 0; $count = count($data->orderItem); @endphp
                          @foreach ($data->orderItem as $item)
                          <tr>
                            @php $item_total_price = ($item->price * $item->qty); @endphp
                            <td>{{ $item->products->name.' x '.$item->qty }}<span style="color:red;">@if($item->status == 'R') (Rejected) @endif</span></td>
                            <td>Rs {{ $item_total_price }}</td>
                            @if($i == 1) <td rowspan={{$count}}> Rs {{ $data->coupon->discounted_price ?? '0' }}</td> @endif
                            <!-- <td> Rs {{ $item->price }}</td> -->
                            <td> Rs {{ $item_total_price }}</td>
                            @if(!empty($taxDataTax)) @if($i == 1) <td rowspan={{$count}}> Rs {{ $taxDataTax["amount"] }}</td> @endif @endif
                            @if(!empty($taxDataTax1)) @if($i == 1) <td rowspan={{$count}}> Rs {{ $taxDataTax1["amount"] }}</td> @endif @endif
                            @if(($data->surcharge)>0) @if($i == 1) <td rowspan={{$count}}> Rs {{ $data->surcharge }}</td> @endif @endif
                            @if(($data->packing_fee)>0) @if($i == 1) <td rowspan={{$count}}> Rs {{ $data->packing_fee }}</td> @endif @endif
                            @if(($data->delivery_charges)>0) @if($i == 1) <td rowspan={{$count}}> Rs {{ $data->delivery_charges }}</td> @endif @endif
                            @if(($data->tip_amount)>0) @if($i == 1) <td rowspan={{$count}}> Rs {{ $data->tip_amount }}</td> @endif @endif
                            <td>
                            @php 
                            if($item->status == 'A') {
                              $subtotal += $item_total_price;
                            }
                            $i++;
                            @endphp
                              Rs {{$item_total_price ?? ''}}
                            </td>
                          </tr>
                          @endforeach
                          <tr>
                            @php $discount_amount = $data->coupon->discounted_price ?? 0; @endphp
                            <th>Total</th>
                            <td>Rs {{ $subtotal }}</td>
                            <td>(-) Rs {{ $discount_amount }}</td>
                            <td>Rs {{ $subtotal - $discount_amount }}</td>
                            @if(!empty($taxDataTax))  <td>Rs {{ $taxDataTax["amount"] }}</td> @endif
                            @if(!empty($taxDataTax1))  <td>Rs {{ $taxDataTax1["amount"] }}</td> @endif
                            @if(($data->surcharge)>0)  <td>Rs {{ $data->surcharge }}</td> @endif
                            @if(($data->packing_fee)>0)  <td>Rs {{ $data->packing_fee }}</td> @endif
                            @if(($data->delivery_charges)>0)  <td>Rs {{ $data->delivery_charges }}</td> @endif
                            @if(($data->tip_amount)>0)  <td>Rs {{ $data->tip_amount }}</td> @endif
                            <td>Rs {{$data->grand_total ?? ''}}</td>
                          </tr>
                          
                      </tbody>
                    </table>
                    </div>
                    <div class="row invoice-info mt-2 mb-3">
                      <div class="col-sm-4 invoice-col edit_shipping1"><br>
                      <span class="mb-0" >
                        Amount (in Words) = {{ $numberToWord ?? '' }}
                      </span>
                      <br><br>
                      <span class="mb-0" >
                        Amount INR {{$data->grand_total ?? ''}} settled through digital mode/payment received upon Delivery against Order ID: #{{$data->id ?? ''}} </br>Supply attracts reverse charge: No 
                      </span>
                      <br />
                    </div>
                  </div><br><br><br><br>
                  <div class="row invoice-info mt-2 mb-3" style="position: absolute;   bottom:2px;">
                    <div class="col-sm-4 invoice-col edit_shipping1">
                    <span class="mb-0" >
                      Ecoveggy Private Limited
                    </span><br>
                    <span class="mb-0" >
                      PAN : {{$pan_no_admin ?? ''}}
                    </span>
                    <br>
                    <span class="mb-0" >
                      CIN : {{$cin_no_admin ?? ''}}
                    </span>
                    <br>
                    <span class="mb-0" >
                      GST : {{$gst_no_admin ?? ''}}
                    </span>
                    <br>
                    <span class="mb-0" >
                      FSSAI : {{$fssai_admin ?? ''}}
                    </span>
                  </div>
                </div>   
                <div class="row invoice-info mt-2 mb-3 float-end" style="position: absolute;   bottom:2px;">
                  <div class="col-sm-4 invoice-col edit_shipping1">
                  <span class="mb-0" >
                     For Ecoveggy Private Limited
                  </span></br>
                  <img src="{{ $sign }}" width="100" height="100" /></br>
                  <span class="mb-0" >
                    Authorized Signatory
                  </span>
                </div>
              </div> 
 

              {{-- <div class="table-responsive">
                <table
                  id=""
                  class="table table-striped table-bordered text-nowrap w-100 no-action"
                >
                  <thead>
                    <tr>
                      <th>#</th>
                      <th class="wd-15p">Product Name</th>
                      <th class="wd-15p">Cost</th>
                      <th class="wd-15p">Item Quantity</th>
                      <th class="wd-15p">Quantity</th>
                      <th class="wd-15p">status</th>
                      <th class="wd-15p">Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $i = 1; $total = 0; $subtotal = 0; @endphp
                    @foreach ($data->orderItem as $item)
                    <tr>
                      <td>{{ $i++ }}</td>
                      <td>{{ $item->products->name }}</td>
                      <td>Rs {{ $item->price }}</td>
                      <td>{{ $item->item_qty }}</td>
                      <td>{{ $item->qty }}</td>
                      <td>
                        {{ $item->status == 'A' ? 'Accepted' :
                        ($item->status == 'R' ? 'Rejected' : '-') }}
                      </td>
                      <td>Rs {{ ($item->price * $item->qty) ?? '-' }}</td>
                      @php $subtotal += ($item->price * $item->qty);
                      @endphp
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                <div class="row float-end w-50">
                  <div class="table-responsive">
                    <table class="table order-detail">
                      <tr>
                        <th>SUBTOTAL :</th>
                        <td>Rs. {{ $subtotal }}</td>
                      </tr>
                      @isset($data->surcharge)
                      <tr>
                        <th>SURCHARGE :</th>
                        <td>Rs. {{ $data->surcharge ?? '' }}</td>
                      </tr>
                      @endisset 
                      @if(isset($data->tax) && ($data->tax > 0))
                      @php $taxDataTax = json_decode($data->tax_id_1,true); 
                      $taxDataTax1 = json_decode($data->tax_id_2,true); @endphp
                      @if(!empty($taxDataTax))
                      <tr>
                        <th>{{ $taxDataTax["type"] }} :</th>
                        <td>Rs. {{ $taxDataTax["amount"] }}</td>
                      </tr>
                      @endif @if(!empty($taxDataTax1))
                      <tr>
                        <th>{{ $taxDataTax1["type"] }} :</th>
                        <td>Rs. {{ $taxDataTax1["amount"] }}</td>
                      </tr>
                      @endif @endif @isset($data->coupon)
                      <tr>
                        <th>COUPON : {{ $data->coupon->coupon_code }}</th>
                        <td>
                          (-) Rs. {{ $data->coupon->discounted_price }}
                        </td>
                      </tr>
                      @endisset @isset($data->delivery_charges)
                      <tr>
                        <th>DELIVERY CHARGE :</th>
                        <td>Rs. {{ $data->delivery_charges ?? '' }}</td>
                      </tr>
                      @endisset @isset($data->packing_fee)
                      <tr>
                        <th>PACKING FEE :</th>
                        <td>Rs. {{ $data->packing_fee ?? '' }}</td>
                      </tr>
                      @endisset @isset($data->tip_amount)
                      <tr>
                        <th>TIP AMOUNT :</th>
                        <td>Rs. {{ $data->tip_amount ?? '' }}</td>
                      </tr>
                      @endisset
                      <tr>
                        <th>PAYMENT MODE :</th>
                        <td>
                          {{ isset($data->order_type) ? ($data->order_type
                          == 'O' ? 'Online' : ($data->order_type == 'C' ?
                          'COD' : '')) : '' }}
                        </td>
                      </tr>
                      <tr>
                        <th>GRAND TOTAL :</th>
                        <td class="border-bottom">
                          Rs {{ $data->grand_total }}
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div> --}}

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
