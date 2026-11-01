<?php
namespace App\Http\Controllers\Grader\Eportal\masterForm;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;

class EportalCategoryController extends Controller
{
    public function index()
    {
        $categories = DB::table('EPORTAL_CATEGORIES')->get();
        return view('ePortal.masterForm.ep_categories', compact('categories'));
    }

    public function create(Request $request)
    {
        // Fetch schools from the schools table
        $schools = DB::table('schools')
            ->select('school_id as id', 'school_code', 'school_name as name')
            ->get()
            ->toArray();
    
        // Fetch departments from the departments table
        $depts = DB::table('departments')
            ->select('dept_id as id', 'dept_name as name')
            ->get()
            ->toArray();
    
        return view('ePortal.masterForm.ep_categories_create', compact('schools', 'depts'));
    }
    

    public function store(Request $request)
    {

        
        // Validate the incoming request data
        $data1 = $request->validate([
            // 'EC_TITLE' => 'required|unique:EPORTAL_CATEGORIES',
            's_id' => 'required', 
            'd_id' => 'required', 
            
        ]);
    
        // dd(111111111111111111);
        // Prepare the data for insertion
        $data = [
            'EC_TITLE' => $request->input('EC_TITLE'),
            'EC_SCHOOL_ID' => $request->input('s_id'),
            'EC_DEPARTMENT_ID' => $request->input('d_id'),
        ];
    
        try {
            // Attempt to insert the data into the database
            $inserted=DB::table('EPORTAL_CATEGORIES')->insert($data);
            if (! $inserted) {
                return response()->json([
                    'data' =>  'Failed',
                    
                ]);
            }
    
            // Redirect on success
            return redirect()->route('ep_permissions')
                         ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
         
           return redirect()->route('ep_categories_create')
            ->with('error', 'Failed to create category. Please try again.');
        }
    }



    public function show($id)
    {
        $category = DB::table('EPORTAL_CATEGORIES')->find($id);
        return view('ePortal.masterForm.ep_categories_show', compact('category'));
    }

    public function edit()
    {
        // $category = DB::table('EPORTAL_CATEGORIES')->find($id);
        // return view('ePortal.masterForm.ep_categories_edit', compact('category'));
        return view ('ePortal.masterForm.ep_categories_edit');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'EC_TITLE' => 'required|unique:EPORTAL_CATEGORIES,EC_TITLE,' . $id,
            // Add other validation rules for your fields
        ]);

        DB::table('EPORTAL_CATEGORIES')->where('id', $id)->update($data);

        return redirect()->route('ePortal.masterForm.ep_categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        DB::table('EPORTAL_CATEGORIES')->delete($id);
        return redirect()->route('ePortal.masterForm.ep_categories.index')->with('success', 'Category deleted successfully.');
    }
}
