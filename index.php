<?php
// --- BACKEND: Form Processing ---

// --- BACKEND: Secure Form Processing & Database Insertion ---
$form_status = '';

// 1. Database Configuration (Change these for your live hosting)
$host = 'localhost';
$db_username = 'root'; 
$db_password = '';     
$db_name = 'port'; // Ensure this matches your actual database name

// 2. Process the form if it is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Create database connection using MySQLi OOP
    $conn = new mysqli($host, $db_username, $db_password, $db_name);

    // Check connection
    if ($conn->connect_error) {
        $form_status = "<div class='error-alert' style='background: rgba(255, 0, 85, 0.1); color: #ff0055; padding: 15px; border-radius: 12px; border: 1px solid #ff0055; margin-bottom: 25px; text-align: center; font-weight: 600;'>Database Connection Failed. Please try again later.</div>";
    } else {
        // Sanitize Input Data
        $name = htmlspecialchars(strip_tags(trim($_POST['name'])));
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $subject = htmlspecialchars(strip_tags(trim($_POST['subject'])));
        $message = htmlspecialchars(strip_tags(trim($_POST['message'])));

        // Basic Validation
        if (!empty($name) && !empty($email) && !empty($message) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            
            // 3. Prepare the SQL statement (Protects against SQL Injection)
            $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $subject, $message);

            // Execute the query
            if ($stmt->execute()) {
                $form_status = "<div class='success-alert' style='background: rgba(0, 243, 255, 0.1); color: var(--neon-cyan); padding: 15px; border-radius: 12px; border: 1px solid var(--neon-cyan); margin-bottom: 25px; text-align: center; font-weight: 600;'>Transmission successful, $name. I will review your message and reply shortly.</div>";
            } else {
                $form_status = "<div class='error-alert' style='background: rgba(255, 0, 85, 0.1); color: #ff0055; padding: 15px; border-radius: 12px; border: 1px solid #ff0055; margin-bottom: 25px; text-align: center; font-weight: 600;'>System Error: Could not save message.</div>";
            }

            // Close statement
            $stmt->close();
        } else {
            $form_status = "<div class='error-alert' style='background: rgba(255, 189, 46, 0.1); color: #ffbd2e; padding: 15px; border-radius: 12px; border: 1px solid #ffbd2e; margin-bottom: 25px; text-align: center; font-weight: 600;'>Please fill in all required fields correctly.</div>";
        }
        
        // Close connection
        $conn->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Muhammad Bilal | Full-Stack Architect</title>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Space+Grotesk:wght@500;700&display=swap');

        :root {
            --bg-dark: #020205;
            --bg-card: rgba(15, 15, 20, 0.75);
            --neon-cyan: #00f3ff;
            --neon-blue: #0077ff;
            --neon-purple: #b026ff;
            --text-main: #ffffff;
            --text-muted: #a1a1aa;
            --glass-border: rgba(255, 255, 255, 0.1);
            --glass-highlight: rgba(255, 255, 255, 0.2);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-dark); color: var(--text-main); overflow-x: hidden; scroll-behavior: smooth; }
        h1, h2, h3, h4, .brand, .stat-num { font-family: 'Space Grotesk', sans-serif; }

        /* Cinematic Background Glows */
        .bg-glow { position: fixed; top: -10%; left: -10%; width: 40vw; height: 40vw; background: radial-gradient(circle, rgba(0, 243, 255, 0.08) 0%, transparent 70%); z-index: -2; filter: blur(60px); }
        .bg-glow-2 { position: fixed; bottom: -10%; right: -10%; width: 40vw; height: 40vw; background: radial-gradient(circle, rgba(176, 38, 255, 0.08) 0%, transparent 70%); z-index: -2; filter: blur(60px); }
        canvas#hero-canvas { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; opacity: 0.5; pointer-events: none; }

        .container { max-width: 1350px; margin: 0 auto; padding: 100px 20px; }
        .section-title { font-size: 3.5rem; margin-bottom: 1rem; text-align: center; background: linear-gradient(90deg, #fff, var(--text-muted)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .section-subtitle { text-align: center; color: var(--neon-cyan); font-size: 1.1rem; margin-bottom: 4rem; letter-spacing: 3px; text-transform: uppercase; font-weight: 600; }

        /* Ultra Realistic Glassmorphism Panel */
        .glass-panel {
            background: var(--bg-card);
            backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px);
            border: 1px solid var(--glass-border);
            border-top: 1px solid var(--glass-highlight);
            border-left: 1px solid var(--glass-highlight);
            border-radius: 24px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.6), inset 0 0 0 1px rgba(255,255,255,0.03);
            padding: 3rem; transition: all 0.4s ease;
        }

        /* --- NAVBAR --- */
        header { position: fixed; top: 0; width: 100%; z-index: 1000; padding: 25px 0; transition: 0.4s; border-bottom: 1px solid transparent; }
        header.scrolled { background: rgba(2, 2, 5, 0.9); backdrop-filter: blur(20px); border-bottom: 1px solid var(--glass-border); padding: 15px 0; }
        .nav-container { max-width: 1350px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; }
        .brand { font-size: 2rem; font-weight: 800; color: #fff; text-decoration: none; letter-spacing: -1px; }
        .brand span { color: var(--neon-cyan); }
        .nav-links { display: flex; gap: 35px; list-style: none; align-items: center; }
        .nav-links a { color: var(--text-main); text-decoration: none; font-size: 1rem; font-weight: 500; transition: 0.3s; }
        .nav-links a:hover { color: var(--neon-cyan); text-shadow: 0 0 10px rgba(0,243,255,0.5); }
        .btn-download-sm { padding: 10px 24px; border: 1px solid var(--neon-cyan); color: var(--neon-cyan); border-radius: 8px; text-decoration: none; font-weight: 600; transition: 0.3s; background: rgba(0, 243, 255, 0.05); }
        .btn-download-sm:hover { background: var(--neon-cyan); color: var(--bg-dark); box-shadow: 0 0 20px rgba(0, 243, 255, 0.5); }

        /* --- SPLIT HERO SECTION --- */
        #hero { min-height: 100vh; display: flex; align-items: center; position: relative; padding-top: 80px; }
        .hero-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 60px; align-items: center; width: 100%; }
        
        .hero-content { z-index: 2; }
        .badge { display: inline-flex; align-items: center; gap: 10px; padding: 10px 20px; background: rgba(0, 243, 255, 0.08); border: 1px solid rgba(0, 243, 255, 0.3); color: var(--neon-cyan); border-radius: 50px; font-size: 0.95rem; font-weight: 600; margin-bottom: 30px; box-shadow: 0 0 20px rgba(0,243,255,0.1); }
        .badge .dot { width: 8px; height: 8px; background: var(--neon-cyan); border-radius: 50%; box-shadow: 0 0 10px var(--neon-cyan); animation: pulse 2s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.4; } 100% { opacity: 1; } }
        
        .hero-content h1 { font-size: clamp(3.5rem, 5vw, 5.5rem); line-height: 1.05; margin-bottom: 25px; font-weight: 800; letter-spacing: -2px; }
        .hero-content p { font-size: 1.25rem; color: var(--text-muted); margin-bottom: 40px; line-height: 1.7; max-width: 650px; }
        
        .btn-group { display: flex; gap: 20px; }
        .btn { padding: 18px 36px; border-radius: 12px; font-weight: 600; text-decoration: none; transition: 0.3s; display: inline-flex; align-items: center; gap: 10px; font-size: 1.1rem; }
        .btn-primary { background: #fff; color: var(--bg-dark); }
        .btn-primary:hover { transform: translateY(-4px); box-shadow: 0 15px 30px rgba(255,255,255,0.15); }
        .btn-neon { background: rgba(0, 243, 255, 0.05); border: 2px solid var(--neon-cyan); color: var(--neon-cyan); }
        .btn-neon:hover { background: var(--neon-cyan); color: var(--bg-dark); box-shadow: 0 0 25px rgba(0, 243, 255, 0.6); transform: translateY(-4px); }

        /* Profile Image Container */
        .hero-img-wrapper { position: relative; display: flex; justify-content: center; align-items: center; z-index: 2; perspective: 1000px; }
        .hero-img-box { position: relative; width: 100%; max-width: 450px; aspect-ratio: 4/5; border-radius: 30px; transform-style: preserve-3d; }
        /* The glowing shadow behind the image */
        .hero-img-box::before { content: ''; position: absolute; inset: -15px; background: linear-gradient(45deg, var(--neon-cyan), var(--neon-purple)); filter: blur(30px); opacity: 0.4; border-radius: 40px; z-index: -1; animation: breathe 4s infinite alternate; }
        @keyframes breathe { 0% { opacity: 0.3; filter: blur(25px); } 100% { opacity: 0.6; filter: blur(40px); } }
        /* The actual image styling */
        .hero-img-box img { width: 100%; height: 100%; object-fit: cover; border-radius: 30px; border: 2px solid rgba(255,255,255,0.2); box-shadow: inset 0 0 20px rgba(255,255,255,0.5), 0 20px 50px rgba(0,0,0,0.8); z-index: 2; position: relative; }

        /* --- STATS COUNTER BAR --- */
        .stats-container { display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; margin-top: -50px; position: relative; z-index: 10; padding: 0 20px; }
        .stat-card { background: rgba(10, 10, 15, 0.85); backdrop-filter: blur(20px); border: 1px solid var(--glass-border); border-top: 1px solid var(--glass-highlight); padding: 30px; border-radius: 20px; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.5); transition: 0.4s; }
        .stat-card:hover { transform: translateY(-10px); border-color: var(--neon-cyan); box-shadow: 0 20px 40px rgba(0,243,255,0.15); }
        .stat-num { font-size: 3.5rem; color: #fff; font-weight: 800; line-height: 1; margin-bottom: 10px; display: flex; justify-content: center; align-items: baseline; }
        .stat-num span { color: var(--neon-cyan); font-size: 2.5rem; }
        .stat-text { color: var(--text-muted); font-size: 1.05rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }

        /* --- ABOUT, SERVICES & EDUCATION --- */
        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        .info-block h3 { color: var(--neon-cyan); font-size: 1.8rem; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .info-block p { color: var(--text-muted); line-height: 1.8; margin-bottom: 20px; font-size: 1.1rem; }
        .info-block strong { color: #fff; font-weight: 600; }
        
        .services-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 30px; }
        .service-box { background: rgba(255,255,255,0.02); border: 1px solid var(--glass-border); padding: 25px; border-radius: 16px; transition: 0.3s; }
        .service-box:hover { background: rgba(0,243,255,0.05); border-color: rgba(0,243,255,0.3); }
        .service-box h4 { font-size: 1.2rem; color: #fff; margin-bottom: 10px; }
        .service-box p { font-size: 0.95rem; color: var(--text-muted); line-height: 1.6; margin: 0; }

        .edu-card { background: rgba(0,0,0,0.4); border: 1px solid var(--glass-border); padding: 25px; border-radius: 16px; margin-top: 25px; border-left: 5px solid var(--neon-purple); transition: 0.3s; }
        .edu-card:hover { border-left-color: var(--neon-cyan); background: rgba(0,0,0,0.6); }

        /* --- EXPERIENCE TIMELINE --- */
        .timeline { position: relative; padding-left: 40px; margin-top: 50px; }
        .timeline::before { content: ''; position: absolute; left: 0; top: 0; height: 100%; width: 2px; background: linear-gradient(to bottom, var(--neon-cyan), var(--neon-purple), transparent); }
        .timeline-item { position: relative; margin-bottom: 50px; }
        .timeline-item::before { content: ''; position: absolute; left: -47px; top: 5px; width: 16px; height: 16px; border-radius: 50%; background: var(--bg-dark); border: 3px solid var(--neon-cyan); box-shadow: 0 0 15px var(--neon-cyan); transition: 0.3s; }
        .timeline-item:hover::before { background: var(--neon-cyan); transform: scale(1.2); }
        .timeline-date { display: inline-block; padding: 6px 15px; background: rgba(0,243,255,0.1); border: 1px solid rgba(0,243,255,0.2); color: var(--neon-cyan); font-size: 0.9rem; font-weight: 600; border-radius: 30px; margin-bottom: 15px; }
        .timeline-item h4 { font-size: 1.5rem; margin-bottom: 8px; color: #fff; }
        .timeline-item h5 { font-size: 1.1rem; color: var(--neon-purple); font-weight: 600; margin-bottom: 15px; }
        .timeline-item p { color: var(--text-muted); line-height: 1.7; font-size: 1.05rem; }

        /* --- HIGH VISIBILITY SKILLS MATRIX --- */
        .skills-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 25px; text-align: center; }
        .skill-item { background: linear-gradient(145deg, rgba(255,255,255,0.05), rgba(0,0,0,0.2)); border: 1px solid rgba(255,255,255,0.1); padding: 30px 20px; border-radius: 20px; transition: 0.4s; display: flex; flex-direction: column; align-items: center; gap: 15px; position: relative; overflow: hidden; }
        .skill-item::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(0,243,255,0.1) 0%, transparent 70%); opacity: 0; transition: 0.4s; }
        .skill-item:hover { border-color: var(--neon-cyan); transform: translateY(-10px); box-shadow: 0 15px 35px rgba(0, 243, 255, 0.15); }
        .skill-item:hover::before { opacity: 1; }
        .skill-item img { width: 60px; height: 60px; object-fit: contain; z-index: 1; filter: drop-shadow(0 5px 5px rgba(0,0,0,0.5)); }
        .skill-item span { font-weight: 700; font-size: 1.2rem; color: #fff; z-index: 1; }

        /* --- PROJECTS GRID (ULTRA REALISTIC TILT CARDS) --- */
        .project-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 50px; perspective: 1200px; }
        .project-card { 
            background: var(--bg-card); border: 1px solid var(--glass-border); border-top: 1px solid rgba(255,255,255,0.2); border-left: 1px solid rgba(255,255,255,0.2);
            border-radius: 24px; overflow: hidden; position: relative; display: flex; flex-direction: column;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5); transform-style: preserve-3d; transition: transform 0.1s;
        }
        .project-img { height: 220px; background: #050508; border-bottom: 1px solid var(--glass-border); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; }
        .project-img iframe { width: 400%; height: 400%; transform: scale(0.25); transform-origin: top left; pointer-events: none; border: none; opacity: 0.8; transition: 0.4s; }
        .project-card:hover .project-img iframe { opacity: 1; filter: brightness(1.1); }
        
        .project-info { padding: 35px; flex-grow: 1; display: flex; flex-direction: column; transform: translateZ(40px); }
        
        /* ULTRA VISIBLE TECH STACK */
        .tech-stack { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
        .tech-stack span { 
            background: rgba(0, 243, 255, 0.15); border: 1px solid var(--neon-cyan); 
            padding: 6px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; 
            color: #fff; text-shadow: 0 0 8px rgba(0, 243, 255, 0.8); letter-spacing: 0.5px;
        }
        
        .project-info h3 { font-size: 1.8rem; margin-bottom: 15px; color: #fff; }
        .project-info p { color: var(--text-muted); font-size: 1.05rem; line-height: 1.7; margin-bottom: 30px; flex-grow: 1; }
        .project-links { display: flex; gap: 20px; }
        .project-links a { flex: 1; text-align: center; padding: 14px; border-radius: 10px; text-decoration: none; font-size: 1rem; font-weight: 700; transition: 0.3s; display: flex; justify-content: center; align-items: center; gap: 8px; }
        .btn-live { background: linear-gradient(90deg, rgba(0,243,255,0.1), rgba(0,119,255,0.1)); color: var(--neon-cyan); border: 1px solid var(--neon-cyan); }
        .btn-live:hover { background: var(--neon-cyan); color: var(--bg-dark); box-shadow: 0 0 20px rgba(0, 243, 255, 0.4); }

        /* --- CONTACT --- */
        .contact-wrapper { max-width: 800px; margin: 0 auto; }
        .contact-form { display: flex; flex-direction: column; gap: 25px; }
        .input-row { display: flex; gap: 25px; }
        .contact-form input, .contact-form textarea { width: 100%; background: rgba(0,0,0,0.4); border: 1px solid var(--glass-border); padding: 20px; border-radius: 14px; color: #fff; font-size: 1.1rem; transition: 0.3s; outline: none; font-family: 'Inter', sans-serif; }
        .contact-form input:focus, .contact-form textarea:focus { border-color: var(--neon-cyan); box-shadow: 0 0 20px rgba(0, 243, 255, 0.15); background: rgba(0,0,0,0.6); }
        .success-alert { background: rgba(0, 243, 255, 0.1); color: var(--neon-cyan); padding: 20px; border-radius: 12px; border: 1px solid var(--neon-cyan); margin-bottom: 25px; text-align: center; font-weight: 600; font-size: 1.1rem; }
        
        .social-bar { display: flex; justify-content: center; gap: 25px; margin-top: 50px; }
        .social-icon { width: 60px; height: 60px; border-radius: 50%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); display: flex; align-items: center; justify-content: center; text-decoration: none; color: #fff; font-weight: bold; font-size: 1.2rem; transition: 0.3s; }
        .social-icon:hover { background: var(--neon-cyan); color: var(--bg-dark); transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0, 243, 255, 0.4); }

        /* Footer */
        footer { border-top: 1px solid var(--glass-border); padding: 40px 20px; text-align: center; color: var(--text-muted); font-size: 1rem; background: rgba(0,0,0,0.5); margin-top: 100px; }

        @media (max-width: 1024px) {
            .hero-grid { grid-template-columns: 1fr; text-align: center; }
            .hero-content p { margin-inline: auto; }
            .btn-group { justify-content: center; }
            .hero-img-box { margin: 50px auto 0; max-width: 350px; }
            .stats-container { grid-template-columns: 1fr 1fr; margin-top: 50px; }
            .about-grid, .input-row { grid-template-columns: 1fr; }
            .nav-links { display: none; } 
        }
        @media (max-width: 600px) {
            .stats-container { grid-template-columns: 1fr; }
            .hero-content h1 { font-size: 2.8rem; }
        }
    </style>
</head>


<body>

    <div class="bg-glow"></div>
    <div class="bg-glow-2"></div>

   <header id="navbar">
    <div class="nav-container">
        <a href="#" class="brand">Muhammad Bilal Ifzal <span>.</span></a>
        
        <div class="menu-toggle" id="mobile-menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>

        <ul class="nav-links" id="nav-links">
            <li><a href="#about">About</a></li>
            <li><a href="#experience">Experience</a></li>
            <li><a href="#projects">Portfolio</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="Muhammad Bilal Ifzal CV.pdf" download class="btn-download-sm">Download CV</a></li>
        </ul>
    </div>
</header>
<style>/* --- UPGRADED MOBILE MENU TOGGLE STYLING --- */
.menu-toggle {
    display: none;
    flex-direction: column;
    gap: 6px;
    cursor: pointer;
    z-index: 10001; /* Extremely high z-index to stay above EVERYTHING */
}

.menu-toggle .bar {
    width: 30px;
    height: 3px;
    background-color: #fff;
    border-radius: 5px;
    transition: all 0.3s ease-in-out;
}

/* Mobile Adjustments (Under 1024px) */
@media (max-width: 1024px) {
    .menu-toggle {
        display: flex; /* Show the hamburger on mobile */
    }
    
    .nav-links {
        position: fixed;
        top: 0;
        right: -100%; /* Hides the menu off-screen to the right by default */
        width: 300px; /* Slightly wider for better text fit */
        height: 100vh;
        /* Made the background darker and solid for perfect text contrast */
        background: rgba(5, 5, 8, 0.98); 
        backdrop-filter: blur(30px);
        -webkit-backdrop-filter: blur(30px);
        border-left: 1px solid var(--neon-cyan); /* Adding a cool neon border line */
        box-shadow: -20px 0 50px rgba(0,0,0,0.9);
        flex-direction: column;
        align-items: flex-start;
        padding: 120px 30px 30px; /* More padding at top to clear the header */
        transition: right 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 10000; /* Stays just below the toggle button, but above the site */
        display: flex; /* Ensure flex is active when sliding in */
    }
    
    /* When JavaScript adds the 'active' class, slide it in! */
    .nav-links.active {
        right: 0; 
    }
    
    .nav-links li {
        width: 100%;
        margin: 10px 0; /* Add spacing between items */
    }

    .nav-links a {
        font-size: 1.3rem; /* Slightly larger text for mobile tapping */
        font-weight: 600;
        display: block;
        width: 100%;
        padding: 12px 15px;
        color: #fff; /* Force pure white text */
        border-radius: 8px;
        transition: 0.3s;
    }

    /* Style the links when pressed/hovered on mobile */
    .nav-links a:hover, .nav-links a:active {
        background: rgba(0, 243, 255, 0.1);
        color: var(--neon-cyan);
        padding-left: 25px; /* Cool sliding text effect */
    }

    .nav-links .btn-download-sm {
        margin-top: 30px;
        text-align: center;
        background: var(--neon-cyan);
        color: var(--bg-dark) !important; /* Force dark text on cyan button */
        border: none;
        padding: 15px;
        font-weight: 800;
        border-radius: 12px;
    }
    
    .nav-links .btn-download-sm:hover {
        background: #fff;
        padding-left: 15px; /* Cancel the sliding text effect for the button */
    }
    
    /* Hamburger Animation -> Turns into an Neon 'X' */
    .menu-toggle.active .bar:nth-child(1) {
        transform: translateY(9px) rotate(45deg);
        background-color: var(--neon-cyan);
        box-shadow: 0 0 10px var(--neon-cyan);
    }
    .menu-toggle.active .bar:nth-child(2) {
        opacity: 0;
    }
    .menu-toggle.active .bar:nth-child(3) {
        transform: translateY(-9px) rotate(-45deg);
        background-color: var(--neon-cyan);
        box-shadow: 0 0 10px var(--neon-cyan);
    }
}
</style>
<script>
    // --- MOBILE NAVBAR TOGGLE LOGIC ---
const mobileMenu = document.getElementById('mobile-menu');
const navLinks = document.getElementById('nav-links');
const navItems = document.querySelectorAll('.nav-links li a');

// Toggle the menu open and closed
mobileMenu.addEventListener('click', () => {
    mobileMenu.classList.toggle('active');
    navLinks.classList.toggle('active');
});

// Close the menu automatically when a link is clicked
navItems.forEach(item => {
    item.addEventListener('click', () => {
        mobileMenu.classList.remove('active');
        navLinks.classList.remove('active');
    });
});
</script>

    <section id="hero" class="container">
        <canvas id="hero-canvas"></canvas>
        <div class="hero-grid">
            <div class="hero-content gs-up">
                <div class="badge"><div class="dot"></div> Full Stack Web Developer</div>
                <h1>Building Scalable <br><span style="color: var(--neon-cyan);" id="typewriter"></span></h1>
                <p>I am a Full-Stack Web Developer from Pakistan, bridging the gap between pixel-perfect frontend aesthetics and robust backend architectures. I don't just write code; I architect elegant solutions.</p>
                <div class="btn-group">
                    <a href="#projects" class="btn btn-primary">Explore Portfolio</a>
                    <a href="Muhammad Bilal Ifzal CV.pdf" download class="btn btn-neon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        Get My CV
                    </a>
                </div>
            </div>
            
           <div class="hero-img-wrapper gs-up" style="transition-delay: 0.2s;">
    <div class="hero-img-box tilt-card" data-tilt data-tilt-max="10" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.3">
        <img src="profile.jpeg" alt="Muhammad Bilal Profile">
    </div>
</div>
<style>
    /* The actual image styling with Auto-Crop */
.hero-img-box img { 
    width: 100%; 
    height: 100%; 
    object-fit: cover; /* This acts as the automatic crop */
    object-position: center top; /* Keeps your face perfectly centered! */
    border-radius: 30px; /* Gives it that modern rounded shape */
    border: 2px solid rgba(255,255,255,0.2); 
    box-shadow: inset 0 0 20px rgba(255,255,255,0.5), 0 20px 50px rgba(0,0,0,0.8); 
    z-index: 2; 
    position: relative; 
}
/* --- HERO SECTION MOBILE RESPONSIVENESS --- */
@media (max-width: 900px) {
    /* 1. Change grid to flex and reverse the column so the image is on top */
    .hero-grid {
        display: flex;
        flex-direction: column;
        gap: 40px;
        text-align: center; /* Centers the text nicely on mobile */
    }

    /* Keep the image size controlled on mobile */
    .hero-img-box {
        max-width: 320px;
        margin: 0 auto;
    }

    /* Centers the badge and paragraph */
    .hero-content .badge, 
    .hero-content p {
        margin-inline: auto;
    }

    /* 2. Stack the buttons and make them 100% width */
    .btn-group {
        flex-direction: column;
        width: 100%;
        gap: 15px;
    }

    .btn {
        width: 100%;
        justify-content: center; /* Keeps the text and icon perfectly centered */
    }
}
</style>
        </div>
    </section>

    <div class="container" style="padding-top: 0;">
        <div class="stats-container">
            <div class="stat-card gs-up">
                <div class="stat-num"><span class="counter" data-target="6">0</span><span>+</span></div>
                <div class="stat-text">Live Projects Built</div>
            </div>
            <div class="stat-card gs-up" style="transition-delay: 0.1s;">
                <div class="stat-num"><span class="counter" data-target="2">0</span><span>+</span></div>
                <div class="stat-text">Freelance Clients</div>
            </div>
            <div class="stat-card gs-up" style="transition-delay: 0.2s;">
                <div class="stat-num"><span class="counter" data-target="2">0</span></div>
                <div class="stat-text">Tech Internships</div>
            </div>
            <div class="stat-card gs-up" style="transition-delay: 0.3s;">
                <div class="stat-num"><span class="counter" data-target="100">0</span><span>%</span></div>
                <div class="stat-text">Code Passion</div>
            </div>
        </div>
    </div>

    <section id="skills" class="container">
    <h2 class="section-title gs-up">Technical Arsenal</h2>
    <p class="section-subtitle gs-up">The tools I use to architect</p>
    
    <div class="skills-grid gs-stagger">
        <div class="skill-item">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg" alt="HTML5">
            <span>HTML5</span>
        </div>
        <div class="skill-item">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg" alt="CSS3">
            <span>CSS3 & Flex</span>
        </div>
       
        <div class="skill-item">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg" alt="JavaScript">
            <span>JavaScript</span>
        </div>
        <div class="skill-item">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" alt="PHP">
            <span>PHP (OOP)</span>
        </div>
        <div class="skill-item">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg" alt="MySQL">
            <span>MySQL DB</span>
        </div>
        <div class="skill-item">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/laravel/laravel-original.svg" alt="Laravel">
            <span>Laravel</span>
        </div>

        <div class="skill-item">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/git/git-original.svg" alt="Git">
            <span>Git</span>
        </div>

        <div class="skill-item">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/github/github-original.svg" style="filter: invert(1);" alt="GitHub">
            <span>GitHub</span>
        </div>

        <div class="skill-item">
    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/bootstrap/bootstrap-original.svg" alt="Bootstrap">
    <span>Bootstrap</span>
</div>

<div class="skill-item">
    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/jquery/jquery-original.svg" alt="jQuery">
    <span>jQuery</span>
</div>

<div class="skill-item">
    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/tailwindcss/tailwindcss-original.svg" alt="Tailwind CSS">
    <span>Tailwind CSS</span>
</div>
    </div>
</section>

 <section id="projects" class="container">
        <h2 class="section-title gs-up">Featured Deployments</h2>
        <p class="section-subtitle gs-up">Showcasing total live environments and local architectures</p>

       <div class="project-grid">
            
    <div class="project-card tilt-card gs-up">
        <div class="project-img">
            <img src="pro (3).png" alt="Fitness Force Pro">
        </div>
        <div class="project-info">
            <div class="tech-stack"><span>FULL-STACK</span><span>UI/UX</span><span>DYNAMIC DB</span></div>
            <h3>Fitness Force Pro</h3>
            <p>A comprehensive web platform for fitness and gym management. Engineered with a focus on high performance, modern UI, and robust backend user data handling.</p>
            <div class="project-links">
                <a href="https://www.fitnessforcepro.com/" target="_blank" class="btn-live">View Live Project &rarr;</a>
            </div>
        </div>
    </div>

    <div class="project-card tilt-card gs-up" style="transition-delay: 0.1s;">
        <div class="project-img">
            <img src="pro (4).png" alt="Nexus Crypto Tracker" style="height:130%">
        </div>
        <div class="project-info">
            <div class="tech-stack"><span>JAVASCRIPT</span><span>FETCH API</span><span>ASYNC UI</span></div>
            <h3>Nexus Crypto Tracker</h3>
            <p>A dynamic dashboard utilizing asynchronous JavaScript to fetch live cryptocurrency market data, coin trends, and pricing without requiring page reloads.</p>
            <div class="project-links">
                <a href="https://nexuxcyrpto.wuaze.com/" target="_blank" class="btn-live">View Live Project &rarr;</a>
            </div>
        </div>
    </div>

    <div class="project-card tilt-card gs-up" style="transition-delay: 0.2s;">
        <div class="project-img">
            <img src="pro (5).png" alt="Multi-Language Translator">
        </div>
        <div class="project-info">
            <div class="tech-stack"><span>VANILLA JS</span><span>REST API</span><span>CSS3</span></div>
            <h3>Multi-Language Translator</h3>
            <p>A fast, responsive web application integrating powerful translation APIs with a clean, minimalist user interface for seamless text conversion globally.</p>
            <div class="project-links">
                <a href="https://bilaltranslator.lovestoblog.com/" target="_blank" class="btn-live">View Live Project &rarr;</a>
            </div>
        </div>
    </div>

    <div class="project-card tilt-card gs-up">
        <div class="project-img">
            <img src="Screenshot (1161).png" alt="COMS Management System">
        </div>
        <div class="project-info">
            <div class="tech-stack"><span>PHP OOP</span><span>MYSQL</span><span>SESSIONS</span></div>
            <h3>COMS Management System</h3>
            <p>A robust backend-heavy management application featuring secure user sessions, complex database queries, and a custom dashboard for data manipulation.</p>
            <div class="project-links">
                <a href="https://coms.infinityfreeapp.com/" target="_blank" class="btn-live">View Live Project &rarr;</a>
            </div>
        </div>
    </div>

    <div class="project-card tilt-card gs-up" style="transition-delay: 0.1s;">
        <div class="project-img">
            <img src="pro (1).png" alt="Blood Donation Platform">
        </div>
        <div class="project-info">
            <div class="tech-stack"><span>PHP</span><span>MYSQL</span><span>RELATIONAL DB</span></div>
            <h3>Blood Donation Platform</h3>
            <p>A critical system engineered to connect blood donors with recipients. Features secure authentication and complex database queries to match blood types geographically.</p>
            <div class="project-links">
                <a href="https://bloodlinkpro.kesug.com/" target="_blank" class="btn-live">View Live Project &rarr;</a>
            </div>
        </div>
    </div>

    <div class="project-card tilt-card gs-up" style="transition-delay: 0.2s;">
        <div class="project-img">
            <img src="pro (2).png" alt="Interactive CV Maker">
        </div>
        <div class="project-info">
            <div class="tech-stack"><span>HTML</span><span>CSS</span><span>DOM MANIPULATION</span></div>
            <h3>Interactive CV Maker</h3>
            <p>A dynamic tool allowing users to input their data into a complex form and instantly generate a formatted, print-ready resume layout using advanced DOM manipulation.</p>
            <div class="project-links">
                <a href="https://bilalcvmaker.lovestoblog.com/" target="_blank" class="btn-live">View Live Project &rarr;</a>
            </div>
        </div>
    </div>

</div>
    </section>

    <section id="achievements" class="container">
    <div class="section-header gs-up">
        <div class="section-subtitle">Verified Proof</div>
        <h2 class="section-title">Career <span>Milestones</span></h2>
    </div>

    <div class="milestone-list">
        
        <div class="milestone-item glass-panel gs-up">
            <div class="milestone-img-container tilt-card">
                <img src="my letter (2).jpeg" alt="112 Page Report">
                <div class="img-overlay-glow"></div>
            </div>
            <div class="milestone-content">
                <div class="badge-row">
                    <span class="m-badge cyan">DOCUMENTATION</span>
                    <span class="m-id">REF: MB-2026-01</span>
                </div>
                <h3>112-Page Technical Corpus</h3>
                <p>An exhaustive academic and professional document detailing the complete lifecycle of backend engineering. This 112-page report covers system architecture, database normalization, and logic flow, serving as a master blueprint for full-stack development.</p>
                <div class="m-footer">
                    <div class="m-status"><div class="dot green"></div> Verified Document</div>
                    <span class="m-metric">112 Pages</span>
                </div>
            </div>
        </div>

        <div class="milestone-item glass-panel gs-up" style="flex-direction: row-reverse;">
            <div class="milestone-img-container tilt-card">
                <img src="Screenshot (1147).png" alt="Arfa Karim Hackathon">
                <div class="img-overlay-glow gold"></div>
            </div>
            <div class="milestone-content">
                <div class="badge-row">
                    <span class="m-badge gold">COMPETITION</span>
                    <span class="m-id">Lahore, PK</span>
                </div>
                <h3>Arfa Karim Hackathon</h3>
                <p>Participated in one of Pakistan's most prestigious technology competitions at Arfa Software Technology Park. Competed against the brightest minds in Lahore to solve real-world problems through rapid logic and innovative web solutions.</p>
                <div class="m-footer">
                    <div class="m-status"><div class="dot gold"></div> Excellence Award</div>
                    <span class="m-metric">Participant</span>
                </div>
            </div>
        </div>

        <div class="milestone-item glass-panel gs-up">
            <div class="milestone-img-container tilt-card">
                <img src="my letter (3).jpeg" alt="Technorift Certificate">
                <div class="img-overlay-glow purple"></div>
            </div>
            <div class="milestone-content">
                <div class="badge-row">
                    <span class="m-badge purple">BACKEND SPECIALIST</span>
                    <span class="m-id">Technorift</span>
                </div>
                <h3>Technorift Experience Letter</h3>
                <p>Officially certified for excellence in PHP and MySQL engineering. During this tenure, I architected a dynamic card generation system that optimized data workflows and demonstrated high-level proficiency in relational database management.</p>
                <div class="m-footer">
                    <div class="m-status"><div class="dot purple"></div> Professional Letter</div>
                    <span class="m-metric">Certified</span>
                </div>
            </div>
        </div>

        <div class="milestone-item glass-panel gs-up" style="flex-direction: row-reverse;">
            <div class="milestone-img-container tilt-card">
                <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80" alt="Csoft Certificate">
                <div class="img-overlay-glow blue"></div>
            </div>
            <div class="milestone-content">
                <div class="badge-row">
                    <span class="m-badge blue">TEAM COLLABORATION</span>
                    <span class="m-id">Csoft Systems</span>
                </div>
                <h3>Csoft Systems Endorsement</h3>
                <p>Received official validation for full-stack contribution within a professional team environment. Recognized for debugging critical codebases and successfully translating complex UI/UX wireframes into functional, production-ready code.</p>
                <div class="m-footer">
                    <div class="m-status"><div class="dot blue"></div> Team Endorsed</div>
                    <span class="m-metric">Completed</span>
                </div>
            </div>
        </div>

    </div>
</section>
    <section id="network" class="container">
    <div class="section-header gs-up">
        <div class="section-subtitle">Digital Presence</div>
        <h2 class="section-title">My Professional <span>Network</span></h2>
    </div>

    <div class="network-grid">
        
        <a href="https://github.com/bilalifzal" target="_blank" class="network-card tilt-card gs-up" style="--brand-color: #ffffff;">
            <div class="network-bg-icon">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
            </div>
            <div class="network-content">
                <div class="network-header">
                    <div class="network-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                    </div>
                    <div class="network-status"><div class="dot white"></div> Repos Active</div>
                </div>
                <div class="network-body">
                    <h3>GitHub</h3>
                    <p>Explore my source code, open-source contributions, and live repository commits.</p>
                </div>
                <div class="network-footer">
                    <span>@mbilalifzal</span>
                    <span class="arrow">&rarr;</span>
                </div>
            </div>
        </a>

        <a href="https://www.linkedin.com/in/muhammad-bilal-ifzal-a82649375/" target="_blank" class="network-card tilt-card gs-up" style="--brand-color: #0a66c2; transition-delay: 0.1s;">
            <div class="network-bg-icon">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
            </div>
            <div class="network-content">
                <div class="network-header">
                    <div class="network-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                    </div>
                    <div class="network-status"><div class="dot blue"></div> Open to Work</div>
                </div>
                <div class="network-body">
                    <h3>LinkedIn</h3>
                    <p>Connect with me professionally. View my endorsements, experience, and full resume.</p>
                </div>
                <div class="network-footer">
                    <span>Muhammad Bilal</span>
                    <span class="arrow">&rarr;</span>
                </div>
            </div>
        </a>

        <a href="https://wa.me/923260102121" target="_blank" class="network-card tilt-card gs-up" style="--brand-color: #25d366; transition-delay: 0.2s;">
            <div class="network-bg-icon">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.88-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.356.194 1.861.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
            </div>
            <div class="network-content">
                <div class="network-header">
                    <div class="network-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.88-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.356.194 1.861.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    </div>
                    <div class="network-status"><div class="dot green"></div> Fast Response</div>
                </div>
                <div class="network-body">
                    <h3>WhatsApp</h3>
                    <p>Need to discuss an internship or freelance project immediately? Send me a direct message.</p>
                </div>
                <div class="network-footer">
                    <span>+92 326 0102121</span>
                    <span class="arrow">&rarr;</span>
                </div>
            </div>
        </a>

    </div>
</section>
<section id="certificates" class="container">
    <div class="section-header gs-up">
        <div class="section-subtitle">Verified Mastery</div>
        <h2 class="section-title">Academic & <span>Technical Credentials</span></h2>
    </div>

    <div class="project-grid">
        
       

        <div class="project-card tilt-card gs-project">
            <div class="project-img"> <img src="nitsep certificate.png" alt="London Cert">
           </div>
            <div class="project-info">
                <div class="tech-stack"><span>PROFESSIONAL</span></div>
                <h3>Frontend Web Development</h3>
                <p>Certified by Skilleducation Institute. [cite: 44] Specialized training in building high-performance, responsive user interfaces.</p>
                <div class="project-links">
                    <span class="btn-live">Skilleducation Institute</span> [cite: 44]
                </div>
            </div>
        </div>

        <div class="project-card tilt-card gs-project">
            <div class="project-img">
                <img src="cer (1).png" alt="London Cert">
            </div>
            <div class="project-info">
                <div class="tech-stack"><span style="border-color: var(--neon-purple); color: var(--neon-purple);">COURSERA</span></div>
                <h3>Responsive Website Basics</h3>
                <p>University of London. [cite: 45] Expertise in coding standard-compliant HTML, CSS, and JavaScript for modern web environments. [cite: 45]</p>
                <div class="project-links">
                    <span class="btn-live" style="border-color: var(--neon-purple); color: var(--neon-purple);">Univ. of London</span> [cite: 45]
                </div>
            </div>
        </div>

        <div class="project-card tilt-card gs-project">
            <div class="project-img"> <img src="cer (2).png" alt="London Cert">    </div>
            <div class="project-info">
                <div class="tech-stack"><span>VERIFIED</span></div>
                <h3>Web Development Intro</h3>
                <p>University Of Leeds. [cite: 46] Foundational mastery of the modern web ecosystem and development best practices. [cite: 46]</p>
                <div class="project-links">
                    <span class="btn-live">University Of Leeds</span> [cite: 46]
                </div>
            </div>
        </div>

        <div class="project-card tilt-card gs-project">
            <div class="project-img"> <img src="cer (3).png" alt="London Cert">     </div>
            <div class="project-info">
                <div class="tech-stack"><span style="border-color: #ff0055; color: #ff0055;">BACKEND</span></div>
                <h3>Web Apps for Everybody</h3>
                <p>University of Michigan. [cite: 47] Advanced understanding of server-side data handling and web application architecture. [cite: 47]</p>
                <div class="project-links">
                    <span class="btn-live" style="border-color: #ff0055; color: #ff0055;">Univ. of Michigan</span> [cite: 47]
                </div>
            </div>
        </div>

        <div class="project-card tilt-card gs-project">
            <div class="project-img"> <img src="nitsep certificate.png" alt="London Cert">  </div>
            <div class="project-info">
                <div class="tech-stack"><span>DESIGN</span></div>
                <h3>Advanced Responsive Styling</h3>
                <p>University of Michigan. [cite: 48] Mastering elite CSS frameworks and responsive design theory for production-ready apps. [cite: 49]</p>
                <div class="project-links">
                    <span class="btn-live">Univ. of Michigan</span> [cite: 49]
                </div>
            </div>
        </div>

    </div>
</section>
<style>
    /* --- SUPER FENTASTIC NETWORK GRID CSS --- */
.network-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 40px;
    margin-top: 40px;
}

.network-card {
    background: rgba(15, 15, 20, 0.75);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-bottom: 3px solid var(--brand-color); /* Bottom color border */
    border-radius: 24px;
    padding: 40px 30px;
    position: relative;
    overflow: hidden;
    text-decoration: none;
    color: #fff;
    display: block;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

/* The giant watermark icon in the background */
.network-bg-icon {
    position: absolute;
    right: -20px;
    bottom: -30px;
    width: 200px;
    height: 200px;
    opacity: 0.03; /* Very subtle */
    color: var(--brand-color);
    transition: 0.5s ease;
    transform: rotate(-15deg);
    pointer-events: none;
}

.network-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.6), 0 0 40px color-mix(in srgb, var(--brand-color) 20%, transparent);
    border-color: color-mix(in srgb, var(--brand-color) 50%, transparent);
}

.network-card:hover .network-bg-icon {
    opacity: 0.1;
    transform: rotate(0deg) scale(1.1);
}

.network-content {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.network-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 30px;
}

.network-icon {
    width: 50px;
    height: 50px;
    color: var(--brand-color);
    filter: drop-shadow(0 0 10px color-mix(in srgb, var(--brand-color) 40%, transparent));
}

/* Pulsing Status Dot */
.network-status {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-muted);
    background: rgba(255, 255, 255, 0.05);
    padding: 6px 12px;
    border-radius: 50px;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.network-status .dot { width: 8px; height: 8px; border-radius: 50%; animation: pulse 2s infinite; }
.network-status .dot.white { background: #fff; box-shadow: 0 0 10px #fff; }
.network-status .dot.blue { background: #0a66c2; box-shadow: 0 0 10px #0a66c2; }
.network-status .dot.green { background: #25d366; box-shadow: 0 0 10px #25d366; }

.network-body flex-grow: 1; margin-bottom: 30px; }
.network-body h3 { font-size: 1.8rem; margin-bottom: 10px; font-family: 'Space Grotesk', sans-serif; }
.network-body p { color: var(--text-muted); font-size: 1rem; line-height: 1.6; }

.network-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.95rem;
    color: var(--brand-color);
    font-weight: 700;
}

.network-footer .arrow {
    font-size: 1.2rem;
    transition: 0.3s;
}

.network-card:hover .network-footer .arrow {
    transform: translateX(10px);
}

@media (max-width: 768px) {
    .network-grid { grid-template-columns: 1fr; }
}
</style>

<script>
    // --- SUPER FENTASTIC INVISIBLE AI VOICE ---
document.addEventListener("DOMContentLoaded", () => {
    
    // Check if the voice already played this session so it doesn't annoy them on refresh
    if (sessionStorage.getItem('aiVoicePlayed') === 'true') {
        return; 
    }

    const speakAboutBilal = () => {
        // Check if the browser supports the AI Speech API
        if ('speechSynthesis' in window) {
            const speech = new SpeechSynthesisUtterance();
            
            // THE SCRIPT: What the AI will say. You can change this text!
            speech.text = "Welcome to the digital portfolio of Muhammad Bilal. A passionate Full Stack Architect and Computer Science undergrad from Faisalabad, Pakistan. Specializing in PHP, MySQL, and modern web technologies. Explore the architecture of tomorrow, today.";
            
            // Voice Settings
            speech.volume = 1;      // Full volume
            speech.rate = 0.95;     // Slightly slower for a professional, clear tone
            speech.pitch = 1.2;     // Slightly higher pitch for a female-sounding voice

            // Attempt to load a female English voice if available on the device
            const voices = window.speechSynthesis.getVoices();
            const femaleVoice = voices.find(voice => 
                voice.name.includes('Female') || 
                voice.name.includes('Samantha') || 
                voice.name.includes('Google UK English Female')
            );
            
            if (femaleVoice) {
                speech.voice = femaleVoice;
            }

            // Speak!
            window.speechSynthesis.speak(speech);
            
            // Save to session storage so it only plays exactly once per visit
            sessionStorage.setItem('aiVoicePlayed', 'true');
        }
    };

    // THE GHOST TRIGGER: Browsers block autoplay. 
    // This triggers the voice the exact moment they move their mouse or scroll!
    const triggerEvents = ['mousemove', 'scroll', 'touchstart', 'click'];
    
    const fireOnce = () => {
        speakAboutBilal();
        // Remove the triggers instantly so it doesn't repeat
        triggerEvents.forEach(event => document.removeEventListener(event, fireOnce));
    };

    triggerEvents.forEach(event => document.addEventListener(event, fireOnce, { once: true }));
});

// Fix for some browsers that take a second to load the voices
window.speechSynthesis.onvoiceschanged = () => {
    window.speechSynthesis.getVoices();
};
</script>
    <section id="about" class="container">
        <h2 class="section-title gs-up">The Architecture</h2>
        <p class="section-subtitle gs-up">Deep Dive Into My Data</p>
        
        <div class="about-grid">
            <div class="glass-panel gs-left">
                <div class="info-block">
                    <h3>Logic & Creativity Combined</h3>
                    <p>My journey in Computer Science is driven by a profound passion for creating structured, highly-functional web ecosystems. I specialize in writing clean, modular <strong>PHP logic</strong>, designing normalized <strong>MySQL databases</strong>, and pairing them with highly engaging, dynamic frontend interfaces utilizing modern <strong>JavaScript</strong>.</p>
                    <p>Beyond the core LAMP stack, I have a strong analytical interest in AI/ML. Whether I am debugging complex backend routing or exploring data algorithms, I bring discipline, dedication, and a rigorous problem-solving mindset to every project I touch.</p>
                </div>
                
                <h3 style="color: var(--neon-cyan); font-size: 1.5rem; margin: 30px 0 15px; font-family: 'Space Grotesk';">Core Services</h3>
                <div class="services-grid">
                    <div class="service-box">
                        <h4>Frontend Engineering</h4>
                        <p>Pixel-perfect, responsive UI/UX using HTML5, CSS3, JS, and GSAP animations.</p>
                    </div>
                    <div class="service-box">
                        <h4>Backend Architecture</h4>
                        <p>Secure routing, OOP principles, and robust logic using PHP and modern frameworks.</p>
                    </div>
                    <div class="service-box">
                        <h4>Database Management</h4>
                        <p>Designing efficient relational schemas and complex queries in MySQL.</p>
                    </div>
                    <div class="service-box">
                        <h4>Freelance Solutions</h4>
                        <p>End-to-end custom web applications delivered to client specifications.</p>
                    </div>
                </div>
            </div>
            
            <div class="glass-panel gs-right">
                <div class="info-block">
                    <h3>Academic & Growth Trajectory</h3>
                    <p>Formal education provides the theory; my side projects and internships provide the reality. I am constantly pushing to bridge the gap between academic concepts and enterprise-grade software architecture.</p>
                    
                    <div class="edu-card">
                        <h4 style="font-size: 1.3rem; color: #fff; margin-bottom: 5px;">Bachelors in Computer Science</h4>
                        <p style="color: var(--neon-cyan); font-size: 1rem; margin-bottom: 12px; font-weight: 700; letter-spacing: 1px;">Current Status: 6th Semester</p>
                        <p style="margin-bottom: 0; font-size: 1.05rem; line-height: 1.6;">Actively mastering Object-Oriented Programming (OOP), Data Structures, and Database Management Systems. I am heavily focused on preparing these core concepts for technical interviews to secure a placement at top software houses like Devsinc or 10Pearls.</p>
                    </div>
                    
                    <div class="edu-card" style="border-left-color: var(--neon-cyan); margin-top: 20px;">
                        <h4 style="font-size: 1.3rem; color: #fff; margin-bottom: 5px;">Continuous Upskilling</h4>
                        <p style="color: var(--text-muted); font-size: 1.05rem; margin-bottom: 0; line-height: 1.6;">Actively upskilling in the <strong>Laravel</strong> framework to transition my raw PHP knowledge into building structured, scalable, and secure enterprise-level MVC applications.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="services" class="container">
    <div class="section-header gs-up">
        <div class="section-subtitle">What I Deliver</div>
        <h2 class="section-title">Premium <span>Services</span></h2>
    </div>

    <div class="services-premium-grid">
        
        <div class="service-premium-card glass-panel tilt-card gs-up" style="--srv-color: var(--neon-cyan);">
            <div class="srv-bg-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
            </div>
            <div class="srv-content">
                <div class="srv-header">
                    <div class="srv-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                    </div>
                    <span class="srv-id">SRV-01</span>
                </div>
                <h3>Frontend Engineering</h3>
                <p>Translating UI/UX designs into pixel-perfect, highly responsive, and interactive web experiences that engage users across all devices[cite: 44, 45, 48].</p>
                <div class="srv-tech-list">
                    <span>HTML5</span><span>CSS3</span><span>JavaScript</span><span>Bootstrap</span>
                </div>
            </div>
        </div>

        <div class="service-premium-card glass-panel tilt-card gs-up" style="--srv-color: var(--neon-purple); transition-delay: 0.1s;">
            <div class="srv-bg-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
            </div>
            <div class="srv-content">
                <div class="srv-header">
                    <div class="srv-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                    </div>
                    <span class="srv-id">SRV-02</span>
                </div>
                <h3>Backend Architecture</h3>
                <p>Architecting secure, scalable server-side logic and robust RESTful APIs to power dynamic web applications seamlessly[cite: 8, 47].</p>
                <div class="srv-tech-list">
                    <span>PHP (OOP)</span><span>Laravel</span><span>REST APIs</span>
                </div>
            </div>
        </div>

        <div class="service-premium-card glass-panel tilt-card gs-up" style="--srv-color: #ffbd2e; transition-delay: 0.2s;">
            <div class="srv-bg-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
            </div>
            <div class="srv-content">
                <div class="srv-header">
                    <div class="srv-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                    </div>
                    <span class="srv-id">SRV-03</span>
                </div>
                <h3>Database Management</h3>
                <p>Designing normalized, highly relational database schemas featuring RBAC and complete data integrity for complex systems[cite: 20, 28].</p>
                <div class="srv-tech-list">
                    <span>MySQL</span><span>InnoDB</span><span>Relational Schemas</span>
                </div>
            </div>
        </div>

        <div class="service-premium-card glass-panel tilt-card gs-up" style="--srv-color: #22c55e; transition-delay: 0.3s;">
            <div class="srv-bg-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>
            </div>
            <div class="srv-content">
                <div class="srv-header">
                    <div class="srv-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>
                    </div>
                    <span class="srv-id">SRV-04</span>
                </div>
                <h3>Full-Stack Solutions</h3>
                <p>End-to-end deployment of production-ready applications, from POS systems to Medical Inventories and LMS portals[cite: 15, 18, 20].</p>
                <div class="srv-tech-list">
                    <span>LAMP Stack</span><span>System Architecture</span>
                </div>
            </div>
        </div>

    </div>
</section>

<section id="system-architecture" class="container">
    <div class="section-header gs-up">
        <div class="section-subtitle">System Design</div>
        <h2 class="section-title">Interactive <span>Architecture</span></h2>
        <p style="text-align: center; color: var(--text-muted); margin-top: 15px; font-size: 1.1rem; max-width: 650px; margin-inline: auto;">
            A live visualization of my standard full-stack data flow. Click "Simulate Request" to trace a secure transaction from the client UI to the InnoDB database.
        </p>
    </div>

    <div class="arch-wrapper glass-panel gs-up tilt-card" style="margin-top: 40px; padding: 50px 30px; position: relative; overflow: hidden;">
        
        <div style="text-align: center; margin-bottom: 50px;">
            <button id="btn-simulate" class="btn-live" style="display: inline-flex; border-color: var(--neon-cyan); color: var(--neon-cyan); font-family: 'Space Grotesk'; font-size: 1.1rem; padding: 12px 30px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 10px;"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                Simulate Data Request
            </button>
        </div>

        <div class="arch-flow-container">
            
            <div class="arch-node" id="node-frontend" style="--node-color: #00f3ff;">
                <div class="node-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                </div>
                <h3>Client UI</h3>
                <p>HTML5 / JS / DOM</p>
                <div class="node-status">Awaiting Input...</div>
            </div>

            <div class="arch-connection">
                <div class="line-track">
                    <div class="data-packet" id="packet-1"></div>
                </div>
                <span class="flow-label">JSON POST Request</span>
            </div>

            <div class="arch-node" id="node-backend" style="--node-color: #b026ff;">
                <div class="node-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                </div>
                <h3>PHP API Layer</h3>
                <p>OOP Logic & RBAC</p>
                <div class="node-status">Idle</div>
            </div>

            <div class="arch-connection">
                <div class="line-track">
                    <div class="data-packet" id="packet-2"></div>
                </div>
                <span class="flow-label">Prepared PDO Query</span>
            </div>

            <div class="arch-node" id="node-database" style="--node-color: #ffbd2e;">
                <div class="node-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                </div>
                <h3>MySQL DB</h3>
                <p>Relational / InnoDB</p>
                <div class="node-status">Standby</div>
            </div>

        </div>
    </div>
</section>
<style>
    /* --- INTERACTIVE ARCHITECTURE CSS --- */
.arch-flow-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    position: relative;
    padding: 20px 0;
}

.arch-node {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    background: rgba(0, 0, 0, 0.4);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-top: 3px solid var(--node-color);
    padding: 30px 20px;
    border-radius: 16px;
    position: relative;
    transition: 0.3s;
    z-index: 2;
}

/* Active State for Nodes during Simulation */
.arch-node.active {
    background: color-mix(in srgb, var(--node-color) 10%, rgba(0,0,0,0.6));
    border-color: var(--node-color);
    box-shadow: 0 0 30px color-mix(in srgb, var(--node-color) 30%, transparent);
    transform: translateY(-5px);
}

.node-icon {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--node-color);
    margin-bottom: 15px;
    transition: 0.3s;
}

