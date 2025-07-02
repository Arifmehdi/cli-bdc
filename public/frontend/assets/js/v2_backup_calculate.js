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
