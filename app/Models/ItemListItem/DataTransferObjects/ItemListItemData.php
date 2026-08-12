<?php
// app/Models/ItemListItem/DataTransferObjects/ItemListItemData.php

namespace App\Models\ItemListItem\DataTransferObjects;

use Spatie\LaravelData\Data;
use Carbon\Carbon;

class ItemListItemData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $item_list_id,
        public readonly int $product_id,
        public readonly int $main_product_id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?int $quantity,
        public readonly ?int $oneprice,
        public readonly ?int $price,
        public readonly ?string $factory_number,
        public readonly ?float $weight,
        public readonly ?bool $investigation,
        public readonly ?Carbon $investigated_at,
        public readonly ?int $investigation_user_id,
        public readonly ?bool $hallmark,
        public readonly ?bool $repair,
        public readonly ?bool $is_fake,
        public readonly ?int $warehouse_from,
        public readonly ?int $warehouse_to,
        public readonly ?string $comment,
        public readonly ?string $inv_comment,
        public readonly Carbon $created_at,
        public readonly Carbon $updated_at,
        public readonly ?bool $status,
        public readonly ?bool $is_unique
    ) {}

    /**
     * Create a new DTO from an array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            item_list_id: $data['item_list_id'],
            product_id: $data['product_id'] ?? 0,
            main_product_id: $data['main_product_id'] ?? 0,
            name: $data['name'],
            description: $data['description'] ?? null,
            quantity: $data['quantity'] ?? null,
            oneprice: $data['oneprice'] ?? null,
            price: $data['price'] ?? null,
            factory_number: $data['factory_number'] ?? null,
            weight: isset($data['weight']) ? (float)$data['weight'] : null,
            investigation: $data['investigation'] ?? null,
            investigated_at: isset($data['investigated_at']) ? Carbon::parse($data['investigated_at']) : null,
            investigation_user_id: $data['investigation_user_id'] ?? null,
            hallmark: $data['hallmark'] ?? null,
            repair: $data['repair'] ?? null,
            is_fake: $data['is_fake'] ?? null,
            warehouse_from: $data['warehouse_from'] ?? null,
            warehouse_to: $data['warehouse_to'] ?? null,
            comment: $data['comment'] ?? null,
            inv_comment: $data['inv_comment'] ?? null,
            created_at: isset($data['created_at']) ? Carbon::parse($data['created_at']) : Carbon::now(),
            updated_at: isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : Carbon::now(),
            status: $data['status'] ?? null,
            is_unique: $data['is_unique'] ?? null
        );
    }

    /**
     * Create a DTO from a model instance
     */
    public static function fromModel($model): self
    {
        return new self(
            id: $model->id,
            item_list_id: $model->item_list_id,
            product_id: $model->product_id,
            main_product_id: $model->main_product_id,
            name: $model->name,
            description: $model->description,
            quantity: $model->quantity,
            oneprice: $model->oneprice,
            price: $model->price,
            factory_number: $model->factory_number,
            weight: $model->weight ? (float)$model->weight : null,
            investigation: $model->investigation,
            investigated_at: $model->investigated_at ? Carbon::parse($model->investigated_at) : null,
            investigation_user_id: $model->investigation_user_id,
            hallmark: $model->hallmark,
            repair: $model->repair,
            is_fake: $model->is_fake,
            warehouse_from: $model->warehouse_from,
            warehouse_to: $model->warehouse_to,
            comment: $model->comment,
            inv_comment: $model->inv_comment,
            created_at: Carbon::parse($model->created_at),
            updated_at: Carbon::parse($model->updated_at),
            status: $model->status,
            is_unique: $model->is_unique
        );
    }
}