<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pricing Plans</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/price.css') }}">
</head>
<body>

    <section class="hero">
        <span class="badge">Transparent pricing · One-time setup · No hidden fees</span>
        <h1>Plans Designed for School Networks</h1>
        <p>
            Choose a ready-to-launch package or design a custom partnership with School Experts.
        </p>
    </section>

    
    <section class="pricing-section">
        <div class="pricing-card">

            <!-- LEFT -->
            <div class="features">
                <h3>Included Features</h3>
                <ul>
                    <li>System activation & launch within 10 working days (max)</li>
                    <li>Dedicated Parent & Supervisor applications</li>
                    <li>Ready-to-use reports & export dashboards</li>
                    <li>Full mobile support from 8 AM to 6 PM</li>
                </ul>
            </div>

            <!-- RIGHT -->
            <div class="discounts">
                <h3>Discount Levels</h3>
                <ul>
                    <li><span>0%</span> Monthly payment</li>
                    <li><span>5%</span> Quarterly payment (every 3 months)</li>
                    <li><span>10%</span> Semi-annual payment (every 6 months)</li>
                    <li><span>15%</span> Full annual contract</li>
                </ul>
            </div>

        </div>

       
        <div class="buttons">
            <button class="outline">For Schools & Institutions</button>
            <button class="filled">Packages</button>
        </div>
    </section>

    <section class="pricing-header">
    <span class="new-badge">NEW · Direct for Families</span>
    <h1>Simple Pricing for Families</h1>
    <p>Choose the plan that fits your child’s school needs. Change anytime.</p>
</section>

<!-- ===== CARDS ===== -->
<section class="pricing-cards">

    <!-- SMART -->
    <div class="card">
        <div class="icon">✨</div>
        <h2>Smart Plan</h2>
        <h3>450</h3>
        <p class="price">EGP / month / per child</p>
        <span class="discount">10% discount for second child</span>

        <ul>
            <li>Live GPS tracking on map</li>
            <li>Pickup & drop-off notifications</li>
            <li>Direct chat with driver</li>
            <li>Monthly trip reports</li>
            <li>Daily school-time support</li>
        </ul>

        <button>Get Started</button>
    </div>

    <!-- PREMIUM -->
    <div class="card featured">
        <span class="popular">Most Popular</span>
        <div class="icon">⭐</div>
        <h2>Premium Plan</h2>
        <h3>650</h3>
        <p class="price">EGP / month / per child</p>
        <span class="discount">15% quarterly discount</span>

        <ul>
            <li>All Smart Plan features</li>
            <li>Priority customer support</li>
            <li>Arrival time sharing with family</li>
            <li>Unlimited parent accounts</li>
            <li>Driver rating & feedback</li>
            <li>Smart route optimization</li>
            <li>Quarterly safety review</li>
        </ul>

        <button>7 Days Free Trial</button>
    </div>

    <!-- VIP -->
    <div class="card">
        <div class="icon">👑</div>
        <h2>Private VIP</h2>
        <h3>950</h3>
        <p class="price">EGP / month / per child</p>
        <span class="discount">25% annual discount</span>

        <ul>
            <li>Private vehicle (6–8 children)</li>
            <li>Dedicated driver</li>
            <li>Door-to-door escort service</li>
            <li>Home to classroom supervision</li>
            <li>Live monitoring all hours</li>
            <li>Phone & WhatsApp support</li>
        </ul>

        <button>Contact Advisor</button>
    </div>

</section>
<div class="page">

    <!-- Offers -->
    <div class="offers">
        <h3>Family Offers</h3>
        <ul>
            <li>7-day free trial for every family</li>
            <li>20% discount for 3 children or more</li>
            <li>25% discount on yearly payment</li>
        </ul>
    </div>

    <!-- Calculator Card -->
    <div class="calculator-card">

        <h1>Family Savings Calculator</h1>
        <p class="desc">
            See how much you can save monthly by switching to our school plans.
        </p>

        <div class="calculator">

            <!-- Result -->
            <div class="result-box">
                <h2>Estimated Monthly Savings</h2>

                <div class="big-number" id="saving">0 EGP</div>

                <div class="lines">
                    <div>
                        <span>Current Monthly Cost</span>
                        <strong id="currentTotal">0</strong> EGP
                    </div>
                    <div>
                        <span>School Monthly Cost</span>
                        <strong id="schoolTotal">0</strong> EGP
                    </div>
                </div>

                <ul class="features">
                    <li>Dedicated family support</li>
                    <li>Real-time tracking</li>
                    <li>Cancel anytime within 48 hours</li>
                </ul>
            </div>

            <!-- Controls -->
            <div class="controls">

                <div class="field">
                    <label>Number of Children</label>
                    <input type="number" id="children" min="1" max="6" value="1">
                </div>

                <div class="field">
                    <label>Current Cost per Child (Monthly)</label>
                    <input type="number" id="currentCost" value="900">
                </div>

                <div class="field">
                    <label>Select Plan</label>

                    <label class="option">
                        <input type="radio" name="plan" value="450" checked>
                        <span>Basic Plan</span>
                        <strong>450 EGP</strong>
                    </label>

                    <label class="option">
                        <input type="radio" name="plan" value="650">
                        <span>Premium Plan</span>
                        <strong>650 EGP</strong>
                    </label>

                    <label class="option">
                        <input type="radio" name="plan" value="950">
                        <span>VIP Plan</span>
                        <strong>950 EGP</strong>
                    </label>
                </div>

                <div class="field">
                    <label>Payment Method</label>

                    <div class="payment">
                        <label>
                            <input type="radio" name="payment" value="monthly" checked>
                            Monthly
                        </label>
                        <label>
                            <input type="radio" name="payment" value="quarterly">
                            Quarterly
                        </label>
                        <label>
                            <input type="radio" name="payment" value="yearly">
                            Yearly
                        </label>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>
<script src="{{ asset('js/price.js') }}"></script>
</body>
</html>
