@props([
    'title' => 'Coming Soon!',
    'message' => 'Please check back later!',
    'countdownDays' => 7,
    'showGif' => false,
    'showButton' => false,
    'showCount' => false
])

{{-- @dump($attributes->getAttributes()) --}}
<!-- Coming Soon Section -->
<section class="sptb">
    <div class="container">
        <div class="row">
            <div class="col-12">
                {{-- <h2 class="new-tit mb-4">Latest News</h2> --}}

                <!-- Coming Soon Section -->
                <div class="coming-soon-container text-center py-5">
                    <div class="coming-soon-card card border-0 bg-light">
                        <div class="card-body p-4 p-md-5">
                            @if($showGif)
                                <img src="https://media.giphy.com/media/3o7TKUM3IgJBX2as9O/giphy.gif" alt="Coming Soon" class="coming-soon-gif mb-4" style="max-width: 200px;">
                            @endif

                            <h3 class="coming-soon-title mb-3">{{ $title }}</h3>
                            {{--<p class="coming-soon-text mb-4">{{ $message }}</p>--}}
                            @if($showCount)
                            <div class="countdown-container mb-4">
                                <div class="countdown-box">
                                    <span class="countdown-number" id="days">00</span>
                                    <span class="countdown-label">Days</span>
                                </div>
                                <div class="countdown-box">
                                    <span class="countdown-number" id="hours">00</span>
                                    <span class="countdown-label">Hours</span>
                                </div>
                                <div class="countdown-box">
                                    <span class="countdown-number" id="minutes">00</span>
                                    <span class="countdown-label">Minutes</span>
                                </div>
                                <div class="countdown-box">
                                    <span class="countdown-number" id="seconds">00</span>
                                    <span class="countdown-label">Seconds</span>
                                </div>
                            </div>
                            @endif
                            @if($showButton)
                                <button class="btn btn-primary notify-btn">Notify Me When Live</button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Comment out the card section below when using coming soon -->
                <!--
                <div class="row">
                    [Your 6 card content here]
                </div>
                <div class="col-12 text-end mt-3">
                    <a href="#" style="border-bottom:1px solid black"><strong>View more opinion</strong></a>
                </div>
                -->
            </div>
        </div>
    </div>
</section>

<style>
    .sptb {
        padding: 60px 0;
    }
    .new-tit {
        font-size: 2rem;
        font-weight: 700;
        color: #333;
    }

    /* Coming Soon Styles */
    .coming-soon-container {
        max-width: 800px;
        margin: 0 auto;
    }
    .coming-soon-card {
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }
    .coming-soon-title {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 1rem;
    }
    .coming-soon-text {
        font-size: 1.1rem;
        color: #7f8c8d;
        max-width: 600px;
        margin: 0 auto;
    }
    .coming-soon-gif {
        border-radius: 50%;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .countdown-container {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin: 30px 0;
    }
    .countdown-box {
        background: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        text-align: center;
        min-width: 80px;
    }
    .countdown-number {
        font-size: 1.8rem;
        font-weight: 700;
        color: #06a3da;
        display: block;
    }
    .countdown-label {
        font-size: 0.8rem;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .notify-btn {
        background: #06a3da;
        border: none;
        padding: 12px 30px;
        font-weight: 600;
        letter-spacing: 1px;
        border-radius: 50px;
        transition: all 0.3s ease;
    }
    .notify-btn:hover {
        background: #0489b9;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(6, 163, 218, 0.3);
    }
</style>

<script>
    // Simple countdown timer (set to 7 days from now)
    const countDownDate = new Date();
    countDownDate.setDate(countDownDate.getDate() + {{ $countdownDays }});

    const x = setInterval(function() {
        const now = new Date().getTime();
        const distance = countDownDate - now;

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("days").innerHTML = days.toString().padStart(2, '0');
        document.getElementById("hours").innerHTML = hours.toString().padStart(2, '0');
        document.getElementById("minutes").innerHTML = minutes.toString().padStart(2, '0');
        document.getElementById("seconds").innerHTML = seconds.toString().padStart(2, '0');

        if (distance < 0) {
            clearInterval(x);
            document.getElementById("days").innerHTML = "00";
            document.getElementById("hours").innerHTML = "00";
            document.getElementById("minutes").innerHTML = "00";
            document.getElementById("seconds").innerHTML = "00";
        }
    }, 1000);
</script>
