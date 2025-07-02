<style>
        #merging-tooltips .noUi-tooltip {
            display: none;
        }

        #merging-tooltips .noUi-active .noUi-tooltip {
            display: block;
        }

        .noUi-connect,
        .noUi-origin {
            will-change: transform;
            position: absolute;
            z-index: 1;
            top: 0;
            left: 0;
            height: 100%;
            width: 96%;
            -ms-transform-origin: 0 0;
            -webkit-transform-origin: 0 0;
            -webkit-transform-style: preserve-3d;
            transform-origin: 0 0;
            transform-style: flat;
        }

        .c-1-color {
            background: red;
        }

        .c-2-color {
            background: yellow;
        }

        .c-3-color {
            background: green;
        }

        .c-4-color {
            background: blue;
        }

        .c-5-color {
            background: purple;
        }


        .item-card9-icons {
            position: absolute;
            top: 32px;
            right: 133px;
            z-index: 98;
        }

        .imageLazy {
            background-color: #e41414;
            height: 250px;
            width: 100%;
            border: 1px solid black;
        }

        /* toggle new/used css */

        .toggle-button-cover {
            display: table-cell;
            position: relative;
            width: 10px;
            ;
            height: 80px;
            box-sizing: border-box;
        }

        .button-cover {
            height: 100px;
            margin: 20px;
            background-color: #fff;
            box-shadow: 0 10px 20px -8px #c5d6d6;
            border-radius: 4px;
        }

        .button-cover,
        .knobs,
        .layer {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }

        .button {
            position: relative;

            width: 234px;
            height: 45px;
            margin-left: -25px;
            overflow: hidden;
        }

        .button.r,
        .button.r .layer {
            border-radius: 100px;
        }

        .button.b2 {
            border-radius: 2px;
        }

        .checkbox {
            position: relative;
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
            opacity: 0;
            cursor: pointer;
            z-index: 3;

        }

        .knobs {
            z-index: 2;
        }

        .layer {
            width: 100%;
            background-color: #ebf7fc;
            transition: 0.3s ease all;
            z-index: 1;
        }

        /* Button 10 */
        #button-10 .knobs:before,
        #button-10 .knobs:after,
        #button-10 .knobs span {
            position: absolute;
            top: 4px;
            width: 120px;
            height: 50px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;

            padding: 12px 5px;
            border-radius: 2px;
            transition: 0.3s ease all;
        }

        #button-10 .knobs:before {
            content: "";
            width: 112px;
            left: 0px;
            top: 0px;
            background-color: darkcyan;
            ;
        }

        #button-10 .knobs:after {
            content: "New Car";
            right: -10px;
            color: #4e4e4e;

        }

        #button-10 .knobs span {
            display: inline-block;
            left: 0px;
            color: #fff;
            z-index: 1;
        }

        #button-10 .checkbox:checked+.knobs span {
            color: #4e4e4e;
        }

        #button-10 .checkbox:checked+.knobs:before {
            left: 122px;
            background: darkcyan;
        }

        #button-10 .checkbox:checked+.knobs:after {
            color: #fff;
        }

        #button-10 .checkbox:checked~.layer {
            background-color: #ebf7fc;
        }

        .input-container {
            position: relative;
            display: inline-block;
        }

        .dollar-sign {
            position: absolute;
            left: 10px;
            top: 18%;
            transform: translateY(-18%);
            color: #8b8a8a;
            font-size: 16px;
        }

        .dollar-sign-modal {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-18%);
            color: #8b8a8a;
            font-size: 16px;
        }

        .common_calculator-modal {

            font-size: 13px;
            padding-left: 23px;


        }

        .dollar-input {

            font-size: 13px;
            padding-left: 23px;


        }

        .dollar-input::placeholder {

            padding-left: 0px;


        }

        .accordion-button:not(.collapsed) {
            background: none !important;
            color: none !important;
            box-shadow: none !important;
        }

        .accordion-button:focus {
            border-color: none !important;
        }

        .nav-tabs .nav-link:hover:not(.disabled),
        .nav-tabs .nav-link.active {
            background: white !important;
            border-top: 0.03px solid gray;
            border-left: 0.03px solid gray;
            border-right: 0.03px solid gray;
            color: black;
            border-top-right-radius: 4px;
            border-top-left-radius: 4px;
            border-bottom-right-radius: 1px;
            border-bottom-left-radius: 1px;
            margin-top: -2px;
        }


        .custom-row {
            display: flex;
            width: 100%;
            flex-wrap: wrap;
            gap: 10px;
        }

        .custom-col {
            flex: 1 1 calc(50% - 5px);
            padding: 15px;
            box-sizing: border-box;
        }



        .sticky-button {
        position: fixed;     /* Adjust bottom position as needed */
        z-index: 999;      /* Ensure button stays on top */
        background-color: darkcyan;
        color: white;
        font-size: 16px;
        width: 14.5%;        /* Ensures full width */
        text-align: center;
        bottom: 5px;
        padding: 10px 0px;
        /* display: none; */
        }

        #searchSecondFilterModelInput, #zipCodeInput {
            padding: 5px;
            width: 100%; /* Ensures inputs take up full width of their container */
            box-sizing: border-box; /* Ensures padding is included in the width */
        }



        #loading {
            text-align: center;
            background: url("{{ asset('frontend/assets/images') }}/loader.gif") no-repeat center;
            height: 150px;
        }


        .overlay-modal {
            position: fixed;
            width: 100%;
            height: 100%;
            display: block;
            background-color: rgba(218, 233, 232, 0.7);
            z-index: 99;
            content: "";
            left: 0;
            top: 0;
        }
    </style>