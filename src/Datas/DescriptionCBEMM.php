<?php

namespace Mactronique\TeleReleve\Datas;

class DescriptionCBEMM
{

    /**
     * Libellé de la valeur
     * @return string
     */
    public static function _label($value)
    {
        $values = [
            'ADCO' => 'Adresse du compteur',
            'OPTARIF' => 'Option tarifaire choisie',
            'ISOUSC' => 'Intensité souscrite',
            'BASE' => 'Index option Base',
            'HCHC' => 'Index option Heures Creuses - Heure Creuses',
            'HCHP' => 'Index option Heures Creuses - Heure Pleines',
            'EJPHN' => 'Index option EJP - Heures Normales',
            'EJPHPM' => 'Index option EJP - Heures de Pointe Mobile',
            'BBRHCJB' => 'Index option Tempo - Heures Creuses Jours Bleus',
            'BBRHPJB' => 'Index option Tempo - Heures Pleines Jours Bleus',
            'BBRHCJW' => 'Index option Tempo - Heures Creuses Jours Blancs',
            'BBRHPJW' => 'Index option Tempo - Heures Pleines Jours Blancs',
            'BBRHCJR' => 'Index option Tempo - Heures Creuses Jours Rouges',
            'BBRHPJR' => 'Index option Tempo - Heures Pleines Jours Rouges',
            'PEJP' => 'Préavis Début EJP (30 min)',
            'PTEC' => 'Période Tarifaire en cours',
            'DEMAIN' => 'Couleur du lendemain',
            'IINST' => 'Intensité Instantanée',
            'ADPS' => 'Avertissement de Dépassement De Puissance Souscrite',
            'IMAX' => 'Intensité maximale appelée',
            'PAPP' => 'Puissance apparente',
            'HHPHC' => 'Horaire Heures Pleines Heures Creuses',
            'MOTDETAT' => 'Mot d\'état du compteur',
        ];

        return $values[$value];
    }


    /**
     * Unité de la valeur
     * @return string
     */
    public static function _unite($value)
    {
        $values = [
            'ADCO' => '',
            'OPTARIF' => '',
            'ISOUSC' => 'A',
            'BASE' => 'Wh',
            'HCHC' => 'Wh',
            'HCHP' => 'Wh',
            'EJPHN' => 'Wh',
            'EJPHPM' => 'Wh',
            'BBRHCJB' => 'Wh',
            'BBRHPJB' => 'Wh',
            'BBRHCJW' => 'Wh',
            'BBRHPJW' => 'Wh',
            'BBRHCJR' => 'Wh',
            'BBRHPJR' => 'Wh',
            'PEJP' => 'min',
            'PTEC' => '',
            'DEMAIN' => '',
            'IINST' => 'A',
            'ADPS' => 'A',
            'IMAX' => 'A',
            'PAPP' => 'VA',
            'HHPHC' => '',
            'MOTDETAT' => '',
        ];

        return $values[$value];
    }
}
