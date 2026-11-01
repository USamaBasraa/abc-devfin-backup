<?php

namespace App\Models\StoreProcurement;

use Illuminate\Database\Eloquent\Model;

class BudgetHead extends Model {
    protected $table = 'SP_BUDGET_HEADS';
    protected $primaryKey = 'bh_id';
    public $timestamps = true;

    protected $fillable = ['dept_id','account_id','account_title','head_title','school_id','school_title','remarks','ay_id','entry_username','entry_dept_id'];

    public function items() {
        $data = $this->hasMany(BudgetItem::class, 'bh_id', 'bh_id');

        return $data;
    }

    public function department() {
        return $this->belongsTo(\App\Models\Grader\Departments::class, 'dept_id', 'dept_id');
    }

    public function academicYear() {
        return $this->belongsTo(\App\Models\AcademicYear::class, 'ay_id', 'ay_id');
    }

    public function getBudgetYearAttribute(): string
    {
        if ($this->relationLoaded('items')) {
            foreach ($this->items as $item) {
                $year = $item->budget_year ?? null;
                if (!empty($year)) {
                    return $year;
                }
            }
        }

        if ($this->academicYear && !empty($this->academicYear->ay_name)) {
            return $this->academicYear->ay_name;
        }

        return 'N/A';
    }

    /**
     * Get budgets with items using raw SQL query
     * @param array $deptIds - Array of department IDs to filter
     * @param int|null $ayId - Academic Year ID (optional)
     * @param int|null $budgetHeadId - Budget Head ID (optional)
     * @param int|null $categoryId - Vendor Category ID (optional)
     * @return \Illuminate\Support\Collection
     */
    public static function getBudgetsWithItems($deptIds = [], $ayId = null, $budgetHeadId = null, $categoryId = null)
    {
        $deptIdsString = implode(',', $deptIds);

        $sql = "
             SELECT
                h.bh_id,
                h.dept_id,
                h.head_title,
                h.remarks,
                h.ay_id,
                ay.AY_NAME as ay_name,
                h.created_at,
                h.updated_at,
                h.account_id,
                i.item_id,
                i.pin_id,
                i.item_title,
                i.brand,
                i.model,
                i.quantity,
                i.rate,
                i.budget_type,
                i.budget_from,
                i.budget_to,
                i.reappropriation_approval,
                i.reappropriation_approval_remarks,
                i.send_reappropriation_approval_reapprop,
                i.remarks as item_remarks,
                d.DEPT_NAME as dept_name,
                pd.DEPT_NAME as parent_dept_name,
                (SELECT COUNT(*) FROM sp_quotation_request WHERE item_id = i.item_id) as quotation_count
            FROM SP_BUDGET_HEADS h
            LEFT JOIN ACADEMIC_YEARS ay ON h.ay_id = ay.AY_ID
            LEFT JOIN SP_BUDGET_ITEMS i ON h.bh_id = i.bh_id
            LEFT JOIN SP_VENDOR_CATEGORY vc ON i.bh_cat_id = vc.CATEGORY_ID
            LEFT JOIN DEPARTMENTS pd ON vc.PARENT_DEPARTMENT_ID = pd.DEPT_ID
            LEFT JOIN DEPARTMENTS d ON h.dept_id = d.DEPT_ID
            WHERE 1=1
        ";
// dd( $sql);
        if (!empty($deptIds)) {
            $sql .= " AND h.dept_id IN ($deptIdsString)";
        }

        $bindings = [];

        if ($ayId) {
            // --- Academic Year date-aware filter ---
            // Lookup AY_NAME (e.g. '2025-26' or '2025-2026') from ACADEMIC_YEARS
            $ayRecord = \DB::selectOne("SELECT AY_NAME FROM ACADEMIC_YEARS WHERE AY_ID = ?", [$ayId]);
            $fromYear = null;
            $toYear   = null;

            if ($ayRecord && !empty($ayRecord->ay_name)) {
                // AY_NAME formats: '2025-26' or '2025-2026'
                $parts = explode('-', $ayRecord->ay_name);
                if (count($parts) === 2) {
                    $fromYear = trim($parts[0]); // e.g. '2025'
                    $rawTo    = trim($parts[1]);  // e.g. '26' or '2026'
                    // Expand short year: '26' => '2026'
                    if (strlen($rawTo) === 2 && strlen($fromYear) === 4) {
                        $toYear = substr($fromYear, 0, 2) . $rawTo; // '20'+'26' = '2026'
                    } else {
                        $toYear = $rawTo;
                    }
                }
            }

            if ($fromYear && $toYear) {
                // Items synced from Pinnacle have BUDGET_FROM/BUDGET_TO => match by year.
                // Items entered manually have NULL dates => fallback to h.AY_ID.
                $sql .= " AND (
                    (
                        i.BUDGET_FROM IS NOT NULL
                        AND TO_CHAR(i.BUDGET_FROM, 'YYYY') = ?
                        AND TO_CHAR(i.BUDGET_TO,   'YYYY') = ?
                    )
                    OR
                    (
                        i.BUDGET_FROM IS NULL
                        AND h.ay_id = ?
                    )
                )";
                $bindings[] = $fromYear;
                $bindings[] = $toYear;
                $bindings[] = $ayId;
            } else {
                // AY_NAME could not be parsed — plain ay_id match on head
                $sql .= " AND h.ay_id = ?";
                $bindings[] = $ayId;
            }
        }