.arch-node.active .node-icon {
    background: var(--node-color);
    color: var(--bg-dark);
    box-shadow: 0 0 20px var(--node-color);
}

.arch-node h3 { font-size: 1.3rem; color: #fff; margin-bottom: 5px; font-family: 'Space Grotesk', sans-serif; }
.arch-node p { font-size: 0.85rem; color: var(--text-muted); font-family: 'JetBrains Mono', monospace; margin-bottom: 15px; }

.node-status {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    padding: 4px 10px;
    border-radius: 4px;
    background: rgba(255,255,255,0.05);
    color: var(--text-muted);
}
.arch-node.active .node-status { color: var(--node-color); background: color-mix(in srgb, var(--node-color) 15%, transparent); }

/* The Connection Tracks */
.arch-connection {
    flex: 1.5;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 1;
}

.line-track {
    width: 100%;
    height: 2px;
    background: rgba(255, 255, 255, 0.1);
    position: relative;
    overflow: hidden;
}

.flow-label {
    margin-top: 15px;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--text-muted);
    font-family: 'JetBrains Mono', monospace;
    opacity: 0.5;
    transition: 0.3s;
}

.arch-connection.active .flow-label { color: var(--neon-cyan); opacity: 1; text-shadow: 0 0 10px var(--neon-cyan); }

