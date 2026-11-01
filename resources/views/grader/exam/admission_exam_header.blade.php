<?php
use App\sessions;
$sess = new sessions();
$sess->startSession();
// echo "in blade".$adm_sch_id;
// exit;

?>

@extends('layouts.app')

@section('title')
    BNU CMS
@stop

@section('content')
<?php

// function my_simple_crypt( $string, $action = 'd' ) {
//   // you may change these values to your own
//   $secret_key = 'my_simple_secret_key';
//   $secret_iv = 'my_simple_secret_iv';

//   $output = false;
//   $encrypt_method = "AES-256-CBC";
//   $key = hash( 'sha256', $secret_key );
//   $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

//   if( $action == 'e' ) {
//       $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
//   }
//   else if( $action == 'd' ){
//       $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
//   }

//   return $output;
// }

//  $fc_co_idd=my_simple_crypt($fc_co_id, 'd' );

?>

<style>
    .online-exam-card {
        border: 0;
        border-radius: 0;
        background-color: #fff;
        margin: 12px;
        width: calc(100% - 24px);
    }

    .online-exam-header {
        background-color: #2F3B4D !important;
        color: #fff;
        padding: 12px 18px;
        border-radius: 0 !important;
    }

    .online-exam-header h3 {
        margin: 0;
        font-family: 'Times New Roman', Times, serif;
        font-size: 20px;
        font-weight: 600;
        line-height: 1.4;
    }

    .online-exam-body {
        padding: 18px;
        background-color: #fff;
    }

    .online-exam-inner {
        border: 1px solid #eeeeee;
        border-radius: 6px;
        background-color: #fff;
        padding: 20px 22px 28px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
    }

    .online-exam-banner {
        padding-bottom: 22px;
        margin-bottom: 28px;
        border-bottom: 1px solid #eeeeee;
    }

    .online-exam-banner img {
        /* width: 260px; */
        max-width: 100%;
        /* height: auto; */
        display: block;
    }

    .online-exam-actions {
        display: flex;
        align-items: stretch;
        justify-content: center;
        gap: 42px;
        padding: 8px 10px 36px;
    }

    .online-exam-action-card {
        width: 260px;
        min-height: 245px;
        border: 1px solid #e5e5e5;
        border-radius: 6px;
        background-color: #fff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #007bff;
        text-decoration: none;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .online-exam-action-card:hover,
    .online-exam-action-card:focus {
        color: #0056b3;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 5px 14px rgba(0, 0, 0, 0.16);
    }

    .online-exam-action-card.create-card {
        border-bottom: 4px solid #007bff;
    }

    .online-exam-action-card.result-card {
        border-bottom: 4px solid #28a745;
    }

    .online-exam-icon-wrap {
        width: 115px;
        height: 115px;
        border-radius: 50%;
        background-color: #eef6ff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 18px;
    }

    .online-exam-icon-wrap img {
        max-width: 100px;
        max-height: 100px;
        height: auto;
        display: block;
    }

    .online-exam-action-card.result-card .online-exam-icon-wrap {
        background-color: #f3fff7;
    }

    .online-exam-action-card span {
        display: block;
        font-size: 22px;
        line-height: 1.2;
        margin-bottom: 10px;
    }

    .online-exam-action-card p {
        margin: 0;
        color: #4f5f73;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.5;
        max-width: 200px;
    }

    .online-exam-divider {
        width: 1px;
        background-color: #eeeeee;
        margin: 32px 0;
    }

    @media (max-width: 767px) {
        .online-exam-card {
            margin: 8px;
            width: calc(100% - 16px);
        }

        .online-exam-body {
            padding: 12px;
        }

        .online-exam-inner {
            padding: 14px;
        }

        .online-exam-banner img {
            width: 220px;
        }

        .online-exam-actions {
            flex-direction: column;
            align-items: center;
            gap: 18px;
            padding-bottom: 18px;
        }

        .online-exam-divider {
            display: none;
        }

        .online-exam-action-card {
            width: 100%;
            max-width: 280px;
            min-height: 220px;
        }
    }
</style>

<div class="card online-exam-card" style="
    margin-bottom: 50px;
">
    {{-- <div class="card-header online-exam-header">
        <h3>Online Exam System</h3>
    </div> --}}

    <style>
        .online-exam-banner {
    width: 100%;
    overflow: hidden;
}

.online-exam-banner img {
    width: 100%;
    max-width: 100%;
    /* height: auto; */
    display: block;
    object-fit: contain;
}
    </style>
    <div class="card-body online-exam-body" style="padding: 0px;">
        <div class="online-exam-inner">
            <div class="online-exam-banner">
                <img title="Online Exam System" src="{{ asset('online-exam.png') }}" alt="Online Exam System" width="100%" height="120">
            </div>

            <div class="online-exam-actions">
                <a href="{{ route('teacher/adm_online_exam') }}/<?= $adm_sch_id ?>" class="online-exam-action-card create-card">
                    <div class="online-exam-icon-wrap">
                        <img title="Create Exam" src="{{ asset('exam33.png') }}" alt="Create Exam">
                    </div>
                    <span>Create Exam</span>
                    <p>Create a new exam, manage questions and publish.</p>
                </a>

                <div class="online-exam-divider"></div>

                <a href="{{ route('teacher/adm_online_result') }}/<?= $adm_sch_id ?>" class="online-exam-action-card result-card">
                    <div class="online-exam-icon-wrap">
                        <img title="Result" src="{{ asset('online.png') }}" alt="Result">
                    </div>
                    <span>Result</span>
                    <p>View and manage exam results, performance and reports.</p>
                </a>
            </div>
        </div>
    </div>
</div>

@stop

@section('pageScripts')