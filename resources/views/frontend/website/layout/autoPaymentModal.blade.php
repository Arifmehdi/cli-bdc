    <!-- Modal -->
    <div class="modal fade" id="PaymentModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                @php
                    $monthly_payment_input = 582;
                    $months = 36;
                    $monthly_interest_rate = 5.85;
                    $price = number_format(($monthly_payment_input * ((pow(1 + $monthly_interest_rate, $months) - 1) / ($monthly_interest_rate * pow(1 + $monthly_interest_rate, $months)))), 2, '.', '');
                @endphp
                <div class="modal-body">
                    <button style="float:right;" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h4 style="text-align:center; margin-top:35px; margin-bottom:15px">Customize your budget</h4>
                    <div style="background-color:rgb(102, 55, 93); color:white" class="card">
                      <p class="text-center mt-3">Est. max vehicle price based on your desired monthly budget (7.0% APR*. and 0.0% sales tax)</p>
                      <h2 class="text-center"><strong id="totalLoanAmount"> $0*</strong></h2>
                      <input type="hidden"  id="hiddenTotalLoanAmount">
                      <input type="hidden"  id="hiddenAutoPayInput">
                    </div>

                     <div style="width:100%" class="input-container">
                            <span class="dollar-sign-modal">$</span>
                            <input class="mt-4 common_calculator-modal" style="width:100%; border-radius:4px; border:1px solid rgb(204, 204, 204); height:50px; background:white" type="text" placeholder="Max Monthly budghet ($4500)" id="monthlyBudget"/>
                            </div>


                    <div class="dropdown">

                        <select class="mt-5" style="width:100%; border:1px solid rgb(204, 204, 204);border-radius:4px;  background:white; height:50px;" name="credit_calculate" id="credit_calculate">
                            <option value="rebuild">Rebuilding (0-620)</option>
                            <option value="fair">Fair (621-699)</option>
                            <option value="good" selected>Good (700-759)</option>
                            <option value="excellent">Excellent (760+)</option>
                        </select>
                        <div>
                            <input type="text" style="width:100%; border:1px solid rgb(204, 204, 204); border-radius:4px; padding:12px" class="mt-5 " placeholder="Interest Rate (APR) %" value="5.82" id="calculate_interest">
                        </div>
                        <input class="mt-5" style="width:100%; border:1px solid rgb(204, 204, 204); border-radius:4px; padding:12px" type="text" placeholder="Zip" />

                        <div class="row">
                            <div class="col-lg-6 col-sm-12">
                                <input class="mt-5" style="width:100%; border:1px solid rgb(204, 204, 204);border-radius:4px; padding:12px" type="text" placeholder="Net trade-in value (optional)" />
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <input class="mt-5" style="width:100%; border:1px solid rgb(204, 204, 204);border-radius:4px; padding:12px" type="text" placeholder="Down Payment (optional)" />
                            </div>
                        </div>
                        <p class="mt-5 mb-3">Length of loan (in months)</p>
                        {{-- <div class="row">
                            <div class="col-lg-3 col-sm-6">
                                <input type='radio' value='36' class="common_calculate common_calculator" name='calculate_month' id='calculate_month' />
                                <label for='36'>36</label>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <input type='radio' value='48' class="common_calculate common_calculator" name='calculate_month' id='calculate_month' />
                                <label for='48'>48</label>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <input type='radio' value='60' class="common_calculate common_calculator" name='calculate_month' id='calculate_month' />
                                <label for='60'>60</label>
                            </div>
                            <div class="col-lg-3 col-sm-6">



                                <input type='radio' value='72' class="common_calculate common_calculator" name='calculate_month' id='calculate_month' checked/>
                                <label for='72'>72</label>



                            </div>
                        </div> --}}
                        <div class="row">
                            <ul class="finance-radio-list">
                                <div class="d-flex flex-wrap mt-3 ms-2">
                                    <div class="p-2 monthly-package">
                                        <li>
                                            <button style="width:69px; height:45px; text-align:center; border-radius:5px; border:1px solid gray"
                                                class="common_calculate common_calculator" data-value="36">36</button>
                                        </li>
                                    </div>
                                    <div class="p-2 monthly-package">
                                        <li>
                                            <button style="width:69px; height:45px; text-align:center; border-radius:5px; border:1px solid gray"
                                                class="common_calculate common_calculator" data-value="48">48</button>
                                        </li>
                                    </div>
                                    <div class="p-2 monthly-package">
                                        <li>
                                            <button style="width:69px; height:45px; text-align:center; border-radius:5px; border:1px solid gray"
                                                class="common_calculate common_calculator" data-value="60">60</button>
                                        </li>
                                    </div>
                                    <div class="p-2 monthly-package">
                                        <li>
                                            <button style="width:69px; height:45px; text-align:center; border-radius:5px; border:1px solid gray"
                                                class="common_calculate common_calculator active" data-value="72">72</button>
                                        </li>
                                    </div>
                                    <div class="p-2 monthly-package">
                                        <li>
                                            <button style="width:69px; height:45px; text-align:center; border-radius:5px; border:1px solid gray"
                                                class="common_calculate common_calculator" data-value="84">84</button>
                                        </li>
                                    </div>
                                </div>
                            </ul>
                        </div>


                        <button style="width:100%; background-color :lightseagreen; color:white; border-radius:4px" class="btn mb-2 mt-5" id="submitCalcultor" >See Matching Car</button>

                    </div>
                </div>

            </div>
        </div>
    </div>
