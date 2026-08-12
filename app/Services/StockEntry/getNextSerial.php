<?php

namespace App\Services\StockEntry;

use DB;

class getNextSerial
{
    public function get()
    {
       $record = DB::table('serial_numbers')
            ->where('module', 'stock_entry')
            ->where('year', date('Y'))
            ->first();

        $serial = $record ? $record->serial_number : null;

        if (!$serial) {
            DB::table('serial_numbers')
                ->insert(['module' => 'stock_entry', 'year' => date('Y'), 'serial_number' => '1']);
            $serial = 1;
        }

        $next = ($serial+1);

        DB::table('serial_numbers')
            ->where('module', 'stock_entry')
            ->where('year', date('Y'))
            ->update(['serial_number' => $next]);

        return $serial;
    }
}