/* The Traveling Data Packets */
.data-packet {
    position: absolute;
    top: -3px;
    left: -20px;
    width: 8px;
    height: 8px;
    background: var(--neon-cyan);
    border-radius: 50%;
    box-shadow: 0 0 15px 5px var(--neon-cyan);
    opacity: 0;
}

/* Forward Animation */
@keyframes transmit {
    0% { left: 0; opacity: 1; }
    50% { opacity: 1; transform: scale(1.5); }
    100% { left: 100%; opacity: 0; }
}

/* Return Animation (Success response) */
@keyframes return {
    0% { left: 100%; opacity: 1; background: #22c55e; box-shadow: 0 0 15px 5px #22c55e; }
    50% { opacity: 1; transform: scale(1.5); }
    100% { left: 0; opacity: 0; }
}

@media (max-width: 900px) {
    .arch-flow-container { flex-direction: column; gap: 20px; }
    .arch-connection { height: 60px; width: 2px; }
    .line-track { width: 2px; height: 100%; }
    .flow-label { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); width: max-content; }
    @keyframes transmit { 0% { top: 0; opacity: 1; } 100% { top: 100%; opacity: 0; } }
    @keyframes return { 0% { top: 100%; opacity: 1; } 100% { top: 0; opacity: 0; } }
}
</style>
<style>
    /* --- SUPER FENTASTIC SERVICES CSS --- */
