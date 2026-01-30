#!/usr/bin/env python3
"""
Script pour exécuter les tests du backend BraindCode avec différentes options
"""

import sys
import subprocess
import argparse
from pathlib import Path
TEST_FILE = "test_main.py"

def run_command(cmd, description=""):
    """Exécute une commande et affiche le résultat"""
    print(f"\n{'='*60}")
    print(f"🧪 {description}")
    print(f"{'='*60}")
    print(f"Commande: {' '.join(cmd)}")
    print()
    
    result = subprocess.run(cmd, capture_output=False)
    return result.returncode == 0

def main():
    parser = argparse.ArgumentParser(description="Exécuteur de tests pour BraindCode")
    parser.add_argument("--quick", "-q", action="store_true", help="Tests rapides uniquement")
    parser.add_argument("--coverage", "-c", action="store_true", help="Avec couverture de code")
    parser.add_argument("--parallel", "-p", action="store_true", help="Tests en parallèle")
    parser.add_argument("--verbose", "-v", action="store_true", help="Mode verbeux")
    parser.add_argument("--failed", "-f", action="store_true", help="Relancer les tests échoués")
    parser.add_argument("--markers", "-m", help="Exécuter tests avec marqueurs spécifiques")
    parser.add_argument("--file", help="Fichier de test spécifique")
    
    args = parser.parse_args()
    
    # Vérifier que pytest est installé
    try:
        subprocess.run(["pytest", "--version"], capture_output=True, check=True)
    except (subprocess.CalledProcessError, FileNotFoundError):
        print("❌ pytest n'est pas installé. Installez avec: pip install -r requirements-test.txt")
        return False
    
    # Construction de la commande pytest
    cmd = ["pytest"]
    
    if args.file:
        cmd.append(args.file)
    else:
        cmd.append(TEST_FILE)
    
    # Options de base
    base_options = ["--tb=short", "--color=yes"]
    cmd.extend(base_options)
    
    if args.verbose:
        cmd.append("-v")
    
    if args.quick:
        cmd.extend(["-m", "not slow"])
        print("🏃‍♂️ Mode rapide: exclusion des tests lents")
    
    if args.coverage:
        cmd.extend([
            "--cov=main",
            "--cov-report=html",
            "--cov-report=term-missing",
            "--cov-fail-under=80"
        ])
        print("📊 Analyse de couverture activée")
    
    if args.parallel:
        cmd.extend(["-n", "auto"])
        print("⚡ Tests en parallèle activés")
    
    if args.failed:
        cmd.append("--lf")
        print("🔄 Relancement des tests échoués uniquement")
    
    if args.markers:
        cmd.extend(["-m", args.markers])
        print(f"🏷️ Filtre par marqueurs: {args.markers}")
    
    # Exécution des tests
    success = run_command(cmd, "Exécution des tests")
    
    if success:
        print("\n✅ Tous les tests sont passés!")
        
        if args.coverage:
            print("\n📊 Rapport de couverture généré dans htmlcov/index.html")
            
        # Tests de performance additionnels
        if not args.quick:
            print("\n🏃‍♂️ Exécution des tests de performance...")
            perf_cmd = ["pytest", TEST_FILE, "-m", "slow", "-v"]
            run_command(perf_cmd, "Tests de performance")
            
    else:
        print("\n❌ Certains tests ont échoué!")
        print("\n💡 Conseils de dépannage:")
        print("  - Vérifiez que toutes les dépendances sont installées")
        print("  - Assurez-vous qu'Ollama est disponible si nécessaire")
        print("  - Utilisez --verbose pour plus de détails")
        return False
    
    return True

def check_test_environment():
    """Vérifie l'environnement de test"""
    print("🔍 Vérification de l'environnement de test...")
    
    # Vérifier les fichiers requis
    required_files = ["main.py", TEST_FILE]
    missing_files = []
    
    for file in required_files:
        if not Path(file).exists():
            missing_files.append(file)
    
    if missing_files:
        print(f"❌ Fichiers manquants: {', '.join(missing_files)}")
        return False
    
    # Vérifier les imports
    try:
        import pytest
        import fastapi
        import httpx
        print("✅ Dépendances principales disponibles")
    except ImportError as e:
        print(f"❌ Dépendance manquante: {e}")
        print("Installez avec: pip install -r requirements-test.txt")
        return False
    
    print("✅ Environnement de test prêt")
    return True

if __name__ == "__main__":
    print("🚀 BraindCode Test Runner")
    print("=" * 50)
    
    if not check_test_environment():
        sys.exit(1)
    
    success = main()
    sys.exit(0 if success else 1)