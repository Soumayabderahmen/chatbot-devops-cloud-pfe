<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #514ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .container {
            text-align: center;
            color: white;
            position: relative;
            z-index: 2;
            padding: 2rem;
        }

        .error-code {
            font-size: 12rem;
            font-weight: 900;
            background: linear-gradient(45deg, #ff6b6b, #feca57, #48dbfb, #ff9ff3);
            background-size: 400% 400%;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradientShift 3s ease-in-out infinite;
            text-shadow: 0 0 30px rgba(255, 255, 255, 0.3);
            margin-bottom: 1rem;
        }

        .error-message {
            font-size: 2rem;
            margin-bottom: 1rem;
            opacity: 0;
            animation: fadeInUp 1s ease-out 0.5s forwards;
        }

        .error-description {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.8;
            animation: fadeInUp 1s ease-out 0.7s forwards;
        }

        .btn-home {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(45deg, #667eea, #56639b );
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            opacity: 0;
            animation: fadeInUp 1s ease-out 0.9s forwards;
            position: relative;
            overflow: hidden;
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
        }

        .btn-home::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-home:hover::before {
            left: 100%;
        }

        /* Particules flottantes */
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: white;
            border-radius: 50%;
            opacity: 0.6;
            animation: float 6s ease-in-out infinite;
        }

        .particle:nth-child(1) { top: 20%; left: 20%; animation-delay: 0s; }
        .particle:nth-child(2) { top: 60%; left: 80%; animation-delay: 1s; }
        .particle:nth-child(3) { top: 80%; left: 30%; animation-delay: 2s; }
        .particle:nth-child(4) { top: 40%; left: 70%; animation-delay: 3s; }
        .particle:nth-child(5) { top: 10%; left: 60%; animation-delay: 4s; }

        /* Formes géométriques flottantes */
        .shape {
            position: absolute;
            opacity: 0.1;
            animation: rotate 20s linear infinite;
        }

        .shape-1 {
            top: 10%;
            left: 10%;
            width: 100px;
            height: 100px;
            border: 2px solid white;
            border-radius: 50%;
        }

        .shape-2 {
            top: 70%;
            right: 10%;
            width: 80px;
            height: 80px;
            border: 2px solid white;
            transform: rotate(45deg);
        }

        .shape-3 {
            bottom: 20%;
            left: 20%;
            width: 0;
            height: 0;
            border-left: 40px solid transparent;
            border-right: 40px solid transparent;
            border-bottom: 70px solid white;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(120deg); }
            66% { transform: translateY(10px) rotate(240deg); }
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .error-code {
                font-size: 8rem;
            }
            .error-message {
                font-size: 1.5rem;
            }
            .error-description {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .error-code {
                font-size: 6rem;
            }
            .error-message {
                font-size: 1.2rem;
            }
            .container {
                padding: 1rem;
            }
        }
    </style>

<template>
    <!-- Particules flottantes -->
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>

    <!-- Formes géométriques -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>

    <div class="container">
        <div class="error-code">404</div>
        <h1 class="error-message">Oups ! Page introuvable</h1>
        <p class="error-description">
            La page que vous cherchez semble avoir disparu dans l'espace numérique.
        </p>
        <a href="/" class="btn-home">
            ← Retour à l'accueil
        </a>
    </div>
</template>
    <script>
        // Animation d'entrée progressive
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.container');
            container.style.transform = 'scale(0.8)';
            container.style.opacity = '0';
            
            setTimeout(() => {
                container.style.transition = 'all 0.8s ease-out';
                container.style.transform = 'scale(1)';
                container.style.opacity = '1';
            }, 200);
        });

        // Effet de parallaxe léger au mouvement de la souris
        document.addEventListener('mousemove', function(e) {
            const shapes = document.querySelectorAll('.shape');
            const particles = document.querySelectorAll('.particle');
            
            const xAxis = (window.innerWidth / 2 - e.pageX) / 25;
            const yAxis = (window.innerHeight / 2 - e.pageY) / 25;
            
            shapes.forEach(shape => {
                shape.style.transform += ` translate(${xAxis}px, ${yAxis}px)`;
            });
            
            particles.forEach(particle => {
                particle.style.transform += ` translate(${xAxis * 0.5}px, ${yAxis * 0.5}px)`;
            });
        });
    </script>

