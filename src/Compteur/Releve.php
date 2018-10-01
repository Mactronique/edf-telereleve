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
     * @var string
     */
    private $compteur;
    /**
     * @var \DateTimeImmutable
     */
    private $recordedAt;

    /**
     * @var array
     */
    private $datas;

    /**
     * @param string the counter name
     * @param array $datas
     * @return self
     * @throws \Exception
     */
    public static function makeFromData($compteur, array $datas)
    {
        $releve = new self();
        $releve->compteur = (string) $compteur;
        $releve->datas = $datas;
        $releve->recordedAt = new \DateTimeImmutable();
        return $releve;
    }

    /**
     * @param string the counter name
     * @param array $datas
     * @return self
     */
    public static function makeFromStorage($compteur, array $datas)
    {
        $releve = new self();
        $releve->compteur = (string) $compteur;
        $releve->recordedAt = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $datas['AT']);
        unset($datas['AT']);
        $releve->datas = $datas;

        return $releve;
    }

    /**
     * Private Constructor
     */
    private function __construct()
    {
    }

    /**
     * @return \DateTimeImmutable
     */
    public function at()
    {
        return $this->recordedAt;
    }

    /**
     * @return array
     */
    public function index()
    {
        return $this->datas;
    }

    /**
     * Retourn the value clean.
     * @param string $code
     * @return mixed
     * @throws \ReflectionException
     */
    public function valueAtIndex($code)
    {
        if (!array_key_exists($code, $this->datas)) {
            return null;
        }
        return $this->cleanAndConvert($code, $this->datas[$code]);
    }

    /**
     * Return array of array with Code, Label, Value, Unit
     * @return array
     * @throws \ReflectionException
     */
    public function describe()
    {
        $description = 'Mactronique\TeleReleve\Datas\Description'.$this->compteur;
        if (!class_exists($description)) {
            throw new \LogicException("Unable to load description for counter : ".$description, 1);
        }
        $datas = [];
        foreach ($this->datas as $key => $value) {
            $lower = strtolower($key);
            if (method_exists('Mactronique\TeleReleve\Datas\ERDF', $lower)) {
                $value = sprintf('%s (%s)', ERDF::$lower($value), $value);
            } else {
                $value = $this->cleanAndConvert($key, $value);
            }
            $ligne = [$key, $description::_label($key), $value, $description::_unite($key)];
            $datas[] = $ligne;
        }
        
        return $datas;
    }

    /**
     * Clean the value and return most detail if available.
     * @param string $code
     * @param string $value
     * @return string
     * @throws \ReflectionException
     */
    private function cleanAndConvert($code, $value)
    {
        $codeA = ucfirst(strtolower($code));
        $className = 'Mactronique\TeleReleve\Datas\Convert'.$codeA;
        if (!class_exists($className)) {
            return $value;
        }
        $rc = new \ReflectionClass($className);
        if (!$rc->isSubclassOf('Mactronique\TeleReleve\Datas\ConverterInterface')) {
            return $value;
        }
        return $className::convert($code, $value);
    }
}
