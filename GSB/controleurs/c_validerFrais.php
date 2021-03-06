<?php

/**
 * Controleur Valider Frais
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
    if (estDateCampagneValidation() /** @TODO supprimer ou ajouter la condition 
                                        // ajout d'une condition vraie pour ne pas limiter aux dates de campagne de validation
                       || 1 == 1
                                        */) {
        $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
        switch ($action) {
            case "selectionnerVisiteur":
                if (nbErreurs() != 0) {
                    include 'vues/v_erreurs.php';
                }
                $lesUtilisateurs = $pdo->getLesVisiteurs();
                include 'vues/v_choixUtilisateurs.php';
                break;
            case "selectionnerMois":
                if ((!empty($visiteurSelectionne))) {
                    $_SESSION['idVisiteurSelectionne'] = $visiteurSelectionne;
                    $utilisateur = $pdo->getLeVisiteur($visiteurSelectionne);
                    $lesMoisDisponibles = $pdo->getLesMoisDisponibles($visiteurSelectionne);
                    include 'vues/v_choixUtilisateurs.php';
                }
                break;
            case "voirFrais":
                // vérification de la sélection du même visiteur avant et après avoir sélectionner le mois
                if (($_SESSION['idVisiteurSelectionne'] == $visiteurSelectionne)) {
                    $_SESSION['moisSelectionne'] = $moisSelectionne;
                    $lesMoisDisponibles = $pdo->getLesMoisDisponibles($_SESSION['idVisiteurSelectionne']);
                    $utilisateur = $pdo->getLeVisiteur($_SESSION['idVisiteurSelectionne']);
                    $infosFichesFrais = $pdo->getLesInfosFicheFrais($_SESSION['idVisiteurSelectionne'], $_SESSION['moisSelectionne']);
                    if (empty($infosFichesFrais)) {
                        ajouterErreur('Aucune fiche de frais ce mois pour ce visiteur.');
                        include 'vues/v_erreurs.php';
                        include 'vues/v_choixUtilisateurs.php';
                        // vérification de la validation effective ou non
                    } else if ($infosFichesFrais['idEtat'] == "VA") {
                        ajouterErreur('La fiche de frais a déjà été validée.');
                        include 'vues/v_erreurs.php';
                        include 'vues/v_choixUtilisateurs.php';
                    } else {
                        $nbrJustificatifs = $infosFichesFrais['nbJustificatifs'];
                        include 'vues/v_choixUtilisateurs.php';
                        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($_SESSION['idVisiteurSelectionne'], $moisSelectionne);
                        $lesFraisForfait = $pdo->getLesFraisForfait($_SESSION['idVisiteurSelectionne'], $moisSelectionne);
                        require 'vues/v_listeFraisForfaitVisiteur.php';
                        require 'vues/v_listeFraisHorsForfaitVisiteur.php';
                    }
                } else {
                    ajouterErreur('L\'identifiant du visiteur ne correspond pas à celui '
                            . 'sélectionné précedemment. Veuillez sélectionner à nouveau '
                            . 'le visiteur.');
                    $lesUtilisateurs = $pdo->getLesVisiteurs();
                    include 'vues/v_erreurs.php';
                    include 'vues/v_choixUtilisateurs.php';
                }
                break;
            case "actualiserFraisForfait":
                $lesFrais = filter_input(INPUT_POST, 'lesFrais', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                if (lesQteFraisValides($lesFrais)) {
                    $visiteurSelectionne = $_SESSION['idVisiteurSelectionne'];
                    $moisSelectionne = $_SESSION['moisSelectionne'];
                    $numAnneeSelectionne = substr($moisSelectionne, 0, 4);
                    $numMoisSelectionne = substr($moisSelectionne, 4, 2);
                    $pdo->majFraisForfait($_SESSION['idVisiteurSelectionne'], $_SESSION['moisSelectionne'], $lesFrais);
                    $lesMoisDisponibles = $pdo->getLesMoisDisponibles($_SESSION['idVisiteurSelectionne']);
                    $utilisateur = $pdo->getLeVisiteur($_SESSION['idVisiteurSelectionne']);
                    $infosFichesFrais = $pdo->getLesInfosFicheFrais($_SESSION['idVisiteurSelectionne'], $_SESSION['moisSelectionne']);
                    $nbrJustificatifs = $infosFichesFrais['nbJustificatifs'];
                    ajouterErreur('Les éléments forfaitisés ont bien été modifiés.');
                    include 'vues/v_erreurs.php';
                    include 'vues/v_choixUtilisateurs.php';
                    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($_SESSION['idVisiteurSelectionne'], $moisSelectionne);
                    $lesFraisForfait = $pdo->getLesFraisForfait($_SESSION['idVisiteurSelectionne'], $moisSelectionne);
                    require 'vues/v_listeFraisForfaitVisiteur.php';
                    require 'vues/v_listeFraisHorsForfaitVisiteur.php';
                } else {
                    ajouterErreur('Les valeurs des frais doivent être numériques');
                    include 'vues/v_erreurs.php';
                }
                break;
            case "actualiserFraisHorsForfait":
                $lesFrais = filter_input(INPUT_POST, 'lesFrais', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
                $pdo->majFraisHorsForfait($_SESSION['idVisiteurSelectionne'], $_SESSION['moisSelectionne'], $lesFrais);
                $visiteurSelectionne = $_SESSION['idVisiteurSelectionne'];
                $moisSelectionne = $_SESSION['moisSelectionne'];
                $numAnneeSelectionne = substr($moisSelectionne, 0, 4);
                $numMoisSelectionne = substr($moisSelectionne, 4, 2);
                $lesMoisDisponibles = $pdo->getLesMoisDisponibles($_SESSION['idVisiteurSelectionne']);
                $utilisateur = $pdo->getLeVisiteur($_SESSION['idVisiteurSelectionne']);
                $infosFichesFrais = $pdo->getLesInfosFicheFrais($_SESSION['idVisiteurSelectionne'], $_SESSION['moisSelectionne']);
                $nbrJustificatifs = $infosFichesFrais['nbJustificatifs'];
                ajouterErreur('Les éléments hors forfait ont bien été modifiés.');
                include 'vues/v_erreurs.php';
                include 'vues/v_choixUtilisateurs.php';
                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($_SESSION['idVisiteurSelectionne'], $moisSelectionne);
                $lesFraisForfait = $pdo->getLesFraisForfait($_SESSION['idVisiteurSelectionne'], $moisSelectionne);
                require 'vues/v_listeFraisForfaitVisiteur.php';
                require 'vues/v_listeFraisHorsForfaitVisiteur.php';
                break;
            case "supprimerFraisHorsForfaitNonValide":
                $idFraisASupprimer = filter_input(INPUT_GET, 'idFrais', FILTER_DEFAULT, FILTER_SANITIZE_NUMBER_INT);
                $pdo->supprimerFrais($idFraisASupprimer);
                $visiteurSelectionne = $_SESSION['idVisiteurSelectionne'];
                $moisSelectionne = $_SESSION['moisSelectionne'];
                $numAnneeSelectionne = substr($moisSelectionne, 0, 4);
                $numMoisSelectionne = substr($moisSelectionne, 4, 2);
                $lesMoisDisponibles = $pdo->getLesMoisDisponibles($_SESSION['idVisiteurSelectionne']);
                $utilisateur = $pdo->getLeVisiteur($_SESSION['idVisiteurSelectionne']);
                $infosFichesFrais = $pdo->getLesInfosFicheFrais($_SESSION['idVisiteurSelectionne'], $_SESSION['moisSelectionne']);
                $nbrJustificatifs = $infosFichesFrais['nbJustificatifs'];
                ajouterErreur('L\'élément a bien été modifié.');
                include 'vues/v_erreurs.php';
                include 'vues/v_choixUtilisateurs.php';
                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($_SESSION['idVisiteurSelectionne'], $moisSelectionne);
                $lesFraisForfait = $pdo->getLesFraisForfait($_SESSION['idVisiteurSelectionne'], $moisSelectionne);
                require 'vues/v_listeFraisForfaitVisiteur.php';
                require 'vues/v_listeFraisHorsForfaitVisiteur.php';
                break;
            case "reporterFraisHorsForfait":
                $moisSelectionne = $_SESSION['moisSelectionne'];
                $numAnneeSelectionne = substr($moisSelectionne, 0, 4);
                $numMoisSelectionne = substr($moisSelectionne, 4, 2);
                if ($numMoisSelectionne < 12) {
                    $moisSuivant = $numMoisSelectionne + 1;
                    $anneeMoisSuivant = $numAnneeSelectionne;
                    if($moisSuivant<10){
                        $moisSuivant = "0".$moisSuivant;
                    }
                } else {
                    $moisSuivant = "01";
                    $anneeMoisSuivant = $numAnneeSelectionne + 1;
                }
                $moisFicheSuivante =  $anneeMoisSuivant.$moisSuivant;
                $idFraisAReporter = filter_input(INPUT_GET, 'idFrais', FILTER_DEFAULT, FILTER_SANITIZE_NUMBER_INT);
                if ($pdo->estPremierFraisMois($_SESSION['idVisiteurSelectionne'], $moisSuivant)) {
                    $pdo->creeNouvellesLignesFrais($_SESSION['idVisiteurSelectionne'], $moisFicheSuivante);
                }
                $leFraisHorsForfait = $pdo->getLeFraisHorsForfait($_SESSION['idVisiteurSelectionne'], $moisSelectionne, $idFraisAReporter);
                $libelleFrais = $leFraisHorsForfait['libelle'];
                $dateFrais = dateAnglaisVersFrancais($leFraisHorsForfait['date']);
                $montantFrais = $leFraisHorsForfait['montant'];
                $pdo->creeNouveauFraisHorsForfait($_SESSION['idVisiteurSelectionne'], $moisFicheSuivante, $libelleFrais, $dateFrais, $montantFrais);
                $pdo->supprimerFraisHorsForfait($idFraisAReporter, $_SESSION['idVisiteurSelectionne']);
                $visiteurSelectionne = $_SESSION['idVisiteurSelectionne'];
                $lesMoisDisponibles = $pdo->getLesMoisDisponibles($_SESSION['idVisiteurSelectionne']);
                $utilisateur = $pdo->getLeVisiteur($_SESSION['idVisiteurSelectionne']);
                $infosFichesFrais = $pdo->getLesInfosFicheFrais($_SESSION['idVisiteurSelectionne'], $_SESSION['moisSelectionne']);
                $nbrJustificatifs = $infosFichesFrais['nbJustificatifs'];
                ajouterErreur('Les éléments ont bien été modifiés.');
                include 'vues/v_erreurs.php';
                include 'vues/v_choixUtilisateurs.php';
                $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($_SESSION['idVisiteurSelectionne'], $moisSelectionne);
                $lesFraisForfait = $pdo->getLesFraisForfait($_SESSION['idVisiteurSelectionne'], $moisSelectionne);
                require 'vues/v_listeFraisForfaitVisiteur.php';
                require 'vues/v_listeFraisHorsForfaitVisiteur.php';
                break;
            case "validerFiche":
                $nbJustificatifs = filter_input(INPUT_POST, 'nbJustificatif', FILTER_DEFAULT, FILTER_SANITIZE_STRING);
                $pdo->majNbJustificatifs($_SESSION['idVisiteurSelectionne'], $_SESSION['moisSelectionne'], $nbJustificatifs);
                $pdo->majEtatFicheFrais($_SESSION['idVisiteurSelectionne'], $_SESSION['moisSelectionne'], "VA");
                $visiteurSelectionne = $_SESSION['idVisiteurSelectionne'];
                $lesMoisDisponibles = $pdo->getLesMoisDisponibles($_SESSION['idVisiteurSelectionne']);
                $utilisateur = $pdo->getLeVisiteur($_SESSION['idVisiteurSelectionne']);
                ajouterErreur('La fiche a bien été validée.');
                include 'vues/v_erreurs.php';
                include 'vues/v_choixUtilisateurs.php';
                break;
        }
    } else {
        ajouterErreur('La validation des fiches de frais des visiteurs '
                . 'n\'est possible qu\'entre le 10 et le 20 du mois.');
        include 'vues/v_erreurs.php';
    }
}
