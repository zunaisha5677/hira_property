<?php
session_start();
include('config/db_connection.php');

$properties = mysqli_query($conn, 
    "SELECT * FROM properties 
     WHERE status='available' 
     ORDER BY created_at DESC 
     LIMIT 6");

$total_properties = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM properties"));
$available = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM properties WHERE status='available'"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hira Property — Find Your Perfect Home in Gujrat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --navy:#1B2340;
            --navy-deep:#12162A;
            --paper:#FAF8F4;
            --panel:#FFFFFF;a
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
        .utility-bar{
            background: var(--navy-deep); color: #C9CCDA; font-size: 12.5px;
        }
        .utility-bar .inner{
            max-width: 1180px; margin: 0 auto;
            display: flex; justify-content: space-between; align-items: center; padding: 9px 40px;
        }
        .utility-bar .contacts{ display: flex; gap: 24px; }
        .utility-bar a{ text-decoration: none; color: inherit; }
        .utility-bar a:hover{ color: #fff; }

        /* NAVBAR */
        .navbar {
            position: sticky;
            top: 0; left: 0; right: 0;
            z-index: 100;
            background: #fff;
            border-bottom: 1px solid var(--line);
        }
        .navbar .inner{
            max-width: 1180px; margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 40px;
        }
        .nav-logo {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--navy);
            display: flex; align-items: center; gap: 9px;
            text-decoration: none;
        }
        .nav-logo span { color: var(--ember); }
        .nav-links {
            display: flex;
            gap: 32px;
            list-style: none;
        }
        .nav-links a {
            color: var(--navy);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
            padding-bottom: 4px;
            border-bottom: 2px solid transparent;
        }
        .nav-links a:hover { color: var(--ember); border-color: var(--ember); }
        .nav-btns {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .btn-login {
            color: var(--navy);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            padding: 9px 20px;
            border: 1px solid var(--line);
            border-radius: 6px;
            transition: all 0.2s;
        }
        .btn-login:hover { border-color: var(--ember); color: var(--ember); }
        .btn-signup {
            color: #fff;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            padding: 10px 22px;
            background: var(--ember);
            border-radius: 6px;
            transition: all 0.2s;
        }
        .btn-signup:hover { background: var(--ember-dark); }

        /* HERO SECTION */
        .hero {
            position: relative;
            min-height: 620px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
        }
        .hero-bg {
            position: absolute;
            inset: 0;
            background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1920&q=80');
            background-size: cover;
            background-position: center;
        }
        .hero-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(18,22,42,0.45) 0%, rgba(18,22,42,0.82) 100%);
        }
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 70px 20px 60px;
        }
        .hero-tag {
            display: inline-block;
            background: rgba(232,98,42,0.18);
            border: 1px solid rgba(232,98,42,0.5);
            color: #FBA97B;
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 6px 18px;
            border-radius: 20px;
            margin-bottom: 25px;
        }
        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 52px;
            line-height: 1.15;
            font-weight: 700;
            margin-bottom: 20px;
            color: #fff;
        }
        .hero h1 em { font-style: italic; color: var(--ember); }
        .hero p {
            font-size: 16px;
            color: #DCDFE8;
            line-height: 1.7;
            margin-bottom: 36px;
            max-width: 580px;
            margin-left: auto;
            margin-right: auto;
        }
        .hero-btns { display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; }
        .hero-btn-primary {
            background: var(--ember); color: #fff; text-decoration: none;
            padding: 14px 35px; border-radius: 6px; font-size: 14px; font-weight: 600; transition: all 0.2s;
        }
        .hero-btn-primary:hover { background: var(--ember-dark); transform: translateY(-2px); }
        .hero-btn-secondary {
            background: transparent; color: #fff; text-decoration: none;
            padding: 14px 35px; border-radius: 6px; font-size: 14px; font-weight: 500;
            border: 1.5px solid rgba(255,255,255,0.5); transition: all 0.2s;
        }
        .hero-btn-secondary:hover { background: rgba(255,255,255,0.1); }

        /* SEARCH BAR (overlapping hero) */
        .search-wrap{ max-width: 1180px; margin: -46px auto 0; padding: 0 40px; position: relative; z-index: 5; }
        .search-box {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 24px 48px rgba(18,22,42,0.16);
            padding: 20px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .search-box input, .search-box select {
            background: var(--paper);
            border: 1px solid var(--line);
            color: var(--text);
            padding: 12px 16px;
            border-radius: 6px;
            font-family: inherit;
            font-size: 13px;
            flex: 1;
            min-width: 150px;
        }
        .search-box input::placeholder { color: #999; }
        .search-box button {
            background: var(--navy);
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .search-box button:hover { background: var(--navy-deep); }

        /* STATS BAR */
        .stats-bar {
            background: var(--navy);
            padding: 34px 40px;
        }
        .stats-bar .inner{
            max-width: 1180px; margin: 0 auto;
            display: flex; justify-content: center; gap: 80px;
        }
        .stat-item { text-align: center; }
        .stat-item .num { font-family: 'Playfair Display', serif; font-size: 30px; font-weight: 700; color: var(--ember); }
        .stat-item .lbl { font-size: 12px; color: #B9BCCB; margin-top: 4px; }

        /* PROPERTIES SECTION */
        .section { padding: 80px 40px; }
        .inner-max{ max-width: 1180px; margin: 0 auto; }
        .section-header { text-align: center; margin-bottom: 50px; }
        .section-tag { color: var(--ember); font-size: 11px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 12px; font-weight: 600; }
        .section-header h2 { font-family: 'Playfair Display', serif; font-size: 32px; color: var(--navy); }
        .section-header p { color: var(--muted); font-size: 14px; margin-top: 10px; }
        .properties-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .prop-card {
            background: #fff; border-radius: 10px; border: 1px solid var(--line);
            overflow: hidden; transition: all 0.2s; text-decoration: none; color: inherit; display: block;
            box-shadow: 0 2px 12px rgba(27,35,64,0.04);
        }
        .prop-card:hover { border-color: var(--ember); transform: translateY(-4px); box-shadow: 0 14px 28px rgba(27,35,64,0.10); }
        .prop-img { width: 100%; height: 200px; object-fit: cover; background: var(--line); }
        .prop-body { padding: 18px; }
        .prop-body h3 { font-size: 15px; font-weight: 600; color: var(--navy); margin-bottom: 6px; }
        .prop-body .location { font-size: 12px; color: var(--muted); margin-bottom: 12px; }
        .prop-body .price { font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: var(--ember); }
        .prop-body .price span { font-family: 'Poppins', sans-serif; font-size: 11px; color: var(--muted); font-weight: 400; }
        .prop-badge { display: inline-block; background: rgba(40,167,69,0.12); color: var(--success); font-size: 10px; padding: 3px 10px; border-radius: 10px; margin-top: 8px; }

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

        /* HOW IT WORKS */
        .how-section { padding: 80px 40px; background: var(--paper); text-align: center; }
        .steps-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; max-width: 1000px; margin: 50px auto 0; }
        .step-item { padding: 30px 20px; background: #fff; border-radius: 10px; border: 1px solid var(--line); }
        .step-num {
            width: 44px; height: 44px; background: var(--ember); color: #fff; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Playfair Display', serif; font-size: 17px; font-weight: 700; margin: 0 auto 15px;
        }
        .step-item h4 { font-size: 14px; font-weight: 600; color: var(--navy); margin-bottom: 8px; }
        .step-item p { font-size: 12px; color: var(--muted); line-height: 1.6; }

        /* CONTACT SECTION */
        .contact-section { padding: 80px 40px; background: #fff; text-align: center; }
        .contact-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; max-width: 900px; margin: 50px auto 0; }
        .contact-card { background: var(--paper); padding: 30px; border-radius: 10px; border: 1px solid var(--line); }
        .contact-card .icon-box{
            width: 44px; height: 44px; margin: 0 auto 14px; background: var(--navy); border-radius: 8px;
            display: flex; align-items: center; justify-content: center; color: #fff;
        }
        .contact-card h4 { font-size: 14px; font-weight: 600; color: var(--navy); margin-bottom: 8px; }
        .contact-card p { font-size: 13px; color: var(--muted); line-height: 1.6; }

        /* FOOTER */
        .footer { background: var(--navy-deep); padding: 30px 40px; display: flex; justify-content: center; }
        .footer .inner{ max-width: 1180px; width: 100%; display: flex; justify-content: space-between; align-items: center; }
        .footer p { font-size: 12px; color: #8A8EA1; }
        .footer .brand { font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: #fff; }
        .footer .brand span { color: var(--ember); }

        @media (max-width: 980px){
            .navbar .inner, .utility-bar .inner, .section, .how-section, .contact-section, .footer{ padding-left: 22px; padding-right: 22px; }
            .nav-links{ display: none; }
            .properties-grid, .steps-grid, .contact-grid{ grid-template-columns: 1fr; }
            .about-section{ flex-direction: column; padding-left: 22px; padding-right: 22px; }
            .hero h1{ font-size: 34px; }
            .stats-bar .inner{ gap: 30px; flex-wrap: wrap; }
            .search-wrap{ padding: 0 22px; margin-top: -30px; }
        }
    </style>
</head>
<body>

    <div class="utility-bar">
        <div class="inner">
            <div class="contacts">
                <a href="tel:+923000000000">📞 +92 333 1626222</a>
                <a href="mailto:info@hiraproperty.com">✉ info@hiraproperty.com</a>
            </div>
            <div>District Gujrat, Punjab</div>
        </div>
    </div>

    <nav class="navbar">
        <div class="inner">
            <a class="nav-logo" href="index.php">
            <img src="assets/logo.png.jpeg" alt="Hira Property" style="height:26px; width:26px; object-fit:cover; border-radius:6px; vertical-align:middle;">
                Hira <span>Property</span>
            </a>
            <ul class="nav-links">
                <li><a href="#properties">Properties</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#how">How It Works</a></li>
                <li><a href="#contact">Contact Us</a></li>
            </ul>
            <div class="nav-btns">
                <a href="login.php" class="btn-login">Login</a>
                <a href="register.php" class="btn-signup">Sign Up</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-content">
            <div class="hero-tag">District Gujrat, Pakistan</div>
            <h1>Find Your Perfect<br><em>Home in Gujrat</em></h1>
            <p>Hira Property connects property owners with tenants across District Gujrat. Browse verified listings, schedule visits, and rent with confidence.</p>
            <div class="hero-btns">
                <a href="register.php" class="hero-btn-primary">Get Started Free</a>
                <a href="#properties" class="hero-btn-secondary">Browse Properties</a>
            </div>
        </div>
    </section>

    <div class="search-wrap">
        <form action="login.php" method="GET">
            <div class="search-box">
                <input type="text" name="search" placeholder="Search by location or property name...">
                <select name="status">
                    <option value="">Any Status</option>
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                </select>
                <input type="number" name="price" placeholder="Max Rent (PKR)">
                <button type="submit">Search Properties</button>
            </div>
        </form>
    </div>

    <div class="stats-bar">
        <div class="inner">
            <div class="stat-item"><div class="num"><?php echo $total_properties['total']; ?>+</div><div class="lbl">Total Properties</div></div>
            <div class="stat-item"><div class="num"><?php echo $available['total']; ?>+</div><div class="lbl">Available Now</div></div>
            <div class="stat-item"><div class="num">100%</div><div class="lbl">Verified Listings</div></div>
            <div class="stat-item"><div class="num">Gujrat</div><div class="lbl">District Coverage</div></div>
        </div>
    </div>

    <section class="section" id="properties">
        <div class="inner-max">
            <div class="section-header">
                <div class="section-tag">Featured Listings</div>
                <h2>Available Properties</h2>
                <p>Browse our latest verified properties across District Gujrat</p>
            </div>
            <div class="properties-grid">
                <?php while($p = mysqli_fetch_assoc($properties)): ?>
                <?php
                $img = (!empty($p['image']) && file_exists("uploads/".$p['image']))
                       ? "uploads/".$p['image']
                       : "https://placehold.co/600x400/1B2340/E8622A?text=Hira+Property";
                ?>
                <a href="login.php" class="prop-card">
                    <img src="<?php echo $img; ?>" class="prop-img" alt="Property">
                    <div class="prop-body">
                        <h3><?php echo htmlspecialchars($p['title']); ?></h3>
                        <div class="location"><?php echo htmlspecialchars($p['location']); ?></div>
                        <div class="price">PKR <?php echo number_format($p['price']); ?><span>/month</span></div>
                        <div class="prop-badge">Available</div>
                    </div>
                </a>
                <?php endwhile; ?>
            </div>
            <div style="text-align:center; margin-top:40px;">
                <a href="login.php" style="color:#E8622A; text-decoration:none; font-size:14px; font-weight:600;">
                    View All Properties → (Login Required)
                </a>
            </div>
        </div>
    </section>

    <section class="about-band" id="about">
        <div class="inner-max">
            <div class="about-section">
                <div class="about-text">
                    <div class="tag">About Hira Property</div>
                    <h2>Gujrat's Trusted<br>Rental Platform</h2>
                    <p>Hira Property is a professional property rental management company serving District Gujrat. We connect property owners with verified tenants, making the rental process smooth, transparent, and secure.</p>
                    <p>Our platform handles everything from property listings to contracts, payments, and maintenance — all in one place.</p>
                    <div class="about-features">
                        <div class="feature-item"><div class="dot"></div>Verified Properties</div>
                        <div class="feature-item"><div class="dot"></div>Secure Payments</div>
                        <div class="feature-item"><div class="dot"></div>Digital Contracts</div>
                        <div class="feature-item"><div class="dot"></div>24/7 Maintenance</div>
                        <div class="feature-item"><div class="dot"></div>Site Visit Booking</div>
                        <div class="feature-item"><div class="dot"></div>District Gujrat Coverage</div>
                    </div>