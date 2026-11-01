<?php
namespace App\Http\Controllers\StoreProcurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreProcurement\VendorCategory;

use App\Models\StoreProcurement\Sp_vendor;
use App\Models\StoreProcurement\QuotationRequest;
use App\Models\StoreProcurement\Recommendation;


use App\Models\StoreProcurement\VendorQuotation;
use App\Models\StoreProcurement\PurchaseOrder;
use App\Models\StoreProcurement\SpApprovalMaster;
use App\Models\StoreProcurement\SpPoApproval;
use App\Services\StoreProcurement\StoreItemsSchemaService;
use DB;
class QuotationRequestController extends Controller
{
        // public function create()
        // {
        //     $categories = VendorCategory::all();
        //     $quotations = QuotationRequest::with('vendorCategory')->orderBy('created_at', 'desc')->get();

        //     return view('store_procurement.purchase_order.quotation_request_create', compact('categories','quotations'));
        // }
        public function create()
        {
            // @dd("123");
            $categories = VendorCategory::all();

            $userSchoolId = auth()->user()->school_id;
            $userDeptId = auth()->user()->dept_id;

            $quotations = QuotationRequest::with(['vendorCategory', 'budgetItem', 'budgetHead', 'vendorQuotations'])
                ->when($userSchoolId == 11, function($query) use ($userDeptId) {
                    return $query->where('request_department_id', $userDeptId);
                }, function($query) use ($userSchoolId) {
                    return $query->where('request_school_id', $userSchoolId);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            return view('store_procurement.quotation_request.quotation_request', compact('categories','quotations'));
         }

                 public function purchase()
        {

                    $categories = VendorCategory::all();
                $quotations = QuotationRequest::with(['vendorCategory', 'budgetItem', 'budgetHead', 'vendorQuotations'])
                    ->orderBy('created_at', 'desc')
                    ->get();


            return view('store_procurement.quotation_request.quotation_request_purchase', compact('categories','quotations'));
         }



        public function store(Request $request)
        {
            // dd($request->all());


        // dd(auth()->user()->employee_id);

            $dept_id = auth()->user()->dept_id;
            $employee_id = auth()->user()->employee_id;

            $request->validate([
                'vendor_category_id' => 'required',
                'item_name' => 'required|string|max:255',
                'purchase_mode' => 'required|string',
                'budget_type' => 'required|string',
                'model_no' => 'nullable|string',
                'remarks' => 'nullable|string',
            ]);
                QuotationRequest::create([
                        'vendor_category_id'     => $request->vendor_category_id,
                        'item_name'              => $request->item_name,
                        'purchase_mode'          => $request->purchase_mode,
                        'budget_type'            => $request->budget_type,
                        'model_no'               => $request->model_no,
                        'remarks'                => $request->remarks,
                        'request_department_id'  => $dept_id,
                        'bt_id'                  => $request->budget_id,
                        'from_budget_id'         => $request->from_budget_id,
                        'to_budget_id'           => $request->to_budget_id,


                        // 'parent_department_id'   => $employee_id,
                        'entry_user_id'          => $employee_id,
                        'created_at'             => now(),
                        'updated_at'             => now()
                    ]);
            // QuotationRequest::create($request->all());

            return response()->json(['message' => 'Quotation request sent to vendors.']);
        }

        public function viewVendorQuotations($id)
        {

            // $quotation = QuotationRequest::with(['vendorCategory', 'vendorQuotations.vendor'])->findOrFail($id);
        // dd($quotation );

            //    $quotation = QuotationRequest::with(['vendorCategory',
        //    'budgetItem',
        //     'budgetHead'])->findOrFail($id);




        //    with([
        //         'vendorCategory',
        //         'vendorQuotations.vendor',
        //         'recommendations.department'
        //     ])->findOrFail($id);
            // dd($quotation);

        //     $quotation = QuotationRequest::with([
        // 'vendorCategory',
        // 'vendorQuotations.vendor',
        // 'recommendations.department' // if you also want dept info
        // ])->findOrFail($id);

        //     return view('store_procurement.purchase_order.related_vendor_quotations', compact('quotation'));

        // $quotation = QuotationRequest::with([
        //     'vendorCategory',
        //     'vendorQuotations.vendor',
        //     'recommendations.department',
        //     'budgetItem',
        //     'budgetHead'
        // ])->findOrFail($id);

        //     $quotation = QuotationRequest::with([
        //     'vendorCategory',
        //     'vendorQuotations.vendor',
        //     'vendorQuotations.recommendations.department', // load dept recs per vendor
        //     'budgetItem',
        //     'budgetHead'
        // ])->findOrFail($id);
        // $quotation = QuotationRequest::with([
        //     'vendorCategory',
        //     'vendorQuotations.vendor',
        //     'recommendations.department',
        //     'recommendations.vendorQuotation.vendor', // âœ… include vendorQuotation + vendor
        //     'budgetItem',
        //     'budgetHead'
        // ])->findOrFail($id);

        $quotation = QuotationRequest::with([
            'vendorCategory',
            'vendorQuotations.vendor',
            'vendorQuotations.purchaseOrder',
            'recommendations.department',
            'recommendations.vendorQuotations.vendor',
            'budgetItem',
            'budgetHead',
            'storeBudgetItem',
            'storeBudgetHead',
            'purchaseOrder'
        ])->findOrFail($id);

        // Fall back to store-item relations when budget item is missing
        if (!$quotation->budgetItem && $quotation->storeBudgetItem) {
            $quotation->setRelation('budgetItem', $quotation->storeBudgetItem);
        }
        if (!$quotation->budgetHead && $quotation->storeBudgetHead) {
            $quotation->setRelation('budgetHead', $quotation->storeBudgetHead);
        }

        // dd($quotation );


        return view('store_procurement.purchase_order.related_vendor_quotations', compact('quotation'));



        }


            public function viewQuotationsSelect($id)
        {

               $quotation = QuotationRequest::with([
            'vendorCategory',
            'vendorQuotations.vendor',
            'recommendations.department',
            'recommendations.vendorQuotations.vendor', // ðŸ‘ˆ now we can access vendors inside recs
            'budgetItem',
            'budgetHead'
        ])->findOrFail($id);

        // $quotation = QuotationRequest::with([
        // 'vendorCategory',
        // 'vendorQuotations.vendor',
        // 'recommendations.department' // if you also want dept info
        // ])->findOrFail($id);

            return view('store_procurement.purchase_order.select_vendor_quotations', compact('quotation'));
        }



        //////////////////////////////  Recmondation ////////////////
        public function storeRecommendation(Request $request)
            {
                $request->validate([
                    'qr_id' => 'required|integer',
                    'vq_id' => 'nullable|integer',
                    'recommended_flag' => 'required|string',
                    'remarks' => 'nullable|string',
                ]);

                $rec = Recommendation::create([
                    'qr_id' => $request->qr_id,
                    'vq_id' => $request->vq_id,
                    'department_id' => auth()->user()->dept_id,
                    'recommended_flag' => $request->recommended_flag,
                    'remarks' => $request->remarks,
                    'entry_user_id' => auth()->id(),
                    'entry_username' => auth()->user()->username,
                ]);

                return response()->json(['message' => 'Recommendation submitted successfully', 'data' => $rec]);
            }




            public function requestVcApproval(Request $request)
            {
                $request->validate([
                    'qr_id' => 'required|integer',
                    'vq_id' => 'required|integer',
                    'requested_budget_type' => 'required|string',
                    'total_price' => 'required|numeric',
                    'available_budget' => 'nullable|numeric',
                    'quantity' => 'nullable|numeric',
                    'reason_for_approval' => 'required|string|max:2000',
                ]);

                $quotationRequest = QuotationRequest::with(['budgetItem', 'budgetHead'])->findOrFail($request->qr_id);
                $vendorQuotation = VendorQuotation::with('vendor')->findOrFail($request->vq_id);
                $budgetItem = $quotationRequest->budgetItem;

                $typeMap = [
                    'partial_reappropriate' => 'Partial Re-appropriate',
                    'partial_unbudgeted' => 'Partial un-Budgeted',
                ];

                $requestedTypeKey = trim($request->requested_budget_type);
                $requestedType = $typeMap[strtolower($requestedTypeKey)] ?? $requestedTypeKey;

                if ($budgetItem) {
                    $budgetItem->update([
                        'REQUESTED_BUDGET_TYPE' => $requestedType,
                        'REASON_FOR_APPROVAL' => $request->reason_for_approval,
                        'REAPPROPRIATION_APPROVAL' => 'Pending',
                        'REAPPROPRIATION_APPROVAL_REMARKS' => null,
                        'VC_BUDGET_UPDATE_FLAG' => '0',
                        'SEND_REAPPROPRIATION_APPROVAL_REAPPROP' => 'YES',
                    ]);
                }

                $updatedItem = $quotationRequest->fresh('budgetItem')->budgetItem;

                $payload = [
                    'qr_id' => $quotationRequest->qr_id,
                    'item_title' => optional($updatedItem)->item_title,
                    'budget_head' => optional($quotationRequest->budgetHead)->head_title,
                    'vendor_name' => optional($vendorQuotation->vendor)->vendor_name,
                    'requested_budget_type' => optional($updatedItem)->REQUESTED_BUDGET_TYPE ?? $requestedType,
                    'total_price' => (float) $request->total_price,
                    'available_budget' => $request->available_budget !== null ? (float) $request->available_budget : null,
                    'quantity' => $request->quantity !== null ? (float) $request->quantity : null,
                    'reason_for_approval' => optional($updatedItem)->REASON_FOR_APPROVAL ?? $request->reason_for_approval,
                    'reappropriation_approval' => optional($updatedItem)->REAPPROPRIATION_APPROVAL ?? 'Pending',
                    'reappropriation_approval_remarks' => optional($updatedItem)->REAPPROPRIATION_APPROVAL_REMARKS,
                    'vc_budget_update_flag' => optional($updatedItem)->VC_BUDGET_UPDATE_FLAG ?? '0',
                    'send_reappropriation_approval_reapprop' => optional($updatedItem)->SEND_REAPPROPRIATION_APPROVAL_REAPPROP ?? 'YES',
                ];

                return response()->json([
                    'message' => 'Request sent to VC for approval.',
                    'data' => $payload,
                ]);
            }


            public function recommendedQuotations()
                {
                    // Load only quotations that are recommended
                    $quotations = QuotationRequest::with([
                        'vendorQuotations.vendor',
                        'recommendations.department',
                        'budgetItem',
                        'budgetHead'
                    ])
                    ->whereHas('recommendations', function ($q) {
                        $q->where('recommended_flag', 'recommended');
                    })
                    ->get();

                    return view('store_procurement.purchase_order.recommended_quotations', compact('quotations'));
                }

            // Helper function: Get Approval Slabs (Dynamic Price Ranges with Role-based Ordering)
            private function getApprovalSlabs($requestDeptId, $requestSchoolId, $totalPrice, $budgetType)
            {
                $approvalSlabs = [];
                // All budget types follow the same approval chain; $budgetType kept for future overrides.

                // Define role priority order (lower number = higher priority)
                $rolePriority = [
                    'hod' => 1,
                    'gm' => 2,
                    'purchase_committee_member' => 3,
                    'purchase_committee_line_department' => 4,
                    'purchase_committee_chair_person' => 5,
                    'vc' => 6,
                ];

                // Get all active approval records that apply to this totalPrice
                $allApprovals = SpApprovalMaster::where('active_status', 'active')
                    ->where(function($query) use ($totalPrice) {
                        $query->where(function($q) use ($totalPrice) {
                            // Price falls within range: min_price <= totalPrice <= max_price
                            $q->where('min_price', '<=', $totalPrice)
                              ->where(function($subQ) use ($totalPrice) {
                                  $subQ->where('max_price', '>=', $totalPrice)
                                       ->orWhereNull('max_price');
                              });
                        })
                        // Also include ranges that are below totalPrice (for cascading approvals)
                        ->orWhere(function($q) use ($totalPrice) {
                            $q->whereNotNull('max_price')
                              ->where('max_price', '<', $totalPrice);
                        });
                    })
                    ->get();
 
                // Group approvals by max_price to create slabs (price ranges)
                $groupedByMaxPrice = $allApprovals->groupBy(function($item) {
                    return $item->max_price ?? PHP_INT_MAX;
                })->sortBy(function($group, $maxPrice) {
                    // Sort groups by max_price ascending
                    return $maxPrice === PHP_INT_MAX ? PHP_INT_MAX - 1 : $maxPrice;
                });

                $slabOrder = 1;

                // Process each price range group
                foreach ($groupedByMaxPrice as $maxPrice => $slabs) {
                    // For first slab (lowest price range), prioritize school/dept specific
                    if ($slabOrder == 1) {
                        // Try school-specific first
                        $filteredSlabs = $slabs->filter(function($slab) use ($requestSchoolId) {
                            return $slab->school_id == $requestSchoolId && !is_null($slab->school_id);
                        });

                        // Fallback to department-specific
                        if ($filteredSlabs->isEmpty()) {
                            $filteredSlabs = $slabs->filter(function($slab) use ($requestDeptId) {
                                return $slab->dept_id == $requestDeptId && !is_null($slab->dept_id);
                            });
                        }

                        // If still empty, use all slabs in this range
                        if ($filteredSlabs->isEmpty()) {
                            $filteredSlabs = $slabs;
                        }
                    } else {
                        // For higher ranges, use all slabs
                        $filteredSlabs = $slabs;
                }

                    // Sort by role priority within this price range
                    $sortedSlabs = $filteredSlabs->sortBy(function($slab) use ($rolePriority) {
                        $role = strtolower($slab->role ?? '');
                        return $rolePriority[$role] ?? 999; // Unknown roles go to end
                    })->values();

                    // Add slabs to approval list with sequential slab_order
                    foreach ($sortedSlabs as $slab) {
                        $approvalSlabs[] = [
                            'slab_number' => $slabOrder,
                            'slab_order' => $slabOrder,
                            'approval_user_id' => $slab->approval_user_id,
                            'school_id' => $slab->school_id,
                            'dept_id' => $slab->dept_id,
                            'role' => $slab->role,
                        ];
                        $slabOrder++;
                    }
                }

                return $approvalSlabs;
            }

            // Combined function: Store Approvals Only (Recommendation and PO will be created when last approver approves)
            public function storeRecommendationAndPO(Request $request)
            {

                // dd($request->all());
                try {
                    DB::beginTransaction();

                // Validate request
                $request->validate([
                    'qr_id' => 'required|integer',
                    'total_price' => 'required|numeric',
                    'budget_type' => 'required|string',
                ]);

                    // 1. Get Quotation Request to extract department and school
                    $quotationRequest = QuotationRequest::findOrFail($request->qr_id);
                    $requestDeptId = $quotationRequest->request_department_id;
                    $requestSchoolId = $quotationRequest->request_school_id;

                    // 2. Get Approval Slabs using helper function
                    $approvalSlabs = $this->getApprovalSlabs(
                        $requestDeptId,
                        $requestSchoolId,
                        $request->total_price,
                        $request->budget_type
                    );

                    // 3. Insert Approval Records in SP_PO_APPROVALS with slab_order, role and qr_id (without po_id initially)
                    // foreach ($approvalSlabs as $slab) {





                    //     SpPoApproval::create([
                    //         'po_id' => null, // Will be updated when PO is created by last approver
                    //         'qr_id' => $request->qr_id,
                    //         'approver_id' => $slab['approval_user_id'],
                    //         'slab_order' => $slab['slab_order'],
                    //         'role' => $slab['role'] ?? null,
                    //         'status' => 'pending',
                    //         'remarks' => null,
                    //         'entry_user_id' => auth()->id(),
                    //         'entry_datetime' => now(),
                    //         'updated_user_id' => null,
                    //         'updated_datetime' => null,
                    //     ]);
                    // }
                   $a = 1;
                    foreach ($approvalSlabs as $slab) {
                        $record = SpPoApproval::firstOrCreate(
                            [
                                'qr_id'       => $request->qr_id,
                                'approver_id' => $slab['approval_user_id'],
                            ],
                            [
                                'po_id'            => null,
                                'slab_order'       => $a,
                                'role'             => $slab['role'] ?? null,
                                'status'           => 'pending',
                                'remarks'          => null,
                                'entry_user_id'    => auth()->id(),
                                'entry_datetime'   => now(),
                                'updated_user_id'  => null,
                                'updated_datetime' => null,
                            ]
                        );

                        // Only increment if a new record was created
                        if ($record->wasRecentlyCreated) {
                            $a++;
                        }
                    }

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => count($approvalSlabs) . ' approval(s) assigned successfully. Recommendation and Purchase Order will be created when last approver approves.',
                        'approval_slabs' => $approvalSlabs,
                        'approvals_count' => count($approvalSlabs),
                        'budget_type' => $request->budget_type
                    ]);

                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Error: ' . $e->getMessage()
                    ], 500);
                }
            }

            // Get PO Approval Details
            public function getPoApprovals(Request $request)
            {
                // dd($request->all());
                try {
                    $request->validate([
                        'qr_id' => 'required|integer'
                    ]);

                    $qrId = $request->qr_id;

                    // Try to get Purchase Order (may not exist yet)
                    $purchaseOrder = PurchaseOrder::where('qr_id', $qrId)->first();

                    // Get ALL approvals (approved + pending) with user details using QR ID
                    $approvals = SpPoApproval::where('qr_id', $qrId)
                        ->orderBy('slab_order', 'asc')
                        ->get()
                        ->map(function($approval) {
                            // Get approver details from emp_employees table
                            $employee = DB::table('emp_employees')
                                ->select('prefix','employee_id', 'emp_f_name', 'emp_l_name')
                                ->where('employee_id', $approval->approver_id)
                                ->first();

                            $approverName = 'Unknown';
                            if ($employee) {
                                $prefix = $employee->prefix ?? '';
                                $firstName = $employee->emp_f_name ?? '';
                                $lastName = $employee->emp_l_name ?? '';
                                $approverName = trim($prefix.' '.$firstName . ' ' . $lastName);
                            }

                            return [
                                'approver_name' => $approverName,
                                'role' => $approval->role,
                                'status' => $approval->status,
                                'remarks' => $approval->remarks,
                                'approval_date' => $approval->updated_datetime ?
                                    \Carbon\Carbon::parse($approval->updated_datetime)->format('M d, Y h:i A') :
                                    'Pending',
                                'slab_order' => $approval->slab_order
                            ];
                        });

                    // Check if all approved
                    $totalCount = $approvals->count();
                    $approvedCount = $approvals->where('status', 'approved')->count();
                    $allApproved = ($totalCount > 0 && $totalCount === $approvedCount);

                    return response()->json([
                        'success' => true,
                        'approvals' => $approvals,
                        'all_approved' => $allApproved,
                        'po_id' => $purchaseOrder->po_id ?? null,
                        'po_generated' => $purchaseOrder ? ($purchaseOrder->po_status === 'generated') : false
                    ]);

                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error: ' . $e->getMessage()
                    ], 500);
                }
            }

            // Generate PO Function
            public function generatePo(Request $request)
            {
                try {
                    $request->validate([
                        'qr_id' => 'required|integer'
                    ]);

                    $qrId = $request->qr_id;

                    // Get Purchase Order by QR ID
                    $purchaseOrder = PurchaseOrder::where('qr_id', $qrId)->first();

                    if (!$purchaseOrder) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No Purchase Order found for this Quotation Request.'
                        ], 404);
                    }

                    // Idempotent guard: if already generated, return existing PO number.
                    if ($purchaseOrder->po_status === 'generated' && !empty($purchaseOrder->po_no)) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Purchase Order already generated. PO Number: ' . $purchaseOrder->po_no,
                            'po_id' => $purchaseOrder->po_id,
                            'qr_id' => $qrId,
                            'po_number' => $purchaseOrder->po_no
                        ]);
                    }

                    $poId = $purchaseOrder->po_id;

                    // Check if all approvals are approved
                    $totalApprovals = SpPoApproval::where('po_id', $poId)->count();
                    $approvedCount = SpPoApproval::where('po_id', $poId)
                                                 ->where('status', 'approved')
                                                 ->count();

                    if ($totalApprovals === 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No approvals found for this Purchase Order.'
                        ], 400);
                    }

                    if ($approvedCount < $totalApprovals) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Not all approvals are completed. Please wait for all approvals to be approved.'
                        ], 400);
                    }

                    // Generate PO Number: BT/month/year/UNI/auto_increment
                    $month = date('m');
                    $year = date('y');
                    $prefix = "BT/{$month}/{$year}/UNI/";

                    // Find last PO for current month/year
                    $lastPO = PurchaseOrder::whereNotNull('po_no')
                        ->where('po_no', 'LIKE', "{$prefix}%")
                        ->orderBy('po_id', 'desc')
                        ->first();

                    if ($lastPO && $lastPO->po_no) {
                        $parts = explode('/', $lastPO->po_no);
                        $lastNumber = intval(end($parts));
                        $newNumber = $lastNumber + 1;
                    } else {
                        $newNumber = 1;
                    }

                    $poNumber = $prefix . $newNumber;

                    // Persist generated PO number and generated state
                    $purchaseOrder->po_no = $poNumber;
                    $purchaseOrder->po_status = 'generated';
                    $purchaseOrder->approvel_status = 'approved';
                    $purchaseOrder->approvel_date = now();
                    $purchaseOrder->po_generated_at = now();
                    $purchaseOrder->po_generated_by = auth()->user()->username;
                    $purchaseOrder->save();

                    // Update SP_QUOTATION_REQUEST table with PO_ID
                    QuotationRequest::where('qr_id', $qrId)
                        ->update(['po_id' => $poId]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Purchase Order generated successfully! PO Number: ' . $poNumber,
                        'po_id' => $poId,
                        'qr_id' => $qrId,
                        'po_number' => $poNumber
                    ]);

                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error: ' . $e->getMessage()
                    ], 500);
                }
            }

            // Get Recommendations Details
            public function getRecommendationsDetails(Request $request)
            {
                try {
                    $request->validate([
                        'qr_id' => 'required|integer'
                    ]);

                    $qrId = $request->qr_id;

                    // Get Quotation Request
                    $quotationRequest = QuotationRequest::with([
                        'recommendations.department',
                        'recommendations.vendorQuotation.vendor',
                        'vendorCategory'
                    ])->findOrFail($qrId);

                    // Get Purchase Order
                    $purchaseOrder = PurchaseOrder::where('qr_id', $qrId)->first();

                    // Build recommendations/approvals data
                    $recommendationsData = [];

                    // Priority 1: Get approvals from SP_PO_APPROVALS (these show approval status)
                    if ($purchaseOrder) {
                        $approvals = SpPoApproval::where('po_id', $purchaseOrder->po_id)
                            ->orderBy('slab_order', 'asc')
                            ->get();

                        foreach ($approvals as $approval) {
                            // Get employee details
                            $employee = DB::table('emp_employees')
                                ->select('prefix', 'emp_f_name', 'emp_l_name')
                                ->where('employee_id', $approval->approver_id)
                                ->first();

                            $personName = 'Unknown';
                            if ($employee) {
                                $prefix = $employee->prefix ?? '';
                                $firstName = $employee->emp_f_name ?? '';
                                $lastName = $employee->emp_l_name ?? '';
                                $personName = trim($prefix . ' ' . $firstName . ' ' . $lastName);
                            }

                            // Get vendor name from Purchase Order
                            $vendorName = 'N/A';
                            if ($purchaseOrder->vendor_id) {
                                $vendor = DB::table('sp_vendors')
                                    ->select('vendor_name')
                                    ->where('vendor_id', $purchaseOrder->vendor_id)
                                    ->first();
                                if ($vendor) {
                                    $vendorName = $vendor->vendor_name;
                                }
                            }

                            // Format approval date
                            $approvalDate = null;
                            if ($approval->updated_datetime) {
                                $approvalDate = \Carbon\Carbon::parse($approval->updated_datetime)->toISOString();
                            } elseif ($approval->entry_datetime) {
                                $approvalDate = \Carbon\Carbon::parse($approval->entry_datetime)->toISOString();
                            }

                            $recommendationsData[] = [
                                'role' => $approval->role,
                                'person_name' => $personName,
                                'vendor_name' => $vendorName,
                                'status' => $approval->status ?? 'pending', // pending, approved, rejected
                                'remarks' => $approval->remarks,
                                'slab_order' => $approval->slab_order,
                                'created_at' => $approval->entry_datetime ? \Carbon\Carbon::parse($approval->entry_datetime)->toISOString() : null,
                                'approved_at' => $approvalDate,
                            ];
                        }
                    }

                    // Priority 2: If no approvals found, get from Recommendation model
                    if (empty($recommendationsData)) {
                        $recommendations = Recommendation::where('qr_id', $qrId)
                            ->with(['department', 'vendorQuotation.vendor'])
                            ->orderBy('created_at', 'asc')
                            ->get();

                        foreach ($recommendations as $rec) {
                            // Get employee details
                            $employee = DB::table('emp_employees')
                                ->select('prefix', 'emp_f_name', 'emp_l_name')
                                ->where('employee_id', $rec->entry_user_id)
                                ->first();

                            $personName = 'Unknown';
                            if ($employee) {
                                $prefix = $employee->prefix ?? '';
                                $firstName = $employee->emp_f_name ?? '';
                                $lastName = $employee->emp_l_name ?? '';
                                $personName = trim($prefix . ' ' . $firstName . ' ' . $lastName);
                            }

                            // Get vendor name
                            $vendorName = 'N/A';
                            if ($rec->vendorQuotation && $rec->vendorQuotation->vendor) {
                                $vendorName = $rec->vendorQuotation->vendor->vendor_name;
                            }

                            $recommendationsData[] = [
                                'role' => null,
                                'person_name' => $personName,
                                'vendor_name' => $vendorName,
                                'status' => 'recommended',
                                'remarks' => $rec->remarks,
                                'slab_order' => null,
                                'created_at' => $rec->created_at ? $rec->created_at->toISOString() : null,
                                'approved_at' => null,
                            ];
                        }
                    }

                    // Build reference ID
                    $referenceId = 'QR-' . str_pad($qrId, 6, '0', STR_PAD_LEFT);
                    if ($purchaseOrder && $purchaseOrder->po_no) {
                        $referenceId .= ' / PO-' . $purchaseOrder->po_no;
                    }

                    return response()->json([
                        'success' => true,
                        'data' => [
                            'reference_id' => $referenceId,
                            'recommendations' => $recommendationsData
                        ]
                    ]);

                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error: ' . $e->getMessage()
                    ], 500);
                }
            }

            // Get Summary Data from SP_PO_APPROVALS
            public function getSummaryData(Request $request)
            {
                try {
                    $request->validate([
                        'qr_id' => 'required|integer'
                    ]);

                    $qrId = $request->qr_id;

                    // Get approvals from SP_PO_APPROVALS using qr_id
                    $approvals = SpPoApproval::where('qr_id', $qrId)
                        ->orderBy('slab_order', 'asc')
                        ->get();

                    if ($approvals->isEmpty()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No approval data found.'
                        ]);
                    }

                    $summaryData = [];

                    // If current user is an approver in this chain, restrict to approvals up to their record
                    $currentEmployeeId = auth()->user()->employee_id ?? null;
                    if ($currentEmployeeId) {
                        $index = $approvals->search(function($a) use ($currentEmployeeId) {
                            return (string)$a->approver_id === (string)$currentEmployeeId;
                        });
                        if ($index !== false) {
                            $approvals = $approvals->slice(0, $index + 1)->values();
                        }
                    }

                    foreach ($approvals as $approval) {
                        // 1. Get employee name from emp_employees using APPROVER_ID
                        $employee = DB::table('emp_employees')
                            ->select('prefix', 'emp_f_name', 'emp_l_name')
                            ->where('employee_id', $approval->approver_id)
                            ->first();

                        $approverName = 'Unknown';
                        if ($employee) {
                            $prefix = $employee->prefix ?? '';
                            $firstName = $employee->emp_f_name ?? '';
                            $lastName = $employee->emp_l_name ?? '';
                            $approverName = trim($prefix . ' ' . $firstName . ' ' . $lastName);
                        }

                        // 2. Get STATUS and ROLE from SP_PO_APPROVALS
                        $status = $approval->status ?? 'pending';
                        $role = $approval->role ?? null;

                        // 3. Get VENDOR_ID from SP_PO_APPROVALS
                        $vendorId = $approval->vendor_id ?? null;
                        $vendorName = null;
                        $vendorCategoryName = null;

                        // 4. If VENDOR_ID is not null, get vendor name and category from SP_VENDORS and SP_VENDOR_CATEGORY
                        if ($vendorId && !empty($vendorId)) {
                            // Get vendor details from sp_vendors
                            $vendor = DB::table('sp_vendors')
                                ->select('vendor_id', 'vendor_name', 'vendor_category_id')
                                ->where('vendor_id', $vendorId)
                                ->first();

                            if ($vendor) {
                                $vendorName = !empty($vendor->vendor_name) ? trim($vendor->vendor_name) : null;

                                // Get category name from SP_VENDOR_CATEGORY
                                if ($vendor->vendor_category_id) {
                                    $vendorCategory = DB::table('sp_vendor_category')
                                        ->select('category_name')
                                        ->where('category_id', $vendor->vendor_category_id)
                                        ->first();

                                    if ($vendorCategory) {
                                        $vendorCategoryName = $vendorCategory->category_name;
                                    }
                                }
                            }
                        }

                        // Format approval and recommendation dates
                        $recommendedDate = null;
                        if ($approval->entry_datetime) {
                            $recommendedDate = \Carbon\Carbon::parse($approval->entry_datetime)->format('d M Y');
                        }

                        $updatedDate = null;
                        if ($approval->updated_datetime) {
                            $updatedDate = \Carbon\Carbon::parse($approval->updated_datetime)->format('d M Y');
                        }

                        $summaryData[] = [
                            'id' => $approval->id,
                            'approver_id' => $approval->approver_id,
                            'approver_name' => $approverName,
                            'status' => $status,
                            'remarks' => $approval->remarks ?? null,
                            'role' => $role,
                            'vendor_id' => $vendorId,
                            'vendor_name' => $vendorName,
                            'vendor_category_name' => $vendorCategoryName,
                            'recommended_datetime' => $recommendedDate,
                            'updated_datetime' => $updatedDate,
                        ];
                    }

                    return response()->json([
                        'success' => true,
                        'data' => $summaryData
                    ]);

                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error: ' . $e->getMessage()
                    ], 500);
                }
            }

            // Get Vendors for Summary Modal Dropdown
            public function getSummaryVendors(Request $request)
            {
                try {
                    $request->validate([
                        'qr_id' => 'required|integer'
                    ]);

                    $qrId = $request->qr_id;

                    // Get vendors from SP_VENDOR_QUOTATIONS where QUOTATION_REQUEST_ID = qr_id
                    $vendorQuotations = DB::table('sp_vendor_quotations')
                        ->where('quotation_request_id', $qrId)
                        ->select('vendor_id', 'quantity', 'rate', 'price', 'currency', 'delivery_time', 'warranty', 'remarks')
                        ->orderBy('vq_id', 'desc')
                        ->get();

                    $budgetRow = DB::table('sp_quotation_request as qr')
                        ->leftJoin('sp_budget_items as bi', 'bi.item_id', '=', 'qr.item_id')
                        ->where('qr.qr_id', $qrId)
                        ->select(
                            'bi.budget_type',
                            'bi.quantity as budget_quantity',
                            'bi.rate as budget_rate',
                            'bi.reappropriation_approval'
                        )
                        ->first();
                    $budgetQty = (float) ($budgetRow->budget_quantity ?? $budgetRow->BUDGET_QUANTITY ?? 0);
                    $budgetRate = (float) ($budgetRow->budget_rate ?? $budgetRow->BUDGET_RATE ?? 0);
                    $budgetAmount = $budgetQty * $budgetRate;
                    $vcStatus = strtolower(trim((string) ($budgetRow->reappropriation_approval ?? $budgetRow->REAPPROPRIATION_APPROVAL ?? '')));

                    $itemDeclinedByVc = in_array($vcStatus, ['declined', 'rejected'], true);

                    $rejectedVendorIds = [];
                    if ($itemDeclinedByVc) {
                        $rejectedVendorIds = DB::table('sp_po_approvals')
                            ->where('qr_id', $qrId)
                            ->whereNotNull('vendor_id')
                            ->pluck('vendor_id')
                            ->map(function ($id) {
                                return (string) $id;
                            })
                            ->all();
                    }

                    $vendors = [];
                    $seenVendors = [];
                    foreach ($vendorQuotations as $vq) {
                        $vqVendorId = $vq->vendor_id ?? $vq->VENDOR_ID ?? null;
                        if ($vqVendorId && !isset($seenVendors[(string) $vqVendorId])) {
                            $seenVendors[(string) $vqVendorId] = true;
                            $vq->vendor_id = $vqVendorId;
                            // Get vendor details
                            $vendor = DB::table('sp_vendors')
                                ->select('vendor_id', 'vendor_name', 'vendor_category_id')
                                ->where('vendor_id', $vq->vendor_id)
                                ->first();

                            if ($vendor) {
                                // Get category name
                                $categoryName = null;
                                if ($vendor->vendor_category_id) {
                                    $category = DB::table('sp_vendor_category')
                                        ->select('category_name')
                                        ->where('category_id', $vendor->vendor_category_id)
                                        ->first();
                                    if ($category) {
                                        $categoryName = $category->category_name;
                                    }
                                }

                                $quotePrice = (float) ($vq->price ?? $vq->PRICE ?? 0);
                                if ($quotePrice <= 0) {
                                    $quotePrice = (float) ($vq->quantity ?? $vq->QUANTITY ?? 0) * (float) ($vq->rate ?? $vq->RATE ?? 0);
                                }
                                $exceedsBudget = $budgetAmount > 0 && $quotePrice > $budgetAmount;
                                $vcApproved = in_array($vcStatus, ['approved', 'approve'], true);

                                $vendors[] = [
                                    'vendor_id' => $vendor->vendor_id,
                                    'vendor_name' => $vendor->vendor_name,
                                    'category_name' => $categoryName,
                                    'vc_rejected' => $itemDeclinedByVc && in_array((string) $vendor->vendor_id, $rejectedVendorIds, true),
                                    'quantity' => $vq->quantity ?? $vq->QUANTITY ?? null,
                                    'rate' => $vq->rate ?? $vq->RATE ?? null,
                                    'price' => $quotePrice,
                                    'currency' => $vq->currency ?? $vq->CURRENCY ?? '',
                                    'delivery_time' => $vq->delivery_time ?? $vq->DELIVERY_TIME ?? '',
                                    'warranty' => $vq->warranty ?? $vq->WARRANTY ?? '',
                                    'remarks' => $vq->remarks ?? $vq->REMARKS ?? '',
                                    'budget_amount' => $budgetAmount,
                                    'exceeds_budget' => $exceedsBudget,
                                    'vc_status' => $vcStatus !== '' ? $vcStatus : 'not sent',
                                    'vc_approved' => $vcApproved,
                                ];
                            }
                        }
                    }

                    return response()->json([
                        'success' => true,
                        'data' => $vendors
                    ]);

                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error: ' . $e->getMessage()
                    ], 500);
                }
            }

            // Update Approval Remarks and Vendor ID
            public function updateApprovalRemarks(Request $request)
            {
                try {
                    DB::beginTransaction();

                    $request->validate([
                        'approval_id' => 'required|integer',
                        'vendor_id' => 'nullable|integer',
                        'remarks' => 'nullable|string|max:2000',
                        'status' => 'nullable|string|in:approved,pending,rejected'
                    ]);

                    $approval = SpPoApproval::findOrFail($request->approval_id);
                    $qrId = $approval->qr_id;

                    if ($request->filled('vendor_id') && $request->status !== 'rejected') {
                        $itemDeclinedByVc = DB::table('sp_quotation_request as qr')
                            ->join('sp_budget_items as bi', 'bi.item_id', '=', 'qr.item_id')
                            ->where('qr.qr_id', $qrId)
                            ->whereRaw("LOWER(TRIM(bi.reappropriation_approval)) IN ('declined', 'rejected')")
                            ->exists();
                        $alreadyRecommended = DB::table('sp_po_approvals')
                            ->where('qr_id', $qrId)
                            ->where('vendor_id', $request->vendor_id)
                            ->where('id', '!=', $approval->id)
                            ->exists();
                        if ($itemDeclinedByVc && $alreadyRecommended) {
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => 'This vendor was rejected by VC and cannot be selected.',
                            ], 422);
                        }

                        if ($request->status === 'approved') {
                            $budget = DB::table('sp_quotation_request as qr')
                                ->leftJoin('sp_budget_items as bi', 'bi.item_id', '=', 'qr.item_id')
                                ->where('qr.qr_id', $qrId)
                                ->select('bi.quantity as budget_quantity', 'bi.rate as budget_rate', 'bi.reappropriation_approval')
                                ->first();
                            $budgetAmount = (float) ($budget->budget_quantity ?? 0) * (float) ($budget->budget_rate ?? 0);
                            $quote = DB::table('sp_vendor_quotations')
                                ->where('quotation_request_id', $qrId)
                                ->where('vendor_id', $request->vendor_id)
                                ->orderBy('vq_id', 'desc')
                                ->first();
                            $quotePrice = (float) ($quote->price ?? $quote->PRICE ?? 0);
                            if ($quotePrice <= 0 && $quote) {
                                $quotePrice = (float) ($quote->quantity ?? $quote->QUANTITY ?? 0) * (float) ($quote->rate ?? $quote->RATE ?? 0);
                            }
                            $vcStatus = strtolower(trim((string) ($budget->reappropriation_approval ?? $budget->REAPPROPRIATION_APPROVAL ?? '')));
                            if ($budgetAmount > 0 && $quotePrice > $budgetAmount && !in_array($vcStatus, ['approved', 'approve'], true)) {
                                DB::rollBack();
                                return response()->json([
                                    'success' => false,
                                    'message' => 'This quotation exceeds the budget. VC approval is required.',
                                ], 422);
                            }
                        }
                    }

                    $updateData = [
                        'updated_user_id' => auth()->id(),
                        'updated_datetime' => now(),
                    ];

                    if ($request->has('vendor_id')) {
                        $updateData['vendor_id'] = $request->vendor_id;
                    }

                    if ($request->has('remarks')) {
                        $updateData['remarks'] = $request->remarks;
                    }

                    if ($request->has('status')) {
                        $updateData['status'] = $request->status;
                    } else {
                        // If status not provided but vendor_id is set, automatically set status to 'approved'
                        if ($request->has('vendor_id') && !empty($request->vendor_id)) {
                            $updateData['status'] = 'approved';
                        }
                    }

                    $approval->update($updateData);

                    // Refresh approval to get updated data
                    $approval->refresh();

                    // Check if this is the last approval (highest slab_order)
                    $maxSlabOrder = SpPoApproval::where('qr_id', $qrId)->max('slab_order');

                    // Check if this is the last approval and vendor_id is set (status can be pending or approved)
                    if ($approval->slab_order == $maxSlabOrder && !empty($approval->vendor_id)) {
                        // Check if Recommendation and PO already exist for this qr_id
                        $existingRecommendation = Recommendation::where('qr_id', $qrId)->first();
                        $existingPO = PurchaseOrder::where('qr_id', $qrId)->first();

                        // Only create if they don't exist
                        if (!$existingRecommendation && !$existingPO) {
                        // This is the last approver who approved with vendor selection
                        // Create Recommendation and Purchase Order

                        $vendorId = $approval->vendor_id;

                        // Get vendor quotation for this qr_id and vendor_id
                        $vendorQuotation = VendorQuotation::where('quotation_request_id', $qrId)
                            ->where('vendor_id', $vendorId)
                            ->first();

                        if (!$vendorQuotation) {
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => 'Vendor quotation not found for selected vendor'
                            ], 400);
                        }

                        // Get quotation request for budget info
                        $quotationRequest = QuotationRequest::findOrFail($qrId);

                        // Create Recommendation
                        $recommendation = Recommendation::create([
                            'qr_id' => $qrId,
                            'vq_id' => $vendorQuotation->vq_id,
                            'department_id' => auth()->user()->dept_id,
                            'recommended_flag' => 'yes',
                            'remarks' => $updateData['remarks'] ?? null,
                            'entry_user_id' => auth()->id(),
                            'entry_username' => auth()->user()->username,
                        ]);

                        // Create Purchase Order
                        $purchaseOrderData = [
                            'qr_id' => $qrId,
                            'vendor_quotation_id' => $vendorQuotation->vq_id,
                            'vendor_id' => $vendorId,
                            'po_date' => now(),
                            'quantity' => $vendorQuotation->quantity,
                            'unit_price' => $vendorQuotation->rate,
                            'total_price' => $vendorQuotation->price,
                            'currency' => $vendorQuotation->currency ?? 'PKR',
                            'budget_type' => $quotationRequest->budget_type ?? 'operational',
                            'budget_amount' => null, // Can be added if available in quotation request
                            'warranty' => $vendorQuotation->warranty,
                            'delivery_time' => $vendorQuotation->delivery_time,
                            'remarks' => $updateData['remarks'] ?? null,
                            'status' => 'pending',
                            'created_by' => auth()->id(),
                            'entry_username' => auth()->user()->username,
                        ];

                        if (app(StoreItemsSchemaService::class)->ensure()) {
                            $purchaseOrderData['request_type'] = $quotationRequest->request_type;
                            $purchaseOrderData['store_item_id'] = $quotationRequest->store_item_id;
                        }

                        $purchaseOrder = PurchaseOrder::create($purchaseOrderData);

                        // Update all approvals for this qr_id with po_id
                        SpPoApproval::where('qr_id', $qrId)
                            ->update(['po_id' => $purchaseOrder->po_id]);

                        DB::commit();

                        return response()->json([
                            'success' => true,
                            'message' => 'Approval updated successfully. Recommendation and Purchase Order created.',
                            'recommendation_id' => $recommendation->rec_id,
                            'purchase_order_id' => $purchaseOrder->po_id
                        ]);
                        }
                    }

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => 'Remarks and vendor updated successfully'
                    ]);

                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Error: ' . $e->getMessage()
                    ], 500);
                }
            }

}