        if ($budgetHeadId) {
            $sql .= " AND h.bh_id = ?";
            $bindings[] = $budgetHeadId;
        }

        if ($categoryId) {
            $sql .= " AND i.bh_cat_id = ?";
            $bindings[] = $categoryId;
        }

        $sql .= " ORDER BY h.bh_id DESC";

        $results = !empty($bindings) ? \DB::select($sql, $bindings) : \DB::select($sql);

        // Group results by head_title
        $grouped = collect($results)->groupBy('head_title')->map(function($items) {

            $firstItem = $items->first();

            $budgetYear = null;
            foreach ($items as $item) {
                $from = $item->budget_from ?? $item->BUDGET_FROM ?? null;
                $to   = $item->budget_to   ?? $item->BUDGET_TO   ?? null;
                $fromYear = null;
                $toYear   = null;

                if (!empty($from)) {
                    try {
                        $fromYear = \Carbon\Carbon::parse($from)->format('Y');
                    } catch (\Throwable $e) {
                        if (preg_match('/\b(20\d\d|19\d\d)\b/', (string) $from, $m)) {
                            $fromYear = $m[1];
                        }
                    }
                }

                if (!empty($to)) {
                    try {
                        $toYear = \Carbon\Carbon::parse($to)->format('Y');
                    } catch (\Throwable $e) {
                        if (preg_match('/\b(20\d\d|19\d\d)\b/', (string) $to, $m)) {
                            $toYear = $m[1];
                        }
                    }
                }

                if ($fromYear && $toYear) {
                    $budgetYear = ($fromYear === $toYear) ? $fromYear : "{$fromYear}-{$toYear}";
                    break;
                } elseif ($fromYear) {
                    $budgetYear = $fromYear;
                    break;
                } elseif ($toYear) {
                    $budgetYear = $toYear;
                    break;
                }
            }

            if (empty($budgetYear)) {
                $budgetYear = $firstItem->ay_name ?? null;
            }

            return (object)[
                'bh_id' => $firstItem->bh_id,
                'dept_id' => $firstItem->dept_id,
                'head_title' => $firstItem->head_title,
                'account_id' => $firstItem->account_id ?? null,
                'remarks' => $firstItem->remarks,
                'ay_id' => $firstItem->ay_id,
                'budget_year' => $budgetYear,
                'department' => (object)[
                    'dept_id' => $firstItem->dept_id,
                    'dept_name' => $firstItem->dept_name
                ],
                'parent_department' => (object)[
                    'dept_name' => $firstItem->parent_dept_name
                ],
                'items' => $items->filter(fn($item) => $item->item_id !== null)->map(function($item) {
                    // dd($item->budget_type);
                    return (object)[
                        'item_id' => $item->item_id,
                        'pin_id' => $item->pin_id ?? null,
                        'item_title' => $item->item_title,
                        'brand' => $item->brand,
                        'model' => $item->model,
                        'quantity' => $item->quantity,
                        'rate' => $item->rate,
                        'budget_type' => $item->budget_type,
                        'budget_from' => $item->budget_from ?? null,
                        'budget_to' => $item->budget_to ?? null,
                        'remarks' => $item->item_remarks,
                         'reappropriation_approval'=> $item->reappropriation_approval,
                         'reappropriation_approval_remarks'=> $item->reappropriation_approval_remarks,
                         'send_reappropriation_approval_reapprop'=> $item->send_reappropriation_approval_reapprop,
                        //  'reappropriation_approval_remarks',
                        // 'send_reappropriation_approval_reapprop',


                        'quotationRequests' => $item->quotation_count > 0
                            ? collect(range(1, $item->quotation_count))
                            : collect([])
                    ];
                })
            ];
        });


        return $grouped;
    }
}