.services-premium-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 35px;
    margin-top: 20px;
}

.service-premium-card {
    position: relative;
    padding: 40px 30px !important; /* Override standard glass-panel padding for this specific card */
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border-top: 2px solid var(--srv-color) !important; /* Unique color top border */
    background: linear-gradient(180deg, color-mix(in srgb, var(--srv-color) 5%, transparent) 0%, rgba(15,15,20,0.8) 100%) !important;
}

.service-premium-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.6), 0 0 30px color-mix(in srgb, var(--srv-color) 15%, transparent);
    border-color: color-mix(in srgb, var(--srv-color) 50%, transparent);
}

/* Faded Watermark Icon in Background */
.srv-bg-icon {
    position: absolute;
    right: -20px;
    bottom: -20px;
    width: 150px;
    height: 150px;
    color: var(--srv-color);
    opacity: 0.05;
    transform: rotate(-10deg);
    transition: 0.5s ease;
    pointer-events: none;
}

.service-premium-card:hover .srv-bg-icon {
    opacity: 0.15;
    transform: rotate(0deg) scale(1.1);
}

.srv-content {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.srv-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 25px;
}

.srv-icon {
    color: var(--srv-color);
    filter: drop-shadow(0 0 10px color-mix(in srgb, var(--srv-color) 50%, transparent));
    background: rgba(255,255,255,0.03);
    padding: 12px;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.05);
}

