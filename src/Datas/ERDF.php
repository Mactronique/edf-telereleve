<?php

namespace Mactronique\TeleReleve\Datas;

class ERDF
{
    /**
     * Periode Tarifaire en cours
     */
    public static function ptec($value)
    {
        $values = [
            'TH..' => 'Toutes les Heures.',
            'HC..' => 'Heures Creuses.',
            'HP..' => 'Heures Pleines.',
            'HN..' => 'Heures Normales.',
            'PM..' => 'Heures de Pointe Mobile.',
            'HCJB' => 'Heures Creuses Jours Bleus.',
            'HCJW' => 'Heures Creuses Jours Blancs.',
            'HCJR' => 'Heures Creuses Jours Rouges.',
            'HPJB' => 'Heures Pleines Jours Bleus.',
            'HPJW' => 'Heures Pleines Jours Blancs.',
            'HPJR' => 'Heures Pleines Jours Rouges.',
        ];

        return $values[$value];
    }

    /**
     * Couleur du lendemain
     */
    public static function demain($value)
    {
        $values = [
            'BLEU' => 'Bleu',
            'BLAN' => 'Blanc',
            'ROUG' => 'Rouge',
        ];

        return $values[$value];
    }

    /**
     * Option du tarif
     */
    public static function optarif($value)
    {
        if (strpos($value, 'BBR')===0) {
            return 'Option Tempo';
        }

        $values = [
            'BASE' => 'Option Base',
            'HC..' => 'Option Heures Creuses',
            'EJP.' => 'Option EJP',
        ];

        return $values[$value];
    }
}
