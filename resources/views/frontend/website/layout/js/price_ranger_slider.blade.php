<script>
        var nonLinearSlider = document.getElementById('price-ranger');
        var input0 = document.getElementById('min-price-ranger');
        var input1 = document.getElementById('max-price-ranger');
        var inputs = [input0, input1];

        var minValue; // Declare minValue variable
        var maxValue; // Declare maxValue variable

        noUiSlider.create(nonLinearSlider, {
            connect: true,
            tooltips: true,
            behaviour: 'tap',
            start: [0, 100000],
            range: {
                // Starting at 500, step the value by 500,
                // until 4000 is reached. From there, step by 1000.
                'min': [0],
                // '10%': [500, 500],
                // '50%': [4000, 1000],
                'max': [100000]
            },
            format: {
        to: function (value) {
            return Math.round(value); // Round the value to remove the decimal part
        },
        from: function (value) {
            return value;
        }
    }
        });

        nonLinearSlider.noUiSlider.on('update', function (values, handle) {
            inputs[handle].value = values[handle];
        });





        nonLinearSlider.noUiSlider.on('change', function (values, handle) {
            inputs[handle].value = values[handle];

            // Store the updated values in the variables
            minValue = parseFloat(input0.value);
            maxValue = parseFloat(input1.value);

            // Display the values in the console for testing
            console.log('Min Value:', minValue, 'Max Value:', maxValue);
            mergeTooltips(nonLinearSlider, 1000, ' - ');
        });
</script>



<script>
function mergeTooltips(slider, threshold, separator) {

var textIsRtl = getComputedStyle(slider).direction === 'rtl';
var isRtl = slider.noUiSlider.options.direction === 'rtl';
var isVertical = slider.noUiSlider.options.orientation === 'vertical';
var tooltips = slider.noUiSlider.getTooltips();
var origins = slider.noUiSlider.getOrigins();

// Move tooltips into the origin element. The default stylesheet handles this.
tooltips.forEach(function (tooltip, index) {
    if (tooltip) {
        origins[index].appendChild(tooltip);
    }
});

slider.noUiSlider.on('update', function (values, handle, unencoded, tap, positions) {

    var pools = [[]];
    var poolPositions = [[]];
    var poolValues = [[]];
    var atPool = 0;

    // Assign the first tooltip to the first pool, if the tooltip is configured
    if (tooltips[0]) {
        pools[0][0] = 0;
        poolPositions[0][0] = positions[0];
        poolValues[0][0] = values[0];
    }

    for (var i = 1; i < positions.length; i++) {
        if (!tooltips[i] || (positions[i] - positions[i - 1]) > threshold) {
            atPool++;
            pools[atPool] = [];
            poolValues[atPool] = [];
            poolPositions[atPool] = [];
        }

        if (tooltips[i]) {
            pools[atPool].push(i);
            poolValues[atPool].push(values[i]);
            poolPositions[atPool].push(positions[i]);
        }
    }

    pools.forEach(function (pool, poolIndex) {
        var handlesInPool = pool.length;

        for (var j = 0; j < handlesInPool; j++) {
            var handleNumber = pool[j];

            if (j === handlesInPool - 1) {
                var offset = 0;

                poolPositions[poolIndex].forEach(function (value) {
                    offset += 1000 - value;
                });

                var direction = isVertical ? 'bottom' : 'right';
                var last = isRtl ? 0 : handlesInPool - 1;
                var lastOffset = 1000 - poolPositions[poolIndex][last];
                offset = (textIsRtl && !isVertical ? 100 : 0) + (offset / handlesInPool) - lastOffset;

                // Center this tooltip over the affected handles
                tooltips[handleNumber].innerHTML = poolValues[poolIndex].join(separator);
                tooltips[handleNumber].style.display = 'block';
                tooltips[handleNumber].style[direction] = offset + '%';
            } else {
                // Hide this tooltip
                tooltips[handleNumber].style.display = 'none';
            }
        }
    });
});
}
</script>

<script>
        var mobileNonLinearSlider = document.getElementById('mobile-price-ranger');
        var mobileInput0 = document.getElementById('mobile-min-price-ranger');
        var mobileInput1 = document.getElementById('mobile-max-price-ranger');
        var mobileInputs = [input0, input1];

        var minValue; // Declare minValue variable
        var maxValue; // Declare maxValue variable

        noUiSlider.create(mobileNonLinearSlider, {
            connect: true,
            tooltips: true,
            behaviour: 'tap',
            start: [0, 100000],
            range: {
                // Starting at 500, step the value by 500,
                // until 4000 is reached. From there, step by 1000.
                'min': [0],
                // '10%': [500, 500],
                // '50%': [4000, 1000],
                'max': [100000]
            },
            format: {
        to: function (value) {
            return Math.round(value); // Round the value to remove the decimal part
        },
        from: function (value) {
            return value;
        }
    }
        });

        mobileNonLinearSlider.noUiSlider.on('update', function (values, handle) {
            mobileInputs[handle].value = values[handle];
        });





        mobileNonLinearSlider.noUiSlider.on('change', function (values, handle) {
            mobileInputs[handle].value = values[handle];

            // Store the updated values in the variables
            minValue = parseFloat(mobileInput0.value);
            maxValue = parseFloat(mobileInput1.value);

            // Display the values in the console for testing
            console.log('Min Value:', minValue, 'Max Value:', maxValue);
            mergeTooltips(mobileNonLinearSlider, 1000, ' - ');
        });
</script>