.srv-id {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--text-muted);
    background: rgba(255,255,255,0.05);
    padding: 4px 10px;
    border-radius: 50px;
}

.srv-content h3 {
    font-size: 1.6rem;
    margin-bottom: 15px;
    color: #fff;
    font-family: 'Space Grotesk', sans-serif;
}

.srv-content p {
    color: var(--text-muted);
    font-size: 1.05rem;
    line-height: 1.7;
    margin-bottom: 30px;
    flex-grow: 1;
}

/* Inner Tech Stack Pills */
.srv-tech-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    border-top: 1px solid rgba(255,255,255,0.05);
    padding-top: 20px;
}

.srv-tech-list span {
    font-size: 0.75rem;
    font-family: 'JetBrains Mono', monospace;
    font-weight: 700;
    color: var(--srv-color);
    background: color-mix(in srgb, var(--srv-color) 10%, transparent);
    padding: 5px 12px;
    border-radius: 6px;
    border: 1px solid color-mix(in srgb, var(--srv-color) 20%, transparent);
}

@media (max-width: 768px) {
    .services-premium-grid { grid-template-columns: 1fr; }
}
</style>

    <section id="experience" class="container">
        <h2 class="section-title gs-up">Professional Milestones</h2>
        <p class="section-subtitle gs-up">Real-world application of logic</p>
        
        <div class="glass-panel gs-up" style="max-width: 1000px; margin: 0 auto; padding: 4rem;">
            <div class="timeline">
                <div class="timeline-item">
                    <span class="timeline-date">Current</span>
                    <h4>Freelance Full-Stack Developer</h4>
                    <h5>Upwork & Independent Global Clients</h5>
                    <p>Operating as an independent contractor delivering bespoke full-stack solutions. Successfully architected interactive UI systems and complex web applications—such as a dynamic Quiz Web App—involving advanced JavaScript DOM manipulation, complex state management, and robust backend data processing.</p>
                </div>
                <div class="timeline-item">
                    <span class="timeline-date">Past Internship</span>
                    <h4>PHP Backend Developer Intern</h4>
                    <h5>Technorift</h5>
                    <p>Tasked with the end-to-end engineering of a dynamic card generation system from scratch. Handled the complete relational database architecture in MySQL, authored the server-side data processing logic in pure PHP, and integrated it seamlessly with the frontend interface for a flawless user journey.</p>
                </div>
                <div class="timeline-item">
                    <span class="timeline-date">Past Internship</span>
                    <h4>Web Development Intern</h4>
                    <h5>Csoft Systems</h5>
                    <p>Thrived in a fast-paced, agile team environment. Contributed to building, debugging, and maintaining multiple collaborative web projects, significantly enhancing my abilities in version control, code review, and translating UI/UX wireframes into functional, responsive web components.</p>
                </div>
            </div>
        </div>
    </section>

    

<section id="live-tools" class="container">
    <div class="section-header gs-up">
        <div class="section-subtitle">Public Utilities</div>
        <h2 class="section-title">Live <span>SaaS Tools</span></h2>
    </div>

    <div class="tools-grid gs-stagger">
        
        <div class="tool-card glass-panel tilt-card" style="border-top-color: var(--neon-cyan);">
            <h3>Want a Free Professional CV?</h3>
            <p>Use my custom-built CV Maker to securely input your details and instantly download a beautifully formatted, print-ready resume.</p>
            <a href="https://bilalcvmaker.lovestoblog.com" target="_blank" class="btn-live">Go To Live Project &rarr;</a>
        </div>

        <div class="tool-card glass-panel tilt-card" style="border-top-color: #ffbd2e;">
            <h3>Need to Check Live Rates?</h3>
            <p>Access real-time market data, instant fiat currency conversions, and live cryptocurrency tracking directly through the Nexus API.</p>
            <a href="https://nexuxcyrpto.wuaze.com/" target="_blank" class="btn-live" style="color: #ffbd2e; border-color: #ffbd2e;">Go To Live Project &rarr;</a>
        </div>

        <div class="tool-card glass-panel tilt-card" style="border-top-color: var(--neon-purple);">
            <h3>Translate Any Language?</h3>
            <p>Break global language barriers instantly. Paste your text and seamlessly translate it into dozens of international languages in real-time.</p>
            <a href="https://bilaltranslator.lovestoblog.com/" target="_blank" class="btn-live" style="color: var(--neon-purple); border-color: var(--neon-purple);">Go To Live Project &rarr;</a>
        </div>

    </div>
</section>


<style>
    /* --- SUPER CLASSIC LIVE TOOLS CSS --- */
.tools-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 35px;
    margin-top: 40px;
}

.tool-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 45px 35px !important; /* Override standard glass-panel padding */
    border-top-width: 4px !important; /* Thick colored top border */
    background: linear-gradient(180deg, rgba(255,255,255,0.03) 0%, rgba(5,5,8,0.6) 100%) !important;
    transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.tool-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.5);
    background: linear-gradient(180deg, rgba(255,255,255,0.05) 0%, rgba(5,5,8,0.8) 100%) !important;
}

.tool-card h3 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 1.8rem;
    color: #fff;
    margin-bottom: 15px;
    line-height: 1.3;
    letter-spacing: -0.5px;
}

.tool-card p {
    color: var(--text-muted);
    font-size: 1.05rem;
    line-height: 1.7;
    margin-bottom: 40px;
    flex-grow: 1;
}

/* Button overrides for the tool cards */
.tool-card .btn-live {
    width: 100%;
    margin-top: auto;
    background: rgba(255,255,255,0.02);
}

.tool-card:hover .btn-live {
    background: rgba(255,255,255,0.1);
    box-shadow: none;
    transform: translateY(-3px);
}

@media (max-width: 768px) {
    .tools-grid { grid-template-columns: 1fr; }
}
</style>






<style>
/* --- COMPLETE MILESTONE SHOWCASE STYLING --- */

.milestone-list { 
    display: flex; 
    flex-direction: column; 
    gap: 40px; 
    margin-top: 50px; 
}

/* 1. THE MAIN CARD FIX */
.milestone-item {
    display: flex; /* This was missing! Aligns image and text side-by-side */
    gap: 40px;
    align-items: center;
    padding: 40px !important;
    opacity: 1 !important; 
    visibility: visible !important;
    transform: translateY(0) !important; /* Forces GSAP to stop hiding it */
}

