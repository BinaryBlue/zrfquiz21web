@extends('layouts.app')


@section('content')

<div class="  ">
    <div class="row pb-4 animated fadeInRight delayp1">
        <div class="col-lg-4">
            <div class="card bg-purple text-white">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="stats">
                            <h1 class="text-white">42,349/-</h1>
                            <button class="btn btn-rounded btn-outline btn-light m-t-8 font-18">Account Balance</button>
                        </div>
                        <div class="stats-icon text-right ml-auto"><i class="fas fa-donate display-2 op-2 text-white"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card bg-red text-white">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="stats">
                            <h1 class="text-white">1,65,324/-</h1>
                            <button class="btn btn-rounded btn-outline btn-light m-t-8 font-18">Market Due</button>
                        </div>
                        <div class="stats-icon text-right ml-auto"><i class="fas fa-hand-holding-usd display-2 op-2 text-white"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card bg-teal text-white">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="stats">
                            <h1 class="text-white">32,349/-</h1>
                            <button class="btn btn-rounded btn-outline btn-light m-t-8 font-18">Today Sell</button>
                        </div>
                        <div class="stats-icon text-right ml-auto"><i class="fas fa-cart-plus display-2 op-2 text-white"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row pb-4 animated fadeInLeft delayp1">
        <div class="col-6 col-lg-3 mb-3">
            <div class="card  ">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="m-r-20 align-self-center">
                            <i class="fas fa-leaf text-info" style="font-size: 40px;"></i>
                        </div>
                        <div class="align-self-center ">
                            <h6 class=" m-t-10 m-b-0">Total Income</h6>
                            <h2 class="m-t-0 ">953,000</h2></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="card ">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="m-r-20 align-self-center">
                            <i class="fas fa-credit-card text-danger" style="font-size: 40px;"></i>
                        </div>
                        <div class="align-self-center">
                            <h6 class="text-muted m-t-10 m-b-0">Total Expense</h6>
                            <h2 class="m-t-0">236,000</h2></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="card ">
                <div class="card-body ">
                    <div class="d-flex">
                        <div class="m-r-20 align-self-center">
                            <i class="icon-badge text-primary" style="font-size: 40px;"></i>
                        </div>
                        <div class="align-self-center">
                            <h6 class=" m-t-10 m-b-0">Total Assets</h6>
                            <h2 class="m-t-0">987,563</h2></div>
                    </div>
                </div>
            </div>
        </div>
        <div class=" col-6 col-lg-3 mb-3">
            <div class="card ">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="m-r-20 align-self-center">
                            <i class="fas fa-users text-warning" style="font-size: 40px;"></i>
                        </div>
                        <div class="align-self-center">
                            <h6 class="text-muted m-t-10 m-b-0">Total Staff</h6>
                            <h2 class="m-t-0">987,563</h2></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

                
</div>
          
@stop