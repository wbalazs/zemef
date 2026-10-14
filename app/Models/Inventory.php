<?php

namespace App\Models;

use App\Enums\InventoryStatus;
use App\Models\Package\PackageItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\InventoryPicture;
use App\Models\ZImage;
/*
Kapcsolatok összefoglalása (logikai modell)
inventory ← purchase_tickets : polimorf (source_type='purchase', source_header_id → purchase_ticket_id)

inventory ← consignment_receipts : polimorf (source_type='consignment')

inventory ← stock_entries : polimorf (source_type='stock_entry')

inventory ← item_lists : polimorf (source_type='item_list')

delivery_note 1→N delivery_note_items

delivery_note_items N→1 inventory (opcionális, SET NULL ha a készletrekord törlődik)

A készletnyilvántartás szempontjából az inventory tábla az egyetlen forrás.
A beérkező bizonylatok (vételi jegy stb.) létrehozásakor a tételeket ebbe a táblába kell beszúrni.
A szállítólevél kiállításakor a kiválasztott tételek status-át frissíteni kell (pl. 0-ra), 
és a delivery_note_items-be egy rekordot kell beszúrni a denormalizált adatokkal.
A készletmozgások (raktárközi átadás, javításra küldés stb.) szintén az inventory rekordok módosításával 
és a kapcsolódó tételek létrehozásával történnek.

Először: inventory.status tipikus értékei egy ékszerbolt nyilvántartóban:

1 = raktáron (fizikailag a boltban/üzletben van)

0 = kivezetve (eladva, selejtezve, visszaküldve)

2 = függőben (pl. bevételkor még nem érkezett meg fizikailag, vagy vizsgálat alatt)

3 = javítás alatt

4 = bizományban (kiadva bizományba)

5 = foglalt (előjegyzett)

6 = Szállítólevél (delivery_note) külső partner (beszállító, vevő, bizományos) felé történő szállításhoz. Ha a beszállítótól veszel át árut, az egy vétel, amihez tartozik egy szállítólevél a beszállítótól. De a te rendszeredben a vételi jegy (purchase_ticket) már rögzíti a vételt, és a szállítólevél (delivery_note) akkor kell, ha te szállítasz tovább (pl. a beszállítótól átvett árut továbbadod a vevőnek). A beszállítótól való átvételnél a saját raktárba történő bevételezés az inventory rekord létrehozásával történik, és a storage_id (aktuális raktárhely) beállításával. Nem kell külön raktárközi átadás, mert az a saját raktáraid között van.

7= raktárközi átadás  (WarehouseTransfer)

*/

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $primaryKey = 'inventory_id';

    protected $fillable = [
        'product_id',
        'sorszam',
        'code',
        'name',
        'description',
        'factory_number',
        'weight',
        'hallmark',
        'repair',
        'is_fake',
        'quantity',
        'oneprice',//beszerzési egységár
        'price',//eladási ár
        'investigation',
        'investigated_at',
        'investigation_user_id',
        'comment',
        'inv_comment',
        'status',
        'storage_id',
        'source_type', //enum('purchase','consignment','stock_entry','item_list') NOT NULL,
        'source_header_id',//purchase_ticket_id / consignment_receipt_id / stock_entry_id / item_list.id
        'warehouse_from',
        'warehouse_to',
        'sku',
        'termek_tipus_id',
    ];

    protected $casts = [
        'investigated_at' => 'datetime',
        'hallmark' => 'boolean',
        'repair' => 'boolean',
        'is_fake' => 'boolean',
        'investigation' => 'boolean',
        'item_data' => 'array',
    ];
    
    public function inventoryPictures()
{
    return $this->hasMany(InventoryPicture::class, 'inventory_id', 'inventory_id');
}
    /**
     * A terméktípus (products tábla)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Aktuális raktárhely (warehouses tábla)
     */
    public function storage(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'storage_id', 'warehouse_id');
    }

    /**
     * Polimorf kapcsolat a forrás bizonylathoz (purchase_ticket, consignment_receipt, stock_entry, item_list)
     */
    public function source(): MorphTo
    {
        return $this->morphTo(null, 'source_type', 'source_header_id');
    }

    /**
     * Szállítólevél tételek, ahol ez az inventory tétel szerepel
     */
    public function deliveryNoteItems(): HasMany
    {
        return $this->hasMany(DeliveryNoteItem::class, 'inventory_item_id', 'inventory_id');
    }

    /**
     * Raktármozgatási tételek, ahol ez az inventory tétel szerepel
     */
    public function warehouseTransferItems(): HasMany
    {
        return $this->hasMany(WarehouseTransferItem::class, 'inventory_item_id', 'inventory_id');
    }

    public function inventory_pictures(): HasMany
    {
        return $this->hasMany(InventoryPicture::class, 'inventory_id', 'inventory_id')->orderBy('created_at', 'DESC');
    }

    public function stock_entry_images(): HasMany
    {
        return $this->hasMany(ZImage::class, 'resource_id', 'inventory_id')
                    ->where('module', 'stock_entry');
    }

    public function consignment_receipt_images(): HasMany
    {
        return $this->hasMany(ZImage::class, 'resource_id', 'inventory_id')
                    ->whereIn('module', ['consignment_receipt', 'cri']);
    }

    public function packageItems()
    {
        return $this->morphMany(PackageItem::class, 'itemable');
    }

    /**
     * Generál egyedi kódot az inventory tételhez az adott termékhez tartozó sorszám alapján.
     * Formátum: {termék_kód}-{háromjegyű sorszám}
     */
    public function generateInventoryCode($termek_tipus_id)
    {
        $count = Inventory::where('termek_tipus_id', $termek_tipus_id)->count();
        $nextNumber = $count + 1;
        
        return 'PRD-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get the next sorszám for the inventory.
     */
    public static function getNextSorszam($stockEntryId = null)
    {
        if ($stockEntryId) {
            $maxSorszam = Inventory::where('source_header_id', $stockEntryId)->max('sorszam');
        } else {
            $maxSorszam = Inventory::max('sorszam');
        }
        return $maxSorszam ? $maxSorszam + 1 : 1;
    }
    /**
     * Get the next alszám for the inventory.
     */
    public static function getNextAlszam($stockEntryId = null, $sorszam = null)
    {
        if ($stockEntryId && $sorszam) {
            $maxAlszam = Inventory::where('source_header_id', $stockEntryId)
                ->where('sorszam', $sorszam)
                ->max('alszam');
        } else {
            return 2; // Ha nincs stockEntryId vagy sorszam, akkor nem tudunk alszamot generálni
        }
        return $maxAlszam ? $maxAlszam + 1 : 2;
    }

    public function statusEnum(): ?InventoryStatus
    {
        return InventoryStatus::tryFrom((int) $this->status);
    }
}