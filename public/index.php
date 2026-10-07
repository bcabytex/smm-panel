<?php

require __DIR__ . '/../vendor/autoload.php';

use App\ApiClient;
use App\Config;

Config::load();

$apiClient = new ApiClient();
$services = $apiClient->getServices();

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order = [
        'service' => $_POST['service'] ?? '',
        'link' => $_POST['link'] ?? '',
        'quantity' => $_POST['quantity'] ?? '',
        'comments' => $_POST['comments'] ?? '',
    ];

    $result = $apiClient->createOrder($order);

    if (($result['status'] ?? '') === 'success') {
        $successMessage = $result['message'] ?? 'Order submitted successfully.';
    } else {
        $errorMessage = $result['message'] ?? 'Something went wrong while placing the order.';
    }
}

$selectedService = $_POST['service'] ?? ($services[0]['name'] ?? '');
$quantity = $_POST['quantity'] ?? 1000;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMM Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
    <header class="topbar">
        <div class="container nav">
            <div class="brand">
                <span class="brand-mark">S</span>
                <span>SocialBoost</span>
            </div>
            <nav>
                <a href="#services">Services</a>
                <a href="#pricing">Pricing</a>
                <a href="#dashboard">Dashboard</a>
                <a href="#contact">Support</a>
            </nav>
            <button class="primary-btn">Login</button>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container hero-grid">
                <div class="hero-copy">
                    <span class="badge">Trusted Growth Platform</span>
                    <h1>Launch high-impact social growth campaigns.</h1>
                    <p>Manage SMM services, orders, and audience growth from one powerful panel.</p>
                    <div class="hero-actions">
                        <a class="primary-btn" href="#services">Order Now</a>
                        <a class="secondary-btn" href="#dashboard">View Dashboard</a>
                    </div>
                    <ul class="stats">
                        <li><strong>12k+</strong><span>Orders</span></li>
                        <li><strong>99.9%</strong><span>Uptime</span></li>
                        <li><strong>24/7</strong><span>Support</span></li>
                    </ul>
                </div>

                <div class="hero-card">
                    <div class="mini-panel">
                        <div class="mini-header">
                            <span>Live Overview</span>
                            <span class="online-dot">●</span>
                        </div>
                        <div class="metric-grid">
                            <div>
                                <label>Revenue</label>
                                <strong>$18,420</strong>
                            </div>
                            <div>
                                <label>Active Orders</label>
                                <strong>842</strong>
                            </div>
                            <div>
                                <label>Success Rate</label>
                                <strong>97.8%</strong>
                            </div>
                            <div>
                                <label>Avg. Speed</label>
                                <strong>2.4h</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="services" class="section">
            <div class="container">
                <div class="section-heading">
                    <span class="section-tag">Our Services</span>
                    <h2>Everything you need to grow faster</h2>
                </div>

                <div class="services-grid">
                    <?php foreach ($services as $service): ?>
                        <article class="service-card">
                            <div class="service-top">
                                <span class="service-icon">●</span>
                                <span><?= htmlspecialchars((string) ($service['name'] ?? 'Service')) ?></span>
                            </div>
                            <p>High-quality social engagement for brands, creators, and agencies.</p>
                            <div class="service-meta">
                                <span>From $<?= number_format((float) ($service['price'] ?? 0), 2) ?></span>
                                <span><?= htmlspecialchars((string) ($service['min'] ?? 0)) ?>+</span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section id="dashboard" class="section alt-bg">
            <div class="container dashboard-grid">
                <div class="panel order-panel">
                    <div class="panel-head">
                        <h3>Place New Order</h3>
                    </div>

                    <?php if ($successMessage): ?>
                        <div class="alert success"><?= htmlspecialchars($successMessage) ?></div>
                    <?php endif; ?>

                    <?php if ($errorMessage): ?>
                        <div class="alert error"><?= htmlspecialchars($errorMessage) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="/">
                        <div class="field">
                            <label for="service">Service</label>
                            <select id="service" name="service">
                                <?php foreach ($services as $service): ?>
                                    <option value="<?= htmlspecialchars((string) ($service['name'] ?? '')) ?>" <?= ($selectedService === ($service['name'] ?? '')) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars((string) ($service['name'] ?? '')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="field">
                            <label for="link">Target URL</label>
                            <input id="link" name="link" type="url" placeholder="https://example.com/profile" required>
                        </div>

                        <div class="field-row">
                            <div class="field">
                                <label for="quantity">Quantity</label>
                                <input id="quantity" name="quantity" type="number" min="1" value="<?= htmlspecialchars((string) $quantity) ?>" required>
                            </div>
                            <div class="field">
                                <label for="speed">Priority</label>
                                <select id="speed" name="speed">
                                    <option>Normal</option>
                                    <option>Priority</option>
                                    <option>VIP</option>
                                </select>
                            </div>
                        </div>

                        <div class="field">
                            <label for="comments">Notes</label>
                            <textarea id="comments" name="comments" rows="4" placeholder="Optional notes for your order"></textarea>
                        </div>

                        <button type="submit" class="primary-btn full-width">Submit Order</button>
                    </form>
                </div>

                <div class="panel status-panel">
                    <div class="panel-head">
                        <h3>Order Activity</h3>
                    </div>

                    <div class="activity-list">
                        <div class="activity-item">
                            <span class="status green"></span>
                            <div>
                                <strong>Instagram Growth</strong>
                                <small>Completed • 2 minutes ago</small>
                            </div>
                        </div>
                        <div class="activity-item">
                            <span class="status orange"></span>
                            <div>
                                <strong>Youtube Views</strong>
                                <small>Processing • 12 minutes ago</small>
                            </div>
                        </div>
                        <div class="activity-item">
                            <span class="status blue"></span>
                            <div>
                                <strong>Spotify Plays</strong>
                                <small>Pending • 1 hour ago</small>
                            </div>
                        </div>
                    </div>

                    <div class="mini-chart">
                        <div class="chart-line"></div>
                    </div>
                </div>
            </div>
        </section>

        <section id="pricing" class="section">
            <div class="container">
                <div class="section-heading">
                    <span class="section-tag">Simple Pricing</span>
                    <h2>Flexible packages for every client</h2>
                </div>

                <div class="pricing-grid">
                    <div class="price-card">
                        <h3>Starter</h3>
                        <div class="price">$29<span>/mo</span></div>
                        <ul>
                            <li>Up to 5 services</li>
                            <li>Basic dashboard</li>
                            <li>Email support</li>
                        </ul>
                        <button class="secondary-btn full-width">Choose Plan</button>
                    </div>
                    <div class="price-card featured">
                        <div class="popular">Popular</div>
                        <h3>Growth</h3>
                        <div class="price">$89<span>/mo</span></div>
                        <ul>
                            <li>Unlimited services</li>
                            <li>API access</li>
                            <li>Priority support</li>
                        </ul>
                        <button class="primary-btn full-width">Choose Plan</button>
                    </div>
                    <div class="price-card">
                        <h3>Agency</h3>
                        <div class="price">$249<span>/mo</span></div>
                        <ul>
                            <li>Team accounts</li>
                            <li>Advanced analytics</li>
                            <li>Dedicated manager</li>
                        </ul>
                        <button class="secondary-btn full-width">Choose Plan</button>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer id="contact" class="footer">
        <div class="container footer-inner">
            <div>
                <div class="brand footer-brand">
                    <span class="brand-mark">S</span>
                    <span>SocialBoost</span>
                </div>
                <p>High-quality SMM solutions for modern brands.</p>
            </div>
            <div class="footer-links">
                <a href="#services">Services</a>
                <a href="#pricing">Pricing</a>
                <a href="#dashboard">Orders</a>
            </div>
        </div>
    </footer>
</body>
</html>
