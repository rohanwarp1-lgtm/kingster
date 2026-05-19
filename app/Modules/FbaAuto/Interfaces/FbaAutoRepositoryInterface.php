<?php

namespace App\Modules\FbaAuto\Interfaces;

interface FbaAutoRepositoryInterface
{
    public function all(array $filters = []);
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function restore(int $id);
    public function forceDelete(int $id);
    public function findByShipmentId(string $shipmentId);
    public function getWarehouses(): array;
    public function getStates(): array;
}
