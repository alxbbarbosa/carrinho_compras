<?php

namespace Abbarbosa\Gadgets\CarrinhoCompras\Contracts;

interface iModel
{
    public static function setConnection(\PDO $connection);
    
    public function _create(array $array = []): ?iModel;

    public function _all(array $criteria): array;

    public function _find(int $id): ?iModel;

    public function save(): ?iModel;

    public function delete(): bool;

    public function __isset($property): bool;

    public function __get($property);

    public function __set($property, $value);

    public function fromArray(array $array): iModel;

    public function toArray(): array;
}
