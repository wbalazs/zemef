<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Karat;
use App\Models\Category2;
use App\Models\ToolType;
use App\Models\Payment;
use App\Models\Hallmark;
use App\Models\Attribute;
use App\Models\ProductStatus;
use App\Models\ProductType;
use App\Models\ProductSign;
use App\Models\ProductColor;
use App\Models\Activity;
use App\Models\NAVStandard;
use App\Models\NAVFuel;
use App\Models\Currency;
use App\Models\DocType;
use App\Models\UnitPriceType;
use App\Models\VatType;
use App\Models\Address\Country;
use App\Models\Address\Zip;
use App\Models\Address\City;
use App\Models\Booking;
use App\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;



class MasterController extends Controller
{
    public function __construct()
    {
        ////$this->authorizeResource(Bank::class);
    }
    

    public function displayBanks(Request $request)
    {
        $model = new Bank();

        $banks = $model::orderBy('name', 'ASC')->get();

        return view('master.banks.index', ['banks' => $banks]);
    }

    public function displayBank(Request $request)
    {
        if($request->bank_id){
            $bank = Bank::find($request->bank_id);
        } else {
            $bank = new Bank();
        }


        return view('master.banks.edit', ['bank' => $bank]);
    }

    public function saveBank(Request $request)
    {
        if($request->bank_id){
            $bank = Bank::find($request->bank_id);
        } else {
            $bank = new Bank();
            $bank->created_at = date('Y-m-d H:i:s');
        }

        $bank->name = $request->name;
        $bank->status = $request->status;
        $bank->updated_at = date('Y-m-d H:i:s');

        $bank->save();

        $redirect_url = '/master/banks';
        $msg[0] = "A bank adatai sikeresen mentve.";

        return redirect($redirect_url)->withMessage($msg);
    }

