<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix My Community</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 30px;
        }

        body {
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            color: white;
            background: linear-gradient(135deg, #063f32, #087f5b, #21b58a);
            overflow-x: hidden;
            position: relative;
        }

        body::before,
        body::after {
            content: "";
            position: fixed;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            filter: blur(90px);
            opacity: .65;
            z-index: 0;
            pointer-events: none;
        }

        body::before {
            background: #5fffd2;
            top: -130px;
            left: 15%;
        }

        body::after {
            background: #a4ff82;
            right: -120px;
            bottom: -120px;
        }

        .glow {
            position: fixed;
            width: 300px;
            height: 300px;
            background: #16c79a;
            filter: blur(100px);
            opacity: .55;
            left: 40%;
            bottom: 5%;
            z-index: 0;
            pointer-events: none;
        }

        /* NAVIGATION */

        nav {
            position: relative;
            z-index: 2;
            max-width: 1250px;
            margin: auto;
            padding: 24px 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 27px;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .logo span {
            color: #a9ffda;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 30px;
            padding: 12px 24px;
            border: 1px solid rgba(255,255,255,.25);
            background: rgba(255,255,255,.13);
            backdrop-filter: blur(15px);
            border-radius: 40px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            transition: .2s;
        }

        .nav-links a:hover {
            color: #a9ffda;
        }

        /* MAIN LANDING PAGE */

        .main {
            position: relative;
            z-index: 1;
            max-width: 1250px;
            min-height: calc(100vh - 90px);
            margin: auto;
            padding: 10px 25px 35px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            align-items: center;
        }

        .glass {
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.3);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
            border-radius: 25px;
            box-shadow: 0 15px 45px rgba(0,0,0,.12);
        }

        /* INTRODUCTION */

        .intro {
            min-height: 480px;
            padding: 55px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .eyebrow {
            display: inline-block;
            width: fit-content;
            padding: 8px 15px;
            border-radius: 30px;
            background: rgba(255,255,255,.17);
            font-size: 11px;
            letter-spacing: 1px;
            margin-bottom: 25px;
        }

        h1 {
            font-size: clamp(36px, 4vw, 54px);
            line-height: 1.15;
            font-weight: 800;
            margin-bottom: 20px;
        }

        h1 span {
            color: #a9ffda;
        }

        .intro p {
            color: rgba(255,255,255,.85);
            font-size: 13px;
            line-height: 2;
            max-width: 440px;
        }

        .features {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 30px;
        }

        .feature {
            padding: 9px 13px;
            border-radius: 30px;
            background: rgba(255,255,255,.13);
            border: 1px solid rgba(255,255,255,.2);
            font-size: 10px;
        }

        /* ACCOUNT PANEL */

        .account {
            min-height: 480px;
            padding: 50px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .account h2 {
            font-size: 30px;
            margin-bottom: 10px;
        }

        .account > p {
            font-size: 12px;
            color: rgba(255,255,255,.8);
            margin-bottom: 35px;
        }

        .account-option {
            display: block;
            text-align: left;
            padding: 20px;
            margin-bottom: 17px;
            border: 1px solid rgba(255,255,255,.4);
            border-radius: 15px;
            background: rgba(255,255,255,.1);
            text-decoration: none;
            color: white;
            transition: .25s;
        }

        .account-option:hover {
            background: rgba(255,255,255,.23);
            transform: translateY(-3px);
        }

        .account-option strong {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .account-option span {
            display: block;
            font-size: 11px;
            color: rgba(255,255,255,.75);
        }

        .primary {
            background: #e0fff0;
            color: #086348;
            border-color: #e0fff0;
        }

        .primary span {
            color: #377d63;
        }

        .primary:hover {
            background: white;
        }

        .account small {
            font-size: 10px;
            color: rgba(255,255,255,.7);
            margin-top: 15px;
        }

        /* FEATURES SECTION */

        .features-section {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1250px;
            margin: 40px auto 70px;
            padding: 60px 35px;
            text-align: center;
        }

        .features-section h2 {
            font-size: 32px;
            margin-bottom: 12px;
        }

        .features-section > p {
            color: rgba(255,255,255,.8);
            font-size: 13px;
            margin-bottom: 35px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }

        .feature-card {
            padding: 35px 28px;
            border-radius: 22px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.3);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            text-align: left;
            box-shadow: 0 12px 30px rgba(0,0,0,.08);
            transition: .3s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            background: rgba(255,255,255,.21);
        }

        .feature-card .symbol {
            font-size: 32px;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            margin-bottom: 12px;
            font-size: 17px;
            color: white;
        }

        .feature-card p {
            font-size: 12px;
            line-height: 1.8;
            color: rgba(255,255,255,.8);
        }

        /* FOOTER */

        footer {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 20px;
            font-size: 10px;
            color: rgba(255,255,255,.65);
        }

        /* RESPONSIVE DESIGN */

        @media(max-width: 800px) {
            nav {
                padding: 20px;
            }

            .nav-links {
                gap: 15px;
                padding: 10px 15px;
            }

            .main {
                grid-template-columns: 1fr;
                padding: 15px 20px 30px;
            }

            .intro,
            .account {
                min-height: auto;
                padding: 40px 30px;
            }

            h1 {
                font-size: 40px;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width: 480px) {
            .logo {
                font-size: 20px;
            }

            .nav-links {
                gap: 10px;
            }

            .nav-links a {
                font-size: 10px;
            }

            .features-section {
                padding: 40px 20px;
            }

            .features-section h2 {
                font-size: 26px;
            }
        }

    </style>
</head>

<body>

    <div class="glow"></div>

    <!-- NAVIGATION -->
    <nav>
        <div class="logo">FIX MY <span>COMMUNITY.</span></div>

        <div class="nav-links">
            <a href="{{ url('/') }}">Home</a>
            <a href="#features">Features</a>
            <a href="#about">About</a>
        </div>
    </nav>

    <!-- MAIN SECTION -->
    <main class="main">

        <!-- INTRODUCTION -->
        <section class="glass intro" id="about">

            <div class="eyebrow">YOUR COMMUNITY, YOUR VOICE</div>

            <h1>
                Let's build a <span>better</span> community together.
            </h1>

            <p>
                Every concern deserves attention. Report community
                issues, monitor their progress, and stay connected
                with the people working toward a safer and better
                neighborhood.
            </p>

            <div class="features">
                <div class="feature">Report Local Issues</div>
                <div class="feature">Track Report Status</div>
                <div class="feature">Community Monitoring</div>
            </div>

        </section>

        <!-- ACCOUNT PANEL -->
        <section class="glass account">

            <h2>Welcome!</h2>

            <p>
                Be part of the change. Get started with
                Fix My Community.
            </p>

            <a href="{{ route('register') }}"
               class="account-option primary">

                <strong>Create an Account →</strong>
                <span>Join your community and start reporting issues.</span>

            </a>

            <a href="{{ route('login') }}"
               class="account-option">

                <strong>Sign In →</strong>
                <span>Already have an account? Continue here.</span>

            </a>

            <small>
                Together, we can make every community better.
            </small>

        </section>

    </main>

    <!-- FEATURES SECTION -->
    <section class="features-section" id="features">

        <h2>Everything Your Community Needs</h2>

        <p>
            Simple tools to make reporting and monitoring
            community concerns easier.
        </p>

        <div class="feature-grid">

            <div class="feature-card">
                <div class="symbol">📋</div>

                <h3>Report Community Issues</h3>

                <p>
                    Easily submit concerns such as damaged roads,
                    waste problems, drainage issues, and other
                    community-related concerns.
                </p>
            </div>

            <div class="feature-card">
                <div class="symbol">📍</div>

                <h3>Track Report Progress</h3>

                <p>
                    Stay updated with your submitted reports and
                    monitor their progress from submission
                    to resolution.
                </p>
            </div>

            <div class="feature-card">
                <div class="symbol">🤝</div>

                <h3>Community Monitoring</h3>

                <p>
                    Help residents and barangay personnel work
                    together through transparent reporting
                    and organized issue management.
                </p>
            </div>

        </div>

    </section>

    <!-- FOOTER -->
    <footer>
        © {{ date('Y') }} Fix My Community. All rights reserved.
    </footer>

</body>
</html>
