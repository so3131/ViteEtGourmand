# Commit 13: View de commande (User) et test du OrderMenuController

À cette étape:
- Création des vues pour les étapes du tunnel de commande (Panier, Livraison, Paiement).
- Développement du `OrderMenuController` pour gérer la persistance des étapes.
- Intégration des formulaires de saisie et de validation pour l'adresse client.

**À tester:**
- **Navigation :** Vérifier que le passage entre les étapes (steps) est fluide.
- **Persistance :** S'assurer que les données saisies par l'utilisateur sont conservées en session entre chaque étape.
- **Controller :** Vérifier que `OrderMenuController` traite correctement les données reçues avant la validation finale.
- **Sécurité :** Tester la validation des champs (champs requis, formats des adresses).