/* 2. THE IMAGE CONTAINER FIX */
.milestone-img-container {
    flex: 0 0 45%; /* Image takes 45% of the card space */
    border-radius: 16px;
    overflow: hidden;
    position: relative;
    border: 1px solid var(--glass-border);
}

.milestone-img-container img {
    width: 100%;
    height: auto;
    display: block;
    opacity: 1 !important;
    filter: brightness(1) !important; /* Keeps image perfectly bright */
    transition: 0.5s ease;
}

/* 3. THE HOVER GLOW FIX */
.img-overlay-glow {
    opacity: 0 !important; /* Hidden by default */
    transition: 0.5s ease;
    position: absolute;
    inset: 0;
    pointer-events: none; 
    z-index: 2;
}

/* Restored Glow Colors */
.img-overlay-glow.gold { background: linear-gradient(to top, rgba(255, 189, 46, 0.5), transparent); }
.img-overlay-glow.purple { background: linear-gradient(to top, rgba(176, 38, 255, 0.5), transparent); }
.img-overlay-glow.blue { background: linear-gradient(to top, rgba(0, 119, 255, 0.5), transparent); }

/* Only show glow and zoom on hover */
.milestone-item:hover .img-overlay-glow {
    opacity: 1 !important; 
}

.milestone-item:hover .milestone-img-container img {
    transform: scale(1.05);
}

/* 4. CONTENT & BADGE STYLING */
.milestone-content { flex: 1; }

.badge-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.m-badge { padding: 6px 14px; border-radius: 6px; font-size: 0.75rem; font-weight: 800; border: 1px solid; }
.m-badge.cyan { color: var(--neon-cyan); border-color: var(--neon-cyan); background: rgba(0, 243, 255, 0.1); }
.m-badge.gold { color: #ffbd2e; border-color: #ffbd2e; background: rgba(255, 189, 46, 0.1); }
.m-badge.purple { color: var(--neon-purple); border-color: var(--neon-purple); background: rgba(176, 38, 255, 0.1); }
.m-badge.blue { color: var(--neon-blue); border-color: var(--neon-blue); background: rgba(0, 119, 255, 0.1); }

.m-id { font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; color: var(--text-muted); opacity: 0.6; }

.milestone-content h3 { font-size: 2rem; margin-bottom: 15px; color: #fff; letter-spacing: -1px; }
.milestone-content p { color: var(--text-muted); font-size: 1.05rem; line-height: 1.7; margin-bottom: 30px; }

/* 5. FOOTER STYLING */
.m-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px; }
.m-status { font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 8px; }
.m-status .dot { width: 8px; height: 8px; border-radius: 50%; }
.m-status .dot.green { background: #22c55e; box-shadow: 0 0 10px #22c55e; }
.m-status .dot.gold { background: #ffbd2e; box-shadow: 0 0 10px #ffbd2e; }
.m-status .dot.purple { background: var(--neon-purple); box-shadow: 0 0 10px var(--neon-purple); }
.m-status .dot.blue { background: var(--neon-blue); box-shadow: 0 0 10px var(--neon-blue); }

.m-metric { font-family: 'JetBrains Mono', monospace; color: #fff; font-weight: 700; font-size: 0.9rem; }

/* 6. RESPONSIVE DESIGN FOR MOBILE */
@media (max-width: 900px) {
    .milestone-item { flex-direction: column !important; padding: 30px !important; }
    .milestone-img-container { flex: 0 0 auto; width: 100%; }
}


</style>







<style>
    /* --- ULTRA PRO MAX CERTIFICATE VAULT CSS --- */

/* The Grid: Ensures 3 cards per row on desktop and 1 on mobile */
.project-grid { 
    display: grid; 
    grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); 
    gap: 40px; 
    perspective: 1200px; 
}

/* The Card: Deep Glassmorphism with 3D Transform support */
.project-card { 
    background: var(--bg-card); 
    border: 1px solid var(--glass-border); 
    border-top: 1px solid rgba(255,255,255,0.2); 
    border-left: 1px solid rgba(255,255,255,0.2);
    border-radius: 24px; 
    overflow: hidden; 
    position: relative; 
    display: flex; 
    flex-direction: column;
    box-shadow: 0 25px 50px rgba(0,0,0,0.5); 
    transform-style: preserve-3d; 
    transition: transform 0.1s;
}

/* Image Frame: High-end display for certificate scans */
.project-img { 
    height: 240px; 
    background: #050508; 
    border-bottom: 1px solid var(--glass-border); 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    position: relative; 
    overflow: hidden; 
}

.project-img img {
    width: 100%;
    height: 100%;
    object-fit: contain; /* Changed from cover so certificates don't crop */
    opacity: 1 !important; /* Forces the image to NEVER be dim */
    background: rgba(255, 255, 255, 0.05); /* Helps transparent PNGs show up */
    transition: 0.5s ease;
}

.project-card:hover .project-img img {
    opacity: 1;
    transform: scale(1.05);
}

/* Info Section: Floating depth effect */
.project-info { 
    padding: 35px; 
    flex-grow: 1; 
    display: flex; 
    flex-direction: column; 
    transform: translateZ(40px); 
}

/* Tech-Stack / Badge Styling */
.tech-stack { 
    display: flex; 
    flex-wrap: wrap; 
    gap: 10px; 
    margin-bottom: 20px; 
}

.tech-stack span { 
    background: rgba(0, 243, 255, 0.1); 
    border: 1px solid var(--neon-cyan); 
    padding: 6px 14px; 
    border-radius: 8px; 
    font-size: 0.85rem; 
    font-weight: 700; 
    color: #fff; 
    text-shadow: 0 0 8px rgba(0, 243, 255, 0.8); 
    letter-spacing: 1px;
    text-transform: uppercase;
}

.project-info h3 { 
    font-size: 1.8rem; 
    margin-bottom: 15px; 
    color: #fff; 
    font-family: 'Space Grotesk', sans-serif;
}

.project-info p { 
    color: var(--text-muted); 
    font-size: 1rem; 
    line-height: 1.7; 
    margin-bottom: 30px; 
    flex-grow: 1; 
}

/* Verification Button / Link */
.project-links { 
    display: flex; 
    gap: 20px; 
}

.btn-live { 
    flex: 1; 
    text-align: center; 
    padding: 14px; 
    border-radius: 10px; 
    text-decoration: none; 
    font-size: 0.9rem; 
    font-weight: 700; 
    transition: 0.3s; 
    display: flex; 
    justify-content: center; 
    align-items: center; 
    background: rgba(255, 255, 255, 0.03); 
    color: var(--text-muted); 
    border: 1px solid var(--glass-border); 
}

.project-card:hover .btn-live {
    border-color: var(--neon-cyan);
    color: var(--neon-cyan);
    background: rgba(0, 243, 255, 0.05);
    box-shadow: inset 0 0 10px rgba(0, 243, 255, 0.1);
}

/* Responsiveness */
@media (max-width: 768px) {
    .project-grid { grid-template-columns: 1fr; }
}
</style>

<section id="architecture" class="container">
    <div class="section-header gs-up">
        <div class="section-subtitle">Behind The Scenes</div>
        <h2 class="section-title">Raw Logic vs <span>Final UI</span></h2>
        <p style="text-align: center; color: var(--text-muted); margin-top: 15px; font-size: 1.1rem;">
            Drag the slider to reveal how my raw backend code translates into pixel-perfect frontend design.
        </p>
    </div>

    <div class="glass-panel gs-up" style="padding: 15px; margin-top: 40px;">
        <div class="comparison-slider" id="comp-slider">
            
            <div class="img-ui" style="background-image: url('https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80');"></div>
            
            <div class="img-code" id="code-layer" style="background-image: url('https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=1200&q=80');"></div>
            
            <div class="slider-handle" id="slider-handle">
                <div class="slider-button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 9l-4 4 4 4M16 9l4 4-4 4"></path></svg>
                </div>
            </div>

            <span class="slide-label label-left">RAW PHP/MYSQL</span>
            <span class="slide-label label-right">FINAL PRODUCT</span>
        </div>
    </div>
</section>
<style>
    /* --- CODE VS UI SLIDER CSS --- */
.comparison-slider {
    position: relative;
    width: 100%;
    height: 500px;
    border-radius: 16px;
    overflow: hidden;
    cursor: ew-resize;
    box-shadow: inset 0 0 20px rgba(0,0,0,0.8);
}

.img-ui, .img-code {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: left top;
    background-repeat: no-repeat;
}

.img-ui { z-index: 1; }

.img-code {
    z-index: 2;
    width: 50%; /* Starts perfectly in the middle */
    border-right: 2px solid var(--neon-cyan);
    box-shadow: 5px 0 15px rgba(0, 243, 255, 0.2);
}

/* The Draggable Neon Handle */
.slider-handle {
    position: absolute;
    top: 0;
    left: 50%; /* Starts perfectly in the middle */
    bottom: 0;
    width: 4px;
    background: var(--neon-cyan);
    z-index: 3;
    transform: translateX(-50%);
    box-shadow: 0 0 15px var(--neon-cyan);
    pointer-events: none; /* Let the container handle the mouse events */
}

.slider-button {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 40px;
    height: 40px;
    background: var(--bg-dark);
    border: 2px solid var(--neon-cyan);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--neon-cyan);
    box-shadow: 0 0 20px rgba(0, 243, 255, 0.5);
}

.slider-button svg { width: 24px; height: 24px; }

/* The Floating Badges */
.slide-label {
    position: absolute;
    bottom: 20px;
    padding: 8px 16px;
    background: rgba(2, 2, 5, 0.8);
    backdrop-filter: blur(10px);
    border: 1px solid var(--glass-border);
    border-radius: 8px;
    color: #fff;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 1px;
    z-index: 4;
    pointer-events: none;
}

.label-left { left: 20px; border-left: 3px solid var(--neon-cyan); }
.label-right { right: 20px; border-right: 3px solid var(--neon-purple); }

@media (max-width: 768px) {
    .comparison-slider { height: 350px; }
}
</style>

<section id="project-metrics" class="container">
    <div class="section-header gs-up">
        <div class="section-subtitle">Engineering Taxonomy</div>
        <h2 class="section-title">Development <span>Metrics</span></h2>
        <p style="text-align: center; color: var(--text-muted); margin-top: 15px; font-size: 1.1rem;">
            A quantitative breakdown of my deployed architectures, ranging from raw UI clones to complex relational databases.
        </p>
    </div>

    <div class="metrics-grid gs-stagger">
        
        <div class="metric-card glass-panel tilt-card" style="--metric-color: #00f3ff;">
            <div class="metric-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            </div>
            <div class="metric-num"><span class="counter" data-target="4">0</span></div>
            <h3 class="metric-title">Management Systems</h3>
            <p class="metric-desc">LMS, POS, and Employee Admin Portals[cite: 18, 20, 40].</p>
        </div>

        <div class="metric-card glass-panel tilt-card" style="--metric-color: #b026ff;">
            <div class="metric-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
            </div>
            <div class="metric-num"><span class="counter" data-target="8">0</span></div>
            <h3 class="metric-title">Full-Stack Architectures</h3>
            <p class="metric-desc">End-to-end PHP/MySQL apps with secure RBAC[cite: 20].</p>
        </div>

        <div class="metric-card glass-panel tilt-card" style="--metric-color: #ffbd2e;">
            <div class="metric-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
            </div>
            <div class="metric-num"><span class="counter" data-target="3">0</span></div>
            <h3 class="metric-title">Micro-SaaS Utilities</h3>
            <p class="metric-desc">Live public tools like CV Maker & Translators[cite: 36, 41].</p>
        </div>

        <div class="metric-card glass-panel tilt-card" style="--metric-color: #22c55e;">
            <div class="metric-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
            </div>
            <div class="metric-num"><span class="counter" data-target="2">0</span></div>
            <h3 class="metric-title">CRM & Dashboards</h3>
            <p class="metric-desc">Financial tracking and realtime market data[cite: 41].</p>
        </div>

        <div class="metric-card glass-panel tilt-card" style="--metric-color: #ff0055;">
            <div class="metric-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
            </div>
            <div class="metric-num"><span class="counter" data-target="15">0</span><span style="color:var(--text-muted); font-size:2rem;">+</span></div>
            <h3 class="metric-title">Static UI Projects</h3>
            <p class="metric-desc">Responsive layouts, templates, and UI/UX clones.</p>
        </div>

        <div class="metric-card glass-panel tilt-card" style="--metric-color: #0077ff;">
            <div class="metric-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
            </div>
            <div class="metric-num"><span class="counter" data-target="12">0</span></div>
            <h3 class="metric-title">Academic Builds</h3>
            <p class="metric-desc">University assignments, DSA logic, and OOP tasks.</p>
        </div>

    </div>
</section>
<style>
    /* --- SUPER CLASSIC PROJECT METRICS CSS --- */
.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    margin-top: 50px;
}

.metric-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 35px 20px !important;
    border-top: 3px solid var(--metric-color) !important;
    position: relative;
    overflow: hidden;
    background: linear-gradient(180deg, rgba(15,15,20,0.9), rgba(5,5,8,0.95)) !important;
    transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

/* Internal Glow Effect */
.metric-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top, color-mix(in srgb, var(--metric-color) 15%, transparent), transparent 60%);
    pointer-events: none;
    opacity: 0.5;
    transition: 0.4s;
}

