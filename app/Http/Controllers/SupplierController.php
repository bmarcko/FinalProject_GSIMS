<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use Exception;
use Session;
use Log;
use Auth;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function viewSuppliers()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            Log::info('listing the suppliers');
            // SELECT * FROM organizations
            $suppliers = Supplier::all();
            return view('suppliers.supplier', compact('suppliers'));
        } else {
            Session::flash('error', 'Only admins are allowed to open this page');
        }

        return redirect('/dashboard');

    }

    public function viewEditSupplierForm($id)
    {
        // SELECT * FROM suppliers WHERE id=$id
        $supplier = Supplier::find($id);
        if (!is_null($supplier)) {
            return view('suppliers.edit-supplier', compact('supplier'));
        }
        Session::flash('error', 'We cannot find the record you are looking for.');
        return redirect()->back();
    }

    public function viewAddSupplierForm()
    {
        return view('suppliers.add-supplier');
    }

    public function saveSupplierChanges(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|max:300',
            'company_province' => 'required|max:300',
            'company_city' => 'required|max:300',
            'company_email' => 'required|max:300',
            'company_phone_number' => 'required|max:300'
        ]);

        try {
            $id = $request->id;
            $supplier = Supplier::find($id);
            // UPDATE organizations SET
            // name=$request->name,
            // type=$request->type,
            // contact_number=$request->contact_number,
            // address=$request->address,
            // website_url=$request->website_url
            // WHERE id=$request->id
            $supplier->update([
                'company_name' => $request->company_name,
                'company_province' => $request->company_province,
                'company_city' => $request->company_city,
                'company_email' => $request->company_email,
                'company_phone_number' => $request->company_phone_number
            ]);
            // $organization->setName($request->name);
            // $organization->setAddress($request->address);
            // $organization->setContactNumber($request->contact_number);
            // $organization->setType($request->type);
            // $organization->setWebsiteURL($request->website_url);

            Session::flash('message', 'Successfully updated Supplier Information');
        } catch (Exception $e) {
            Session::flash('error', 'Something went wrong, please try again later');
        }
        
        return redirect('supplier');
    }

    public function saveNewSupplier(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|max:300',
            'company_province' => 'required|max:300',
            'company_city' => 'required|max:300',
            'company_email' => 'required|max:300',
            'company_phone_number' => 'required|max:300'
        ]);

        try {
            $sup = Supplier::create([
                'company_name' => $request->company_name,
                'company_province' => $request->company_province,
                'company_city' => $request->company_city,
                'company_email' => $request->company_email,
                'company_phone_number' => $request->company_phone_number
            ]);
            if (!is_null($sup)) {
                Session::flash('message', 'Successfully added a new supplier');
            } else {
                throw new Exception('Unable to create a new supplier');
            }
            
        } catch (Exception $e) {
            Session::flash('error', 'Something went wrong, please try again later');
        }

        return redirect('supplier');
    }

    public function deleteSupplier($id)
    {
        $supplier = Supplier::find($id);
        $supplier->delete();
        // DELETE FROM organizations WHERE id=$id

        Session::flash('message', 'Successfully removed a record');
        return redirect('supplier');
    }
}
