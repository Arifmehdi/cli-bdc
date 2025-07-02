$(document).ready(function(){
    // $('.common_calculate').on('change click', function() {
    //     var price = parseFloat($('#price_calculate').val());
    //     var credit = $('#credit_calculate').val();
    //     var interest_rate = parseFloat($('#calculate_interest').val());
    //     var down_payment_percentage = 10 / 100;
    //     var calculateMonthValue = $('input[name="calculate_month"]:checked').val();

    //     // Calculate down payment
    //     var down_payment = price * down_payment_percentage;
    //     $('#calculate_downpayment').val(down_payment);

    //     var loan_amount = price - down_payment;

    //     // Set interest rate based on credit
    //     if (credit == 'rebuild') {
    //         interest_rate = 11 / 100;
    //         $('#calculate_interest').val(11);
    //     } else if (credit == 'fair') {
    //         interest_rate = 6.85 / 100;
    //         $('#calculate_interest').val(6.85);
    //     } else if (credit == 'good') {
    //         interest_rate = 5.82 / 100;
    //         $('#calculate_interest').val(5.85);
    //     } else if (credit == 'excellent') {
    //         interest_rate = 4 / 100;
    //         $('#calculate_interest').val(4);
    //     }

    //     // Calculate monthly payment
    //     var months = calculateMonthValue; // Assuming a 72-month (6-year) loan term
    //     var monthly_interest_rate = interest_rate / 12;

    //     var monthly_payment = Math.ceil((loan_amount * monthly_interest_rate) / (1 - Math.pow(1 +
    //         monthly_interest_rate, -months)));

    //     $('#monthly_pay').html(monthly_payment); // Remove .toFixed(2)
    //     $('#loan_amount').html('Total Loan Amount: $' + Math.floor(loan_amount)); // Remove .toFixed(2)

    // });
    $('.calculate_month').on('click', function() {
        $('.calculate_month').removeClass('active');
        $(this).addClass('active');
        var price = parseFloat($('#price_calculate').val());
        var credit = $('#credit_calculate').val();
        var interest_rate = parseFloat($('#calculate_interest').val());
        var down_payment_percentage = 10 / 100;
        var calculateMonthValue = $(this).val();

        // Calculate down payment
        var down_payment = price * down_payment_percentage;
        $('#calculate_downpayment').val(down_payment);

        var loan_amount = price - down_payment;

        // Set interest rate based on credit
        if (credit == 'rebuild') {
            interest_rate = 11 / 100;
            $('#calculate_interest').val(11);
        } else if (credit == 'fair') {
            interest_rate = 6.85 / 100;
            $('#calculate_interest').val(6.85);
        } else if (credit == 'good') {
            interest_rate = 5.82 / 100;
            $('#calculate_interest').val(5.85);
        } else if (credit == 'excellent') {
            interest_rate = 4 / 100;
            $('#calculate_interest').val(4);
        }

        // Calculate monthly payment
        var months = calculateMonthValue; // Assuming a 72-month (6-year) loan term
        var monthly_interest_rate = interest_rate / 12;
        var monthly_payment = Math.ceil((loan_amount * monthly_interest_rate) / (1 - Math.pow(1 +
            monthly_interest_rate, -months)));

        $('#monthly_pay').html(monthly_payment); // Remove .toFixed(2)
        $('#loan_amount').html('Total Loan Amount: $' + Math.floor(loan_amount)); // Remove .toFixed(2)
    });

    // auto page calculation
    $(document).on('input', '#autoPayInput', function(){
        var autopay = $('#autoPayInput').val();

        if(autopay !== null && autopay.trim() !== ''){
            $('#withModal').prop('disabled', false); // Enable the button
            $('#hiddenAutoPayInput').val(autopay);

            var monthly = $('#hiddenAutoPayInput').val();
            $('#monthlyBudget').val(monthly);
            calculate();
        } else {
            $('#withModal').prop('disabled', true); // Disable the button
        }
    });

    $(document).on('click','.common_calculate', function(){
        $('.common_calculate').removeClass('active');
        $(this).addClass('active');
        calculate();
    })

    $(document).on('change','#credit_calculate', function(){

        var credit = $('#credit_calculate').val();

        // Set interest rate based on credit
        if (credit == 'rebuild') {
            interest_rate = 11 / 100;
            $('#calculate_interest').val(11);
        } else if (credit == 'fair') {
            interest_rate = 6.85 / 100;
            $('#calculate_interest').val(6.85);
        } else if (credit == 'good') {
            interest_rate = 5.82 / 100;
            $('#calculate_interest').val(5.85);
        } else if (credit == 'excellent') {
            interest_rate = 4 / 100;
            $('#calculate_interest').val(4);
        }
        calculate();
    });


    function calculate(){
        var monthly_payment_input = parseFloat($('#autoPayInput').val());
        var monthly_hidden_payment_input = $('#hiddenAutoPayInput').val(monthly_payment_input);
        var credit = $('#credit_calculate').val();
        var interest_rate = parseFloat($('#calculate_interest').val());
        var down_payment_percentage = 10 / 100;
        // var calculateMonthValue = $('input[name="calculate_month"]:checked').val();
        var calculateMonthValue = $('.common_calculator.active').data('value');

        var down_payment = price * down_payment_percentage;
        var loan_amount = price - down_payment;

        // Set interest rate based on credit
        if (credit == 'rebuild') {
            interest_rate = 11 / 100;
        } else if (credit == 'fair') {
            interest_rate = 6.85 / 100;
        } else if (credit == 'good') {
            interest_rate = 5.85 / 100;
        } else if (credit == 'excellent') {
            interest_rate = 4 / 100;
        }

        var months = calculateMonthValue; // Assuming a 72-month (6-year) loan term
        var monthly_interest_rate = interest_rate / 12;
        // Calculate price based on monthly payment
        var price = (monthly_payment_input * ((Math.pow(1 + monthly_interest_rate, months) - 1) / (monthly_interest_rate * Math.pow(1 + monthly_interest_rate, months)))).toFixed(2);
        // Calculate down payment
        var down_payment = price * down_payment_percentage;
        var formattedPrice = numberWithCommas(Math.floor(price));

        $('#monthlyBudget').val(monthly_payment_input);
        $('#hiddenTotalLoanAmount').val(formattedPrice);
        $('#totalLoanAmount').html(' $' + (formattedPrice));
    }

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    // formattedPrice = numberWithCommas(price);
})
 