.metric-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.5), 0 -5px 20px color-mix(in srgb, var(--metric-color) 20%, transparent);
}

.metric-card:hover::before {
    opacity: 1;
}

.metric-icon {
    color: var(--metric-color);
    margin-bottom: 15px;
    filter: drop-shadow(0 0 8px color-mix(in srgb, var(--metric-color) 60%, transparent));
}

.metric-icon svg {
    width: 32px;
    height: 32px;
}

.metric-num {
    font-size: 3.5rem;
    font-weight: 800;
    font-family: 'Space Grotesk', sans-serif;
    color: #fff;
    line-height: 1;
    margin-bottom: 10px;
    text-shadow: 0 0 15px color-mix(in srgb, var(--metric-color) 40%, transparent);
    display: flex;
    align-items: baseline;
    justify-content: center;
}

.metric-title {
    font-size: 1rem;
    font-weight: 800;
    color: var(--metric-color);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
}

.metric-desc {
    font-size: 0.85rem;
    color: var(--text-muted);
    line-height: 1.5;
}

@media (max-width: 600px) {
    .metrics-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 400px) {
    .metrics-grid { grid-template-columns: 1fr; }
}
</style>
<div class="ai-bot-container">
    <div class="ai-bot-trigger" id="ai-trigger">
        <div class="ai-core-glow"></div>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path><circle cx="9" cy="10" r="1" fill="currentColor"></circle><circle cx="15" cy="10" r="1" fill="currentColor"></circle></svg>
    </div>

    <div class="ai-chat-window glass-panel" id="ai-window">
        <div class="ai-chat-header">
            <div class="ai-header-info">
                <div class="ai-status-dot"></div>
                <div>
                    <h4>Bilal-Bot AI</h4>
                    <span>v2.0.26 • Online</span>
                </div>
            </div>
            <button class="ai-close-btn" id="ai-close">✖</button>
        </div>
        
        <div class="ai-chat-body" id="ai-chat-body">
            </div>

        <div class="ai-chat-footer">
            <div class="ai-quick-replies" id="ai-quick-replies">
                </div>
        </div>
    </div>
</div>
<style>
    /* --- AI ASSISTANT CSS --- */
.ai-bot-container {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 9000;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.ai-bot-trigger {
    width: 60px;
    height: 60px;
    background: var(--bg-dark);
    border: 2px solid var(--neon-cyan);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--neon-cyan);
    cursor: pointer;
    position: relative;
    box-shadow: 0 0 20px rgba(0, 243, 255, 0.4);
    transition: 0.3s;
}

.ai-bot-trigger:hover {
    transform: scale(1.1);
    box-shadow: 0 0 30px rgba(0, 243, 255, 0.6);
}

.ai-core-glow {
    position: absolute;
    inset: 5px;
    background: var(--neon-cyan);
    border-radius: 50%;
    opacity: 0.2;
    animation: breathe 2s infinite alternate;
}

.ai-bot-trigger svg { width: 30px; height: 30px; z-index: 2; }

/* Chat Window */
.ai-chat-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 350px;
    height: 450px;
    padding: 0 !important;
    display: flex;
    flex-direction: column;
    border-radius: 20px;
    opacity: 0;
    pointer-events: none;
    transform: translateY(20px) scale(0.95);
    transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    transform-origin: bottom right;
}

.ai-chat-window.active {
    opacity: 1;
    pointer-events: all;
    transform: translateY(0) scale(1);
}

.ai-chat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    background: rgba(2, 2, 5, 0.5);
    border-radius: 20px 20px 0 0;
}

.ai-header-info { display: flex; align-items: center; gap: 12px; }
.ai-status-dot { width: 10px; height: 10px; background: #22c55e; border-radius: 50%; box-shadow: 0 0 10px #22c55e; animation: pulse 2s infinite; }
.ai-header-info h4 { font-family: 'Space Grotesk', sans-serif; font-size: 1.1rem; color: #fff; margin: 0; }
.ai-header-info span { font-size: 0.75rem; color: var(--neon-cyan); font-family: 'JetBrains Mono', monospace; }

.ai-close-btn { background: transparent; border: none; color: var(--text-muted); font-size: 1rem; cursor: pointer; transition: 0.3s; }
.ai-close-btn:hover { color: #ff5f56; }

/* Chat Body */
.ai-chat-body {
    flex-grow: 1;
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.ai-chat-body::-webkit-scrollbar { width: 6px; }
.ai-chat-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }

.ai-msg { max-width: 85%; padding: 12px 16px; border-radius: 12px; font-size: 0.95rem; line-height: 1.5; animation: popIn 0.3s ease-out; }
@keyframes popIn { 0% { opacity: 0; transform: translateY(10px); } 100% { opacity: 1; transform: translateY(0); } }

.msg-bot { background: rgba(0, 243, 255, 0.1); border: 1px solid rgba(0, 243, 255, 0.2); color: #fff; align-self: flex-start; border-bottom-left-radius: 4px; }
.msg-user { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: #fff; align-self: flex-end; border-bottom-right-radius: 4px; }

.typing-indicator { display: flex; gap: 4px; padding: 15px 20px; align-items: center; }
.typing-indicator span { width: 6px; height: 6px; background: var(--neon-cyan); border-radius: 50%; animation: typing 1s infinite alternate; }
.typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
@keyframes typing { 0% { transform: translateY(0); opacity: 0.5; } 100% { transform: translateY(-4px); opacity: 1; } }

/* Chat Footer (Quick Replies) */
.ai-chat-footer { padding: 15px; border-top: 1px solid rgba(255,255,255,0.05); background: rgba(2, 2, 5, 0.3); border-radius: 0 0 20px 20px; }
.ai-quick-replies { display: flex; flex-wrap: wrap; gap: 8px; }
.ai-chip { background: transparent; border: 1px solid var(--neon-cyan); color: var(--neon-cyan); padding: 8px 12px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: 0.3s; font-family: 'Inter', sans-serif; }
.ai-chip:hover { background: rgba(0, 243, 255, 0.1); box-shadow: 0 0 10px rgba(0, 243, 255, 0.2); transform: translateY(-2px); }

@media (max-width: 400px) {
    .ai-chat-window { width: calc(100vw - 40px); right: -10px; }
}
</style>

   
    <section id="contact" class="container">
    <div class="section-header gs-up">
        <div class="section-subtitle">Initialize Contact</div>
        <h2 class="section-title">Let's <span>Connect</span></h2>
    </div>

    <div class="contact-split-container gs-up">
        
        <div class="contact-info-side">
            <div class="availability-badge">
                <div class="dot"></div> Open to Summer Internships
            </div>
            
            <h3 class="contact-heading">Got a project or a role? <br><span>Let's talk.</span></h3>
            <p class="contact-description">I am actively looking for summer internships in Lahore and taking on select freelance projects. Drop a message, and I'll respond within 24 hours.</p>
            
            <div class="contact-details">
                <div class="detail-item">
                    <span class="detail-title">EMAIL</span>
                    <span class="detail-value">mbilalifzal82@gmail.com</span>
                </div>
                <div class="detail-item">
                    <span class="detail-title">LOCATION</span>
                    <span class="detail-value">Faisalabad, Pakistan</span>
                </div>
                <div class="detail-item">
                    <span class="detail-title">PHONE</span>
                    <span class="detail-value">+92 326 0102121</span>
                </div>
            </div>

            <div class="social-links">
                <a href="#network" class="social-btn">GitHub</a>
                <a href="#network" class="social-btn">LinkedIn</a>
                 <a href="#network" class="social-btn">Whatsapp</a>
                
            </div>
        </div>

        <div class="contact-form-side glass-panel tilt-card" data-tilt data-tilt-max="5" data-tilt-speed="400" data-tilt-glare="true" data-tilt-max-glare="0.1">
            <?php echo $form_status; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>#contact" method="POST" class="classic-form">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" placeholder="e.g. John Doe" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="e.g. john@company.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" placeholder="e.g. Internship Opportunity">
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" placeholder="Tell me about the role or project..." required></textarea>
                </div>

                <button type="submit" class="btn-submit-classic">Transmit Message</button>
            </form>
        </div>

    </div>
</section>
<style>/* --- SUPER CLASSIC SPLIT CONTACT SECTION CSS --- */

.contact-split-container {
    display: grid;
    grid-template-columns: 1fr 1.1fr; /* Form side is slightly larger */
    gap: 70px;
    align-items: center;
}

/* --- Left Side (Info) Styling --- */
.contact-info-side {
    display: flex;
    flex-direction: column;
}

.availability-badge {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: rgba(0, 243, 255, 0.1);
    border: 1px solid rgba(0, 243, 255, 0.3);
    color: var(--neon-cyan);
    padding: 8px 18px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 700;
    width: max-content;
    margin-bottom: 30px;
}
.availability-badge .dot {
    width: 8px; height: 8px; background: var(--neon-cyan); border-radius: 50%;
    box-shadow: 0 0 10px var(--neon-cyan); animation: pulse 2s infinite;
}

.contact-heading {
    font-size: 3rem;
    line-height: 1.1;
    margin-bottom: 20px;
    color: #fff;
    letter-spacing: -1px;
}
.contact-heading span {
    color: var(--neon-cyan);
}

.contact-description {
    color: var(--text-muted);
    font-size: 1.1rem;
    line-height: 1.7;
    margin-bottom: 40px;
}

.contact-details {
    display: flex;
    flex-direction: column;
    gap: 25px;
    margin-bottom: 40px;
}
.detail-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.detail-title {
    font-size: 0.8rem;
    font-weight: 800;
    color: var(--neon-cyan);
    letter-spacing: 1.5px;
}
.detail-value {
    font-size: 1.2rem;
    font-weight: 600;
    color: #fff;
}

.social-links {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}
.social-btn {
    padding: 12px 25px;
    border: 1px solid var(--glass-border);
    border-radius: 10px;
    color: #fff;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
    background: rgba(255,255,255,0.03);
}
.social-btn:hover {
    border-color: var(--neon-cyan);
    color: var(--neon-cyan);
    background: rgba(0, 243, 255, 0.05);
    transform: translateY(-3px);
}

/* --- Right Side (Classic Form) Styling --- */
.contact-form-side {
    padding: 50px !important;
    border-radius: 24px;
}

.classic-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text-muted);
}

.form-group input, .form-group textarea {
    width: 100%;
    background: rgba(0, 0, 0, 0.4);
    border: 1px solid var(--glass-border);
    padding: 16px 18px;
    border-radius: 12px;
    color: #fff;
    font-size: 1rem;
    font-family: 'Inter', sans-serif;
    transition: 0.3s;
    outline: none;
}

.form-group input:focus, .form-group textarea:focus {
    border-color: var(--neon-cyan);
    background: rgba(0, 0, 0, 0.6);
    box-shadow: 0 0 15px rgba(0, 243, 255, 0.15);
}

.btn-submit-classic {
    background: #fff;
    color: var(--bg-dark);
    border: none;
    padding: 18px;
    border-radius: 12px;
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 800;
    font-size: 1.15rem;
    cursor: pointer;
    transition: 0.4s;
    margin-top: 10px;
}

.btn-submit-classic:hover {
    background: var(--neon-cyan);
    box-shadow: 0 15px 30px rgba(0, 243, 255, 0.3);
    transform: translateY(-3px);
}

/* Responsive Breakpoints */
@media (max-width: 900px) {
    .contact-split-container { grid-template-columns: 1fr; gap: 50px; }
    .form-row { grid-template-columns: 1fr; }
    .contact-form-side { padding: 30px !important; }
}
</style>


    <footer>
        <div class="container" style="padding: 20px 0;">
            <p>&copy; 2026 Engineered by Muhammad Bilal. <br> Built with raw code, logic, and relentless ambition.</p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js"></script>

    <script>
        // 1. Typewriter Effect
        const words = [ "Laravel Developer" , "Backend & APIs" , "Full Stack Web Developer." ,"Web Solutions.", "PHP Backends.", "Dynamic UIs.", "Databases."];
        let i = 0, j = 0; let isDeleting = false;
        function typeWriter() {
            const currentWord = words[i];
            const typeElement = document.getElementById("typewriter");
            
            if(isDeleting) { typeElement.innerHTML = currentWord.substring(0, j-1); j--; } 
            else { typeElement.innerHTML = currentWord.substring(0, j+1); j++; }

            let typeSpeed = isDeleting ? 40 : 100;

            if(!isDeleting && j === currentWord.length) { typeSpeed = 2000; isDeleting = true; } 
            else if(isDeleting && j === 0) { isDeleting = false; i = (i + 1) % words.length; typeSpeed = 500; }
            
            setTimeout(typeWriter, typeSpeed);
        }
        window.onload = typeWriter;

        // 2. Animated Counters
        const counters = document.querySelectorAll('.counter');
        const speed = 50; 
        
        const animateCounters = () => {
            counters.forEach(counter => {
                const updateCount = () => {
                    const target = +counter.getAttribute('data-target');
                    const count = +counter.innerText;
                    const inc = target / speed;
                    if (count < target) { counter.innerText = Math.ceil(count + inc); setTimeout(updateCount, 40); } 
                    else { counter.innerText = target; }
                };
                updateCount();
            });
        }
        
        // Trigger counters when scrolled into view
        let counted = false;
        window.addEventListener('scroll', () => {
            const statsSection = document.querySelector('.stats-container');
            if(!statsSection) return;
            const position = statsSection.getBoundingClientRect().top;
            const screenPosition = window.innerHeight;
            if(position < screenPosition && !counted) { animateCounters(); counted = true; }
        });

        // 3. Interactive Background Canvas (Subtle Particles)
        const canvas = document.getElementById('hero-canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth; canvas.height = window.innerHeight;
        let particles = [];

        class Particle {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 2 + 0.5;
                this.speedX = Math.random() * 1 - 0.5;
                this.speedY = Math.random() * 1 - 0.5;
            }
            update() {
                this.x += this.speedX; this.y += this.speedY;
                if (this.x > canvas.width) this.x = 0; else if (this.x < 0) this.x = canvas.width;
                if (this.y > canvas.height) this.y = 0; else if (this.y < 0) this.y = canvas.height;
            }
            draw() {
                ctx.fillStyle = 'rgba(0, 243, 255, 0.2)';
                ctx.beginPath(); ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2); ctx.fill();
            }
        }
        for (let i = 0; i < 100; i++) particles.push(new Particle());
        function animateCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => { p.update(); p.draw(); });
            requestAnimationFrame(animateCanvas);
        }
        animateCanvas();

        // 4. Header Scroll Shrink
        window.addEventListener('scroll', () => {
            document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 50);
        });

        // 5. Ultra Realistic 3D Tilt Initialization
        VanillaTilt.init(document.querySelectorAll(".tilt-card"), {
            max: 12, speed: 400, glare: true, "max-glare": 0.25, perspective: 1200, scale: 1.03
        });

        // 6. GSAP Scroll Animations
        gsap.registerPlugin(ScrollTrigger);

        gsap.utils.toArray('.gs-up').forEach(elem => {
            gsap.from(elem, { scrollTrigger: { trigger: elem, start: "top 85%" }, y: 60, opacity: 0, duration: 1, ease: "power3.out" });
        });

        gsap.from(".gs-left", { scrollTrigger: { trigger: ".about-grid", start: "top 80%" }, x: -70, opacity: 0, duration: 1.2, ease: "power3.out" });
        gsap.from(".gs-right", { scrollTrigger: { trigger: ".about-grid", start: "top 80%" }, x: 70, opacity: 0, duration: 1.2, ease: "power3.out" });

        gsap.from(".skill-item", { scrollTrigger: { trigger: ".skills-grid", start: "top 85%" }, scale: 0.8, opacity: 0, duration: 0.6, stagger: 0.1, ease: "back.out(1.5)" });
     gsap.utils.toArray('.project-grid').forEach(grid => {
    gsap.from(grid.querySelectorAll('.gs-project'), { 
        scrollTrigger: { trigger: grid, start: "top 80%" }, 
        y: 70, opacity: 0, duration: 0.8, stagger: 0.2, ease: "power2.out" 
    });
});
    </script>

    <script>
    gsap.registerPlugin(ScrollTrigger);

    // This ensures that when the page loads, GSAP calculates positions correctly
    window.addEventListener('load', () => {
        
        // Target the skill items specifically
        gsap.from(".skill-item", {
            scrollTrigger: {
                trigger: ".skills-grid",
                start: "top 90%", // Starts animation when the grid is 90% from the top
                toggleActions: "play none none none", // Ensures it only plays once
            },
            y: 50,
            opacity: 0,
            duration: 0.8,
            stagger: 0.15,
            ease: "back.out(1.7)",
            clearProps: "all" // This removes the opacity:0 after the animation finishes!
        });

        // Force GSAP to catch up
        ScrollTrigger.refresh();
    });


    // --- CODE VS UI SLIDER LOGIC ---
