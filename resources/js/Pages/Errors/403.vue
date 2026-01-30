<style scoped>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 50%, #2b77cb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Éléments d'arrière-plan animés */
        .bg-element {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: rotate 10s linear infinite;
        }

        .bg-element:nth-child(1) {
            width: 200px;
            height: 200px;
            top: 10%;
            left: 10%;
            background: radial-gradient(circle, #4299e1, transparent);
            animation-duration: 15s;
        }

        .bg-element:nth-child(2) {
            width: 150px;
            height: 150px;
            top: 70%;
            right: 20%;
            background: radial-gradient(circle, #3182ce, transparent);
            animation-duration: 12s;
            animation-direction: reverse;
        }

        .bg-element:nth-child(3) {
            width: 100px;
            height: 100px;
            bottom: 20%;
            left: 80%;
            background: radial-gradient(circle, #2b77cb, transparent);
            animation-duration: 8s;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg) scale(1); }
            50% { transform: rotate(180deg) scale(1.2); }
            100% { transform: rotate(360deg) scale(1); }
        }

        /* Container principal */
        .container {
            text-align: center;
            z-index: 10;
            max-width: 700px;
            padding: 3rem 2rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 32px;
            border: 2px solid rgba(66, 153, 225, 0.2);
            box-shadow: 0 30px 60px rgba(49, 130, 206, 0.2);
            animation: slideIn 1s ease-out;
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4299e1, #3182ce, #2b77cb);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(80px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Icône d'interdiction */
        .forbidden-icon {
            width: 140px;
            height: 140px;
            margin: 0 auto 2rem;
            background: linear-gradient(135deg, #4299e1, #3182ce);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: shake 3s ease-in-out infinite;
            box-shadow: 0 20px 40px rgba(49, 130, 206, 0.3);
            position: relative;
        }

        .forbidden-icon::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 3px solid rgba(66, 153, 225, 0.3);
            animation: pulse-ring 2s ease-out infinite;
        }

        @keyframes pulse-ring {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(1.3); opacity: 0; }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
            20%, 40%, 60%, 80% { transform: translateX(2px); }
        }

        .forbidden-icon svg {
            width: 80px;
            height: 80px;
            fill: white;
            z-index: 1;
        }

        /* Code d'erreur 403 */
        .error-code {
            font-size: 9rem;
            font-weight: 900;
            background: linear-gradient(135deg, #4299e1, #3182ce, #2b77cb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
            text-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            animation: colorShift 4s ease-in-out infinite;
            background-size: 200% 200%;
        }

        @keyframes colorShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Titre */
        .title {
            font-size: 2.8rem;
            font-weight: 800;
            color: #2d3748;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Description */
        .description {
            font-size: 1.3rem;
            color: #4a5568;
            margin-bottom: 3rem;
            line-height: 1.7;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Boutons */
        .button-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1.2rem 2.5rem;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 40px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: rgba(66, 153, 225, 0.1);
            color: #2b6cb0;
            border: 2px solid rgba(66, 153, 225, 0.3);
        }

        .btn-secondary:hover {
            background: rgba(66, 153, 225, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(66, 153, 225, 0.2);
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn svg {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }

        /* Section d'aide */
        .help-section {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(66, 153, 225, 0.2);
        }

        .help-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 1rem;
        }

        .help-list {
            list-style: none;
            color: #718096;
            font-size: 1rem;
            line-height: 1.8;
        }

        .help-list li {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .help-list li::before {
            content: '•';
            color: #4299e1;
            font-weight: bold;
            font-size: 1.2rem;
        }

        /* Responsivité */
        @media (max-width: 768px) {
            .container {
                margin: 1rem;
                padding: 2rem 1.5rem;
            }

            .error-code {
                font-size: 6rem;
            }

            .title {
                font-size: 2.2rem;
            }

            .description {
                font-size: 1.1rem;
            }

            .forbidden-icon {
                width: 120px;
                height: 120px;
            }

            .forbidden-icon svg {
                width: 70px;
                height: 70px;
            }

            .button-group {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 280px;
            }
        }

        @media (max-width: 480px) {
            .error-code {
                font-size: 4.5rem;
            }

            .title {
                font-size: 1.8rem;
            }

            .btn {
                padding: 1rem 2rem;
                font-size: 1rem;
            }
        }
    </style>

<template>
    <!-- Éléments d'arrière-plan -->
    <div class="bg-element"></div>
    <div class="bg-element"></div>
    <div class="bg-element"></div>

    <div class="container">
        <!-- Icône d'interdiction -->
        <div class="forbidden-icon">
            <svg viewBox="0 0 24 24">
                <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,4A8,8 0 0,1 20,12C20,14.4 19,16.5 17.3,18L6,6.7C7.5,5 9.6,4 12,4M12,20A8,8 0 0,1 4,12C4,9.6 5,7.5 6.7,6L18,17.3C16.5,19 14.4,20 12,20Z"/>
            </svg>
        </div>

        <!-- Code d'erreur -->
        <h1 class="error-code">403</h1>

        <!-- Titre -->
        <h2 class="title">Accès Refusé</h2>

        <!-- Description -->
        <p class="description">
            Désolé, vous n'avez pas les permissions nécessaires pour accéder à cette ressource. 
            Cette zone est protégée et nécessite des autorisations spéciales.
        </p>

        <!-- Boutons d'action -->
        <div class="button-group">
            <a href="/" class="btn btn-primary">
                <svg viewBox="0 0 24 24">
                    <path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z"/>
                </svg>
                Retour à l'accueil
            </a>
            
            <a href="/contact" class="btn btn-secondary">
                <svg viewBox="0 0 24 24">
                    <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,17C11.45,17 11,16.55 11,16C11,15.45 11.45,15 12,15C12.55,15 13,15.45 13,16C13,16.55 12.55,17 12,17M12,7A2,2 0 0,1 14,9C14,10.5 12.5,10.72 12.5,12.5H11.5C11.5,10.39 13,10.22 13,9A1,1 0 0,0 12,8A1,1 0 0,0 11,9H10A2,2 0 0,1 12,7Z"/>
                </svg>
                Demander l'accès
            </a>
        </div>

        <!-- Section d'aide -->
        <div class="help-section">
            <h3 class="help-title">Que puis-je faire ?</h3>
            <ul class="help-list">
                <li>Vérifier que vous êtes connecté avec le bon compte</li>
                <li>Contacter l'administrateur pour obtenir les permissions</li>
                <li>Retourner à la page d'accueil et naviguer différemment</li>
                <li>Vérifier l'URL pour vous assurer qu'elle est correcte</li>
            </ul>
        </div>
    </div>
</template>
    <script>
        // Animation d'entrée progressive
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.forbidden-icon, .error-code, .title, .description, .button-group, .help-section');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                setTimeout(() => {
                    el.style.transition = 'all 0.8s ease';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });

        // Effet de mouvement subtil au survol
        document.addEventListener('mousemove', function(e) {
            const container = document.querySelector('.container');
            const rect = container.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
            
            const mouseX = e.clientX - centerX;
            const mouseY = e.clientY - centerY;
            
            const rotateX = (mouseY / rect.height) * 5;
            const rotateY = (mouseX / rect.width) * -5;
            
            container.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
        });

        // Réinitialisation au départ de la souris
        document.addEventListener('mouseleave', function() {
            const container = document.querySelector('.container');
            container.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
        });
    </script>