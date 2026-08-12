<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use App\Models\Employee;
use App\Models\BankAccount;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        //////$this->authorizeResource(User::class);
    }

    public function kenyszer_teszt()
    {
        $path = base_path().'/public';
        $content = file_get_contents($path.'/kenyszer.txt');
        $lines = explode("\n", $content);

        $zalogjegyek = [];
        $current_zalogjegy = null;
        $is_targyleiras = false;
        $is_ugyfel = false;

        foreach ($lines as $line) {
            // Kihagyjuk az üres sorokat és a fejléc sorokat
            if (trim($line) === '' || strpos($line, 'Zálogjegy') !== false || strpos($line, 'sor') !== false) {
                continue;
            }

            // Ha a sor tartalmazza a zálogjegy adatokat
            if (preg_match('/^\s*(\d+)\.\s+(\d+\/\d+)\s+([\d,\.]+)\s+([\d,\.]+)\s+([\d,\.]+)\s+(\d+)\s+([\d,\.]+)\s+([\d,\.]+)\s+([\d,\.]+)\s+([\d,\.]+)\s+([\d,\.]+)/', $line, $matches)) {
                if ($current_zalogjegy !== null) {
                    // Az előző zálogjegy adatait hozzáadjuk a listához
                    $zalogjegyek[] = $current_zalogjegy;
                }
                // Új zálogjegy kezdődik
                $current_zalogjegy = [
                    'zalogjegy_szam' => $matches[2],
                    'fenntarto_hitel' => str_replace(',', '', $matches[3]),
                    'kamat' => str_replace(',', '', $matches[4]),
                    'kesedelmi_kamat' => str_replace(',', '', $matches[5]),
                    'egyeb_koltsegek' => $matches[6],
                    'kozv_dij' => str_replace(',', '', $matches[7]),
                    'atveteli_ar' => str_replace(',', '', $matches[8]),
                    'becsertek' => str_replace(',', '', $matches[9]),
                    'netto_suly' => str_replace(',', '.', $matches[10]),
                    'brutto_suly' => str_replace(',', '.', $matches[11]),
                    'targyleiras' => '',
                    'allapot' => '',
                    'karat_finomsag' => '',
                    'netto_suly_targy' => '',
                    'brutto_suly_targy' => '',
                    'ugyfel' => '',
                ];
                $is_targyleiras = true;
            } elseif ($is_targyleiras && $current_zalogjegy !== null) {
                // A tárgyleírás feldolgozása
                if (preg_match('/^\s*(.*)\s+Magyar és egyenértékes\s+([\d,\.]+)\s+([\d,\.]+)\s+([\d,\.]+)/', $line, $matches)) {
                    $current_zalogjegy['targyleiras'] = trim($matches[1]);
                    $current_zalogjegy['karat_finomsag'] = str_replace(',', '.', $matches[2]);
                    $current_zalogjegy['netto_suly_targy'] = str_replace(',', '.', $matches[3]);
                    $current_zalogjegy['brutto_suly_targy'] = str_replace(',', '.', $matches[4]);
                    $is_targyleiras = false;
                    $is_ugyfel = true;
                } else {
                    $current_zalogjegy['targyleiras'] .= ' ' . trim($line);
                }
            } elseif ($is_ugyfel && strpos($line, 'Ügyfél:') !== false && $current_zalogjegy !== null) {
                // Az ügyfél adatait dolgozzuk fel
                $current_zalogjegy['ugyfel'] = trim(substr($line, strpos($line, 'Ügyfél:') + 7));
                $is_ugyfel = false;
            }
        }

        // Az utolsó zálogjegy hozzáadása a listához
        if ($current_zalogjegy !== null) {
            $zalogjegyek[] = $current_zalogjegy;
        }

        // Adatok megjelenítése ellenőrzés céljából
        foreach ($zalogjegyek as $zalogjegy) {
            echo "Zálogjegy száma: {$zalogjegy['zalogjegy_szam']}<br>";
            echo "Fennálló hitel: {$zalogjegy['fenntarto_hitel']}<br>";
            echo "Kamat: {$zalogjegy['kamat']}<br>";
            echo "Késedelmi kamat: {$zalogjegy['kesedelmi_kamat']}<br>";
            echo "Egyéb költségek: {$zalogjegy['egyeb_koltsegek']}<br>";
            echo "Közvetítői díj: {$zalogjegy['kozv_dij']}<br>";
            echo "Átvételi ár: {$zalogjegy['atveteli_ar']}<br>";
            echo "Becsérték: {$zalogjegy['becsertek']}<br>";
            echo "Nettó összsúly: {$zalogjegy['netto_suly']}<br>";
            echo "Bruttó összsúly: {$zalogjegy['brutto_suly']}<br>";
            echo "Tárgyleírás: {$zalogjegy['targyleiras']}<br>";
            echo "Állapot: {$zalogjegy['allapot']}<br>";
            echo "Karát/Finomság: {$zalogjegy['karat_finomsag']}<br>";
            echo "Nettó súly (tárgy): {$zalogjegy['netto_suly_targy']}<br>";
            echo "Bruttó súly (tárgy): {$zalogjegy['brutto_suly_targy']}<br>";
            echo "Ügyfél: {$zalogjegy['ugyfel']}<br><br>";
        }
    }

    public function kenyszer_teszt1()
    {
        $path = base_path().'/public';
        $content = file_get_contents($path.'/kenyszer.txt');
        // Tördeljük sorokra
        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            // Kihagyjuk az üres sorokat és a fejléc sorokat
            if (trim($line) === '' || strpos($line, 'Zálogjegy') !== false || strpos($line, 'sor') !== false) {
                continue;
            }

            // Ellenőrizzük, hogy a sor tartalmazza-e a szükséges adatokat
            if (preg_match('/^\s*\d+\.\s+(\d+\/\d+)\s+([\d\.]+)\s+([\d\.]+)\s+([\d\.]+)\s+(\d+)\s+([\d\.]+)\s+([\d\.]+)\s+([\d\.]+)\s+(.*)/', $line, $matches)) {
                $zalogjegy_szam = $matches[1];
                $fenntarto_hitel = $matches[2];
                $kamat = $matches[3];
                $kesedelmi_kamat = $matches[4];
                $egyeb_koltsegek = $matches[5];
                $becserték = $matches[6];
                $netto_suly = $matches[7];
                $brutto_suly = $matches[8];
                $ugyfel = $matches[9];

                echo "Zálogjegy száma: $zalogjegy_szam<br>";
                echo "Fennálló hitel: $fenntarto_hitel<br>";
                echo "Kamat: $kamat<br>";
                echo "Késedelmi kamat: $kesedelmi_kamat<br>";
                echo "Egyéb költségek: $egyeb_koltsegek<br>";
                echo "Becsérték: $becserték<br>";
                echo "Nettó súly: $netto_suly<br>";
                echo "Bruttó súly: $brutto_suly<br>";
                echo "Ügyfél: $ugyfel<br><br>";
            }
        }
    }


    /**
     * Display a listing of the users
     *
     * @param  \App\User  $model
     * @return \Illuminate\View\View
     */
    public function index(User $model)
    {
        //$this->authorize('index', User::class);

        return view('users.index', ['users' => $model->with('role')->get()]);
    }

    /**
     * Show the form for creating a new user
     *
     * @param  \App\Role  $model
     * @return \Illuminate\View\View
     */
    public function create(Role $model)
    {
        $employees = Employee::where('status', 1)->orderBy('lastname', 'ASC')->orderBy('firstname', 'ASC')->get();
        $bank_accounts = BankAccount::where('status', 1)->orderBy('bank_account_number', 'ASC')->get();

        return view('users.create', ['roles' => $model->get(['id', 'name']), 'employees' => $employees, 'bank_accounts' => $bank_accounts]);
    }

    /**
     * Store a newly created user in storage
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\User  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request, User $model)
    {
        $model->create($request->merge([
            'picture' => $request->photo ? $request->photo->store('profile', 'public') : null,
            'bank_contact' => $request->bank_contact ? 1 : null,
            'is_goldsmith' => $request->is_goldsmith ? 1 : null,
            'password' => Hash::make($request->get('password')),
            'sign_code' => $request->get('sign_code')
        ])->all());

        return redirect()->route('user.index')->withStatus('A felhasználó sikeresen létrehozva.');
    }

    /**
     * Show the form for editing the specified user
     *
     * @param  \App\User  $user
     * @param  \App\Role  $model
     * @return \Illuminate\View\View
     */
    public function edit(User $user, Role $model)
    {
        $employees = Employee::where('status', 1)->orderBy('lastname', 'ASC')->orderBy('firstname', 'ASC')->get();
        $bank_accounts = BankAccount::where('status', 1)->orderBy('bank_account_number', 'ASC')->get();

        return view('users.edit', ['user' => $user->load('role'), 'roles' => $model->get(['id', 'name']), 'employees' => $employees, 'bank_accounts' => $bank_accounts]);
    }

    /**
     * Update the specified user in storage
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request, User $user)
    {
        $hasPassword=$request->get('password');
        $hasSignCode=$request->get('sign_code');
        $user->update(
            $request->merge([
                'picture' => $request->photo ? $request->photo->store('profile', 'public') : $user->picture,
                'bank_contact' => $request->bank_contact ? 1 : null,
                'is_goldsmith' => $request->is_goldsmith ? 1 : null,
                'password' => Hash::make($request->get('password')),
                'sign_code' => $request->get('sign_code')
            ])->except([$hasPassword ? '' : 'password', $hasSignCode ? '' : 'sign_code'])
        );

        $user->bank_accounts()->sync($request->bank_accounts);

        return redirect()->route('user.index')->withStatus('A felhasználó sikeresen módosítva.');
    }

    /**
     * Remove the specified user from storage
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Ellenőrizze, hogy a felhasználóhoz kapcsolódik-e alkalmazott
        if ($user->employee_id) {
            $employeeExists = DB::table('employees')->where('employee_id', $user->employee_id)->exists();
            if ($employeeExists) {
                return redirect()->route('user.index')->withErrors('A felhasználó nem törölhető, mert kapcsolódik egy alkalmazotthoz.');
            }
        }

        $user->delete();

        return redirect()->route('user.index')->withStatus('A felhasználó sikeresen törölve.');
    }
}