// exclusive exclusive exclusive exclusive exclusive  exclusive exclusive exclusive exclusive

// new calculator logic here  and worked 
// $(document).ready(function () {
//     exclusive_calculator();
//     $(document).on('input change click', '#credit_calculator, #price_calculator, #calculator_interest, #calculator_downpayment, #trade_in_value, .calculate_month.active', function () {
//         exclusive_calculator();
//     });

//     function exclusive_calculator(){
//         var cal_interest_rate = $('#credit_calculator').val();
//         $('#calculator_interest').val(cal_interest_rate);
//         // alert(cal_interest_rate)
//         // var cal_interest_rate;

//         // // Set the correct interest rate based on credit score
//         // if (cal_credit == 'rebuild') {
//         //     cal_interest_rate = 18;
//         //     $('#calculator_interest').val(18);
//         // } else if (cal_credit == 'fair') {
//         //     cal_interest_rate = 12;
//         //     $('#calculator_interest').val(12);
//         // } else if (cal_credit == 'good') {
//         //     cal_interest_rate = 9;
//         //     $('#calculator_interest').val(9);
//         // } else if (cal_credit == 'excellent') {
//         //     cal_interest_rate = 8;
//         //     $('#calculator_interest').val(8);
//         // }

//         // Ensure all inputs are treated as numbers
//         var price_calculator = parseFloat($('#price_calculator').val()) || 0;
//         var price_down_pay_calculator = parseFloat($('#calculator_downpayment').val()) || 0;
//         var price_trade_in_value_calculator = parseFloat($('#trade_in_value').val()) || 0;
//         var price_calculator_month = parseInt($('.calculate_month.active').val()) || 0;
//         var salesTaxRate = $('#sales_tax_rate').val(); // Assuming the sales tax is fixed at 8%

//         // Step 1: Calculate total loan amount (P)
//         const salesTax = price_calculator * (salesTaxRate / 100);
//         const loanAmount = price_calculator + salesTax - (price_down_pay_calculator + price_trade_in_value_calculator);

//         // alert(price_trade_in_value_calculator)
//         // Step 2: Calculate monthly interest rate (r)
//         const monthlyInterestRate = (parseFloat(cal_interest_rate) / 100) / 12;

//         // Step 3: Calculate the monthly payment (M)
//         if (price_calculator_month > 0 && monthlyInterestRate > 0) {
//             const numerator = loanAmount * monthlyInterestRate * Math.pow(1 + monthlyInterestRate, price_calculator_month);
//             const denominator = Math.pow(1 + monthlyInterestRate, price_calculator_month) - 1;
//             var monthlyPayment = numerator / denominator;
//         } else {
//             var monthlyPayment = 0; // Prevent invalid calculations
//         }

//         // Display loan amount and monthly payment
//         var calculator_loan_amount = price_calculator + salesTax - price_down_pay_calculator;
//         var calculator_loan_amount_element = "<small>Total Loan Amount : " + numberWithCommas(calculator_loan_amount.toFixed(2)) + "</small>";
//         $('#sales_tax').val(salesTax.toFixed(0));
//         $('#calculator_loan_amount').html(calculator_loan_amount_element);

//         // $('#calculator_monthly_pay').html(monthlyPayment.toFixed(0));
//         math_monthly_pay = Math.floor(monthlyPayment)
//         $('#calculator_monthly_pay').html(math_monthly_pay);
//         $('#mobile_monthly_pay').html('$'+math_monthly_pay+'/ mo*');
//         $('#strong_monthly_pay').html('$'+math_monthly_pay+'/ mo*');
//     }
//     function numberWithCommas(x) {
//         return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
//     }
// });

