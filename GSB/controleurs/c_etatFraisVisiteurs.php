<?php
/**
 * Controleur Etat Frais Visiteurs
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @author    Thomas Marioli
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
$mois = getMois(date('d/m/Y'));
$numAnnee = substr($mois, 0, 4);
$numMois = substr($mois, 4, 2);
$visiteurSelectionne = filter_input(INPUT_POST, 'lstVisiteur', FILTER_SANITIZE_STRING);
$moisSelectionne = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
$numAnneeSelectionne = substr($moisSelectionne, 0, 4);
$numMoisSelectionne = substr($moisSelectionne, 4, 2);
if (estConnecte() && estComptable()) {
        $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
        switch ($action) {
            case "selectionnerVisiteur":
                if (nbErreurs() != 0) {
                    include 'vues/v_erreurs.php';
                }
                $lesUtilisateurs = $pdo->getLesVisiteurs();
                include 'vues/v_choixVisiteur.php';
                break;
            case "selectionnerMois":
                if ((!empty($visiteurSelectionne))) {
                    $_SESSION['idVisiteurSelectionne'] = $visiteurSelectionne;
                    $utilisateur = $pdo->getLeVisiteur($visiteurSelectionne);
                    $lesFichesDisponibles = $pdo->getLesFichesFrais($visiteurSelectionne);
                    include 'vues/v_choixVisiteur.php';
                }
                break;
            case "voirFrais":
                // vérification de la sélection du même visiteur avant et après avoir sélectionner le mois
                if (($_SESSION['idVisiteurSelectionne'] == $visiteurSelectionne)) {
                    $_SESSION['moisSelectionne'] = $moisSelectionne;
                    $lesFichesDisponibles = $pdo->getLesFichesFrais($_SESSION['idVisiteurSelectionne']);
                    $utilisateur = $pdo->getLeVisiteur($_SESSION['idVisiteurSelectionne']);
                    $infosFichesFrais = $pdo->getLesInfosFicheFrais($_SESSION['idVisiteurSelectionne'], $_SESSION['moisSelectionne']);
                    if (empty($infosFichesFrais)) {
                        ajouterErreur('Aucune fiche de frais ce mois pour ce visiteur.');
                        include 'vues/v_erreurs.php';
                        include 'vues/v_choixVisiteur.php';
                    } else {
                        $leMois = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
    $lesMois = $pdo->getLesMoisDisponibles($_SESSION['idVisiteurSelectionne']);
    $moisASelectionner = $leMois;
                        $nbrJustificatifs = $infosFichesFrais['nbJustificatifs'];
                        include 'vues/v_choixVisiteur.php';
                        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($_SESSION['idVisiteurSelectionne'], $moisSelectionne);
                        $lesFraisForfait = $pdo->getLesFraisForfait($_SESSION['idVisiteurSelectionne'], $moisSelectionne);
    $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($_SESSION['idVisiteurSelectionne'], $leMois);
    $numAnnee = substr($leMois, 0, 4);
    $numMois = substr($leMois, 4, 2);
    $libEtat = $lesInfosFicheFrais['libEtat'];
    $montantValide = $lesInfosFicheFrais['montantValide'];
    $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
    $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
                        require 'vues/v_listeFraisForfaitVisiteurEtat.php';
                    }
                } else {
                    ajouterErreur('L\'identifiant du visiteur ne correspond pas à celui '
                            . 'sélectionné précedemment. Veuillez sélectionner à nouveau '
                            . 'le visiteur.');
                    $lesUtilisateurs = $pdo->getLesVisiteurs();
                    include 'vues/v_erreurs.php';
                    include 'vues/v_choixVisiteur.php';
                }
                break;
            case "actualiserEtatFiche":
                $clkBtnMettreEnPaiment = filter_input(INPUT_POST, 'btnMettreEnPaiement', FILTER_SANITIZE_STRING);
                $clkBtnFichePayee = filter_input(INPUT_POST, 'btnFichePayee', FILTER_SANITIZE_STRING);
                if (isset($clkBtnMettreEnPaiment)){
                    $pdo->majEtatFicheFrais($_SESSION['idVisiteurSelectionne'], $_SESSION['moisSelectionne'], "PA");
                } else if (isset($clkBtnFichePayee)){
                    $pdo->majEtatFicheFrais($_SESSION['idVisiteurSelectionne'], $_SESSION['moisSelectionne'], "RB");
                } else {
                    ajouterErreur('Une erreur est survenue. Veuillez recommencer '
                            . 'en sélectionnant un visiteur.');
                    include 'vues/v_erreurs.php';
                    $lesUtilisateurs = $pdo->getLesVisiteurs();
                    include 'vues/v_choixVisiteur.php';
                }
        }
    } else {
        ajouterErreur('La validation des fiches de frais des visiteurs '
                . 'n\'est possible qu\'entre le 10 et le 20 du mois.');
        include 'vues/v_erreurs.php';
    
}