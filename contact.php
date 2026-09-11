<?php
session_start();
include('config/db_connection.php');
include('config/mail_config.php');

$contact_success = "";
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['contact_submit'])){
    $c_name = $_POST['c_name'];
    $c_email = $_POST['c_email'];
    $c_subject = $_POST['c_subject'] != '' ? $_POST['c_subject'] : 'General Inquiry';
    $c_message = $_POST['c_message'];

    if(send_contact_email($c_name, $c_email, $c_subject, $c_message)){
        $contact_success = "✅ Shukriya! Aapka message bhej diya gaya hai, hum jald contact karenge.";
    } else {
        $contact_success = "❌ Message bhejne mein masla hua, dobara try karein.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Hira Property</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --navy:#1B2340;
            --navy-deep:#12162A;
            --paper:#FAF8F4;
            --panel:#FFFFFF;
            --ember:#E8622A;
            --ember-dark:#c94d1a;
            --text:#221F1C;
            --muted:#726C63;
            --line:#EDE8DE;
            --success:#28a745;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: var(--paper); color: var(--text); }

        /* UTILITY BAR */
        .utility-bar{ background: var(--navy-deep); color: #C9CCDA; font-size: 12.5px; }
        .utility-bar .inner{
            max-width: 1180px; margin: 0 auto;
            display: flex; justify-content: space-between; align-items: center; padding: 9px 40px;
        }
        .utility-bar .contacts{ display: flex; gap: 24px; }
        .utility-bar a{ text-decoration: none; color: inherit; }
        .utility-bar a:hover{ color: #fff; }

        /* NAVBAR */
        .navbar {
            position: sticky; top: 0; left: 0; right: 0;
            z-index: 100; background: #fff;
            border-bottom: 1px solid var(--line);
        }
        .navbar .inner{
            max-width: 1180px; margin: 0 auto;
            display: flex; justify-content: space-between;
            align-items: center; padding: 18px 40px;
        }
        .nav-logo {
            font-family: 'Playfair Display', serif;
            font-size: 20px; font-weight: 700; color: var(--navy);
            display: flex; align-items: center; gap: 9px; text-decoration: none;
        }
        .nav-logo span { color: var(--ember); }
        .nav-links { display: flex; gap: 32px; list-style: none; }
        .nav-links a {
            color: var(--navy); text-decoration: none;
            font-size: 14px; font-weight: 500; transition: color 0.2s;
            padding-bottom: 4px; border-bottom: 2px solid transparent;
        }
        .nav-links a:hover { color: var(--ember); border-color: var(--ember); }
        .nav-btns { display: flex; gap: 12px; align-items: center; }
        .btn-login {
            color: var(--navy); text-decoration: none;
            font-size: 13.5px; font-weight: 500; padding: 9px 20px;
            border: 1px solid var(--line); border-radius: 6px; transition: all 0.2s;
        }
        .btn-login:hover { border-color: var(--ember); color: var(--ember); }
        .btn-signup {
            color: #fff; text-decoration: none;
            font-size: 13.5px; font-weight: 600; padding: 10px 22px;
            background: var(--ember); border-radius: 6px; transition: all 0.2s;
        }
        .btn-signup:hover { background: var(--ember-dark); }

        /* HERO */
        .hero {
            position: relative; min-height: 620px;
            display: flex; align-items: center; justify-content: center;
            text-align: center; overflow: hidden;
        }
        .hero-bg {
            position: absolute; inset: 0;
            background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1920&q=80');
            background-size: cover; background-position: center;
        }
        .hero-bg::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(180deg, rgba(18,22,42,0.45) 0%, rgba(18,22,42,0.82) 100%);
        }
        .hero-content {
            position: relative; z-index: 2;
            max-width: 800px; padding: 70px 20px 60px;
        }
        .hero-tag {
            display: inline-block;
            background: rgba(232,98,42,0.18); border: 1px solid rgba(232,98,42,0.5);
            color: #FBA97B; font-size: 11px; letter-spacing: 2px;
            text-transform: uppercase; padding: 6px 18px;
            border-radius: 20px; margin-bottom: 25px;
        }
        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 52px; line-height: 1.15; font-weight: 700;
            margin-bottom: 20px; color: #fff;
        }
        .hero h1 em { font-style: italic; color: var(--ember); }
        .hero p {
            font-size: 16px; color: #DCDFE8; line-height: 1.7;
            margin-bottom: 36px; max-width: 580px;
            margin-left: auto; margin-right: auto;
        }
        .hero-btns { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
        .hero-btn-primary {
            background: var(--ember); color: #fff; text-decoration: none;
            padding: 13px 32px; border-radius: 7px; font-size: 14px;
            font-weight: 600; transition: all 0.2s;
        }
        .hero-btn-primary:hover { background: var(--ember-dark); transform: translateY(-2px); }
        .hero-btn-secondary {
            background: rgba(255,255,255,0.1); color: #fff;
            text-decoration: none; padding: 13px 32px; border-radius: 7px;
            font-size: 14px; border: 1px solid rgba(255,255,255,0.25); transition: all 0.2s;
        }
        .hero-btn-secondary:hover { background: rgba(255,255,255,0.18); }

        /* SEARCH */
        .search-wrap {
            max-width: 900px; margin: -36px auto 0;
            position: relative; z-index: 10; padding: 0 40px;
        }
        .search-box {
            background: #fff; border-radius: 10px;
            padding: 16px; display: flex; gap: 10px; flex-wrap: wrap;
            box-shadow: 0 8px 32px rgba(27,35,64,0.14);
        }
        .search-box input, .search-box select {
            border: 1px solid var(--line); color: var(--text);
            padding: 11px 14px; border-radius: 7px;
            font-family: inherit; font-size: 13px; flex: 1; min-width: 140px;
            background: var(--paper);
        }
        .search-box button {
            background: var(--ember); color: #fff; border: none;
            padding: 11px 28px; border-radius: 7px;
            font-family: inherit; font-size: 13px; font-weight: 600;
            cursor: pointer; transition: background 0.2s; white-space: nowrap;
        }
        .search-box button:hover { background: var(--ember-dark); }

        /* STATS BAR */
        .stats-bar { background: var(--navy); margin-top: 50px; }
        .stats-bar .inner {
            max-width: 1180px; margin: 0 auto;
            display: flex; justify-content: center;
            gap: 80px; padding: 28px 40px;
        }
        .stat-item { text-align: center; }
        .stat-item .num {
            font-family: 'Playfair Display', serif;
            font-size: 30px; font-weight: 700; color: var(--ember);
        }
        .stat-item .lbl { font-size: 12px; color: #9297A8; margin-top: 4px; }

        /* SECTIONS */
        .section { padding: 80px 40px; }
        .inner-max { max-width: 1180px; margin: 0 auto; }
        .section-header { text-align: center; margin-bottom: 48px; }
        .section-tag {
            color: var(--ember); font-size: 11px; letter-spacing: 2px;
            text-transform: uppercase; margin-bottom: 12px; font-weight: 600;
        }
        .section-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 34px; color: var(--navy);
        }
        .section-header p { color: var(--muted); font-size: 14px; margin-top: 10px; }

        /* PROPERTIES GRID */
        .properties-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;
        }
        .prop-card {
            background: #fff; border-radius: 10px; border: 1px solid var(--line);
            overflow: hidden; text-decoration: none; color: inherit;
            display: block; transition: all 0.2s;
        }
        .prop-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(27,35,64,0.10); }
        .prop-img { width: 100%; height: 200px; object-fit: cover; display: block; }
        .prop-body { padding: 18px; }
        .prop-body h3 { font-size: 15px; font-weight: 600; color: var(--navy); margin-bottom: 6px; }
        .location { font-size: 12px; color: var(--muted); margin-bottom: 10px; }
        .price { font-size: 17px; font-weight: 700; color: var(--ember); }
        .price span { font-size: 11px; color: var(--muted); font-weight: 400; }
        .prop-badge {
            display: inline-block; background: rgba(40,167,69,0.12);
            color: var(--success); font-size: 10px;
            padding: 3px 10px; border-radius: 10px; margin-top: 8px;
        }
        .no-results{
            grid-column: 1 / -1;
            text-align: center;
            padding: 50px 20px;
            color: var(--muted);
            background: #fff;
            border-radius: 10px;
            border: 1px solid var(--line);
        }

        /* ABOUT SECTION */
        .about-band{ background: #fff; }
        .about-section { padding: 80px 0; display: flex; gap: 60px; align-items: center; }
        .about-text { flex: 1; }
        .about-text .tag { color: var(--ember); font-size: 11px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 15px; font-weight: 600; }
        .about-text h2 { font-family: 'Playfair Display', serif; font-size: 32px; color: var(--navy); line-height: 1.3; margin-bottom: 20px; }
        .about-text p { color: var(--muted); font-size: 14px; line-height: 1.8; margin-bottom: 15px; }
        .about-features { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 25px; }
        .feature-item { display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--text); }
        .feature-item .dot { width: 8px; height: 8px; background: var(--ember); border-radius: 50%; flex-shrink: 0; }
        .about-image { flex: 1; }
        .about-image img { width: 100%; border-radius: 10px; }

        /* TEAM SECTION */
        .team-section { padding: 60px 40px; background: var(--paper); text-align: center; }
        .team-grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 24px; max-width: 1000px; margin: 40px auto 0;
        }
        .team-card {
            background: #fff; padding: 28px 20px;
            border-radius: 10px; border: 1px solid var(--line);
        }
        .team-avatar {
            width: 60px; height: 60px; border-radius: 50%;
            background: linear-gradient(135deg, var(--ember), var(--ember-dark));
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; font-weight: 700; color: #fff;
            margin: 0 auto 14px;
        }
        .team-card h4 { font-size: 14px; font-weight: 600; color: var(--navy); margin-bottom: 5px; }
        .team-card .role { font-size: 11px; color: var(--ember); font-weight: 600; margin-bottom: 8px; }
        .team-card p { font-size: 12px; color: var(--muted); line-height: 1.6; }
        .team-email{
            display: inline-block; margin-top: 10px;
            font-size: 11px; color: var(--ember);
            text-decoration: none; font-weight: 600;
            word-break: break-all;
        }
        .team-email:hover{ text-decoration: underline; }

        /* HOW IT WORKS */
        .how-section { padding: 80px 40px; background: #fff; text-align: center; }
        .steps-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; max-width: 1000px; margin: 50px auto 0; }
        .step-item { padding: 30px 20px; background: var(--paper); border-radius: 10px; border: 1px solid var(--line); }
        .step-num {
            width: 44px; height: 44px; background: var(--ember); color: #fff; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Playfair Display', serif; font-size: 17px; font-weight: 700; margin: 0 auto 15px;
        }
        .step-item h4 { font-size: 14px; font-weight: 600; color: var(--navy); margin-bottom: 8px; }
        .step-item p { font-size: 12px; color: var(--muted); line-height: 1.6; }

        /* CONTACT SECTION */
        .contact-section { padding: 80px 40px; background: var(--paper); text-align: center; }
        .contact-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; max-width: 900px; margin: 50px auto 0; }
        .contact-card { background: #fff; padding: 30px; border-radius: 10px; border: 1px solid var(--line); }
        .contact-card .icon-box{
            width: 44px; height: 44px; margin: 0 auto 14px; background: var(--navy); border-radius: 8px;
            display: flex; align-items: center; justify-content: center; color: #fff; font-size: 18px;
        }
        .contact-card h4 { font-size: 14px; font-weight: 600; color: var(--navy); margin-bottom: 8px; }
        .contact-card p { font-size: 13px; color: var(--muted); line-height: 1.6; }
        .contact-form {
            max-width: 600px; margin: 40px auto 0;
            display: grid; gap: 14px;
        }
        .contact-form input,
        .contact-form textarea {
            width: 100%; padding: 12px 16px;
            border: 1px solid var(--line); border-radius: 8px;
            font-family: inherit; font-size: 13px; color: var(--text);
            background: #fff;
        }
        .contact-form textarea { height: 120px; resize: vertical; }
        .contact-form button {
            background: var(--ember); color: #fff; border: none;
            padding: 13px; border-radius: 8px;
            font-family: inherit; font-size: 14px; font-weight: 600;
            cursor: pointer; transition: background 0.2s;
        }
        .contact-form button:hover { background: var(--ember-dark); }
        .contact-msg{
            max-width: 600px; margin: 20px auto 0;
            padding: 13px 16px; border-radius: 8px;
            font-size: 13.5px; background: #fff;
            border: 1px solid var(--line);
        }

        /* FOOTER */
        .footer { background: var(--navy-deep); padding: 30px 40px; }
        .footer .inner{ max-width: 1180px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .footer p { font-size: 12px; color: #8A8EA1; }
        .footer .brand { font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: #fff; }
        .footer .brand span { color: var(--ember); }
        .footer-links { display: flex; gap: 20px; }
        .footer-links a { color: #8A8EA1; text-decoration: none; font-size: 12px; }
        .footer-links a:hover { color: var(--ember); }

        @media (max-width: 980px){
            .navbar .inner, .utility-bar .inner, .section, .how-section, .contact-section, .footer{ padding-left: 22px; padding-right: 22px; }
            .nav-links{ display: none; }
            .properties-grid, .steps-grid, .contact-grid, .team-grid{ grid-template-columns: 1fr; }
            .about-section{ flex-direction: column; padding-left: 22px; padding-right: 22px; }
            .hero h1{ font-size: 34px; }
            .stats-bar .inner{ gap: 30px; flex-wrap: wrap; }
            .search-wrap{ padding: 0 22px; margin-top: -30px; }
        }

        /* PAGE HEADER BANNER (sub-pages) */
        .page-header{
            background: var(--navy);
            padding: 55px 40px;
            text-align: center;
        }
        .page-header .tag{
            color: #FBA97B; font-size: 11px; letter-spacing: 2px;
            text-transform: uppercase; margin-bottom: 12px; font-weight: 600;
        }
        .page-header h1{
            font-family: 'Playfair Display', serif;
            font-size: 34px; color: #fff; font-weight: 700;
        }
        .page-header p{ color: #C9CCDA; font-size: 14px; margin-top: 10px; }

        /* Always-visible Back to Home button (never hidden, even on mobile) */
        .back-home-fixed{
            position: fixed; top: 14px; right: 20px;
            color: #fff; text-decoration: none;
            font-size: 12px; font-weight: 600; z-index: 999;
            background: var(--navy-deep);
            border: 1px solid rgba(255,255,255,0.15);
            padding: 7px 16px; border-radius: 20px; transition: all 0.2s;
        }
        .back-home-fixed:hover{ background: var(--ember); border-color: var(--ember); }
    </style>

</head>
<body>
    <a href="index.php" class="back-home-fixed">← Back to Home</a>
    <!-- UTILITY BAR -->
    <div class="utility-bar">
        <div class="inner">
            <div class="contacts">
                <a href="tel:+923331626222">📞 +92 333 1626222</a>
                <a href="mailto:info@hiraproperty.com">✉ info@hiraproperty.com</a>
            </div>
            <div>District Gujrat, Punjab</div>
        </div>
    </div>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="inner">
            <a class="nav-logo" href="index.php">
                <img src="assets/logo.png" alt="Hira Property" 
                     style="height:26px; width:26px; object-fit:cover; border-radius:6px;"
                     onerror="if(!this.dataset.fb){this.dataset.fb=1;this.src='assets/logo.png.jpeg';}else{this.style.display='none';}">
                Hira <span>Property</span>
            </a>
            <ul class="nav-links">
                <li><a href="index.php">🏠 Home</a></li>
                <li><a href="properties-list.php">Properties</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="how-it-works.php">How It Works</a></li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
            <div class="nav-btns">
                <a href="login.php" class="btn-login">Login</a>
                <a href="register.php" class="btn-signup">Sign Up</a>
            </div>
        </div>
    </nav>
    <!-- PAGE HEADER -->
    <div class="page-header">
        <div class="tag">Get In Touch</div>
        <h1>Contact Us</h1>
        <p>We're here to help — reach out anytime</p>
    </div>

    <!-- CONTACT US -->
    <section class="contact-section" id="contact">
        <div class="inner-max">
            <div class="contact-grid">
                <div class="contact-card">
                    <div class="icon-box">📍</div>
                    <h4>Location</h4>
                    <p>District Gujrat, Punjab, Pakistan</p>
                </div>
                <div class="contact-card">
                    <div class="icon-box">📞</div>
                    <h4>Phone</h4>
                    <p>+92 333 1626222<br>Mon–Sat 9AM–6PM</p>
                </div>
                <div class="contact-card">
                    <div class="icon-box">✉️</div>
                    <h4>Email</h4>
                    <p>info@hiraproperty.com<br>propertymanager@gmail.com</p>
                </div>
            </div>

            <?php if($contact_success): ?>
                <p class="contact-msg"><?php echo $contact_success; ?></p>
            <?php endif; ?>

            <!-- CONTACT FORM - ab asal mein email bhejta hai -->
            <form class="contact-form" method="POST">
                <input type="text" name="c_name" placeholder="Your Name" required>
                <input type="email" name="c_email" placeholder="Your Email" required>
                <input type="text" name="c_subject" placeholder="Subject">
                <textarea name="c_message" placeholder="Your Message..." required></textarea>
                <button type="submit" name="contact_submit">Send Message</button>
            </form>
        </div>
    </section>

<!-- FOOTER -->
    <footer class="footer">
        <div class="inner">
            <div class="brand">🏠 Hira <span>Property</span></div>
            <p>© 2026 Hira Property Rental System. District Gujrat, Pakistan.</p>
            <div class="footer-links">
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
            </div>
        </div>
    </footer>

</body>
</html>
