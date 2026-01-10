<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPass | Next-Gen Transit</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --primary: #6366f1;
            --secondary: #a855f7;
            --dark-bg: #0f172a;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }

        /* Hero Section */
        .hero-master {
            background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)), 
                        url('https://images.unsplash.com/photo-1570125909232-eb263c188f7e?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            min-height: 60vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom-left-radius: 80px;
            border-bottom-right-radius: 80px;
            padding-bottom: 80px;
            color: white;
            text-align: center;
        }

        .hero-content h1 {
            font-size: 4rem;
            font-weight: 800;
            letter-spacing: -2px;
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Compact Portal Cards */
        .portal-container {
            margin-top: -80px;
            position: relative;
            z-index: 10;
        }

        .premium-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 35px 25px;
            transition: 0.3s;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .premium-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.2);
        }

        .icon-floating {
            width: 65px; height: 65px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; margin: 0 auto 20px;
        }

        .btn-modern {
            background: var(--dark-bg);
            color: white;
            padding: 12px 25px;
            border-radius: 15px;
            font-weight: 700;
            width: 100%;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
        }

        .btn-modern:hover { background: var(--primary); color: white; }

        /* Image Feature Section */
        .feature-image-box {
            border-radius: 35px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <section class="hero-master">
        <div class="container hero-content">
            <h1>SmartPass</h1>
            <p class="lead opacity-75">India's most advanced digital bus pass ecosystem.</p>
        </div>
    </section>

    <div class="container portal-container">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-4 col-md-6">
                <div class="premium-card">
                    <div class="icon-floating"><i class="fa fa-graduation-cap"></i></div>
                    <h4 class="fw-bold">Student Hub</h4>
                    <p class="text-muted small mb-4">Apply for passes and track your digital travel wallet.</p>
                    <a href="user_login.php" class="btn-modern">Access ➔</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="premium-card">
                    <div class="icon-floating" style="background: var(--dark-bg);"><i class="fa fa-user-shield"></i></div>
                    <h4 class="fw-bold">Administrator</h4>
                    <p class="text-muted small mb-4">Official panel for verification and route management.</p>
                    <a href="admin_login.php" class="btn-modern">Login ➔</a>
                </div>
            </div>
        </div>
    </div>

    <section class="container py-5 mt-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="feature-image-box">
                    <img src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&w=1000&q=80" class="img-fluid w-100" alt="Smart Bus">
                </div>
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Commute without limits</h2>
                <p class="text-muted mb-4">Ditch the paper queues. SmartPass allows you to carry your travel ID right on your phone with instant QR verification.</p>
                <div class="d-flex align-items-center mb-3">
                    <i class="fa fa-check-circle text-primary me-2"></i>
                    <span class="fw-bold">Secure Digital Wallet</span>
                </div>
                <div class="d-flex align-items-center">
                    <i class="fa fa-check-circle text-primary me-2"></i>
                    <span class="fw-bold">Real-time Approval Tracking</span>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5 text-center text-muted border-top mt-5">
        <p class="small">© 2026 SmartPass Transit Labs. All rights reserved.</p>
    </footer>

</body>
</html>