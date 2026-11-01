<?php

namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use App\Services\StoreProcurement\BudgetAnnexureSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DB;


class BudgetImportController extends Controller
{
       public function index()
    {
        $items = DB::table('SP_BUDGET_ITEMS')
            ->orderBy('CREATED_AT', 'desc')
            ->limit(100)
            ->get();

        return view('store_procurement.budget-import.index', compact('items'));
    }


        //////////////////////////
    private $apiUrl = 'http://172.20.250.66:1086/api/x/a1f0';
    // private $apiUrl = 'http://localhost:1086/api/x/b2a0';
    private $token = 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJhLmd1bHphciIsImV4cCI6NDkyOTg0ODI5MCwiaWF0IjoxNzc2MjQ4MjkwLCJzY29wZSI6IkVYVEVSTkFMX0FQSSJ9.wvxlmLzUmU5ZB0dLzND-hkx6BcI7JaLs7zl3bwuuT1U';

 

    public function import2()
    {

//  $ip = "172.20.250.66";

//     $output = null;
//     $status = null;

//     exec("ping -c 4 $ip", $output, $status);

//     return response()->json([
//         'status' => $status === 0 ? 'reachable' : 'unreachable',
//         'output' => $output
//     ]);















           $response = Http::timeout(15)
                ->retry(3, 2000) // 3 retries, 2 sec gap
                ->withToken($this->token)
                ->get($this->apiUrl);

                dd($response);

        if (!$response->successful()) {
            return redirect()->back()->with('error', 'API Failed');
        }

        $data = $response->json();
        $inserted = 0;

        DB::beginTransaction();

        try {
            foreach ($data as $row) {

                $exists = DB::table('SP_BUDGET_ITEMS')
                    ->where('BH_ID', $row['EntryNo'])
                    ->where('ITEM_TITLE', trim($row['Item_Title']))
                    ->exists();

                if (!$exists) {
                    DB::table('SP_BUDGET_ITEMS')->insert([
                        'BH_ID'        => $row['EntryNo'],
                        'ITEM_TITLE'   => trim($row['Item_Title']),
                        'QUANTITY'     => $row['BudgetedQty'],
                        'RATE'         => $row['BudgetedRate'],
                        'REMARKS'      => $row['Remarks'],
                        'CREATED_AT'   => now(),
                        'UPDATED_AT'   => now(),
                        'BUDGET_TYPE'  => 'BUDGETED',
                        'STATUS'       => 'ACTIVE',
                        'ENTRY_USERNAME' => 'system'
                    ]);
                    $inserted++;
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('budget.index')
            ->with('success', "$inserted records imported successfully");
    }










    /**
     * Import data from API
     */
    public function import(BudgetAnnexureSyncService $syncService)
    {
        try {
            $deptId = (int) auth()->user()->dept_id;
            $summary = $syncService->sync($deptId, null, auth()->user()->username ?? 'system');

            $headsTotal = ($summary['heads_created'] ?? 0) + ($summary['heads_updated'] ?? 0);
            $itemsTotal = ($summary['items_created'] ?? 0) + ($summary['items_updated'] ?? 0);

            if ($headsTotal === 0 && $itemsTotal === 0) {
                return back()->with('error', $summary['message'] ?? 'No budget records were synced.');
            }

            return back()->with(
                'success',
                "Synced {$headsTotal} budget head(s) and {$itemsTotal} item(s) from Pinnacle."
            );
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Connection error', ['message' => $e->getMessage()]);

            return back()->with('error', 'Cannot connect to API (Network issue)');
        } catch (\Exception $e) {
            Log::error('Import error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

 public function importItems()


    {


    

       try {

            // 🔁 API Call with retry + timeout
            $response = Http::timeout(30)
              ->retry(3, 5000)
            ->withHeaders([
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJhLmd1bHphciIsImV4cCI6NDkyOTg0ODI5MCwiaWF0IjoxNzc2MjQ4MjkwLCJzY29wZSI6IkVYVEVSTkFMX0FQSSJ9.wvxlmLzUmU5ZB0dLzND-hkx6BcI7JaLs7zl3bwuuT1U'
            ])
            ->get('http://172.20.250.66:1086/api/x/b2a2');

                    if ($response->failed()) {
                        return response()->json([
                            'error' => 'API not reachable',
                            'details' => $response->body()
                        ]);
                    }
            //   dd($response);
            // ❌ If API fails
            if (!$response->successful()) {
                Log::error('API failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return back()->with('error', 'API request failed');
            }

            $data = $response->json();
               
            if (!is_array($data)) {
               
                return back()->with('error', 'Invalid API response');
            }
 
            DB::beginTransaction();

            $count = 0;

            foreach ($data as $item) {


           
                // 🛑 Skip invalid rows
                if (!isset($item['Title'])) {
                    continue;
                   
                }
                //  dd($item,$item['ID'],$item['Title'],$item['ItemType'],$item['FinAcc01_ID'] );

                DB::table('SP_ITEMS')->updateOrInsert(
                    [
                        // 🔑 Unique key (prevent duplicates)
                        'PIN_ID' => $item['ID'],
                        'ITEM_TITLE' => trim($item['Title']),
                        'BUDGET_TYPE' => $item['ItemType'] ?? null,
                        'FinAcc01_ID' => $item['FinAcc01_ID'] ?? 0,
                        
                        // 'REMARKS' => $item['Remarks'] ?? null,
                       
                    ]
                );

                $count++;
                // dd($count);
                
            }

            DB::commit();

            return back()->with('success', "$count records imported/updated successfully");

        } 
        catch (\Illuminate\Http\Client\ConnectionException $e) {
 dd(  $e->getMessage());
            Log::error('Connection error', ['message' => $e->getMessage()]);

            return back()->with('error', 'Cannot connect to API (Network issue)');

        } 
        catch (\Exception $e) {
 dd(  $e->getMessage());
            DB::rollBack();

            Log::error('Import error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

 public function importCostCenter()


    {


    

       try {

            // 🔁 API Call with retry + timeout
            $response = Http::timeout(30)
              ->retry(3, 5000)
            ->withHeaders([
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJhLmd1bHphciIsImV4cCI6NDkyOTg0ODI5MCwiaWF0IjoxNzc2MjQ4MjkwLCJzY29wZSI6IkVYVEVSTkFMX0FQSSJ9.wvxlmLzUmU5ZB0dLzND-hkx6BcI7JaLs7zl3bwuuT1U'
            ])
            ->get('http://172.20.250.66:1086/api/x/a1f0');

                    if ($response->failed()) {
                        return response()->json([
                            'error' => 'API not reachable',
                            'details' => $response->body()
                        ]);
                    }
            //   dd($response);
            // ❌ If API fails
            if (!$response->successful()) {
                Log::error('API failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return back()->with('error', 'API request failed');
            }

            $data = $response->json();
               
            if (!is_array($data)) {
               
                return back()->with('error', 'Invalid API response');
            }
 
            $deptId = (int) auth()->user()->dept_id;
            $ayId = (int) (\App\Models\AcademicYear::latest('ay_id')->value('ay_id') ?: 0);

            if ($deptId <= 0) {
                return back()->with('error', 'Your account does not have a valid department assigned.');
            }

            DB::beginTransaction();

            $count = 0;

            foreach ($data as $item) {
                if (!isset($item['Title'])) {
                    continue;
                }

                DB::table('SP_BUDGET_HEADS')->updateOrInsert(
                    [
                        'AY_ID' => $ayId,
                        'DEPT_ID' => $deptId,
                        'PIN_ID' => $item['ID'] ?? null,
                    ],
                    [
                        'HEAD_TITLE' => $item['Title'] ?? null,
                        'School_ID' => $item['School_ID'] ?? null,
                        'School_Title' => $item['School_Title'] ?? null,
                        'REMARKS' => $item['School_Title'] ?? null,
                        'ENTRY_USERNAME' => auth()->user()->username ?? 'system',
                        'UPDATED_AT' => now(),
                       
                        
                        
                        // 'REMARKS' => $item['Remarks'] ?? null,
                       
                    ]
                );
// AY_ID
// BH_ID
// BUDGET_TYPE
// CREATED_AT
// DEPT_ID
// ENTRY_USERNAME
// HEAD_TITLE
// REMARKS
// UPDATED_AT

//   {
//         "ID": "04004",
//         "Title": "Safari Zoo Hostel",
//         "School_ID": "0101044",
//         "School_Title": "Hostels"
//     },



                $count++;
                // dd($count);
                
            }

            DB::commit();

            return back()->with('success', "$count records imported/updated successfully");

        } 
        catch (\Illuminate\Http\Client\ConnectionException $e) {
 dd(  $e->getMessage());
            Log::error('Connection error', ['message' => $e->getMessage()]);

            return back()->with('error', 'Cannot connect to API (Network issue)');

        } 
        catch (\Exception $e) {
 dd(  $e->getMessage());
            DB::rollBack();

            Log::error('Import error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

 public function importVendors()


    {


    

       try {

            // 🔁 API Call with retry + timeout
            $response = Http::timeout(30)
              ->retry(3, 5000)
            ->withHeaders([
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJhLmd1bHphciIsImV4cCI6NDkyOTg0ODI5MCwiaWF0IjoxNzc2MjQ4MjkwLCJzY29wZSI6IkVYVEVSTkFMX0FQSSJ9.wvxlmLzUmU5ZB0dLzND-hkx6BcI7JaLs7zl3bwuuT1U'
            ])
            ->get('http://172.20.250.66:1086/api/x/b2a3');

                    if ($response->failed()) {
                        return response()->json([
                            'error' => 'API not reachable',
                            'details' => $response->body()
                        ]);
                    }
            //   dd($response);
            // ❌ If API fails
            if (!$response->successful()) {
                Log::error('API failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return back()->with('error', 'API request failed');
            }

            $data = $response->json();
               
            if (!is_array($data)) {
               
                return back()->with('error', 'Invalid API response');
            }
 
            DB::beginTransaction();

            $count = 0;

            foreach ($data as $item) {


           
                // 🛑 Skip invalid rows
                if (!isset($item['Title'])) {
                    continue;
                   
                }
               //  dd($item );

                DB::table('sp_vendors')->updateOrInsert(
                    [
                        // 🔑 Unique key (prevent duplicates)
                        'VENDOR_NAME' => $item['Title'],
                        'VENDOR_MOBILE' => $item['MobileNo']?? 0,
                        'VENDOR_ADDRESS' => $item['SAddress'] ?? null,
                        'VENDOR_CONTACT_PERSON' => $item['ContactPerson'] ?? null,
                        'VENDOR_EMAIL' => $item['Email'] ?? null,
                        'NTN_CNIC' => $item['NTN'] ?? 0,
                        'BANK_IBAN' => $item['Bank_ID'] ?? null,
                        'BANK_NAME' => $item['Bank_Title'] ?? null,
                        'BENEFICIARY_NAME' => $item['BeneficiaryName'] ?? null,
                       // 'VENDOR_ID' => $item['ID'] ?? null,
                        // '' => $item[''] ?? null,
                        // '' => $item[''] ?? null,
                        // '' => $item[''] ?? null,
                        // '' => $item[''] ?? null,
                        
                        // 'REMARKS' => $item['Remarks'] ?? null,
                       
                    ]
                );
//                 ATTN
// BANK_IBAN
// BANK_NAME
// BENEFICIARY_NAME
// CREATED_AT
// NTN_CNIC
// SUPPLIER
// UPDATED_AT
// USER_ENTRY
// VENDOR_ADDRESS
// VENDOR_CATEGORY_ID
// VENDOR_CONTACT_PERSON
// VENDOR_EMAIL
// VENDOR_ID
// VENDOR_MOBILE
// VENDOR_NAME
// VENDOR_PHONE
// VENDOR_REGISTRATION_DATE

//      "ID": "020306010005",
//         "Title": "Interflow Communication (Pvt.) Ltd",
//         "ContactPerson": "",
//         "MobileNo": "",
//         "Email": "",
//         "SAddress": "12-TIPU SULTAN ROAD MCHS BLOCK-7 & 8,KARACHI  12-TIPU SULTAN ROAD MCHS BLOCK-7 & 8,KARACHI ",
//         "Bank_ID": null,
//         "Bank_Title": null,
//         "IBAN_NO": null,
//         "NTN": "2211230-0",
//         "CNIC": "",
//         "BeneficiaryName": null


                $count++;
             //   dd($count);
                
            }

            DB::commit();

            return back()->with('success', "$count records imported/updated successfully");

        } 
        catch (\Illuminate\Http\Client\ConnectionException $e) {
 dd(  $e->getMessage());
            Log::error('Connection error', ['message' => $e->getMessage()]);

            return back()->with('error', 'Cannot connect to API (Network issue)');

        } 
        catch (\Exception $e) {
 dd(  $e->getMessage());
            DB::rollBack();

            Log::error('Import error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

}