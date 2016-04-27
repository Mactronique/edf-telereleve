<?php
/**
 * This file is part of Mactronique EDF TeleReleve package.
 *
 * @author Jean-Baptiste Nahan <jbnahan@gmail.com>
 * @copyright 2016 - Jean-Baptiste Nahan
 * @license MIT
 */
namespace Mactronique\TeleReleve\Compteur;

use Mactronique\TeleReleve\Datas\ERDF;

class Releve implements ReleveInterface
{
    /**
     * @var \DateTimeImmutable
     */
    private $recordedAt;

    /**
     * @var array
     */
    private $datas;

    /**
     * @return array
     */
    public static function makeFromData(array $datas)
    {
        $releve = new self();
        $releve->datas = $datas;
        $releve->recordedAt = new \DateTimeImmutable();
        return $releve;
    }

    /**
     * Private Constructor
     */
    private function __construct()
    {
    }

    /*
     * @return \DateTimeImmutable
     */
    public function at()
    {
        return $this->recordedAt;
    }

    /*
     * @return array
     */
    public function index()
    {
        return $this->datas;
    }

    /**
     * Return array of array with Code, Label, Value, Unit
     * @return array
     */
    public function describe()
    {
        $datas = [];
        foreach ($this->datas as $key => $value) {
            $lower = strtolower($key);
            if (method_exists('Mactronique\TeleReleve\Datas\ERDF', $lower)) {
                $value = sprintf('%s (%s)', ERDF::$lower($value), $value);
            } else {
                $value = ERDF::_cleanAndConvert($key, $value);
            }
            $ligne = [$key, ERDF::_label($key), $value, ERDF::_unite($key)];
            $datas[] = $ligne;
        }
        
        return $datas;
    }
}
