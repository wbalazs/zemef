<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Models\Tool;
use App\Models\ToolType;
use App\Models\DocType;
use App\Models\Business;
use App\Models\Employee;
use App\Models\Customer;
use App\Category;
use Carbon\Carbon;
use App\Http\Requests\ToolRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;



class ToolController extends Controller
{
    public function __construct()
    {
        ////$this->authorizeResource(Tool::class);
    }

    /**
     * Display a listing of the tools
     *
     * @param \App\Item  $model
     * @return \Illuminate\View\View
     */
    public function index(Tool $model)
    {
        Tool::where('name', '--empty_row--')->delete();
        return view('tools.index', ['tools' => $model->get()]);
    }

    /**
     * Show the form for creating a new item
     *
     * @param  \App\Tag $tagModel
     * @param  \App\Category $categoryModel
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $doc_types = DocType::orderBy('name', 'ASC')->get();
        $tool_types = ToolType::where('status', 1)->orderBy('name', 'ASC')->get();
        $maintainers = Customer::where('status', 1)->where('is_company', 1)->orderBy('name', 'ASC')->get();
        $businesses = Business::where('status', 1)->orderBy('name', 'ASC')->get();
        $employees = Employee::where('status', 1)->orderBy('lastname', 'ASC')->orderBy('firstname', 'ASC')->get();

        $tool = Tool::create(['name' => '--empty_row--']);
        $tool->name = '';

        return view('tools.edit', [
            'tool' => $tool,
            'doc_types' => $doc_types,
            'tool_types' => $tool_types,
            'maintainers' => $maintainers,
            'businesses' => $businesses,
            'employees' => $employees
        ]);
    }

    /**
     * Store a newly created item in storage
     *
     * @param  \App\Http\Requests\ToolRequest  $request
     * @param  \App\Item  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ToolRequest $request, Tool $model)
    {
        $tool = $model->create($request->merge([
        ])->all());

        return redirect()->route('tool.index')->withStatus('Az eszköz sikeresen létrehozva.');
    }

    /**
     * Show the form for editing the specified item
     *
     * @param  \App\Item  $tool
     * @param  \App\Tag   $tagModel
     * @param  \App\Category $categoryModel
     * @return \Illuminate\View\View
     */
    public function edit(Tool $tool)
    {
        $doc_types = DocType::orderBy('name', 'ASC')->get();
        $tool_types = ToolType::where('status', 1)->orderBy('name', 'ASC')->get();
        $maintainers = Customer::where('status', 1)->where('is_company', 1)->orderBy('name', 'ASC')->get();
        $businesses = Business::where('status', 1)->orderBy('name', 'ASC')->get();
        $employees = Employee::where('status', 1)->orderBy('lastname', 'ASC')->orderBy('firstname', 'ASC')->get();

        return view('tools.edit', [
            'tool' => $tool,
            'doc_types' => $doc_types,
            'tool_types' => $tool_types,
            'businesses' => $businesses,
            'employees' => $employees,
            'maintainers' => $maintainers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Itemuest  $request
     * @param  \App\Item  $tool
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ToolRequest $request, Tool $tool)
    {
        $tool->update(
            array_merge($request->all(),
                ['status' => $request->status, 'review_needed' => $request->review_needed]
            )
        );

        return redirect()->route('tool.index')->withStatus('Az eszköz adatai sikeresen mentve.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $tool
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Tool $tool)
    {
        $tool->delete();

        return redirect()->route('tool.index')->withStatus('Az eszköz sikeresen törölve.');
    }


    public function uploadToolDoc(Request $request)
    {
        $file = $request->file('tool_doc');
        return $file->storeAs('/tool_docs/' . $request->tool_id, $file->getClientOriginalName(), 'document_storage');
    }


    public function saveToolDocData(Request $request)
    {
        DB::table('documents')
            ->insert([
                'resource_id' => $request->tool_id,
                'document_path' => $request->doc_file,
                'document_comment' => $request->tool_doc_comment,
                'document_type_id' => $request->doc_type_id,
                'doc_issue_date' => $request->tool_doc_issue_date,
                'doc_expire_date' => $request->tool_doc_expire_date,
                'doc_reminder_days' => $request->tool_doc_reminder_days,
                'created_at' => date('Y-m-d H:i:s'),
                'document_module' => 'tool_doc'
            ]);
    }


    public function loadToolDoc(Request $request)
    {
        $html = "";

        $tool = Tool::find($request->tool_id);

        foreach($tool->documents as $doc)
        {
            $html .= "<tr class='grey_background'>
                        <td><a href='" . getenv('APP_URL') . '/document_storage/' . $doc->document_path . "' target='_blank'>" . $doc->document_path . "</a></td>
                        <td>" . $doc->document_comment . "</td>
                        ";
                        

            if($doc->doc_type){
                $html .= "<td>" . $doc->doc_type->name . "</td>";
            } else {
                $html .= "<td></td>";
            }

            $html .= "
                        <td>" . $doc->doc_issue_date . "</td>
                        <td>" . $doc->doc_expire_date . "</td>
                        <td>" . $doc->doc_reminder_days . "</td>
                        <td>" . date('Y. m. d. H:i', strtotime($doc->created_at)) . "</td>
                        <td>
                            <div data-doc_id='" . $doc->document_id . "' class='doc_wrapper' >
                                <button class='btn btn-danger delete_doc' data-document_id='" . $doc->document_id . "'>
                                    <i class='nc-icon nc-simple-remove'></i> Törlés
                                </button>
                            </div>
                        </td>
                    </tr>";
        }
        
        return $html;
    }

    //saveInvestigation
    public function saveInvestigation(Request $request)
    {
        $act_user_sign_code = Auth::user()->sign_code;
        $sign_code = $request->sign_code;
        if($sign_code != $act_user_sign_code)
        {
            return response()->json(['success' => false,'message' => 'Hibás aláírási kód!'], 200);
        }else{
            $employee = Auth::user()->employee;
            DB::table('tool_investigations')
            ->insert([
                'tool_id' => $request->tool_id,
                'date' => $request->date,
                'value' => $request->value,
                'employee_id' => $employee->employee_id,
                'employee_name' => $employee->lastname." ".$employee->firstname,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return response()->json(['success' => true,'message' => 'Sikeres felülvizsgálat rögzítés!'], 200);
        }
    }

    //load tool investigations
    public function loadToolInvestigations(Request $request)
    {
        $html = "";

        $tool = Tool::find($request->tool_id);

        foreach($tool->investigations as $investigation)
        {
            $html .= "<tr class='grey_background'>
                        <td>" . $investigation->date . "</td>
                        <td>" . $investigation->value . "</td>
                        <td>" . $investigation->employee_name . "</td>
                        <td>
                            <div data-investigation_id='" . $investigation->investigation_id . "' class='investigation_wrapper' >
                                <button class='btn btn-danger delete_investigation' data-investigation_id='" . $investigation->investigation_id . "'>
                                    <i class='nc-icon nc-simple-remove'></i> Törlés
                                </button>
                            </div>
                        </td>
                    </tr>";
        }
        return $html;
    }

    //delete_investigation
    public function deleteInvestigation(Request $request)
    {
        DB::table('tool_investigations')
            ->where('investigation_id', $request->investigation_id)
            ->delete();
    }
}