const compSlider = document.getElementById('comp-slider');
const codeLayer = document.getElementById('code-layer');
const sliderHandle = document.getElementById('slider-handle');

let isSliding = false;

// Function to move the slider based on cursor position
const slide = (e) => {
    if (!isSliding) return;
    
    // Get the slider's bounding rectangle
    let rect = compSlider.getBoundingClientRect();
    
    // Calculate mouse X position relative to the container
    let x = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
    
    // Convert to percentage
    let percentage = (x / rect.width) * 100;
    
    // Keep it within bounds (0 to 100%)
    if (percentage < 0) percentage = 0;
    if (percentage > 100) percentage = 100;
    
    // Apply the percentage to the image width and handle position
    codeLayer.style.width = percentage + '%';
    sliderHandle.style.left = percentage + '%';
}

// Mouse Events
compSlider.addEventListener('mousedown', () => { isSliding = true; });
window.addEventListener('mouseup', () => { isSliding = false; });
window.addEventListener('mousemove', slide);

// Touch Events for Mobile
compSlider.addEventListener('touchstart', () => { isSliding = true; });
window.addEventListener('touchend', () => { isSliding = false; });
window.addEventListener('touchmove', slide);










// --- BILAL-BOT AI LOGIC ---
const aiTrigger = document.getElementById('ai-trigger');
const aiWindow = document.getElementById('ai-window');
const aiClose = document.getElementById('ai-close');
const chatBody = document.getElementById('ai-chat-body');
const quickReplies = document.getElementById('ai-quick-replies');

// Bot Brain: The Knowledge Base
const botData = {
    greeting: "Hi! I am Bilal's personal AI. I can answer questions about his tech stack, internships, or education. What would you like to know?",
    options: [
        { label: "Tech Stack & Skills", key: "skills" },
        { label: "Internship Experience", key: "experience" },
        { label: "Education Status", key: "education" },
        { label: "Hire Bilal", key: "hire" }
    ],
    responses: {
        skills: "Bilal is a Full-Stack developer. His core logic is built in <b>PHP (OOP)</b> and <b>MySQL</b>. He is currently mastering <b>Laravel</b>. On the frontend, he uses HTML5, CSS3, JavaScript, Bootstrap, and Tailwind. He's also exploring AI integrations!",
        experience: "Bilal has completed two internships! At <b>TechnoRift</b>, he engineered a PHP/MySQL Employee ID Generation system. At <b>Csoft Systems</b>, he contributed to complex web applications as part of a fast-paced development team.",
        education: "He is currently a 5th-semester BSCS student at the <b>University Of Agriculture Faisalabad (Main Campus)</b>. He has a strong foundation in Data Structures, Algorithms, and Database Management Systems.",
        hire: "Excellent choice! Bilal is actively looking for Summer Internships in Lahore. You can reach him at <b>mbilalifzal82@gmail.com</b> or call <b>+92 326 0102121</b>. Want me to scroll you to the contact form?",
        contactForm: "scrolling..."
    }
};

let isBotActive = false;

// Toggle Window
aiTrigger.addEventListener('click', () => {
    aiWindow.classList.toggle('active');
    if(!isBotActive) {
        isBotActive = true;
        setTimeout(() => addBotMessage(botData.greeting), 500);
        setTimeout(renderOptions, 1500);
    }
});

aiClose.addEventListener('click', () => { aiWindow.classList.remove('active'); });

// UI Functions
function addBotMessage(text) {
    // Show typing indicator first
    const typing = document.createElement('div');
    typing.className = 'ai-msg msg-bot typing-indicator';
    typing.innerHTML = '<span></span><span></span><span></span>';
    chatBody.appendChild(typing);
    chatBody.scrollTop = chatBody.scrollHeight;

    setTimeout(() => {
        typing.remove();
        const msg = document.createElement('div');
        msg.className = 'ai-msg msg-bot';
        msg.innerHTML = text;
        chatBody.appendChild(msg);
        chatBody.scrollTop = chatBody.scrollHeight;
    }, 800);
}

function addUserMessage(text) {
    const msg = document.createElement('div');
    msg.className = 'ai-msg msg-user';
    msg.innerHTML = text;
    chatBody.appendChild(msg);
    chatBody.scrollTop = chatBody.scrollHeight;
}

function renderOptions() {
    quickReplies.innerHTML = '';
    botData.options.forEach(opt => {
        const btn = document.createElement('button');
        btn.className = 'ai-chip';
        btn.innerText = opt.label;
        btn.onclick = () => handleUserSelection(opt);
        quickReplies.appendChild(btn);
    });
}

function handleUserSelection(opt) {
    quickReplies.innerHTML = ''; // Hide options while replying
    addUserMessage(opt.label);
    
    if(opt.key === 'hire') {
        setTimeout(() => addBotMessage(botData.responses[opt.key]), 500);
        setTimeout(() => {
            const yesBtn = document.createElement('button');
            yesBtn.className = 'ai-chip'; yesBtn.innerText = "Yes, take me there";
            yesBtn.onclick = () => { document.getElementById('contact').scrollIntoView({behavior: 'smooth'}); aiWindow.classList.remove('active'); };
            quickReplies.appendChild(yesBtn);
        }, 1500);
    } else {
        setTimeout(() => addBotMessage(botData.responses[opt.key]), 500);
        setTimeout(renderOptions, 2500); // Bring options back
    }
}


// --- INTERACTIVE ARCHITECTURE SIMULATION LOGIC ---
const btnSimulate = document.getElementById('btn-simulate');
const nodeFront = document.getElementById('node-frontend');
const nodeBack = document.getElementById('node-backend');
const nodeDB = document.getElementById('node-database');
const packet1 = document.getElementById('packet-1');
const packet2 = document.getElementById('packet-2');
const connections = document.querySelectorAll('.arch-connection');

let isSimulating = false;

btnSimulate.addEventListener('click', () => {
    if (isSimulating) return; // Prevent spam clicking
    isSimulating = true;
    
    // Reset visual state
    btnSimulate.innerHTML = "Processing...";
    btnSimulate.style.borderColor = "#ffbd2e";
    btnSimulate.style.color = "#ffbd2e";

    // Step 1: User hits submit (Frontend Active)
    setTimeout(() => {
        nodeFront.classList.add('active');
        nodeFront.querySelector('.node-status').innerText = "Form Submitted";
        connections[0].classList.add('active');
        packet1.style.animation = "transmit 0.8s ease-in-out forwards";
    }, 200);

    // Step 2: Payload hits API (Backend Active)
    setTimeout(() => {
        nodeFront.classList.remove('active');
        nodeFront.querySelector('.node-status').innerText = "Waiting for response...";
        
        nodeBack.classList.add('active');
        nodeBack.querySelector('.node-status').innerText = "Validating Data & RBAC";
        connections[0].classList.remove('active');
        
        connections[1].classList.add('active');
        packet2.style.animation = "transmit 0.8s ease-in-out forwards";
    }, 1000);

    // Step 3: API hits Database (DB Active)
    setTimeout(() => {
        nodeBack.classList.remove('active');
        nodeBack.querySelector('.node-status').innerText = "Executing PDO...";
        
        nodeDB.classList.add('active');
        nodeDB.querySelector('.node-status').innerText = "Writing to InnoDB";
        connections[1].classList.remove('active');
    }, 1800);

    // Step 4: Success Return Path
    setTimeout(() => {
        nodeDB.classList.remove('active');
        nodeDB.querySelector('.node-status').innerText = "Query Success (200 OK)";
        
        connections[1].classList.add('active');
        packet2.style.animation = "return 0.6s ease-in-out forwards";
    }, 2800);

    // Step 5: Backend gets Success
    setTimeout(() => {
        nodeBack.classList.add('active');
        connections[1].classList.remove('active');
        nodeBack.querySelector('.node-status').innerText = "Formatting JSON Response";
        
        connections[0].classList.add('active');
        packet1.style.animation = "return 0.6s ease-in-out forwards";
    }, 3400);

    // Step 6: Frontend Displays Success
    setTimeout(() => {
        nodeBack.classList.remove('active');
        connections[0].classList.remove('active');
        nodeBack.querySelector('.node-status').innerText = "Idle";
        
        nodeFront.classList.add('active');
        nodeFront.querySelector('.node-status').innerText = "UI Updated Successfully";
        nodeFront.style.setProperty('--node-color', '#22c55e'); // Turn green for success
    }, 4000);

    // Reset everything back to normal
    setTimeout(() => {
        nodeFront.classList.remove('active');
        nodeFront.querySelector('.node-status').innerText = "Awaiting Input...";
        nodeFront.style.setProperty('--node-color', '#00f3ff'); // Reset to cyan
        nodeDB.querySelector('.node-status').innerText = "Standby";
        
        packet1.style.animation = "none";
        packet2.style.animation = "none";
        
        btnSimulate.innerHTML = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 10px;"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg> Simulate Data Request`;
        btnSimulate.style.borderColor = "var(--neon-cyan)";
        btnSimulate.style.color = "var(--neon-cyan)";
        
        isSimulating = false;
    }, 6000);
});

</script>
<style>
    html {
    scroll-behavior: smooth;
    scroll-padding-top: 100px; 
}
</style>
</body>
</html>