    public function deleteBank(Request $request)
    {
        $bank = Bank::find($request->bank_id);
        $redirect_url = '/master/banks';
        $msg[0] = "A bank adatai sikeresen törölve.";

        try {
            $bank->delete();
            return redirect($redirect_url)->withStatus('A bank sikeresen törölve.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect($redirect_url)->withErrors('Nem lehet törölni a bankot, mert kapcsolódó bankszámlái vannak.');
        }
    }

    public function displayKarats(Request $request)
    {
        $model = new Karat();

        $karats = $model::orderBy('name', 'ASC')->get();

        return view('master.karats.index', ['karats' => $karats]);
    }

    public function displayKarat(Request $request)
    {
        if($request->karat_id){
            $karat = Karat::find($request->karat_id);
        } else {
            $karat = new Karat();
        }


        return view('master.karats.edit', ['karat' => $karat]);
    }

    public function saveKarat(Request $request)
    {
        if($request->karat_id){
            $karat = Karat::find($request->karat_id);
        } else {
            $karat = new Karat();
            $karat->created_at = date('Y-m-d H:i:s');
        }

        $karat->name = $request->name;
        $karat->status = $request->status;
        $karat->updated_at = date('Y-m-d H:i:s');

        $karat->save();

        $redirect_url = '/master/karats';
        $msg[0] = "A karát adatai sikeresen mentve.";

        return redirect($redirect_url)->withMessage($msg);
    }

    public function deleteKarat(Request $request)
    {
        $karat = Karat::find($request->karat_id);
        $redirect_url = '/master/karats';
        $msg[0] = "A karát adatai sikeresen törölve.";

        try {
            $karat->delete();
            return redirect($redirect_url)->withStatus('A karát sikeresen törölve.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect($redirect_url)->withErrors('Nem lehet törölni a karátot, mert kapcsolódó adatai vannak.');
        }
    }

    public function displayCategory2s(Request $request)
    {
        $model = new Category2();

        $category2s = $model::orderBy('name', 'ASC')->get();

        return view('master.category2s.index', ['category2s' => $category2s]);
    }

    public function displayCategory2(Request $request)
    {
        if($request->category2_id){
            $category2 = Category2::find($request->category2_id);
        } else {
            $category2 = new Category2();
        }


        return view('master.category2s.edit', ['category2' => $category2]);
    }

    public function saveCategory2(Request $request)
    {
        if($request->category2_id){
            $category2 = Category2::find($request->category2_id);
        } else {
            $category2 = new Category2();
            $category2->created_at = date('Y-m-d H:i:s');
        }

        $category2->name = $request->name;
        $category2->status = $request->status;
        $category2->updated_at = date('Y-m-d H:i:s');

        $category2->save();

        $redirect_url = '/master/category2s';
        $msg[0] = "A kategória 2. adatai sikeresen mentve.";

        return redirect($redirect_url)->withMessage($msg);
    }

    public function deleteCategory2(Request $request)
    {
        $category2 = Category2::find($request->category2_id);
        $redirect_url = '/master/category2s';
        $msg[0] = "A kategória 2. adatai sikeresen törölve.";

        try {
            $category2->delete();
            return redirect($redirect_url)->withStatus('A kategória 2. sikeresen törölve.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect($redirect_url)->withErrors('Nem lehet törölni a kategória 2. elemet, mert kapcsolódó adatai vannak.');
        }
    }

    /**
     * Árfolyamok listázása.
     */
    public function displayCurrencies(Request $request)
    {
        $model = new Currency();

        $currencies = $model::orderBy('name', 'ASC')->get();

        return view('master.currencies.index', ['currencies' => $currencies]);
    }

    public function displayCurrency(Request $request)
    {
        if($request->currency_id){
            $currency = Currency::find($request->currency_id);
        } else {
            $currency = new Currency();
        }


        return view('master.currencies.edit', ['currency' => $currency]);
    }

    public function saveCurrency(Request $request)
    {
        if($request->currency_id){
            $currency = Currency::find($request->currency_id);
        } else {
            $currency = new Currency();
            $currency->created_at = date('Y-m-d H:i:s');
        }

        $currency->name = $request->name;
        $currency->sign = $request->sign;
        $currency->value = $request->value;
        $currency->status = $request->status;
        $currency->default = $request->has('default') ? $request->default : 0;
        $currency->editable = $request->has('editable') ? $request->editable : 0;
        $currency->updated_at = date('Y-m-d H:i:s');

        $currency->save();

        $redirect_url = '/master/currencies';
        $msg[0] = "A deviza adatai sikeresen mentve.";

        return redirect($redirect_url)->withMessage($msg);
    }

    public function deleteCurrency(Request $request)
    {
        $currency = Currency::find($request->currency_id);
        $redirect_url = '/master/currencies';
        $msg[0] = "A deviza adatai sikeresen törölve.";

        try {
            $currency->delete();
            return redirect($redirect_url)->withStatus('A deviza sikeresen törölve.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect($redirect_url)->withErrors('Nem lehet törölni a devizát , mert kapcsolódó adatai vannak.');
        }
    }

    /**
     * Deviza AJAX
     */
    public function get_currency_by_id(Request $request)
    {
        $currency = Currency::find($request->currency_id);
        return response()->json($currency);
    }


     /**
     * Dokumentum típusok listázása.
     */
    public function displayDoc_types(Request $request)
    {
        $model = new DocType();

        $doc_types = $model::orderBy('name', 'ASC')->get();

        return view('master.doc_types.index', ['doc_types' => $doc_types]);
    }

    public function displayDoc_type(Request $request)
    {
        if($request->doc_type_id){
            $doc_type = DocType::find($request->doc_type_id);
        } else {
            $doc_type = new DocType();
        }


        return view('master.doc_types.edit', ['doc_type' => $doc_type]);
    }

    public function saveDoc_type(Request $request)
    {
        if($request->doc_type_id){
            $doc_type = DocType::find($request->doc_type_id);
        } else {
            $doc_type = new DocType();
            $doc_type->created_at = date('Y-m-d H:i:s');
        }

        $doc_type->name = $request->name;
        $doc_type->updated_at = date('Y-m-d H:i:s');

        $doc_type->save();

        $redirect_url = '/master/doc_types';
        $msg[0] = "A dokumentum típus sikeresen mentve.";

        return redirect($redirect_url)->withMessage($msg);
    }

    public function deleteDoc_type(Request $request)
    {
        $doc_type = DocType::find($request->doc_type_id);
        $redirect_url = '/master/doc_types';
        $msg[0] = "A dokumentum típus sikeresen törölve.";

        try {
            $doc_type->delete();
            return redirect($redirect_url)->withStatus('A dokumentum típus sikeresen törölve.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect($redirect_url)->withErrors('Nem lehet törölni a dokumentum típust , mert kapcsolódó adatai vannak.');
        }
    }


    /**
     * Egységár típusok listázása.
     */
    public function displayUnitPriceTypes(Request $request)
    {
        $model = new UnitPriceType();

        $unit_price_types = $model::orderBy('name', 'ASC')->get();

        return view('master.unit_price_types.index', ['unit_price_types' => $unit_price_types]);
    }

    public function displayUnitPriceType(Request $request)
    {
        if($request->unit_price_type_id){
            $unit_price_type = UnitPriceType::find($request->unit_price_type_id);
        } else {
            $unit_price_type = new UnitPriceType();
        }


        return view('master.unit_price_types.edit', ['unit_price_type' => $unit_price_type]);
    }

    public function saveUnitPriceType(Request $request)
    {
        if($request->unit_price_type_id){
            $unit_price_type = UnitPriceType::find($request->unit_price_type_id);
        } else {
            $unit_price_type = new UnitPriceType();
            $unit_price_type->created_at = date('Y-m-d H:i:s');
        }

        $unit_price_type->name = $request->name;
        $unit_price_type->price = $request->price;
        $unit_price_type->from_date = $request->from_date;
        $unit_price_type->updated_at = date('Y-m-d H:i:s');

        $unit_price_type->save();

        $redirect_url = '/master/unit_price_types';
        $msg[0] = "Az egységár típus sikeresen mentve.";

        return redirect($redirect_url)->withMessage($msg);
    }

    public function deleteUnitPriceType(Request $request)
    {
        $unit_price_type = UnitPriceType::find($request->unit_price_type_id);
        $redirect_url = '/master/unit_price_types';
        $msg[0] = "Az egységár típus sikeresen törölve.";

        try {
            $unit_price_type->delete();
            return redirect($redirect_url)->withStatus('Az egységár típus sikeresen törölve.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect($redirect_url)->withErrors('Nem lehet törölni az egységár típust , mert kapcsolódó adatai vannak.');
        }
    }


    /**
     * Áfa típusok listázása.
     */
    public function displayVatTypes(Request $request)
    {
        $model = new VatType();

        $vat_types = $model::orderBy('name', 'ASC')->get();

        return view('master.vat_types.index', ['vat_types' => $vat_types]);
    }

    public function displayVatType(Request $request)
    {
        if($request->vat_type_id){
            $vat_type = VatType::find($request->vat_type_id);
        } else {
            $vat_type = new VatType();
        }


        return view('master.vat_types.edit', ['vat_type' => $vat_type]);
    }

    public function saveVatType(Request $request)
    {
        if($request->vat_type_id){
            $vat_type = VatType::find($request->vat_type_id);
        } else {
            $vat_type = new VatType();
            $vat_type->created_at = date('Y-m-d H:i:s');
        }

        $vat_type->name = $request->name;
        $vat_type->value = $request->value;
        $vat_type->updated_at = date('Y-m-d H:i:s');

        $vat_type->save();

        $redirect_url = '/master/vat_types';
        $msg[0] = "Az Áfa típus sikeresen mentve.";

        return redirect($redirect_url)->withMessage($msg);
    }

    public function deleteVatType(Request $request)
    {
        $vat_type = VatType::find($request->vat_type_id);
        $redirect_url = '/master/vat_types';
        $msg[0] = "Az áfa típus sikeresen törölve.";

        try {
            $vat_type->delete();
            return redirect($redirect_url)->withStatus('Az áfa típus sikeresen törölve.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect($redirect_url)->withErrors('Nem lehet törölni az áfa típust , mert kapcsolódó adatai vannak.');
        }
    }


    /**
     * Települések listázása.
     */
    public function displaySettlements(Request $request)
    {
        $cities = City::with(['country', 'zips'])->orderBy('name', 'ASC')->get();

        return view('master.cities.index', ['cities' => $cities]);
    }

    /**
     * Település szerkesztése.
     */
    public function editSettlement(Request $request)
    {
        $zip = Zip::find($request->id);

        if (!$zip) {
            return redirect()->route('master.cities.index')->with('error', 'A keresett irányítószám nem található.');
        }

        $countries = Country::orderBy('name', 'ASC')->get();

        $city = City::whereHas('zips', function($query) use ($zip) {
            $query->where('zips.id', $zip->id);
        })->first();

        return view('master.cities.edit', ['city' => $city, 'countries' => $countries, 'zip' => $zip]);
    }


    /**
     * Új település létrehozása.
     */
    public function createSettlement()
    {
        $countries = Country::orderBy('name', 'ASC')->get();
        return view('master.cities.edit', ['countries' => $countries]);
    }

    /**
     * Új település és irányítószám mentése.
     */
    public function storeSettlement(Request $request)
    {
        $rules = [
            'country_id' => 'required|exists:countries,id',
            'zip' => 'required|max:100',
            'city' => 'required|max:100',
        ];

        $request->validate($rules);

        // Irányítószám létrehozása vagy frissítése
        $zip = Zip::firstOrCreate(['name' => $request->zip, 'country_id' => $request->country_id]);

        // Település létrehozása vagy frissítése
        $cityData = [
            'name' => $request->city,
            'country_id' => $request->country_id
        ];

        if ($request->has('city_id') && $request->city_id) {
            $city = City::findOrFail($request->city_id);
            $city->update($cityData);
        } else {
            $city = City::create($cityData);
        }

        // Kapcsolódó adatok frissítése a zip_city táblában
        $zipCityData = [
            'zip_id' => $zip->id,
            'city_id' => $city->id,
        ];
        DB::table('zip_city')->updateOrInsert($zipCityData);

        return redirect()->route('master.cities.index')->withStatus('A település sikeresen mentve.');
    }

    /**
     * Település törlése.
     */
    public function deleteSettlement(Request $request)
    {
        $city = City::find($request->id);

        if (!$city) {
            return redirect()->route('master.cities.index')->withErrors(['A keresett település nem található.']);
        }


        //Zip_cíity tábla frissítése
        $zip_city = DB::table('zip_city')->where('city_id', $city->id)->first();
        //Zips tábla frissítése
        $zip = Zip::find($zip_city->zip_id);
        
        //Törlések
        if($zip) {
            $zip->delete();
        }
        DB::table('zip_city')->where('city_id', $city->id)->delete();
        if($city) {
            $city->delete();
        }
        DB::table('zip_city')->where('city_id', $city->id)->delete();

        return redirect()->route('master.cities.index')->withMessage(['A település sikeresen törölve.']);
    }






    public function displayBankAccounts(Request $request)
    {
        $model = new BankAccount();

        $bank_accounts = $model::with('bank')->orderBy('bank_account_id', 'DESC')->get();

        return view('master.bank_accounts.index', ['bank_accounts' => $bank_accounts]);
    }

    public function displayBankAccount(Request $request)
    {
        if($request->bank_account_id){
            $bank_account = BankAccount::find($request->bank_account_id);
        } else {
            $bank_account = new BankAccount();
        }

        $banks = Bank::where('status', 1)->orderBy('name', 'ASC')->get();

        return view('master.bank_accounts.edit', ['bank_account' => $bank_account, 'banks' => $banks]);
    }

    public function saveBankAccount(Request $request)
    {
        if($request->bank_account_id){
            $bank_account = BankAccount::find($request->bank_account_id);
        } else {
            $bank_account = new BankAccount();
            $bank_account->created_at = date('Y-m-d H:i:s');
        }

        $bank_account->bank_account_number = $request->bank_account_number;
        $bank_account->bank_id = $request->bank_id;
        $bank_account->status = $request->status;
        $bank_account->updated_at = date('Y-m-d H:i:s');

        $bank_account->save();

        $redirect_url = '/master/bank_accounts';
        $msg[0] = "A bankszámla adatai sikeresen mentve.";

        return redirect($redirect_url)->withMessage($msg);
    }

    public function deleteBankAccount(Request $request)
    {
        try {
            $bank_account = BankAccount::find($request->bank_account_id);
            
            if(!$bank_account) {
                return redirect('/master/bank_accounts')->withErrors(['Hiba történt! A keresett bankszámla nem található.']);
            }

            $bank_account->delete();
            return redirect('/master/bank_accounts')->withMessage(["A bankszámla adatai sikeresen törölve."]);

        } catch (\Exception $e) {
            return redirect('/master/bank_accounts')->withErrors(['A bankszámla nem törölhető, mert kapcsolódó tranzakciók vannak hozzá.']);
        }
    }


    public function displayToolTypes(Request $request)
    {
        $model = new ToolType();

        $tool_types = $model::orderBy('tool_type_id', 'DESC')->get();

        return view('master.tool_types.index', ['tool_types' => $tool_types]);
    }

    public function displayToolType(Request $request)
    {
        if($request->tool_type_id){
            $tool_type = ToolType::find($request->tool_type_id);
        } else {
            $tool_type = new ToolType();
        }

        $banks = Bank::where('status', 1)->orderBy('name', 'ASC')->get();

        return view('master.tool_types.edit', ['tool_type' => $tool_type, 'banks' => $banks]);
    }

    public function saveToolType(Request $request)
    {
        if($request->tool_type_id){
            $tool_type = ToolType::find($request->tool_type_id);
        } else {
            $tool_type = new ToolType();
            $tool_type->created_at = date('Y-m-d H:i:s');
        }

        $tool_type->name = $request->name;
        $tool_type->status = $request->status;
        $tool_type->updated_at = date('Y-m-d H:i:s');

        $tool_type->save();

        $redirect_url = '/master/tool_types';
        $msg[0] = "Az eszköz típus adatai sikeresen mentve.";

        return redirect($redirect_url)->withMessage($msg);
    }

    public function deleteToolType(Request $request)
    {
        try {
            $tool_type = ToolType::findOrFail($request->tool_type_id);
            $tool_type->delete();

            $msg[0] = "Az eszköz típus sikeresen törölve.";
            return redirect('/master/tool_types')->withMessage($msg);
        } catch (ModelNotFoundException $e) {
            return redirect('/master/tool_types')->withErrors(['Az eszköz típus nem található és ezért nem lehet törölni.']);
        } catch (\Exception $e) {
            return redirect('/master/tool_types')->withErrors(['Az eszköz típus nem törölhető mert kapcsolódó tárgyi-eszközök vannak hozzá.']);
        }
    }


    public function displayPayments(Request $request)
    {
        $model = new Payment();

        $payments = $model::orderBy('payment_id', 'DESC')->get();

        return view('master.payments.index', ['payments' => $payments]);
    }

    public function displayPayment(Request $request)
    {
        if($request->payment_id){
            $payment = Payment::find($request->payment_id);
        } else {
            $payment = new Payment();
        }

        $banks = Bank::where('status', 1)->orderBy('name', 'ASC')->get();

        return view('master.payments.edit', ['payment' => $payment, 'banks' => $banks]);
    }

    public function savePayment(Request $request)
    {
        if($request->payment_id){
            $payment = Payment::find($request->payment_id);
        } else {
            $payment = new Payment();
            $payment->created_at = date('Y-m-d H:i:s');
        }

        $payment->name = $request->name;
        $payment->days = $request->days;
        $payment->status = $request->status;
        $payment->updated_at = date('Y-m-d H:i:s');

        $payment->save();

        $redirect_url = '/master/payments';
        $msg[0] = "A fizetési mód adatai sikeresen mentve.";

        return redirect($redirect_url)->withMessage($msg);
    }

    public function deletePayment(Request $request)
    {
        try {
            $payment = Payment::findOrFail($request->payment_id);
            $payment->delete();

            $msg[0] = "A fizetési mód sikeresen törölve.";
            return redirect('/master/payments')->withMessage($msg);
        } catch (ModelNotFoundException $e) {
            return redirect('/master/payments')->withErrors(['A fizetési mód nem található, ezért nem lehet törölni.']);
        } catch (\Exception $e) {
            return redirect('/master/payments')->withErrors(['A fizetési mód nem törölhető, mert kapcsolódó adatok vannak hozzá.']);
        }
    }


    public function displayHallmarks(Request $request)
    {
        $model = new Hallmark();

        $hallmarks = $model::orderBy('hallmark_id', 'DESC')->get();

        return view('master.hallmarks.index', ['hallmarks' => $hallmarks]);
    }

    public function displayHallmark(Request $request)
    {
        if($request->hallmark_id){
            $hallmark = Hallmark::find($request->hallmark_id);
        } else {
            $hallmark = new Hallmark();
        }

        return view('master.hallmarks.edit', ['hallmark' => $hallmark]);
    }

    public function saveHallmark(Request $request)
    {
        if($request->hallmark_id){
            $hallmark = Hallmark::find($request->hallmark_id);
        } else {
            $hallmark = new Hallmark();
            $hallmark->created_at = date('Y-m-d H:i:s');
        }

        $hallmark->name = $request->name;
        $hallmark->type = $request->type;
        $hallmark->status = $request->status;
        $hallmark->updated_at = date('Y-m-d H:i:s');

        $hallmark->save();

        $redirect_url = '/master/hallmarks';
        $msg[0] = "A fémjelzés adatai sikeresen mentve.";

        return redirect($redirect_url)->withMessage($msg);
    }

    public function deleteHallmark(Request $request)
    {
        $hallmark = Hallmark::find($request->hallmark_id);
        $hallmark->delete();

        $redirect_url = '/master/hallmarks';
        $msg[0] = "A fémjelzés adatai sikeresen törölve.";

        return redirect($redirect_url)->withMessage($msg);
    }


    public function displayAttributes(Request $request)
    {
        $model = new Attribute();

        $attributes = $model::orderBy('attribute_id', 'DESC')->get();

        return view('master.attributes.index', ['attributes' => $attributes]);
    }

    public function displayAttribute(Request $request)
    {
        if($request->attribute_id){
            $attribute = Attribute::find($request->attribute_id);
        } else {
            $attribute = new Attribute();
        }

        return view('master.attributes.edit', ['attribute' => $attribute]);
    }

    public function saveAttribute(Request $request)
    {
        if($request->attribute_id){
            $attribute = Attribute::find($request->attribute_id);
        } else {
            $attribute = new Attribute();
            $attribute->created_at = date('Y-m-d H:i:s');
        }

        $attribute->name = $request->name;
        $attribute->type = $request->type;
        $attribute->status = $request->status;
        $attribute->updated_at = date('Y-m-d H:i:s');

        $attribute->save();

        $redirect_url = '/master/attributes';
        $msg[0] = "Az ingatlan tulajdonság adatai sikeresen mentve.";

        return redirect($redirect_url)->withMessage($msg);
    }

    public function deleteAttribute(Request $request)
    {
        try {
            $attribute = Attribute::findOrFail($request->attribute_id);
            $attribute->delete();

            $msg[0] = "Az ingatlan tulajdonság sikeresen törölve.";
            return redirect('/master/attributes')->withMessage($msg);
        } catch (ModelNotFoundException $e) {
            return redirect('/master/attributes')->withErrors(['Az ingatlan tulajdonság nem található és ezért nem lehet törölni.']);
        } catch (\Exception $e) {
            return redirect('/master/attributes')->withErrors(['Hiba történt az ingatlan tulajdonság törlése közben. Kérjük, próbálja meg újra később.']);
        }
    }



    public function displayNAVStandard(Request $request)
    {
        $standard = NAVStandard::find('1');

        return view('master.standards.edit', ['nav_standard' => $standard]);
    }

    public function saveNAVStandard(Request $request)
    {
        if($request->standard_id){
            $standard = NAVStandard::find($request->standard_id);
        } else {
            $standard = new NAVStandard();
            $standard->created_at = date('Y-m-d H:i:s');
        }

        $standard->benzin_1000 = $request->benzin_1000;
        $standard->benzin_1500 = $request->benzin_1500;
        $standard->benzin_2000 = $request->benzin_2000;
        $standard->benzin_3000 = $request->benzin_3000;
        $standard->benzin_5000 = $request->benzin_5000;
        $standard->dizel_1500 = $request->dizel_1500;
        $standard->dizel_2000 = $request->dizel_2000;
        $standard->dizel_3000 = $request->dizel_3000;
        $standard->dizel_5000 = $request->dizel_5000;
        $standard->amort = $request->amort;

        $standard->updated_at = date('Y-m-d H:i:s');

        $standard->save();

        $redirect_url = '/master/nav_standard';
        $msg[0] = "A norma adatai sikeresen mentve.";

        return redirect($redirect_url)->withMessage($msg);
    }

    public function deleteNAVStandard(Request $request)
    {
        $standard = NAVStandard::find($request->standard_id);
        $standard->delete();

        $redirect_url = '/master/nav_standard';
        $msg[0] = "A norma adatai sikeresen törölve.";

        return redirect($redirect_url)->withMessage($msg);
    }


    public function displayNAVFuels(Request $request)
    {
        $model = new NAVFuel();

        $nav_fuels = $model::orderBy('year', 'DESC')->orderBy('month', 'DESC')->get();

        return view('master.nav_fuels.index', ['nav_fuels' => $nav_fuels]);
    }

    public function displayNAVFuel(Request $request)
    {
        if($request->standard_id){
            $nav_fuel = NAVFuel::find($request->standard_id);
        } else {
            $nav_fuel = new NAVFuel();
        }

        return view('master.nav_fuels.edit', ['nav_fuel' => $nav_fuel]);
    }

    public function saveNAVFuel(Request $request)
    {
        if($request->standard_id){
            $nav_fuels = NAVFuel::find($request->standard_id);
        } else {
            $nav_fuels = new NAVFuel();
            $nav_fuels->created_at = date('Y-m-d H:i:s');
        }

        $nav_fuels->year = $request->year;
        $nav_fuels->month = $request->month;
        $nav_fuels->benzin95 = $request->benzin95;
        $nav_fuels->dizel = $request->dizel;
        $nav_fuels->updated_at = date('Y-m-d H:i:s');

        $nav_fuels->save();

        $redirect_url = '/master/nav_fuels';
        $msg[0] = "Az adatok sikeresen mentve.";

        return redirect($redirect_url)->withMessage($msg);
    }

    public function deleteNAVFuel(Request $request)
    {
        $nav_fuel = NAVFuel::find($request->standard_id);
        $nav_fuel->delete();

        $redirect_url = '/master/nav_fuels';
        $msg[0] = "Az adatok sikeresen törölve.";

        return redirect($redirect_url)->withMessage($msg);
    }


    public function displayProductStatuses(Request $request)
    {
        $model = new ProductStatus();

        $product_statuses = $model::with('vatType')->orderBy('product_status_id', 'DESC')->get();

        return view('master.product_statuses.index', ['product_statuses' => $product_statuses]);
    }

    public function displayProductStatus(Request $request)
    {
        if($request->product_status_id){
            $product_status = ProductStatus::find($request->product_status_id);
        } else {
            $product_status = new ProductStatus();
        }

        $vat_types = VatType::orderBy('name', 'ASC')->get();

        return view('master.product_statuses.edit', ['product_status' => $product_status, 'vat_types' => $vat_types]);
    }

    public function saveProductStatus(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['nullable', 'string', 'max:255'],
            'vat_type_id' => ['nullable', 'integer', 'exists:master_vat_types,vat_type_id'],
            'from' => ['nullable', 'date'],
        ]);

        if($request->product_status_id){
            $product_status = ProductStatus::find($request->product_status_id);
        } else {
            $product_status = new ProductStatus();
            $product_status->created_at = date('Y-m-d H:i:s');
        }

        $product_status->name = $request->name;
        $product_status->status = $request->has('status') ? 1 : 0;
        $product_status->price = $request->price;
        $product_status->vat_type_id = $request->vat_type_id;
        $product_status->from = $request->from;
        $product_status->label = $request->has('label') ? 1 : 0;
        $product_status->updated_at = date('Y-m-d H:i:s');

        $product_status->save();

        $redirect_url = '/master/product_statuses';
        $msg[0] = "A termék állapot adatai sikeresen mentve.";

        return redirect($redirect_url)->withMessage($msg);
    }

    public function deleteProductStatus(Request $request)
    {
        try {
            $product_status = ProductStatus::find($request->product_status_id);

            if (!$product_status) {
                throw new \Exception('A termék állapot nem található.');
            }

            $product_status->delete();
            $msg[0] = "A termék állapot sikeresen törölve.";
            return redirect('/master/product_statuses')->withMessage($msg);

        } catch (\Exception $e) {
            return redirect('/master/product_statuses')->withErrors(["A termék állapot nem törölhető, mert kapcsolódó termékek vannak hozzá."]);
        }
    }

    public function displayProductTypes(Request $request)
    {
        $model = new ProductType();

        $product_types = $model::orderBy('product_type_id', 'DESC')->get();

        return view('master.product_types.index', ['product_types' => $product_types]);
    }

    public function displayProductType(Request $request)
    {
        if($request->product_type_id){
            $product_type = ProductType::find($request->product_type_id);
        } else {
            $product_type = new ProductType();
        }

        return view('master.product_types.edit', ['product_type' => $product_type]);
    }

    public function saveProductType(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        if($request->product_type_id){
            $product_type = ProductType::find($request->product_type_id);
        } else {
            $product_type = new ProductType();
            $product_type->created_at = date('Y-m-d H:i:s');
        }

        $product_type->name = $request->name;
        $product_type->status = $request->has('status') ? 1 : 0;
        $product_type->updated_at = date('Y-m-d H:i:s');

        $product_type->save();

        $redirect_url = '/master/product_types';
        $msg[0] = "A termék típus adatai sikeresen mentve.";

        return redirect($redirect_url)->withMessage($msg);
    }

    public function deleteProductType(Request $request)
    {
        try {
            $product_type = ProductType::find($request->product_type_id);

            if (!$product_type) {
                throw new \Exception('A termék típus nem található.');
            }

            $product_type->delete();
            $msg[0] = "A termék típus sikeresen törölve.";
            return redirect('/master/product_types')->withMessage($msg);

        } catch (\Exception $e) {
            return redirect('/master/product_types')->withErrors(["A termék típus nem törölhető, mert kapcsolódó adatok vannak hozzá."]);
        }
    }



    public function displayProductSigns(Request $request)
    {
        $model = new ProductSign();

        $product_signs = $model::orderBy('product_sign_id', 'DESC')->get();

        return view('master.product_signs.index', ['product_signs' => $product_signs]);
    }

    public function displayProductSign(Request $request)
    {
        if($request->product_sign_id){
            $product_sign = ProductSign::find($request->product_sign_id);
        } else {
            $product_sign = new ProductSign();
        }

        $banks = Bank::where('status', 1)->orderBy('name', 'ASC')->get();

        return view('master.product_signs.edit', ['product_sign' => $product_sign, 'banks' => $banks]);
    }

    public function saveProductSign(Request $request)
    {
        if($request->product_sign_id){
            $product_sign = ProductSign::find($request->product_sign_id);
        } else {
            $product_sign = new ProductSign();
            $product_sign->created_at = date('Y-m-d H:i:s');
        }

        $product_sign->name = $request->name;
        $product_sign->status = $request->status;
        $product_sign->updated_at = date('Y-m-d H:i:s');

        $product_sign->save();

        $redirect_url = '/master/product_signs';
        $msg[0] = "A jelzés adatai sikeresen mentve.";

        return redirect($redirect_url)->withMessage($msg);
    }

    public function deleteProductSign(Request $request)
    {
        try {
            $product_sign = ProductSign::findOrFail($request->product_sign_id);
            $product_sign->delete();

            $msg[0] = "A termék-jelzés sikeresen törölve.";
            return redirect('/master/product_signs')->withMessage($msg);
        } catch (ModelNotFoundException $e) {
            return redirect('/master/product_signs')->withErrors(['error' => "A termék-jelzés nem található."]);
        } catch (\Exception $e) {
            return redirect('/master/product_signs')->withErrors(['error' => "A termék-jelzés nem törölhető mert kapcsolódó termékek vannak hozzá."]);
        }
    }



    public function displayProductColors(Request $request)
    {
        $model = new ProductColor();

        $product_colors = $model::orderBy('product_color_id', 'DESC')->get();

        return view('master.product_colors.index', ['product_colors' => $product_colors]);
    }

    public function displayProductColor(Request $request)
    {
        if($request->product_color_id){
            $product_color = ProductColor::find($request->product_color_id);
        } else {
            $product_color = new ProductColor();
        }

        $banks = Bank::where('status', 1)->orderBy('name', 'ASC')->get();

        return view('master.product_colors.edit', ['product_color' => $product_color, 'banks' => $banks]);
    }

    public function saveProductColor(Request $request)
    {
        if($request->product_color_id){
            $product_color = ProductColor::find($request->product_color_id);
        } else {
            $product_color = new ProductColor();
            $product_color->created_at = date('Y-m-d H:i:s');
        }

        $product_color->name = $request->name;
        $product_color->status = $request->status;
        $product_color->updated_at = date('Y-m-d H:i:s');

        $product_color->save();

        $redirect_url = '/master/product_colors';
        $msg[0] = "A szín adatai sikeresen mentve.";

        return redirect($redirect_url)->withMessage($msg);
    }

    public function deleteProductColor(Request $request)
    {
        try {
            $product_color = ProductColor::findOrFail($request->product_color_id);
            $product_color->delete();

            $msg[0] = "A szín típus sikeresen törölve.";
            return redirect('/master/product_colors')->withMessage($msg);
        } catch (ModelNotFoundException $e) {
            return redirect('/master/product_colors')->withErrors(['error' => "A szín típus nem található."]);
        } catch (\Exception $e) {
            return redirect('/master/product_colors')->withErrors(['error' => "A szín típus nem törölhető mert kapcsolódó termékek vannak hozzá."]);
        }
    }


    public function displayActivities(Request $request)
    {
        $model = new Activity();

        $activities = $model::orderBy('activity_id', 'DESC')->get();

        return view('master.activities.index', ['activities' => $activities]);
    }

    public function displayActivity(Request $request)
    {
        if($request->activity_id){
            $activity = Activity::find($request->activity_id);
        } else {
            $activity = new Activity();
        }

        $banks = Bank::where('status', 1)->orderBy('name', 'ASC')->get();

        return view('master.activities.edit', ['activity' => $activity, 'banks' => $banks]);
    }

    public function saveActivity(Request $request)
    {
        if($request->activity_id){
            $activity = Activity::find($request->activity_id);
        } else {
            $activity = new Activity();
            $activity->created_at = date('Y-m-d H:i:s');
        }

        $activity->name = $request->name;
        $activity->status = $request->status;
        $activity->updated_at = date('Y-m-d H:i:s');

        $activity->save();

        $redirect_url = '/master/activities';
        $msg[0] = "A tevékenység sikeresen mentve.";

        return redirect($redirect_url)->withMessage($msg);
    }

    public function deleteActivity(Request $request)
    {
        try {
            $activity = Activity::findOrFail($request->activity_id);
            $activity->delete();

            $msg[0] = "A tevékenység sikeresen törölve.";
            return redirect('/master/activities')->withMessage($msg);
        } catch (ModelNotFoundException $e) {
            $msg[0] = "A tevékenység nem található.";
            return redirect('/master/activities')->withErrors($msg);
        } catch (\Exception $e) {
            $msg[0] = "A tevékenység nem törölhető, mert kapcsolódó adatok tartoznak hozzá.";
            return redirect('/master/activities')->withErrors($msg);
        }
    }

    public function displayBookings(Request $request)
    {
        $bookings = Booking::orderBy('booking_id', 'DESC')->get();

        return view('master.bookings.index', ['bookings' => $bookings]);
    }


    public function displayBooking(Request $request)
    {
        $booking = Booking::find($request->booking_id);

        return view('master.bookings.edit', ['booking' => $booking]);
    }

    public function saveBooking(Request $request)
    {
        $booking = Booking::find($request->booking_id);

        // Foglalási mezők frissítése
        $booking->min_amount = $request->min_amount;
        $booking->max_amount = $request->max_amount;
        $booking->duration_months = $request->duration_months;
        $booking->updated_at = now();

        $booking->save();

        $msg[0] = "A foglalási szabály sikeresen mentve.";
        return redirect('/master/bookings')->withMessage($msg);
    }
}
