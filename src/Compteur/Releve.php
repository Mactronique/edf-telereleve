<?php

declare(strict_types=1);
/**
 * This file is part of Mactronique EDF TeleReleve package.
 *
 * @author Jean-Baptiste Nahan <814683+macintoshplus@users.noreply.github.com>
 * @copyright 2016,2024 - Jean-Baptiste Nahan
 * @license MIT
 */

namespace Mactronique\TeleReleve\Compteur;

use Mactronique\TeleReleve\Datas\ERDF;

class Releve implements ReleveInterface
{
    private string $compteur;

    private \DateTimeImmutable $recordedAt;

    private array $datas;

    private function __construct()
    {
    }

    /**
     * @param string $compteur the counter name
     *
     * @throws \Exception
     */
    public static function makeFromData(string $compteur, array $datas): self
    {
        $releve = new self();
        $releve->compteur = $compteur;
        $releve->datas = $datas;
        $releve->recordedAt = new \DateTimeImmutable();

        return $releve;
    }

    /**
     * @param string $compteur the counter name
     */
    public static function makeFromStorage(string $compteur, array $datas): self
    {
        $releve = new self();
        $releve->compteur = $compteur;
        $releve->recordedAt = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $datas['AT']);
        unset($datas['AT']);
        $releve->datas = $datas;

        return $releve;
    }

    public function at(): \DateTimeImmutable
    {
        return $this->recordedAt;
    }

    public function index(): array
    {
        return $this->datas;
    }

    /**
     * Retourn the value clean.
     *
     * @throws \ReflectionException
     */
    public function valueAtIndex($code)
    {
        if (!\array_key_exists($code, $this->datas)) {
            return null;
        }

        return $this->cleanAndConvert($code, $this->datas[$code]);
    }

    /**
     * Return array of array with Code, Label, Value, Unit.
     *
     * @throws \ReflectionException
     */
    public function describe(): array
    {
        $description = 'Mactronique\TeleReleve\Datas\Description'.$this->compteur;
        if (!class_exists($description)) {
            throw new \LogicException('Unable to load description for counter : '.$description, 1);
        }
        $datas = [];
        foreach ($this->datas as $key => $value) {
            $lower = strtolower($key);
            if (method_exists('Mactronique\TeleReleve\Datas\ERDF', $lower)) {
                $value = \sprintf('%s (%s)', ERDF::$lower($value), $value);
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
     *
     * @throws \ReflectionException
     */
    private function cleanAndConvert(string $code, string $value)
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
