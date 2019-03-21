<?php
/**
 * Vue Liste des frais hors forfait
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
?>
<hr>
<div class="row">
    <div class="panel panel-info">
        <div class="panel-heading">Descriptif des éléments hors forfait</div>
        <form method="POST" action="index.php?uc=validerFrais&action=actualiserFraisHorsForfait">
            <table class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th class="date">Date</th>
                        <th class="libelle">Libellé</th>  
                        <th class="montant">Montant</th>  
                        <th class="action">&nbsp;</th> 
                    </tr>
                </thead>  

                <tbody>
                    <?php
                    foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                        $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
                        $date = $unFraisHorsForfait['date'];
                        $montant = $unFraisHorsForfait['montant'];
                        $id = $unFraisHorsForfait['id'];
                        ?>  

                        <tr>
                            <td>
                                <input type="text" 
                                       name="lesFrais[<?php echo $id; ?>][date]"
                                       class="form-control"
                                       value="<?php
                                       echo $date;
                                       ?>">
                            </td>
                            <td>
                                <input type="text" 
                                       name="lesFrais[<?php echo $id; ?>][libelle]"
                                       class="form-control"
                                       value="<?php
                                       echo $libelle;
                                       ?>">
                            </td>
                            <td>
                                <input type="number" 
                                       name="lesFrais[<?php echo $id; ?>][montant]"
                                       class="form-control"
                                       value="<?php
                                       echo $montant;
                                       ?>">
                            </td>
                            <td>            
                                <button class="btn btn-success" type="submit">Corriger</button>
                                <a href="index.php?uc=validerFrais&action=supprimerFraisHorsForfaitNonValide&idFrais=<?php
                                    echo $id;
                                ?>" onclick="return confirm('Voulez-vous vraiment supprimer ce frais?');" class="btn btn-danger" type="reset">Supprimer</a>
                                <a href="index.php?uc=validerFrais&action=reporterFraisHorsForfait&idFrais=<?php
                                    echo $id;
                                ?>" onclick="return confirm('Voulez-vous vraiment reporter ce frais au mois suivant?');" class="btn btn-danger" type="reset">Reporter</a>
                            </td>

                        </tr>

                        <?php
                    }
                    ?>
                </tbody>  
            </table>       
        </form>         
    </div>
    <form method="POST" action="index.php?uc=validerFrais&action=validerFiche">
        <div class="form-group">
            <label for="nbJustificatif">Nombre de justificatifs</label>
            <input type="number" id="nbJustificatif" 
                   name="nbJustificatif"
                   size="10" maxlength="5" 
                   value="<?php echo $nbrJustificatifs ?>" 
                   class="form-control">
        </div>
        <button class="btn btn-success" type="submit" 
                onclick="return confirm('Voulez-vous vraiment valider cette fiche de frais ?');">Valider</button>
        <button class="btn btn-danger" type="reset">Réinitialiser</button>
    </form>
</div>