@extends('layouts.master')
@section('content')
<style>
    .payment-wrapper {
  width: 100%;
  min-height: calc(100vh - 81px);
  background-color: #f0f7ff;
  display: flex;
  align-items: center;
  justify-content: center;
}
.pay_method {
  border: 1px solid #e1e0e0 !important;
  padding: 30px 20px;
  background-color: #fff;
}
.pay_head {
  font-size: 20px;
  font-weight: 600;
  color: #353f5e;
}
.pay_para {
  font-size: 16px;
  color: #353f5e;
  line-height: 30px;
  margin-top: 20px;
}
.payment-card-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
}

.payment-card-list li {
  margin-right: 10px;
}

.pay_input_div {
  display: flex;
  flex-wrap: wrap;
  margin-top: 30px;
}

.input_div {
  display: flex;
  flex-direction: column;
  width: 50%;
  color: #646363;
  margin-bottom: 20px;
}

.input_div label {
  margin-bottom: 5px !important;
}
.input_div .form-wrapperInput {
  width: 90%;
  height: 42px;
  padding: 8px 16px;
  border: 1px solid #e6e6e6;
  margin-bottom: 5px;
  outline: none;
  -webkit-appearance: none;
  appearance: none;
  border-radius: 5px;
}

.input_div .form-wrapperInput::placeholder {
  font-size: 14px;
  color: #7b7b7b;
}

.proceed-btn {
  padding: 12px 40px;
  outline: none;
  border: none;
  text-transform: uppercase;
  font-size: 16px;
  font-weight: 500;
  background-color: #2c91ff;
  color: #fff;
  margin-top: 10px;
}

.proceed-btn .loader-spinner .loading-gif {
  animation-name: spin;
  animation-duration: 2000ms;
  animation-iteration-count: infinite;
  animation-timing-function: linear;
}
    </style>
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        @if(Session::has('success'))
        @section('scripts')
        <script>
        swal("Good job!", "{{ Session::get('success') }}", "success");
        </script>
        @endsection
        @endif

        @if(Session::has('error'))
        @section('scripts')
        <script>
        swal("Oops...", "{{ Session::get('error') }}", "error");
        </script>
        @endsection
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="row tabelhed d-flex justify-content-between">
                    {{-- <div class="col-lg-2 col-md-2 col-sm-2 d-flex">
                        <a class="ad-btn btn text-center" href="{{ route('admin.subscription-plan.create') }}"> Add</a>
                    </div> --}}

                    <div class="col-lg-10 col-md-10">

                        {{-- <div class="right-item d-flex justify-content-end">
                            <form action="" method="GET" class="d-flex">

                                <input type="text" name="keyword" id="keyword" class="form-control"
                                    value="{{ request()->get('keyword', '') }}" placeholder="Search Subscription">

                                <button class="btn-sm search-btn keyword-btn" type="submit">
                                    <i class="ti-search pl-3" aria-hidden="true"></i>
                                </button>

                                <a href="{{ route('admin.subscription-plan.index') }}" class="btn-sm reload-btn">
                                    <i class="ti-reload pl-3 redirect-icon" aria-hidden="true"></i>
                                </a>

                                @if(isset($_GET['items']))<input type="hidden" name="items"
                                    value="{{$_GET['items']}}">@endif
                            </form>
                        </div> --}}
                    </div>
                </div>

                <div class="card">
                    <div class="card-header ">
                        <div class="row">
                            <div class="col-xl-6 col-md-6 mt-auto">
                                <h5>Subscription </h5>
                            </div>
                            <div class="col-xl-6 col-md-6">
                                <div class="row float-end">
                                    <div class="col-xl-12 d-flex float-end">
                                        <div class="items paginatee">
                                            {{-- <form action="" method="GET">
                                                <select class="form-select m-0 items" name="items" id="items"
                                                    aria-label="Default select example">
                                                    <option value='10'
                                                        {{ isset($items) ? ($items == '10' ? 'selected' : '' ) : '' }}>
                                                        10</option>
                                                    <option value='20'
                                                        {{ isset($items) ? ($items == '20' ? 'selected' : '' ) : '' }}>
                                                        20</option>
                                                    <option value='30'
                                                        {{ isset($items) ? ($items == '30' ? 'selected' : '' ) : '' }}>
                                                        30</option>
                                                    <option value='40'
                                                        {{ isset($items) ? ($items == '40' ? 'selected' : '' ) : '' }}>
                                                        40</option>
                                                    <option value='50'
                                                        {{ isset($items) ? ($items == '50' ? 'selected' : '' ) : '' }}>
                                                        50</option>
                                                </select>

                                                @if(isset($_GET['type']))<input type="hidden" name="type"
                                                    value="{{$_GET['type']}}">@endif
                                                @if(isset($_GET['status']))<input type="hidden" name="status"
                                                    value="{{$_GET['status']}}">@endif
                                                @if(isset($_GET['keyword']))<input type="hidden" name="keyword"
                                                    value="{{$_GET['keyword']}}">@endif
                                            </form> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <form action="{{route('admin.buy-plan')}}" method="POST">
                                        @csrf
                                    <section id="pricing" class="pricing-content section-padding">
                                        <div class="container">					
                                            <div class="section-title text-center">
                                                <h3><b> Bronze Plan </b></h3>
                                            </div>	
                                            <div class="payment-wrapper">
                                                <div class="container">
                                                  <div class="pay_method">
                                                    <h4 class="pay_head">Billing Method</h4>
                                                    <p class="pay_para">Payment Type*</p>
                                                    <ul class="payment-card-list">
                                                      <li>
                                                        <img src="/assets/american-express.svg" alt="" />
                                                      </li>
                                                      <li>
                                                        <img src="/assets/master-card.svg" alt="" />
                                                      </li>
                                                      <li>
                                                        <img src="/assets/visa-card-icon.svg" alt="" />
                                                      </li>
                                                      <li>
                                                        <img src="/assets/paypal-icon.svg" alt="" />
                                                      </li>
                                                    </ul>
                                                    <form class="pay_input_div">
                                                      <div class="input_div">
                                                        <label htmlFor="">Card Number*</label>
                                                        <input
                                                          type="number"
                                                          class="form-wrapperInput"
                                                          name="card_number"
                                                          placeholder="Your Card"
                                                        />
                                                      </div>
                                                      <div class="input_div">
                                                        <label htmlFor="">Expiry Month*</label>
                                                        <input
                                                          type="number"
                                                          class="form-wrapperInput"
                                                          name="exp_month"
                                                          placeholder="Expiry Month"
                                                        />
                                                      </div>
                                              
                                                      <div class="input_div">
                                                        <label htmlFor="">Expiry Year*</label>
                                                        <input
                                                          type="number"
                                                          class="form-wrapperInput"
                                                          name="exp_year"
                                                          placeholder="Expiry Year"
                                                        />
                                                      </div>
                                                      <div class="input_div">
                                                        <label htmlFor="">CVV*</label>
                                                        <input
                                                          type="number"
                                                          class="form-wrapperInput"
                                                          name="cvv"
                                                          placeholder="Your CVV"
                                                        />
                                                      </div>
                                                      <button type="submit" class="proceed-btn proceed-btnyyyy">
                                                        <span class="loader-spinner">Pay Now</span>
                                                      </button>
                                                    </form>
                                                  </div>
                                                </div>
                                              </div>			
                                          		  
                                            </div>
                                        </div>                                      
                                    </section>                               
                               </form>
                                </div>
                        </div>
                    </div>

                  
                </div>
            </div>
        </div>
    </div>
</div>

@endsection