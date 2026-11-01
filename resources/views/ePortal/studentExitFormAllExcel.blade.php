<?php
use Illuminate\Support\Facades\DB;

// Handle large data - increase limits for user-friendly experience
set_time_limit(300); // 5 minutes max execution time
ini_set('memory_limit', '512M'); // Increase memory limit
ini_set('max_execution_time', '300');

// Get request parameters
$fromDate = request()->get('from_date');
$toDate = request()->get('to_date');

// Build query with date filters
$additionalConditions = array();

if (!empty($fromDate) && !empty($toDate)) {
    $fromDateTime = $fromDate . ' 00:00:00';
    $toDateTime   = $toDate . ' 23:59:59';

    $additionalConditions[] = "TRIM(er_entry_datetime) BETWEEN '$fromDateTime' AND '$toDateTime'";
}

$studentQuery = "SELECT er_sai_enrollment_no,
                er_entry_datetime,
                ec_title,
                ec_id,
                er_request_status,
                er_resolving_datetime,
                CASE 
                    WHEN ec_id = 120 AND er_sai_enrollment_no IS NOT NULL THEN 
                        (SELECT api_name
                            FROM admission_apply_info aai
                            LEFT JOIN admission_personal_info api 
                                ON to_char(aai.aai_api_id) = to_char(api.api_id)
                            WHERE 
                            to_char(aai.aai_id) = to_char(er_sai_enrollment_no)
                            OR
                            to_char(aai.cms_sai_enrollment_no) = to_char(er_sai_enrollment_no)
                            FETCH FIRST 1 ROWS ONLY
                        )
                    ELSE spi_name
                END AS spi_name,
                (SELECT aai_id FROM admission_apply_info WHERE cms_sai_enrollment_no = sai_enrollment_no) AS aai_id,
                er_applicant_request_type,
                er_request_status,
                CASE 
                    WHEN ec_id = 120 THEN er_applicant_request_type
                    ELSE sai_student_status
                END AS applicant_status
                FROM eportal_request 
                LEFT JOIN eportal_categories ON eportal_request.ec_id = eportal_categories.id
                LEFT JOIN students_apply_info ON to_char(sai_enrollment_no) = to_char(er_sai_enrollment_no)
                LEFT JOIN students_personal_info ON sai_spi_id = spi_id                         
                WHERE ec_id IN (86,120,23,121)";

// If there are additional conditions, append them to the query
if (!empty($additionalConditions)) {
    $studentQuery .= " AND " . implode(" AND ", $additionalConditions);
}
$studentQuery .= " ORDER BY er_entry_datetime DESC";

// Execute query using Laravel DB
$rsStdFeeStatus = DB::select($studentQuery);

// Convert stdClass objects to array (Oracle returns column names in lowercase when aliased)
$rsStdFeeStatus = array_map(function($row) {
    return (array) $row;
}, $rsStdFeeStatus);

// Get unique departments from the actual data being displayed
$uniqueDeptNames = array();
$hasOtherDepts = false;

foreach ($rsStdFeeStatus as $studentRow) {
    // Skip if enrollment number is not set
    if (!isset($studentRow['er_sai_enrollment_no'])) {
        continue;
    }
    
    // Get department details for this student (only for school ID 11)
    $tempSqlDetail = "SELECT DISTINCT d.dept_name 
                     FROM eportal_request_details erd
                     LEFT JOIN departments d ON d.dept_id = erd.er_ecd_dep
                     LEFT JOIN eportal_request er ON erd.er_id = er.id
                     WHERE erd.erd_sai_enrollment_no = '" . $studentRow['er_sai_enrollment_no'] . "'
                     AND erd.er_ecd_instruction_categories IS NULL
                     AND erd.er_ecd_completion_days IS NULL
                     AND erd.er_ecd_viewer_user IS NULL
                     AND erd.er_ecd_dep IS NOT NULL
                     AND er.ec_id IN (86,120,121)
                     AND d.DEPT_SCHOOL_ID = 11";
    
    $tempDeptResult = DB::select($tempSqlDetail);
    $tempDeptResult = array_map(function($row) {
        return (array) $row;
    }, $tempDeptResult);

    foreach ($tempDeptResult as $deptRow) {
        if (!empty($deptRow['dept_name'])) {
            $uniqueDeptNames[] = $deptRow['dept_name'];
        }
    }

    // Check if there are any departments other than school ID 11
    $tempSqlOtherDepts = "SELECT DISTINCT d.dept_name 
                         FROM eportal_request_details erd
                         LEFT JOIN departments d ON d.dept_id = erd.er_ecd_dep
                         LEFT JOIN eportal_request er ON erd.er_id = er.id
                         WHERE erd.erd_sai_enrollment_no = '" . $studentRow['er_sai_enrollment_no'] . "'
                         AND erd.er_ecd_instruction_categories IS NULL
                         AND erd.er_ecd_completion_days IS NULL
                         AND erd.er_ecd_viewer_user IS NULL
                         AND erd.er_ecd_dep IS NOT NULL
                         AND er.ec_id IN (86,120,121)
                         AND (d.DEPT_SCHOOL_ID != 11 OR d.DEPT_SCHOOL_ID IS NULL)";
    
    $tempOtherResult = DB::select($tempSqlOtherDepts);

    if (count($tempOtherResult) > 0) {
        $hasOtherDepts = true;
    }
}

