<?php

//print_r($obj);exit();

// echo public_path().'/fpdf/fpdf.php'; exit;

error_reporting(0);

include(public_path().'/fpdf/fpdf.php');


class PDF extends FPDF   // Inheriting the parent Class
{
    public $feeObj;

    public $oneRowSize = 90;

    public $paidAmt = 0;

    //Page header
    function Header(){
        global $assess,$cou_name,$total_assessments,$teacher_name,$dept_name,$paidAmt;

      //  print_r($this->feeObj); exit;

        $this->paidAmt 		= $this->fee_format($this->feeObj['paidamt']);
        $this->PaidOn 		= $this->feeObj['paidon'];


        $this->SetX(0);
        $this->SetY(0);

        $this->SetX(3);
        $this->SetY(0);
        $logo 	= 5;
        $a 		= 1;

        while($a<4){
            if($a == 1){
                $logo = 5;
            }elseif($a == 2){
                $logo = 100;
            }elseif($a == 3){
                $logo = 195;
            }

            if($this->PaidOn!=''){
                $a=3;
                $logo = 100;

            }

            $this->Cell($this->oneRowSize,22,'',0,0,'L',false);
            $this->Cell(5,5,'',0,0,'C',false);
            $this->Image(public_path().'/fpdf/logoimages/bnulogo1.jpg',$logo+5,1.5,10,12);
            $this->SetFont('times','B',10);
            $this->text($logo+28, 5, 'Beaconhouse National University'.$paidAmt);
            //$this->SetX(0);
            //$this->Cell(1, 5,'Beaconhouse National University'.$logo,1,0,'C',false);
            $this->SetFont('times','',8);
            $this->text($logo+26, 9, '13-km Raiwind Road, Lahore 54400, Pakistan');
            $this->text($logo+16, 13, 'Phone: 042-38100156 Ext:473 email: accounts.finance@bnu.edu.pk');
            //  $this->text($logo+22, 25, 'email: accounts.finance@bnu.edu.pk');
            $this->SetFont('times','',10);
            $this->text($logo+45, 17, 'NTN: 4098876-7');




            $a++;
        }

        if($this->PaidOn!=''){
            $this->SetFont('times','',49);
            $this->SetTextColor(220,50,50);
            $this->SetXY(50,30);
            // $this->Rotate(30,80,80);
            $loop=1;
            $this->Cell(200, 150, "ALREADY PAID",0, false, 'C');
           // $this->Rotate(0);
            $this->Ln(25);

        }



    }

    //Page footer
    function Footer(){


        $this->SetX(3);
        $this->SetY(28);
        $logo = 2;
        $a = 1;
        $entity_text = '';
        while($a<4){
            if($a == 1){
                $logo = 5;
                $entity_text = 'BANK COPY';
            }elseif($a == 2){
                $logo = 100;
                $entity_text = 'ACCOUNTS COPY';
            }elseif($a == 3){
                $logo = 195;
                $entity_text = 'STUDENT COPY';
            }
            if($this->PaidOn!=''){
                $a=3;
                $logo = 100;
                $entity_text = 'STUDENT COPY';

            }
            $this->SetXY($logo+5,203);
            $this->SetFont('Arial','B',10);
            $this->Cell(90, 5, $entity_text, 0, 0, 'C');
            $this->SetFont('times','',8);
            $this->text($logo+11, 209, 'This is a system generated Fee Challan Signatures not required');
            $a++;

        }
    }

    function student_info(){
        $this->student_info_inner();
    }

