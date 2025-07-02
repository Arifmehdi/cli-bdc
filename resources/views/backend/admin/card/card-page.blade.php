@extends('backend.admin.layouts.master')

@section('content')
<div class="row">
    <div class="col-md-12">
        <section class="content">

            <!-- Default box -->
            <div class="card" style="background: #ddd">
                <div class="card-body">
                <div class="row">
                    <div class="col-md-8">

                            <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Name on Card *</label>
                              <div class="col-sm-10">
                                <input type="text" placeholder="Name of Card" class="form-control" id="nameOfCard" name="nameOfCard">
                              </div>
                            </div>
                            <div class="form-group row">
                              <label for="inputPassword" class="col-sm-2 col-form-label">Billing Address</label>
                              <div class="col-sm-10">
                                <input type="text" class="form-control" name="billingAddress" id="billingAddress" placeholder="Billing Address">
                              </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-sm-4 col-form-label">City / State</label>
                                        <div class="col-sm-8">
                                          <input type="text" class="form-control" name="city" id="city" placeholder="Anywhere">
                                        </div>
                                      </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                          <input type="text" class="form-control" name="state">
                                        </div>
                                      </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputPassword" class="col-sm-2 col-form-label">Zip</label>
                                <div class="col-sm-10">
                                  <input type="text" class="form-control" id="zip_code" name="zip_code" placeholder="Zip Code">
                                </div>
                              </div>

                              <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-sm-4 col-form-label">Email Address</label>
                                        <div class="col-sm-8">
                                          <input type="email" class="form-control" id="email" placeholder="Enter Email Address" name="email">
                                        </div>
                                      </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                          <label><input type="checkbox" id="email_receipt" name="email_receipt">  Email Receipt</label>
                                        </div>
                                      </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputPassword" class="col-sm-2 col-form-label">Cell</label>
                                <div class="col-sm-10">
                                  <input type="text" class="form-control" id="cell" name="cell" placeholder="Cell Number">
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="inputPassword" class="col-sm-2 col-form-label"> </label>
                                <div class="col-sm-10">
                                  <label><input type="checkbox" id="checkbox_customer_profile" name="checkbox_customer_profile"> Store Customer Profile</label>
                                </div>
                              </div>
                            </br>
                            </br>

                            <span style="color: #504d4d">Transaction Details</span>
                            <hr>

                            <div class="form-group row">
                                <label for="inputPassword" class="col-sm-2 col-form-label">Amount</label>
                                <div class="col-sm-10">
                              <input type="text" class="form-control" id="amount" name="amount">
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="inputPassword" class="col-sm-2 col-form-label">Total</label>
                                <div class="col-sm-10">
                              <input type="text" class="form-control" id="total_amount" name="total_amount">
                                </div>
                              </div>
                              <hr>

                              <button class="btn btn-primary px-4">Process Transaction</button> Or <a href="#" style="color: rgb(226, 177, 14);font-weight:bold"> <u>Cancel</u></a>


                    </div>
                    <div class="col-md-4">
                        <div class="row" style="background-color: rgb(54, 55, 51)">
                            <div class="col-md-12 p-2">
                                <a href="" style="font-size:20px;color:white"><i class="fa fa-plus"></i></a>
                                <a href="" style="font-size: 20px;color:white;margin-left:100px">Add New Credit Card</a>
                            </div>
                            <div class="row">
                                <div class="col-md-8 p-3">
                                    <input type="text" class="form-control" placeholder="Card Number">
                                    <div class="row">
                                        <div class="col-md-6 mt-2">
                                            <select name="month" id="month" class="form-control">
                                                <option value="1">02</option>
                                                <option value="1">02</option>
                                                <option value="1">02</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <select name="year" id="year" class="form-control">
                                                <option value="2024">2024</option>
                                                <option value="2024">2024</option>
                                                <option value="2024">2024</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 p-3">
                                    <input type="text" class="form-control">
                                </div>

                            </div>
                            <div class="col-md-12 p-5">

                                <a href="" style="font-size: 20px;color:rgb(247, 242, 242)"><u>Device/Terminal Card Entry</u></a> <span class="px-4" style="padding:10px;background:#fff;color: #504d4d;
                                margin-left: 14px;border-radius: 10px">VISA</span>
                            </div>

                            <div class="col-md-12 p-2" style="background-color: #fff">
                                <a href="" style="font-size:20px;color:rgb(41, 39, 39)"><i class="fa fa-plus"></i></a>
                                <a href="" style="font-size: 20px;color:rgb(24, 22, 22);margin-left:100px">Add New eCheck</a>
                            </div>

                        </div>
                    </div>
                </div>



                </div>
                <!-- /.card-body -->
            </div>
                <!-- /.card-body -->



        </section>

    </div>
</div>
@endsection