// Get unique department names and sort them
$uniqueDeptNames = array_unique($uniqueDeptNames);
sort($uniqueDeptNames);



// Set Excel headers
header("Content-Type: application/vnd.ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
$file_time = date('D_d-m-Y_H-i-s');
header("Content-Disposition: attachment; filename=Student_Exit_Form_Report_$file_time.xls");

// Calculate total columns
$totalCols = 8 + count($uniqueDeptNames);
if ($hasOtherDepts) {
    $totalCols++;
}
?>
<style type="text/css">
.headingtd {
    border: 2px solid #000;
    background-color: #999;
    font-weight: bold;
}
.cntr {
    text-align: center;
}
</style>

<table border='1' align='center'>
    <tr>
        <td colspan="<?=$totalCols?>" style="border:none;"><font style="font-size:36px;">Beaconhouse National University</font></td>
    </tr>
    <tr>
        <td colspan="<?=$totalCols?>" style="border:none;" height="10"></td>
    </tr>
    <tr>
        <td colspan="<?=$totalCols?>" style="border:none;"><font style="font-size:24px;">Student University Leaving Exit Form Report</font></td>
    </tr>
    <tr>
        <td colspan="<?=$totalCols?>" style="border:none;" height="10"></td>
    </tr>
    
    <!-- Table Headers -->
    <tr>
        <td class="headingtd" align="center" width="100">Sr #</td>
        <td class="headingtd" align="center" width="100">Student ID</td>
        <td class="headingtd" align="center" width="100">Roll #</td>
        <td class="headingtd" align="center" width="180">Student Name</td>
        <td class="headingtd" align="center" width="120">Status</td>
        <td class="headingtd" align="center" width="120">Request Date</td>
        <td class="headingtd" align="center" width="200">Category</td>
        <td class="headingtd" align="center" width="150">Account</td>
        <?php 
        // Dynamic department column headers
        foreach($uniqueDeptNames as $deptName){
            echo '<td class="headingtd" align="center" width="150">' . htmlspecialchars($deptName) . '</td>';
        }
        
        // Add Department HOD column header if there are other departments
        if($hasOtherDepts){
            echo '<td class="headingtd" align="center" width="150">Department HOD</td>';
        }
        ?>
    </tr>

<?php
if (count($rsStdFeeStatus) >= 1) {
    $sr = 1;
    
    foreach ($rsStdFeeStatus as $rec) {
        // Get detail records
        $sqlDetail = "SELECT
            erd_sai_enrollment_no,
            emp_name,
            dept_name,
            dept_school_id,
            CASE
                WHEN erd_status IS NULL THEN er_request_status
                ELSE erd_status
            END AS erd_status,
            CASE
                WHEN er_ecd_appr_datetime IS NULL THEN er_resolving_datetime
                ELSE er_ecd_appr_datetime
            END AS er_ecd_appr_datetime
        FROM (
            SELECT
                erd_sai_enrollment_no,
                CASE
                    WHEN er_ecd_approval_user IS NULL THEN empres.emp_f_name || ' ' || empres.emp_l_name
                    ELSE empapr.emp_f_name || ' ' || empapr.emp_l_name
                END AS emp_name,
                CASE
                    WHEN dept_name IS NULL THEN 'A/F'
                    ELSE dept_name
                END AS dept_name,
                deptapr.dept_school_id,
                CASE
                    WHEN eportal_request_details.erd_status = 'Approved' THEN 'Approved'
                    WHEN eportal_request_details.erd_status = 'Declined' THEN 'Declined'
                END AS erd_status,
                er_ecd_appr_datetime,
                er_request_status,
                er_resolving_datetime
            FROM eportal_request_details
            LEFT JOIN emp_employees empapr ON empapr.employee_id = er_ecd_approval_user
            LEFT JOIN emp_employees empres ON empres.employee_id = er_ecd_resolving_user
            LEFT JOIN departments deptapr ON deptapr.dept_id = er_ecd_dep
            LEFT JOIN eportal_request ON er_id = eportal_request.id
            WHERE er_id = (
                SELECT id
                FROM eportal_request
                WHERE ec_id IN (86,120,23,121)
                AND er_sai_enrollment_no = '" . $rec['er_sai_enrollment_no'] . "'
                AND er_entry_datetime = '" . $rec['er_entry_datetime'] . "'
                FETCH FIRST 1 ROWS ONLY
            )
            AND erd_sai_enrollment_no = '" . $rec['er_sai_enrollment_no'] . "'
            AND er_ecd_instruction_categories IS NULL
            AND er_ecd_completion_days IS NULL
            AND ER_ECD_APPROVAL_USER IS NOT NULL
            AND er_ecd_viewer_user IS NULL
            ORDER BY dept_name ASC
        )";

        $rowssqlDetail = DB::select($sqlDetail);
        $rowssqlDetail = array_map(function($row) {
            return (array) $row;
        }, $rowssqlDetail);
        ?>
        
        <tr <?php if($rec['ec_id'] == 120) echo 'style="background-color: #fafafa;"'; ?>>
            <td align="center"><?=$sr++?></td>
            
            <!-- Student ID -->
            <td align="center">
                <?php 
                if ($rec['ec_id'] == 120) {
                    echo !empty($rec['er_sai_enrollment_no']) ? $rec['er_sai_enrollment_no'] : 'N/A';
                } else {
                    echo !empty($rec['aai_id']) ? $rec['aai_id'] : 'N/A';
                }
                ?>
            </td>
            
            <!-- Roll # -->
            <td align="center">
                <?php 
                if ($rec['ec_id'] == 120) {
                    echo '';
                } else {
                    echo !empty($rec['er_sai_enrollment_no']) ? $rec['er_sai_enrollment_no'] : 'N/A';
                }
                ?>
            </td>
            
            <!-- Student Name -->
            <td align="center"><?=htmlspecialchars($rec['spi_name'])?></td>
            
            <!-- Status -->
            <td align="center">
                <?php 
                if ($rec['ec_id'] == 120 && (empty($rec['applicant_status']) || $rec['applicant_status'] === null)) {
                    echo ucfirst($rec['er_applicant_request_type']);
                } else {
                    echo htmlspecialchars($rec['applicant_status']);
                }
                ?>
            </td>
            
            <!-- Request Date -->
            <td align="center"><?=date('Y-m-d', strtotime($rec['er_entry_datetime']))?><br><?=date('H:i', strtotime($rec['er_entry_datetime']))?></td>
            
            <!-- Category -->
            <td align="center"><?=htmlspecialchars($rec['ec_title'])?></td>
            
            <!-- Account -->
            <td align="center">
                <?php 
                $accountValue = $rec['er_request_status'];
                if (!empty($rec['er_resolving_datetime']) && strtoupper($rec['er_request_status']) != 'PENDING') {
                    $datePart = date('Y-m-d', strtotime($rec['er_resolving_datetime']));
                    $timePart = date('H:i', strtotime($rec['er_resolving_datetime']));
                    $accountValue = $rec['er_request_status'] . '<br>' . $datePart . '<br>' . $timePart;
                }
                echo $accountValue;
                ?>
            </td>
            
            <?php 
            // Organize department data by department name and school id
            $deptData = array();
            $otherDeptData = array();
            
            foreach ($rowssqlDetail as $deptRow) {
                $deptName = $deptRow['dept_name'];
                $deptSchoolId = $deptRow['dept_school_id'];
                
                if ($deptSchoolId == 11) {
                    // School 11 departments - separate columns
                    $deptData[$deptName] = array(
                        'status' => $deptRow['erd_status'],
                        'datetime' => $deptRow['er_ecd_appr_datetime']
                    );
                } else {
                    // Other departments - combine in one column
                    $otherDeptData[] = array(
                        'dept_name' => $deptName,
                        'status' => $deptRow['erd_status'],
                        'datetime' => $deptRow['er_ecd_appr_datetime']
                    );
                }
            }
            
            // Display each school-11 department column in the correct order
            foreach ($uniqueDeptNames as $deptName) {
                if (isset($deptData[$deptName])) {
                    $deptValue = $deptData[$deptName]['status'];
                    if (!empty($deptData[$deptName]['datetime']) && strtoupper($deptData[$deptName]['status']) != 'PENDING') {
                        $datePart = date('Y-m-d', strtotime($deptData[$deptName]['datetime']));
                        $timePart = date('H:i', strtotime($deptData[$deptName]['datetime']));
                        $deptValue .= '<br>' . $datePart . '<br>' . $timePart;
                    }
                    echo '<td align="center">' . $deptValue . '</td>';
                } else {
                    echo '<td align="center">-</td>';
                }
            }
            
            // Display Department HOD column with all non-school-11 departments
            if ($hasOtherDepts) {
                if (count($otherDeptData) > 0) {
                    echo '<td align="center">';
                    $hodValues = array();
                    foreach ($otherDeptData as $otherDept) {
                        if (strtoupper($otherDept['status']) != 'PENDING') {
                            $datePart = date('Y-m-d', strtotime($otherDept['datetime']));
                            $timePart = date('H:i', strtotime($otherDept['datetime']));
                            $hodValues[] = $otherDept['status'] . '<br>' . $datePart . ' ' . $timePart;
                        } else {
                            $hodValues[] = $otherDept['status'];
                        }
                    }
                    echo implode('<br>', $hodValues);
                    echo '</td>';
                } else {
                    echo '<td align="center">-</td>';
                }
            }
            ?>
        </tr>
        <?php
    }
    ?>
    
    <tr style="background-color:#CCC;">
        <td colspan="<?=$totalCols?>" align="left">Report generated on: <?=date('D d-m-Y H:i:s')?></td>
    </tr>
    
<?php
} else {
    ?>
    <tr style="background-color:#FFA8A8">
        <td colspan="<?=$totalCols?>" align="center" height="28" style="font-size:18px;">No Record Available</td>
    </tr>
    <?php
}
?>
</table>