    function getFormatedDate($date){
        return  \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date )->format('d M Y');
    }

    function student_info_inner(){
        //echo $this->feeObj['issuedate']; exit;
// 2019-08-05 00:00:00




        $issueDate 	= $this->getFormatedDate($this->feeObj['issuedate']);
        $session 	= $this->feeObj['session_id'];
        $studentID 	= $this->feeObj['pvnoslipnostudentid'];
        $challanNo	= $this->feeObj['buyercodechallanno'];
        $registerNo	= $this->feeObj['regno'];
        $studentName 		= $this->feeObj['studentname'];
        $parentGuardianName = $this->feeObj['parentguardianname'];
        $parentGuardiancnic = $this->feeObj['parentguardiancnic'];
        $program 	= $this->feeObj['program'];
        $school 	= $this->feeObj['school'];
        $department = $this->feeObj['department'];

        // echo $issueDate;


        $this->SetX(3);
        $logo = 5;
        // $count = 1;
        $code_txt = '';
        $studentID_txt = '';
        $a = 1;
        while($a<4){
            if($a == 1){
                $logo 			= 5;
                $code_txt 		= 'Buyer Code:';
                $studentID_txt 	= 'PV # / Slip #:';
            }elseif($a == 2){
                $logo 			= 100;
                $code_txt 		= 'Challan No:';
                $studentID_txt 	= 'Student ID:';
            }elseif($a == 3){
                $logo 			= 195;
                $code_txt 		= 'Challan No:';
                $studentID_txt 	= 'Student ID:';
            }

            $cell_height = 5.5;
            $start_height = 12;
            if($this->PaidOn!=''){
                $a=3;
                $logo = 100;

            }


            // 1
            $this->SetXY($logo+4,$start_height+($cell_height*1));
            $this->SetFont('times','B',9);
            $this->Cell(25, $cell_height, 'Issue Date', 0, 0, 'L');
            $this->Cell(20, $cell_height, $issueDate, 'B', 0, 'L');
            // 1
            $this->SetXY($logo+50,$start_height+($cell_height*1));
            $this->Cell(25, $cell_height, 'Session', 0, 0, 'C');
            $this->SetFont('times','',8);
            $this->Cell(20, $cell_height, $session, 'B', 1, 'L');
            // 2
            $this->SetFont('times','B',9);
            $this->SetXY($logo+4,$start_height+($cell_height*2));
            $this->Cell(25, $cell_height, $code_txt, 0, 0, 'L');
            $this->SetFont('times','',9);
            $this->Cell(20, $cell_height, $challanNo, 'B', 0, 'L');
            // 2
            $this->SetFont('times','B',9);
            $this->SetXY($logo+50,$start_height+($cell_height*2));
            $this->Cell(25, $cell_height, ' Reg #.', 0, 0, 'C');
            $this->SetFont('times','',9);
            $this->Cell(20, $cell_height, $registerNo, 'B', 0, 'L');

            // 325
            $this->SetXY($logo+4,$start_height+($cell_height*3));
            $this->SetFont('times','B',9);
            $this->Cell(25, $cell_height, $studentID_txt, 0, 0, 'L');
            $this->SetFont('times','',9);
            $this->Cell(30, $cell_height, $studentID, 'B', 0, 'L');
            // 4
            $this->SetXY($logo+4,$start_height+($cell_height*4));
            $this->SetFont('times','B',9);
            $this->Cell(25, $cell_height, 'Name:', 0, 0, 'L');
            $this->SetFont('times','',9);
            $this->Cell(60, $cell_height, $studentName, 'B', 0, 'L');
            // 5
            $this->SetXY($logo+4,$start_height+($cell_height*5));
            $this->SetFont('times','B',9);
            $this->Cell(25, $cell_height, 'Parent/Guardian:', 0, 0, 'L');
            $this->SetFont('times','',9);
            $this->Cell(34, $cell_height, $parentGuardianName, 'B', 0, 'L');
            $this->Cell(2, $cell_height,'',0, 0, 'L');
            $this->Cell(24, $cell_height, $parentGuardiancnic, 'B', 0, 'R');
            $yyy = 46;
            $cell_height = 6;
            $this->SetXY($logo+4,$yyy);
            $this->SetFont('times','B',9);
            $this->Cell(25, $cell_height,'Programme:', 0, 0, 'L');
            $this->SetFont('times','',9);
            if(strlen(trim($program))>45){
                $this->Cell(60,9,trim($program), 'B', 0, 'L',false);
            }else{
                $this->Cell(60, 8,trim($program), 'B', 0, 'L');
            }


            $cell_height = 5.9;


            // 7
            $kkkk = $start_height+($cell_height*7)+1;

            $kkkk = 54.2;
            $this->SetXY($logo+4,$kkkk);
            $cell_height = 10;
            $this->SetFont('times','B',9);
            $this->Cell(25, $cell_height, 'School:', 0, 0, 'L');
            $this->SetFont('times','',9);
            if($school!=''){
                $this->Cell(60, $cell_height, trim($school), 'B', 0, 'L',false);
            }else{
                $this->Cell(60, $cell_height,trim($school), 'B', 0, 'L');
            }


            //$this->Cell(60, $cell_height, $school, 'B', 0, 'L');


            $cell_height = 7;


            // 8
            $this->SetXY($logo+4,$start_height+($cell_height*7)+3);
            $cell_height = 9;
            $this->SetFont('times','B',9);
            $this->Cell(25, $cell_height, 'Department:', 0, 0, 'L');
            $this->SetFont('times','',9);
            if($department!=''){
                $this->Cell(60, $cell_height,trim($department), 'B', 0, 'L',false);
            }else{
                $this->Cell(60, $cell_height,trim($department), 'B', 0, 'L');

            }
            //$this->MultiCell(60, $cell_height, $department, 'B', 0, 'L');


            $cell_height = 6;

            $a++;



        } // while end

        $this->fee_table();

    } // function end



    function fee_format($amount){
        return number_format($amount);
    }


    function fee_table(){

        

       
        $adminFee 	= $this->fee_format($this->feeObj['admfee']);
        $security 	= $this->fee_format($this->feeObj['secfee']);
        $MedicalInsurance 	= $this->fee_format($this->feeObj['medicalinsurance']);
        $NeedBaseP 	= $this->fee_format($this->feeObj['needbasep']);
        $MeritBaseP 	= $this->fee_format($this->feeObj['meritbasep']);
        $tutionFee 	= $this->fee_format($this->feeObj['tutionfee']);
        $scholarships 		= $this->fee_format($this->feeObj['scholarships']);
        $discounts 			= $this->fee_format($this->feeObj['discounts']);
        $shortCourseFee 	= $this->fee_format($this->feeObj['shortcoursefee']);
        $hostelFee 			= $this->fee_format($this->feeObj['hostelfee']);
        $workshopfee 		= $this->fee_format($this->feeObj['workshopfee']);
        $miscellaneous 		= $this->fee_format($this->feeObj['miscellaneous']);
        $totalAmountPayable = $this->fee_format($this->feeObj['totalamountpayable']);
        $incomeTax 		= $this->fee_format($this->feeObj['incometaxus236i']);
        // BSSEmpPerc
        $bssEmpPerc 	= $this->fee_format($this->feeObj['bssempperc']);
        // BNUEmpPerc
        $bnuEmpPerc 	= $this->fee_format($this->feeObj['bnuempperc']);
        // InstDiscPerc
        $instDiscPerc 	= $this->fee_format($this->feeObj['instdiscperc']);
        // Discounts
        $discounts 	= $this->fee_format($this->feeObj['discounts']);

        $needBasedAmtTotal 		= $this->fee_format($this->feeObj['needbasedamttotal']);
        $meritBasedAmtTotal 	= $this->fee_format($this->feeObj['meritbasedamttotal']);
        $bnuEmpAmtTotal 		= $this->fee_format($this->feeObj['bnuempamttotal']);
        $bssEmpAmtTotal 		= $this->fee_format($this->feeObj['bssempamttotal']);
        $instDiscAmt 			= $this->fee_format($this->feeObj['instdiscamt']);



        $logo 		= 0;
        $a 			= 1;
        while($a<4){
            if($a == 1){
                $logo = 10;
            }elseif($a == 2){
                $logo = 105;
            }elseif($a == 3){
                $logo = 200;
            }
            // $this->SetXY($logo,68);



            if($this->PaidOn!=''){
                $a=3;
                $logo = 105;

            }
            $tableY = 74;

            $this->SetXY($logo,$tableY);
            $this->Cell(52, 84, '', 1, 0, 'L');
            $this->Cell(10, 84, '', 1, 0, 'L');
            $this->Cell(10, 84, '', 1, 0, 'C');
            $this->Cell(18, 84, '', 1, 0, 'L');


            $this->SetXY($logo,$tableY);

            $this->SetFont('times','B',8);
            $this->Cell(52, 7, 'Particulars', 1, 0, 'L');

            if($this->feeObj['installmentno'] > 0){
                $this->text($logo+22, $tableY+4, 'Inst # - '.$this->feeObj['installmentno']);
            }

            $this->Cell(10, 7, 'Sch.%', 1, 0, 'L');
            $this->Cell(10, 7, 'Cr.Hrs.', 1, 0, 'C');
            $this->Cell(18, 7, 'Amount (Rs)', 1, 0, 'L');


            $cell_height = 3;
            // $start_height = 70;


            $start_height = 76;

            $this->SetFont('times','',9);


            $this->SetXY($logo,$start_height+($cell_height*2));


            if($adminFee>0){
                $this->Cell(46, $cell_height, 'Admission Fee', 0, 0, 'L');
                $this->Cell(22, $cell_height, '', 0, 0, 'R');
                $this->Cell(22, $cell_height, $adminFee, 0,1, 'R');
            }
            $this->SetX($logo);
            if($security>0){
                $this->Cell(46, $cell_height,'Security Deposit', 0, 0, 'L');
                $this->Cell(22, $cell_height, '', 0, 0, 'C');
                $this->Cell(22, $cell_height, $security, 0,1, 'R');
            }
          // $this->Cell(22, $cell_height, '', 1, 1, 'C');
            $cell_height = 10;

            $sub_array = $this->feeObjDetails;

            $courseX = 1;
            $courseY = 1;
            $course = 0;

//            $this->SetXY($logo,$start_height+($cell_height*1));
           
            
            foreach($sub_array as $rec_details){

                //if($a<2)
                {

             $show_course_title = substr($rec_details->course_title,0,36);


//                $this->SetXY($logo,$start_height+($cell_height*1));

                if(strlen($show_course_title)<20){
                     $course_title_height =3.5;
                }else if(strlen($show_course_title)<40){
                    $course_title_height = 8;
                }else if(strlen($show_course_title)<60){
                    $course_title_height = 8;
                }else if(strlen($show_course_title)<80){
                    $course_title_height = 10;    
                }else {
                    $course_title_height = 11;
                }
                $course_title_height = 4;
               // $course_title_height = strlen($rec_details->course_title);
            // if($a<2){
            //     $this->SetX($logo);
            // }else{
            //     $this->SetX($logo+40);
            // }
             
            $this->SetX($logo);
            $this->SetFont('times','',9);

            //    //  $this->MultiCell($course*45, $course_title_height,strlen($rec_details->cmstitle)."__".$rec_details->cmstitle,'', 0, 'FJ',false);
            //     //$this->Cell(45, $cell_height,$rec_details->course_title, 0, 0, 'C');

                //$show_course_title = $this->GetX()."__".$a."__".count($sub_array)."_".$rec_details->cmstitle;
                //$show_course_title = $rec_details->course_title;
                 //$show_course_title = $rec_details->course_title;
                 $this->SetFillColor(202,191,191);



               
              $this->Cell(45,$course_title_height,$show_course_title, 0, 0, 'L');
            //     $this->MultiCell(45, $course_title_height,$course_title_height."__>__".strlen($rec_details->cmstitle)."__".$rec_details->cmstitle,'',0,1, 'FJ',false);
            //  $this->MultiCell(45, $course_title_height,$show_course_title,'', 0, 'FJ',false);
            //     $this->Cell(22,$course_title_height,'<>', 1, 1, 'C');

            //     //---------------------------
                 $this->Cell(17, $course_title_height,'',0, 0, 'R');

            //     $this->SetFillColor(229,255,204);
                 $this->Cell(10, $course_title_height,$rec_details->credithours,0, 0, 'C');
            //     $this->SetFillColor(229,191,191);
                 $this->Cell(18, $course_title_height,$this->fee_format($rec_details->total), 0,1, 'R');

                 $course++;
                
               //  $this->SetXY($logo, $start_height+( $course*15)   );
               }
            }


            // $this->SetFillColor(255,255,255);


            $sub_array = $this->feeObjDetails;
            // if($this->feeObj['course1_title']!=""){
            
            
            
            // }
           
           
           
           
            // 1

            $cell_height=3;
            
            $this->SetX($logo);
            if($tutionFee>0){
                $this->Cell(46, $cell_height, 'Tuition Fee', 0, 0, 'L');
                $this->Cell(22, $cell_height, '', 0, 0, 'R');
                $this->Cell(22, $cell_height, $tutionFee, 0,1, 'R');
            }
            //------------------------------
               
          
             if($shortCourseFee>0){
                 $this->SetX($logo);
                 $this->Cell(46, $cell_height, 'Short Course Fee', 0, 0, 'L');
                 $this->Cell(22, $cell_height, '', 0, 0, 'R');
                 $this->Cell(22, $cell_height, $shortCourseFee, 0,1, 'R');
             }
//------------------------------
            if($hostelFee != 0){
                $this->SetX($logo);
                $this->Cell(46, $cell_height, 'Hostel Fee', 0, 0, 'L');
                $this->Cell(22, $cell_height, '', 0, 0, 'R');
                $this->Cell(22, $cell_height, $hostelFee, 0, 1, 'R');
				$this->Ln();
            }
            $cell_height=3;
            if(($needBasedAmtTotal) || ($meritBasedAmtTotal)  || ($bnuEmpAmtTotal)   || ($bssEmpPerc)|| ($discounts) || ($instDiscPerc)  || ($instDiscAmt) || ($bssEmpAmtTotal) || ($bnuEmpAmtTotal) || ($meritBasedAmtTotal) || ($NeedBaseP)){
                $this->SetX($logo);
                $this->SetFont('times','B',9);
                $this->Cell(46, $cell_height, 'Less Scholarship', 0, 0, 'L');
                $this->Cell(22, $cell_height, '', 0, 0, 'R');
                $this->Cell(22, $cell_height, '', 0, 1, 'R');
            }




            $cell_height=3;

            $logo_addwidth = 7;
            if($NeedBaseP != 0 or $needBasedAmtTotal != 0){
                $this->SetX($logo+$logo_addwidth);
                $this->SetFont('times','',9);
                $this->Cell(39, $cell_height, 'Need Based',0, 0, 'L');
                $this->SetFont('times','',8);
                $this->Cell(22, $cell_height,(($NeedBaseP > 0)?$NeedBaseP.'%':''), 0, 0, 'C');
                $this->SetFont('times','',9);
                $this->Cell(22, $cell_height, $this->minusMsg($needBasedAmtTotal), 0, 1, 'R');
            }





            if($meritBasedAmtTotal != 0 or $meritBasedAmtTotal != 0){
                $this->SetFont('times','',9);
                $this->SetX($logo+$logo_addwidth);
                $this->Cell(39, $cell_height, 'Merit Based', 0, 0, 'L');
                $this->SetFont('times','',8);
                $this->Cell(22, $cell_height,(($MeritBaseP > 0)?$MeritBaseP.'%':'') /*$MeritBaseP.'%' */, 0, 0, 'C');
                $this->SetFont('times','',9);
                $this->Cell(22, $cell_height, $this->minusMsg($meritBasedAmtTotal), 0, 1, 'R');
            }




            if($bnuEmpAmtTotal != 0 or $bnuEmpPerc!=0){
                $this->SetFont('times','',9);
                $this->SetX($logo+$logo_addwidth);
                $this->Cell(39, $cell_height, 'BNU Employee Ward', 0, 0, 'L');
                $this->SetFont('times','',8);
                $this->Cell(22, $cell_height, (($bnuEmpPerc > 0)?$bnuEmpPerc.'%':'') /* $bnuEmpPerc.'%' */, 0, 0, 'C');
                $this->SetFont('times','',9);
                $this->Cell(22, $cell_height, $this->minusMsg($bnuEmpAmtTotal), 0,1, 'R');
            }




            // -----------------------------
            if($bssEmpAmtTotal != 0 or $bssEmpPerc != 0){
                $this->SetFont('times','',9);
                $this->SetX($logo+$logo_addwidth);
                $this->Cell(39, $cell_height, 'BSS Employee Ward', 0, 0, 'L');
                $this->SetFont('times','',8);
                $this->Cell(22, $cell_height, (($bssEmpPerc > 0)?$bssEmpPerc.'%':'') /* $bssEmpPerc.'%' */ , 0, 0, 'C');
                $this->SetFont('times','',9);
                $this->Cell(22, $cell_height, $this->minusMsg($bssEmpAmtTotal), 0, 1, 'R');
            }



            // -----------------------------
            if($instDiscPerc != 0 or $instDiscAmt != 0){
                $this->SetFont('times','',9);
                $this->SetX($logo+$logo_addwidth);
                $this->Cell(39, $cell_height, 'Institutional Discount', 0, 0, 'L');
                $this->SetFont('times','',8);
                $this->Cell(22, $cell_height,(($instDiscPerc > 0)?$instDiscPerc.'%':'') /* $instDiscPerc.'%' */ , 0, 0, 'C');
                $this->SetFont('times','',9);
                $this->Cell(22, $cell_height, $this->minusMsg($instDiscAmt), 0,1, 'R');
            }




            // -----------------------------
            if($discounts != 0){
                $this->SetFont('times','',9);
                $this->SetX($logo+$logo_addwidth);
                $this->Cell(39, $cell_height, 'Others', 0, 0, 'L');
                $this->Cell(22, $cell_height,'', 0, 0, 'C');
                $this->Cell(22, $cell_height,$discounts, 0, 1, 'R');
            }



            
            //------------------------
            if($MedicalInsurance>0 && $MedicalInsurance!=''){
                $this->SetX($logo);
                $this->Cell(46, $cell_height, 'Medical Insurance', 0, 0, 'L');
                $this->Cell(22, $cell_height, '', 0, 0, 'R');
                $this->Cell(22, $cell_height, $MedicalInsurance, 0,1, 'R');
            }
            //------------------------
            if($miscellaneous != 0){
                $this->SetX($logo);
                $this->Cell(46, $cell_height, 'Miscellaneous', 0, 0, 'L');
                $this->Cell(22, $cell_height, '', 0, 0, 'R');
                $this->Cell(22, $cell_height, $miscellaneous, 0,1, 'R');
            }
            //------------------------
            if($workshopfee != 0){
                $this->SetX($logo);
                $this->Cell(46, $cell_height, 'Workshop Fee', 0, 0, 'L');
                $this->Cell(22, $cell_height, '', 0, 0, 'R');
                $this->Cell(22, $cell_height, $workshopfee, 0,1, 'R');
            }
            //------------------------
            if($incomeTax != 0){
                $this->SetX($logo);
                $this->Cell(46, $cell_height, 'Income Tax (u/s  236 I)', 0, 0, 'L');
                $this->Cell(22, $cell_height, '', 0, 0, 'R');
                $this->Cell(22, $cell_height, $incomeTax, 0,1, 'R');
            }




            // $start_height+($cell_height*16.4)

            $cc = $start_height+($cell_height*16.4);

            $cc = 151;
            $this->SetXY($logo,$cc);
            $this->SetFont('times','B',10);
            $this->Cell(52, 7, 'Total Amount Payable', 1, 0, 'L');
            $this->Cell(10, 7, '', 1, 0, 'L');
            $this->Cell(10, 7, '', 1, 0, 'L');
            $this->SetFont('times','B',9);
            $this->Cell(18, 7, $totalAmountPayable, 1, 0, 'R');
            $a++;

        } // while end


        $this->add_dd_po();

    } // fun



    function minusMsg($amount){
        if($amount < 0){
            return '('. str_replace('-','',$amount).')';
        }else{
            return $amount;
        }
    }



    function add_dd_po(){


        $dueDate 	=  $this->getFormatedDate($this->feeObj['duedate']); // (isset($this->feeObj['duedate']))?$this->feeObj['duedate']->format('d-M-Y'):'';
        $validDate 	=  $this->getFormatedDate($this->feeObj['validdate']); // (isset($this->feeObj['validdate']))?$this->feeObj['validdate']->format('d-M-Y'):'';
        $remarks	= $this->feeObj['remarks'];
        $logo 		= 0;
        $a 			= 1;
        // $y = 139;



        while($a<4){
            $y = 160;

            if($a == 1){
                $logo = 5;
            }elseif($a == 2){
                $logo = 100;
            }elseif($a == 3){
                $logo = 195;
            }
            $dd_no_start=7;
            if($this->PaidOn!=''){
                $a=3;
                $logo = 100;
                $dd_no_start=107;
            }


            $cell_height = 7;
            // $this->SetXY($logo+5,139);

            $this->SetXY($logo+5,$y);

            $this->SetFont('times','B',9);
            //$this->Cell(20, $cell_height, 'DD/PO No:', 1, 0, 'L');

            $box = 1;
            while($box < 11){
                //$this->Cell(7, $cell_height,'', 1, 0, 'R');
                $box++;
            }

            if($this->PaidOn!=''){
                $a=3;
                $logo = 100;
            }


            //$this->SetXY($logo+5,142);
            $this->Cell(20, 5, 'Due Date:', 0, 0, 'L');
            $this->Cell(20, 5, $dueDate, 'B', 0, 'L');
            // 1
            // $this->SetXY($logo+7,163);
            $this->Cell(10, 5, '', 0, 0, 'C');
            $this->Cell(20, 5, 'Valid Date:', 0, 0, 'C');
            $this->Cell(20, 5, $validDate, 'B', 1, 'L');


            $y = 155;
            $this->SetFont('times','',13.5);
			$this->SetXY($logo+6,$y+11);
			$color_set = 155;
			$this->SetFillColor($color_set, $color_set, $color_set);
			$this->Cell(89, 5, '', 0, 0, 'L',true);
$this->text($logo+6, $y+15, 'Payable at any Branch of UBL & Askari Bank ');

// Fee to be deposited in:
$this->SetFont('times','B',9);
			$this->SetXY($logo+5,$y+17);
			$this->Cell(18,4, 'Fee to be', 0, 1, 'L');
			$this->SetX($logo+5);
			$this->Cell(18,3, 'Deposited in:',0, 1, 'L');
			//$this->text($logo+5, $y+13, 'Fee to be');
			//$this->text($logo+5, $y+16, 'deposited in:');
			$this->SetFont('times','B',8);
			$this->SetXY($logo+24,$y+18);
			$color_set = 155;
			$this->SetFillColor($color_set, $color_set, $color_set);
			$this->Cell(71,3, '1) UBL A/c No 222858368',0, 1, 'L',true);
			$this->SetFont('times','B',7);
			$this->SetXY($logo+24,$y+22);
			$color_set = 155;
			$this->SetFillColor($color_set, $color_set, $color_set);
			$this->Cell(71,3, '2) Askari Bank All Collection Will Be Processed Through ACMS',0, 1, 'L',true);

            $this->Ln(100);

            // Modes of Payment: // Tu jey mere hamesha kol rawen
            $this->SetXY($logo+5,$y+27);
			
			$this->SetFont('times','B',9);
			$this->Cell(30,4,'Modes of', 0, 1, 'L');
			$this->SetX($logo+5);
			$this->Cell(30,2,'Payment : ',0, 0, 'L');
			//$this->text($logo+5, $y+24, ' Payment : ');
			//$this->text($logo+5, $y+29, '222');
			$this->SetFont('times','',7.5);
			$this->text($logo+24, $y+30, '1) Cash at UBL Counter (BNU Premises)');
			$this->text($logo+24, $y+33, '2) DD.PO or visa/Master Card (+ Bank Charges) At BNU Premises.');


            //	$this->Cell(135,22,'',1,0,'L',false);
            //	$this->Cell(10,10,'',1,0,'C',false);

            // $this->SetXY($logo+5,170);
            $this->SetXY($logo+6,$y+35);
			$color_set = 155;
			$this->SetFillColor($color_set, $color_set, $color_set);
			$this->Cell(89, 9, '', 0, 0, 'L',true);

			$this->SetFont('Times','',7.5);
			//$this->MultiCell(60, $cell_height,'Fine @ Rs.100/- per day after Due Date ' , 'B', 0, 'L',false);
			$this->text($logo + 6, $y+37, 'Fine @ Rs.100/- per day after Due Date After the "Valid Date" student will be ');
            $this->text($logo + 6, $y+40, 'Automatically de-enrolled.');
			// $this->text($logo + 6, $y+40, 'Automatically de-enrolled For re-registration student will have to pay Rs.10,000');

			// $this->text($logo + 6, $y+43, 'withholding tax (section 236 I )');

            //$this->text($logo + 5, 181, 'have to pay Rs.10,000 + withholding tax (section 236 I)');
            $this->SetFont('Arial','',7.8);
			$this->SetXY($logo+5,$y+44);
			$this->Cell(90, 5, $remarks, 0, 0, 'L');

            /*

                        $stampY = 124;
                        $x_movement = 40.5;
                        $this->SetXY($logo+$x_movement,$stampY);
                        //$this->SetFillColor(226, 226, 208);

                        $color_set = 155;
                        $this->SetFillColor($color_set, $color_set, $color_set);

                        $this->Cell(42,24, '', 0, 0, 'L',true);
                        $this->SetXY($logo+$x_movement,$stampY);
                        $this->SetFont('times','',10);
                        $this->Cell(42,8, 'ONLY PAYABLE AT', 0, 0, 'C');
                        $this->SetXY($logo+$x_movement,$stampY+7);
                        $this->SetFont('times','B',12);
                        $this->Cell(42,8, 'UBL', 0, 0, 'C');
                        $this->SetXY($logo+$x_movement,$stampY+15);
                        $this->SetFont('times','B',14);
                        $this->Cell(42,8, 'BRANCHES', 0, 0, 'C');

                    */
            $a++;

        }

        // $this->SetXY($logo+5,163);
    }


}



$pdf = new PDF('L','mm','A4');
//$pdf->SetLeftMargin(1);
$pdf->SetLineWidth(0.4);
$pdf->SetAutoPageBreak(false);



$pdf->feeObj = (array) $obj[0];
$pdf->feeObjDetails = (array) $details;
$pdf->AddPage();
$pdf->student_info();

//while( $row = sqlsrv_fetch_array(  $stmt, SQLSRV_FETCH_ASSOC )) {
//    $pdf->feeObj = $row;
//    $pdf->AddPage();
//    $pdf->student_info();
//    // print_r($row);
//}



$pdf->output();
exit;

?>