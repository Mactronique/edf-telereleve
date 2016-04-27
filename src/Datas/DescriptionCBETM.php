<?php

namespace Mactronique\TeleReleve\Datas;

class DescriptionCBETM
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
            'IINST1' => 'Intensité Instantanée Phase 1',
            'IINST2' => 'Intensité Instantanée Phase 2',
            'IINST3' => 'Intensité Instantanée Phase 3',
            'ADPS' => 'Avertissement de Dépassement De Puissance Souscrite',
            'IMAX1' => 'Intensité maximale appelée phase 1',
            'IMAX2' => 'Intensité maximale appelée phase 2',
            'IMAX3' => 'Intensité maximale appelée phase 3',
            'PAPP' => 'Puissance apparente triphasée',
            'PMAX' => 'Puissance maximale triphasée atteinte',
            'PPOT' => 'Présence des potentiels',
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
            'IINST1' => 'A',
            'IINST2' => 'A',
            'IINST3' => 'A',
            'ADPS' => 'A',
            'IMAX1' => 'A',
            'IMAX2' => 'A',
            'IMAX3' => 'A',
            'PAPP' => 'VA',
            'PMAX' => '',
            'PPOT' => '',
            'HHPHC' => '',
            'MOTDETAT' => '',
        ];

        return $values[$value];
    }
}
