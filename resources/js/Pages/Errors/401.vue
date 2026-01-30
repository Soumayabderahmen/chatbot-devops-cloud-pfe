<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Particles d'arrière-plan animées */
        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .particle:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            width: 60px;
            height: 60px;
            top: 60%;
            left: 80%;
            animation-delay: 2s;
        }

        .particle:nth-child(3) {
            width: 40px;
            height: 40px;
            top: 40%;
            right: 20%;
            animation-delay: 4s;
        }

        .particle:nth-child(4) {
            width: 100px;
            height: 100px;
            bottom: 20%;
            left: 70%;
            animation-delay: 1s;
        }

        .particle:nth-child(5) {
            width: 50px;
            height: 50px;
            top: 10%;
            right: 10%;
            animation-delay: 3s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(120deg); }
            66% { transform: translateY(20px) rotate(240deg); }
        }

        /* Container principal */
        .container {
            text-align: center;
            z-index: 10;
            max-width: 600px;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Icône de cadenas */
        .lock-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s ease-in-out infinite;
            box-shadow: 0 15px 30px rgba(238, 90, 36, 0.3);
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .lock-icon svg {
            width: 60px;
            height: 60px;
            fill: white;
        }

        /* Code d'erreur 401 */
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            background: linear-gradient(135deg, #ffecd2, #fcb69f);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            text-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            animation: glow 3s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from { filter: brightness(1); }
            to { filter: brightness(1.2); }
        }

        /* Titre */
        .title {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Description */
        .description {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }

        /* Bouton de connexion */
        .login-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2.5rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }

        .login-btn svg {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }

        /* Bouton retour */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateY(-1px);
        }

        .back-btn svg {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }

        /* Responsivité */
        @media (max-width: 768px) {
            .container {
                margin: 1rem;
                padding: 1.5rem;
            }

            .error-code {
                font-size: 6rem;
            }

            .title {
                font-size: 2rem;
            }

            .description {
                font-size: 1rem;
            }

            .lock-icon {
                width: 100px;
                height: 100px;
            }

            .lock-icon svg {
                width: 50px;
                height: 50px;
            }
        }

        @media (max-width: 480px) {
            .error-code {
                font-size: 4.5rem;
            }

            .title {
                font-size: 1.8rem;
            }

            .login-btn {
                padding: 0.875rem 2rem;
                font-size: 1rem;
            }
        }
    </style>

<template>
    <!-- Particules animées -->
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>

    <div class="container">
        <!-- Icône de cadenas -->
        <div class="lock-icon">
            <svg viewBox="0 0 24 24">
                <path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/>
            </svg>
        </div>

        <!-- Code d'erreur -->
        <h1 class="error-code">401</h1>

        <!-- Titre -->
        <h2 class="title">Accès Non Autorisé</h2>

        <!-- Description -->
        <p class="description">
            Vous devez être connecté pour accéder à cette page protégée. 
            Veuillez vous authentifier pour continuer.
        </p>

        <!-- Bouton de connexion -->
        <a href="/login" class="login-btn">
            <svg viewBox="0 0 24 24">
                <path d="M10,17V14H3V10H10V7L15,12L10,17M10,2H19A2,2 0 0,1 21,4V20A2,2 0 0,1 19,22H10A2,2 0 0,1 8,20V18H10V20H19V4H10V6H8V4A2,2 0 0,1 10,2Z"/>
            </svg>
            Se Connecter
        </a>

        <!-- Bouton retour -->
        <br>
        <a href="/" class="back-btn">
            <svg viewBox="0 0 24 24">
                <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
            </svg>
            Retour à l'accueil
        </a>
    </div>

    
</template>
    <script>
        // Animation d'entrée progressive
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.lock-icon, .error-code, .title, .description, .login-btn');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    el.style.transition = 'all 0.6s ease';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 150);
            });
        });

        // Effet de parallaxe léger sur les particules
        document.addEventListener('mousemove', function(e) {
            const particles = document.querySelectorAll('.particle');
            const mouseX = e.clientX / window.innerWidth;
            const mouseY = e.clientY / window.innerHeight;

            particles.forEach((particle, index) => {
                const speed = (index + 1) * 0.5;
                const x = (mouseX * speed * 10) - (speed * 5);
                const y = (mouseY * speed * 10) - (speed * 5);
                particle.style.transform = `translate(${x}px, ${y}px)`;
            });
        });
    </script>