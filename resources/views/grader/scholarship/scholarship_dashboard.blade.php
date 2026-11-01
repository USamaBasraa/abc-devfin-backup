@extends('layouts.app')
@section('title')
    BNU CMS
@stop
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@section('content')
<style>
    /* Custom Styles for BNU CMS */

/* General Styles */

/* Sidebar Styles */
.SDU-sidebar {
    background-color: #FFFFFF;
    min-height: 100vh;
    box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
    border-radius: 0;
}

@media (min-width: 992px) {
    .SDU-sidebar {
        flex: 0 0 280px;
        max-width: 280px;
        position: sticky; /* keep sidebar fixed while scrolling */
        top: 0;
        height: 100vh;
        z-index: 150;
    }

    .SDU-main-content-area {
        flex: 1 1 auto;
        max-width: 100%;
    }

    /* Mini state (desktop) - show compact sidebar similar to sample */
    body.SDU-sidebar-mini .SDU-sidebar {
        flex: 0 0 80px;
        max-width: 80px;
        overflow: hidden;
    }

    body.SDU-sidebar-mini .SDU-main-content-area {
        max-width: 100%;
    }
}

.SDU-sidebar-container {
    padding: 24px 20px;
    height: 100vh;
    position: sticky;
    top: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Sidebar header stays visible while the menu scrolls */
.SDU-sidebar-header {
    position: sticky;
    top: 0;
    background: #FFFFFF;
    z-index: 100;
    /* Ensure solid block; prevent bleed-through */
    margin: 0 !important;
    padding: 24px 20px 16px !important;
    padding-top: 0px !important;
    flex: 0 0 10%;
}

/* Middle scrollable area */
.SDU-sidebar-scroll {
    flex: 1 1 auto;
    overflow-y: auto;
    padding-bottom: 12px;
    /* Firefox */
    scrollbar-width: thin;
    scrollbar-color: #CFCFCF transparent;
}

/* Footer fixed visually at bottom */
.SDU-sidebar-footer {
    background: #FFFFFF;
    border-top: 1px solid #E8E8E8;
    padding: 12px 0 0;
    position: sticky;
    bottom: 0;
    z-index: 90;
}

/* WebKit scrollbars (Chrome, Edge, Safari) scoped to sidebar only */
.SDU-sidebar-scroll::-webkit-scrollbar {
    width: 10px;
}

.SDU-sidebar-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.SDU-sidebar-scroll::-webkit-scrollbar-thumb {
    background-color: #CFCFCF;
    border-radius: 8px;
    border: 3px solid transparent; /* creates a gap to track for a softer look */
    background-clip: content-box;
}

.SDU-sidebar-scroll::-webkit-scrollbar-thumb:hover {
    background-color: #B8B8B8;
}

/* Logo Section - Using Bootstrap flex utilities in HTML, minimal custom CSS */
.SDU-logo-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #000 0%, #333 100%);
    border-radius: 8px;
    font-size: 24px;
    font-weight: bold;
    color: white;
    font-family: 'Times New Roman', serif;
    box-shadow: inset 2px 2px 4px rgba(255, 255, 255, 0.2);
}

/* Sidebar image logo (replacing .SDU-logo-icon) */
.SDU-logo-image {
    width: 46px;
    height: 69px; /* exact requested size */
    border-radius: 8px;
    display: block;
}

.SDU-logo-text {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    letter-spacing: 1px;
}

.SDU-toggle-image {
    width: 24px;
    height: 24px;
    object-fit: contain;
    cursor: pointer;
}

.SDU-sidebar-toggle {
    font-size: 28px;
    color: #666;
}

.SDU-sidebar-toggle:hover {
    color: #000;
}

/* Menu Sections */
.SDU-menu-section {
    padding-top: 8px;
}

.SDU-section-title {
    font-size: 11px;
    font-weight: 600;
    color: #B0B0B0;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.SDU-menu-separator {
    height: 1px;
    background-color: #E8E8E8;
    margin: 24px 0;
}

/* Navigation Menu */
.SDU-nav-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

/* Dropdown Parent */
.SDU-dropdown-parent {
    position: relative;
}

/* Nav Dropdown Toggle */
.SDU-nav-dropdown-toggle {
    cursor: pointer;
}

/* Submenu Styles */
.SDU-nav-submenu {
    display: none;
    padding-left: 20px;
    margin-top: 0;
    margin-bottom: 8px;
    position: relative;
}

.SDU-nav-submenu::before {
    content: '';
    position: absolute;
    left: 4px;
    top: 0;
    bottom: 0;
    width: 1px;
    background-color: #E0E0E0;
}

.SDU-dropdown-parent.SDU-expanded > .SDU-nav-submenu {
    display: block;
}

.SDU-dropdown-parent.SDU-expanded {
    margin-bottom: 4px;
}

.SDU-submenu-item {
    display: block;
    padding: 8px 0;
    padding-left: 20px;
    color: #666;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s ease;
    position: relative;
}

.SDU-submenu-item:hover {
    color: #333;
    text-decoration: none;
}

.SDU-submenu-item.SDU-active {
    color: #4555C3;
    font-weight: 600;
}

.SDU-submenu-item.SDU-active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background-color: #4555C3;
    border-radius: 0 2px 2px 0;
}

/* Rotate arrow when expanded - rotate 90 degrees clockwise to point down */
.SDU-dropdown-parent.SDU-expanded .SDU-nav-arrow-img {
    transform: rotate(90deg);
}

.SDU-nav-item {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    margin-bottom: 8px;
    color: #333;
    text-decoration: none;
    border-radius: 12px;
    transition: all 0.2s ease;
    position: relative;
    font-size: 15px;
    font-weight: 500;
}

.SDU-nav-item:hover {
    background-color: #F5F5F5;
    color: #333;
    text-decoration: none;
}

.SDU-nav-item.SDU-active {
    background-color: #4555C3;
    color: #FFFFFF;
}

.SDU-nav-item.SDU-active:hover {
    background-color: #4555C3;
    color: #FFFFFF;
}

.SDU-nav-icon {
    font-size: 20px;
    margin-right: 12px;
    width: 19px;      /* per spec */
    height: 22px;     /* per spec */
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 22px;
    text-align: center;
}

/* Image icon variant for nav items */
.SDU-nav-icon-img {
    width: 19px;      /* per spec */
    height: 22px;     /* per spec */
    margin-right: 12px;
    object-fit: contain;
    display: inline-block;
}

.SDU-nav-item.SDU-active .SDU-nav-icon {
    color: #FFFFFF;
}

.SDU-nav-text {
    flex: 1;
}

.SDU-nav-arrow {
    color: #B0B0B0;
    font-size: 20px;
    font-weight: normal;
    margin-left: auto;
}

.SDU-nav-item.SDU-active .SDU-nav-arrow {
    color: #FFFFFF;
}

/* Image replacement for right-side arrow */
.SDU-nav-arrow-img {
    width: 16px;
    height: 16px;
    margin-left: auto;
    object-fit: contain;
    opacity: 0.6;
}

.SDU-nav-item.SDU-active .SDU-nav-arrow-img {
    /* invert to appear light on active background */
    filter: invert(1);
    opacity: 1;
}

/* Mobile Menu Toggle */
.SDU-mobile-menu-toggle {
    position: fixed;
    top: 16px;
    left: 16px;
    background-color: #FFFFFF;
    border: none;
    width: 48px;
    height: 48px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    cursor: pointer;
    z-index: 999;
    display: none;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #333;
}

.SDU-mobile-menu-toggle:hover {
    background-color: #F5F5F5;
}

/* Header Menu Toggle */
.SDU-header-menu-toggle {
    background-color: #EBEBEB;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #333;
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 0;
}

.SDU-header-menu-toggle:hover {
    background-color: #E0E0E0;
}

.SDU-header-menu-toggle:focus {
    box-shadow: none;
    outline: none;
}

/* Overlay for Mobile */
.SDU-sidebar-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 998;
}

.SDU-sidebar-overlay.SDU-show {
    display: block;
}

/* Header Styles */
.SDU-header {
    background-color: #F5F5F5;
    border-bottom: 1px solid #E0E0E0;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    /* Keep header visible while scrolling main content */
    position: sticky;
    top: 0;
    z-index: 200;
}

/* Enforce header height to 144px while keeping horizontal padding */
.SDU-header .row {
    min-height: 96px;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
}

/* Session Selector - Using Bootstrap flex utilities in HTML */
.SDU-gap-3 {
    gap: 1rem;
}

.SDU-btn-session {
    background-color: #EBEBEB;
    border: none;
    border-radius: 9999px;
    padding: 12px 16px;
    height: 48px;
    color: #333;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    text-align: left;
    transition: all 0.2s ease;
}

.SDU-btn-session:hover {
    background-color: #E0E0E0;
    color: #333;
}

.SDU-btn-session:focus {
    box-shadow: none;
    outline: none;
}

/* Hide default Bootstrap caret since we use a custom chevron */
.SDU-btn-session.dropdown-toggle::after {
    content: none;
}

.SDU-session-chevron {
    font-size: 12px;
    color: #666;
    font-style: normal;
    margin-left: 12px;
}

/* Fixed width on large screens to match design */
@media (min-width: 992px) {
    .SDU-btn-session {
        width: 330px !important;
    }

    /* Prevent parent container from expanding */
    .flex-grow-1 {
        flex-grow: 0 !important;
        width: auto !important;
    }
}

/* Header Actions */
.SDU-header-actions {
    display: inline-flex;
    align-items: center;
    gap: 12px;
}

.SDU-btn-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background-color: #EBEBEB;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    cursor: pointer;
    padding: 0;
}

.SDU-btn-icon:hover {
    background-color: #E0E0E0;
}

.SDU-btn-icon:focus {
    box-shadow: none;
    outline: none;
}

.SDU-icon-envelope,
.SDU-icon-bell {
    font-size: 24px;
    color: #666;
}

/* Image variant for header icons (inside .SDU-btn-icon) */
.SDU-header-icon-img {
    width: 24px;
    height: 24px;
    object-fit: contain;
    display: block;
}

.SDU-btn-profile {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: none;
    padding: 0;
    overflow: hidden;
    cursor: pointer;
    background: none;
    transition: all 0.2s ease;
}

.SDU-btn-profile:hover {
    transform: scale(1.05);
}

.SDU-btn-profile:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
    outline: none;
}

.SDU-profile-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

/* Dropdown Styling */
.dropdown-menu {
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border-radius: 12px;
    padding: 8px 0;
    margin-top: 8px;
}

.dropdown-item {
    padding: 10px 20px;
    font-size: 14px;
    color: #333;
    transition: background-color 0.2s ease;
}

.dropdown-item:hover {
    background-color: #F5F5F5;
    color: #333;
}

.dropdown-divider {
    margin: 8px 0;
    border-top: 1px solid #E8E8E8;
}

/* Main Content Area Styles */
.SDU-main-content-area {
    background-color: #F8F8F8;
    min-height: 100vh;
}

.SDU-main-content {
    padding: 20px 0;
}

/* Responsive adjustments */
@media (max-width: 991.98px) {
    .SDU-sidebar {
        position: fixed;
        left: -100%;
        transition: left 0.3s ease;
        z-index: 1000;
        max-width: 280px;
        width: 100%;
        top: 0;
        height: 100vh;
    }

    .SDU-sidebar.SDU-show {
        left: 0;
    }

    .SDU-main-content-area {
        margin-left: 0;
        width: 100%;
    }

    .SDU-mobile-menu-toggle {
        display: flex !important;
        top: 12px;
        left: 12px;
    }

    .SDU-sidebar-header .SDU-sidebar-toggle {
        display: flex !important;
    }

    /* Header remains sticky on mobile as well */
    .SDU-header {
        position: sticky;
        top: 0;
        z-index: 200;
    }

    /* Add vertical breathing room for header on tablets/phones */
    .SDU-header .row {
        padding-top: 12px !important;
        padding-bottom: 12px !important;
    }
}

/*=======================*/
/* Desktop MINI Sidebar  */
/*=======================*/
@media (min-width: 992px) {
    body.SDU-sidebar-mini .SDU-sidebar-container {
        padding: 16px 12px;
    }

    body.SDU-sidebar-mini .SDU-section-title,
    body.SDU-sidebar-mini .SDU-nav-text,
    body.SDU-sidebar-mini .SDU-nav-arrow,
    body.SDU-sidebar-mini .SDU-nav-arrow-img,
    body.SDU-sidebar-mini .SDU-menu-separator {
        display: none !important;
    }

    body.SDU-sidebar-mini .SDU-nav-item {
        padding: 12px 8px;
        justify-content: center;
        margin-bottom: 6px;
    }

    body.SDU-sidebar-mini .SDU-nav-icon {
        margin-right: 0;
        width: 32px;
        height: 32px;
        line-height: 32px;
        font-size: 20px;
    }

    body.SDU-sidebar-mini .SDU-logo-image {
        width: 40px;
        height: 60px;
    }

    body.SDU-sidebar-mini .SDU-sidebar-header {
        padding: 16px 12px !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
    }

    body.SDU-sidebar-mini .SDU-sidebar-footer {
        padding-top: 8px;
    }

    /* Place toggle under logo when collapsed */
    body.SDU-sidebar-mini #SDU-sidebarToggle.SDU-toggle-image {
        margin-top: 10px;
        margin-right: 0;
        width: 28px;
        height: 28px;
    }
}

@media (max-width: 767.98px) {
    /* Small adjustments for mobile - Bootstrap handles most layout with utility classes */
    .SDU-sidebar {
        max-width: 300px;
    }

    .SDU-sidebar-container {
        padding: 20px 16px !important;
    }

    .SDU-nav-item {
        padding: 10px 12px !important;
        font-size: 14px !important;
    }

    .SDU-nav-icon {
        font-size: 18px !important;
    }

    .SDU-logo-icon {
        width: 40px !important;
        height: 40px !important;
        font-size: 20px !important;
    }

    .SDU-logo-text {
        font-size: 20px !important;
    }

    .SDU-btn-icon,
    .SDU-btn-profile {
        width: 40px !important;
        height: 40px !important;
    }

    .SDU-icon-envelope,
    .SDU-icon-bell {
        font-size: 16px !important;
    }

    .SDU-header-icon-img {
        width: 18px !important;
        height: 18px !important;
    }
}

@media (max-width: 575.98px) {
    /* Extra Small Devices - Minimal custom adjustments */
    .SDU-btn-icon,
    .SDU-btn-profile {
        width: 38px !important;
        height: 38px !important;
    }

    .SDU-icon-envelope,
    .SDU-icon-bell {
        font-size: 15px !important;
    }

    .SDU-header-icon-img {
        width: 15px !important;
        height: 15px !important;
    }

    /* Extra padding on very small screens */
    .SDU-header .row {
        padding-top: 16px !important;
        padding-bottom: 16px !important;
    }
}

/* ===================== */
/* Scholarship Dashboard */
/* ===================== */
.SDU-dashboard-header h4 {
    font-weight: 700;
}

.SDU-filter-card {
    border-radius: 16px;
}

.SDU-filter-label {
    font-size: 13px;
    font-weight: 600;
    color: #6B7280;
}

.SDU-btn-filter {
    background-color: #EBEBEB;
    border: none;
    border-radius: 9999px;
    padding: 12px 16px;
    height: 44px;
    color: #333;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.SDU-btn-filter:hover {
    background-color: #E0E0E0;
    color: #333;
}

.SDU-btn-filter:focus,
.SDU-btn-filter:active {
    box-shadow: none !important;
    outline: none !important;
}

.SDU-note-text {
    color: #4555C3;
    font-size: 13px;
}

.dropdown-menu {
    max-height: 260px;
    overflow-y: auto;
}

/* ===================== */
/* Stats Summary Cards   */
/* ===================== */
.SDU-stats-card {
    background: #FFFFFF;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
}

.SDU-stats-card-primary {
    background: #4153c8; /* main primary color */
    color: #fff;
    transition: background-color 0.2s ease-in-out; /* smooth switch */
}

.SDU-stats-card-header {
    background: inherit; /* uses parent’s background instantly */
    color: inherit; /* ensures text stays synced */
    font-weight: 700;
    font-size: 16px;
    line-height: 1.2;
    transition: none; /* avoid delay flicker */
}

.SDU-stats-card-value {
    font-weight: 800;
    font-size: 28px;
    margin-top: 8px;
}

.SDU-stats-card-sub {
    font-size: 12px;
    margin-top: 8px;
}

.SDU-stats-divider {
    border-top: 1px solid rgba(255,255,255,0.35);
    margin: 14px 0 12px;
}

.SDU-stats-divider.SDU-light {
    border-top-color: #E6E6E6;
}

.SDU-stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.SDU-stats-chip {
    background: rgba(255,255,255,0.15);
    border-radius: 10px;
    padding: 10px 12px;
}

.SDU-stats-chip.SDU-light {
    background: #F1F3FE;
}

.SDU-stats-chip-label {
    font-size: 11px;
    color: rgba(255,255,255,0.85);
}

.SDU-stats-card .SDU-stats-chip-label,
.SDU-stats-card .SDU-stats-chip-value {
    color: #1F2937; /* default dark for light cards */
}

.SDU-stats-card-primary .SDU-stats-chip-label,
.SDU-stats-card-primary .SDU-stats-chip-value {
    color: #FFFFFF;
}

.SDU-stats-chip-value {
    font-weight: 700;
    font-size: 14px;
}

.SDU-stats-chip.SDU-active {
    background: #FFFFFF;
}

.SDU-stats-card-primary .SDU-stats-chip.SDU-active .SDU-stats-chip-label,
.SDU-stats-card-primary .SDU-stats-chip.SDU-active .SDU-stats-chip-value {
    color: #4153c8;
}

.SDU-stats-btn {
    height: 36px;
}

/* =============================== */
/* School Wise Segregation styles  */
/* =============================== */
.SDU-rounded-16 {
    border-radius: 16px !important;
}

.SDU-seg-table-wrapper {
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    overflow: hidden;
    height: 100%;
}

.SDU-seg-table thead th {
    background: #4153C8;
    color: #FFF;
    border: none !important;
    font-weight: 600;
}

.SDU-seg-table tbody tr:nth-child(odd) td {
    background: #F8F9FF;
}

.SDU-seg-table tbody td {
    border-top: none !important;
}

.SDU-seg-chart {
    display: block;
    width: 100%;
    background: #F6F7FB;
    padding: 12px;
    border-radius: 12px;
    box-shadow: 0 1px 0 rgba(0,0,0,0.02), 0 8px 20px rgba(65,83,200,0.06);
    height: 100%;
}

.SDU-seg-chart svg {
    width: 100%;
    height: 100%;
}

/* =============================== */
/* Scholarship Criteria section    */
/* =============================== */
.SDU-criteria-table-wrapper {
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    overflow: hidden;
}

.SDU-criteria-table thead th {
    background: #4153C8;
    color: #FFF;
    border: none !important;
    font-weight: 600;
}

.SDU-criteria-table tbody tr:nth-child(odd) td { background: #FAFAFF; }
.SDU-criteria-table tbody td { border-top: none !important; }

.SDU-badge-pill-soft {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 9999px;
    font-size: 12px;
    font-weight: 600;
}
.SDU-badge-green { background: #E6F9EE; color: #0F9D58; }
.SDU-badge-blue { background: #EAF4FF; color: #2563EB; }

.SDU-btn-action-dots {
    background: transparent;
    border: none;
    padding: 0 8px;
    line-height: 1;
    font-weight: 700;
}
/* Common styling for all selects */
.custom-select-gray,
.select2-container--default .select2-selection--single,
.select2-container--default .select2-selection--multiple {
  width: 100%;
  background-color: #f1f1f1 !important;
  border: none !important;
  border-radius: 50px !important;
  padding: 10px 16px !important;
  font-size: 15px;
  color: #333 !important;
  height: auto !important;
  min-height: 45px !important;
  display: flex !important;
  align-items: center !important;
  transition: background-color 0.2s ease-in-out;
}

/* Hover and focus */
.custom-select-gray:hover,
.select2-container--default .select2-selection--single:hover,
.select2-container--default .select2-selection--multiple:hover {
  background-color: #e5e5e5 !important;
}
.custom-select-gray:focus,
.select2-container--default .select2-selection--single:focus,
.select2-container--default .select2-selection--multiple:focus {
  outline: none;
  box-shadow: 0 0 0 2px #d1d1d1 !important;
}

/* Dropdown arrow */
.select2-container--default .select2-selection__arrow b {
  border-color: #333 transparent transparent transparent !important;
}

/* Multi-select tag (pill) style */
.select2-container--default .select2-selection--multiple .select2-selection__choice {
  background-color: #ddd !important;
  border: none !important;
  border-radius: 30px !important;
  color: #333 !important;
  padding: 5px 10px !important;
  margin-top: 4px !important;
  margin-right: 6px !important;
  font-size: 14px !important;
}

/* Remove icon inside tag */
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
  color: #666 !important;
  margin-right: 6px !important;
  font-weight: bold !important;
}

/* Dropdown list */
.select2-container--default .select2-results__option--highlighted[aria-selected] {
  background-color: #007bff !important;
  color: white !important;
}

/* Dropdown box */
.select2-container--default .select2-dropdown {
  border-radius: 12px !important;
  border: none !important;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
}
/* ///////// */
/* Prevent Select2 multi-select from growing vertically */
.select2-container--default .select2-selection--multiple {
  overflow-x: auto !important;   /* allow horizontal scrolling */
  overflow-y: hidden !important; /* prevent vertical expansion */
  white-space: nowrap !important;
  height: 45px !important;       /* fixed height */
  display: flex !important;
  align-items: center !important;
  flex-wrap: nowrap !important;
  padding-right: 8px !important;
}

/* Prevent line breaks between tags */
.select2-container--default .select2-selection--multiple .select2-selection__rendered {
  display: inline-flex !important;
  flex-wrap: nowrap !important;
  overflow-x: auto !important;
  overflow-y: hidden !important;
  white-space: nowrap !important;
  scrollbar-width: thin;
}

/* Make tags stay in one line and scroll smoothly */
.select2-container--default .select2-selection--multiple .select2-selection__choice {
  white-space: nowrap !important;
  flex: 0 0 auto;
}

/* Optional: Hide scrollbar for cleaner look */
.select2-container--default .select2-selection--multiple .select2-selection__rendered::-webkit-scrollbar {
  height: 6px;
}
.select2-container--default .select2-selection--multiple .select2-selection__rendered::-webkit-scrollbar-thumb {
  background-color: #ccc;
  border-radius: 10px;
}
/* /////// */
/* Fix multi-select height and horizontal scrolling only when needed */
.select2-container--default .select2-selection--multiple {
  height: 45px !important;
  background-color: #f1f1f1 !important;
  border-radius: 50px !important;
  display: flex !important;
  align-items: center !important;
  flex-wrap: nowrap !important;
  overflow-x: hidden !important; /* Hide scroll by default */
  overflow-y: hidden !important;
  white-space: nowrap !important;
  transition: all 0.2s ease;
  padding-right: 10px !important;
}

/* Show scroll only when necessary */
.select2-container--default .select2-selection--multiple .select2-selection__rendered {
  display: inline-flex !important;
  flex-wrap: nowrap !important;
  overflow-x: auto !important;
  overflow-y: hidden !important;
  white-space: nowrap !important;
  scrollbar-width: thin;
  scroll-behavior: smooth;
}

/* Style scrollbar only when visible */
.select2-container--default .select2-selection--multiple .select2-selection__rendered::-webkit-scrollbar {
  height: 5px;
  display: none; /* hidden by default */
}
.select2-container--default .select2-selection--multiple .select2-selection__rendered:hover::-webkit-scrollbar {
  display: block; /* show on hover */
}

/* Keep tags neat and single-line */
.select2-container--default .select2-selection--multiple .select2-selection__choice {
  flex: 0 0 auto;
  white-space: nowrap !important;
  margin-right: 6px !important;
}
/* JS handlind classes */

.stat-card-selectable {
    cursor: pointer;
    transition: all 0.3s ease;
}
.stat-card-selectable:hover {
    transform: translateY(-3px);
}
 /* Make modal width auto-fit content, up to 95% of viewport */
.modal-xxl {
    max-width: 95vw !important;
    width: auto !important;
}
.SDU-seg-chart {
    overflow-x: auto;
    overflow-y: hidden;
}



</style>
<div class="container-fluid px-0">
<div class="col-12 SDU-main-content-area">
    <!-- Main Content -->
    {{-- {{dd($scholarships)}} --}}
    <div class="SDU-main-content">
        <div class="container-fluid p-4">
            <!-- Scholarship Dashboard -->
            <div class="SDU-dashboard-header mb-3">
                <h2 class="mb-1">Scholarship Dashboard</h2>
                <p class="text-muted mb-0">View scholarship statics & details</p>
            </div>

            <div class="card SDU-filter-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-stretch">
                        <div class="col-lg-3 col-md-6 mb-3">
                            {{-- <label class="SDU-filter-label d-block mb-2">Session</label> --}}
                            {{-- <div class="dropdown w-100">
                                <button class="btn SDU-btn-filter dropdown-toggle w-100" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Summer 2025
                                </button>
                                <div class="dropdown-menu w-100">
                                    <a class="dropdown-item" href="#">Summer 2025</a>
                                    <a class="dropdown-item" href="#">Spring 2025</a>
                                    <a class="dropdown-item" href="#">Fall 2024</a>
                                </div>
                            </div> --}}
                           <div class="form-group mb-3">
                                <label class="SDU-filter-label d-block mb-2" for="session-select" class="form-label">Session</label>
                               <select class="custom-select-gray" id="session_sel" name="session">
                                    @foreach($academic_session as $session)
                                        <option value="{{ $session->sess_id }}"
                                            {{ $session->sess_id == 165 ? 'selected' : '' }}>
                                            {{ $session->sess_name }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>

                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            {{-- <label class="SDU-filter-label d-block mb-2">Road Map</label> --}}
                            {{-- <div class="dropdown w-100">
                                <button class="btn SDU-btn-filter dropdown-toggle w-100" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Merit Base
                                </button>
                                <div class="dropdown-menu w-100">
                                    <a class="dropdown-item" href="#">Merit Base</a>
                                    <a class="dropdown-item" href="#">Need Base</a>
                                </div>
                            </div> --}}
                            <div class="form-group mb-3">
                                <label class="SDU-filter-label d-block mb-2" for="roadmap-select" class="form-label">Road Map</label>
                              <select class="custom-select-gray" id="roadmap-select" name="roadmap">
                                    @foreach($academic_session as $session)
                                        <option value="{{ $session->sess_id }}"
                                            {{ $session->sess_id == 165 ? 'selected' : '' }}>
                                            {{ $session->sess_name }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                         <div class="col-lg-3 col-md-6 mb-3">
                            {{-- <label class="SDU-filter-label d-block mb-2">Scholarship</label>
                            <div class="dropdown w-100">
                                <button class="btn SDU-btn-filter dropdown-toggle w-100" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    SVAD
                                </button>
                                <div class="dropdown-menu w-100">
                                    <a class="dropdown-item" href="#">SVAD</a>
                                    <a class="dropdown-item" href="#">NTHP</a>
                                    <a class="dropdown-item" href="#">Sports</a>
                                </div>
                            </div> --}}
                            <div class="form-group mb-3">
                                <label class="SDU-filter-label d-block mb-2" for="sch_type"><i class="bi bi-building me-1"></i>Scholarships</label>
                                <select class="form-control select2-show-search custom-select-gray"
                                        name="sch_type[]"
                                        id="sch_type"
                                        multiple="multiple"
                                        data-placeholder="All Scholarships"
                                        style="width:100%;">
                                    <option value="">All Scholarships</option>
                                    @foreach($scholarships as $row)
                                        <option value="{{ $row->scholarship_id }}">{{ $row->scholarship_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- <div class="form-group mb-3">
                                <label for="sch_type"><i class="bi bi-building me-1"></i>Scholarships</label>
                                <select class="form-control custom-select-gray select2-show-search custom-select-gray"
                                        name="sch_type[]"
                                        id="sch_type"
                                        multiple="multiple"
                                        data-placeholder="All Scholarships"
                                        style="width:100%;">
                                    <option value="">All Scholarships</option>
                                </select>

                                <select class="academicOptions custom-select-gray">
                                    @foreach($scholarships as $row)
                                            <option value="{{ $row->scholarship_id }}">{{ $row->scholarship_name }}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                        </div>
                    </div>
                    <div class="row align-items-stretch">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label for="school-select" class="SDU-filter-label d-block mb-2">School</label>
                            <select class="custom-select-gray" id="school_sel" name="school" onchange="getDepartments();">
                                @php
                                    $allSchools = [];
                                    foreach ($recSchool as $value) {
                                        $allSchools[] = $value['school_id'];
                                    }
                                @endphp
                                    <option value="">Select School</option>
                                    <option value="[{{implode(',', $allSchools) }}]">All School</option>
                                @foreach ($recSchool as $row)
                                    <option value="{{ $row->school_id }}">{{ $row->school_code.' ('.$row->school_name.')' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="SDU-filter-label d-block mb-2">Department</label>
                            <select class="custom-select-gray" name="department" disabled id="department" class="form-control" onchange="getPrograms();">
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="SDU-filter-label d-block mb-2">Program</label>
                            <select class="custom-select-gray" name="program" disabled id="program" class="form-control">
                            </select>
                        </div>
                    </div>
                     {{-- <p class="SDU-note-text mb-3 mt-1">Note: Filter data by specific scholarships or view the overall summary.</p> --}}

                    <div class="d-flex gap-2">
                        <button id="apply-filters-btn" class="btn btn-primary rounded-pill px-4 mr-1">Apply Filters</button>
                        <button class="btn btn-outline-secondary rounded-pill px-4">Clear</button>
                    </div>
                </div>


            </div>
        </div>

            <!-- Stats Summary Section -->
            <div class="row mt-4">
                <!-- Card 1: Total Scholarships Awarded -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="SDU-stats-card SDU-stats-card-primary h-100 stat-card-selectable" id="scholarship_card">
                        <div class="header SDU-stats-card-header">Total Scholarships<br> Awarded</div>
                        <div class="head_total SDU-stats-card-value" id="total-awarded">-</div>
                        <div class="head_description SDU-stats-card-sub">Includes both academic and hostel scholarships.</div>
                        <hr class="SDU-stats-divider">
                        <div class="SDU-stats-grid">
                            <div class="SDU-stats-chip SDU-active">
                                <div class="SDU-stats-chip-label" data-chip="academic">Academics</div>
                                <div class="SDU-stats-chip-value" id="academic-total-scholarships">-</div>
                            </div>
                            <div class="SDU-stats-chip">
                                <div class="SDU-stats-chip-label" data-chip="hostel">Hostel</div>
                                <div class="SDU-stats-chip-value" id="hostel-total-scholarships">-</div>
                            </div>
                           <div class="SDU-stats-chip">
                                <div class="SDU-stats-chip-label" data-chip="meal">Meal</div>
                                <div class="SDU-stats-chip-value" id="total-meal-scholarships">-</div>
                            </div>

                            <div class="SDU-stats-chip">
                                <div class="SDU-stats-chip-label" data-chip="material">Material</div>
                                <div class="SDU-stats-chip-value" id="total-material-scholarships">-</div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-light rounded-pill px-4 SDU-stats-btn" id="scholarship-view_btn">View All</button>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Total Scholarships Holders -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="SDU-stats-card h-100 stat-card-selectable" id="holder_card">
                        <div class="header stats-card-header text-dark">Total Scholarships<br> Holders</div>
                        <div class="head_total stats-card-value text-dark" id="total-holders">-</div>
                        <div class="head_description stats-card-sub text-muted">Includes both academic and hostel scholarships.</div>
                        <hr class="SDU-stats-divider SDU-light">
                        <div class="SDU-stats-grid">
                            <div class="SDU-stats-chip SDU-light">
                                <div class="SDU-stats-chip-label" data-chip="academic">Academics</div>
                                <div class="SDU-stats-chip-value" id="academic-total-students">-</div>
                            </div>
                            <div class="SDU-stats-chip SDU-light">
                                <div class="SDU-stats-chip-label" data-chip="hostel">Hostel</div>
                                <div class="SDU-stats-chip-value" id="hostel-total-students">-</div>
                            </div>
                            <div class="SDU-stats-chip SDU-light">
                                <div class="SDU-stats-chip-label" data-chip="meal" id="total-meal-students">Meal</div>
                                <div class="SDU-stats-chip-value">-</div>
                            </div>
                            <div class="SDU-stats-chip SDU-light">
                                <div class="SDU-stats-chip-label" data-chip="material" id="total-material-students">Material</div>
                                <div class="SDU-stats-chip-value">-</div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-primary rounded-pill px-4 SDU-stats-btn" id="students-view_btn">View All</button>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Scholarship Holders On Alert -->
                <div class="col-xl-4 col-md-12 mb-4">
                    <div class="SDU-stats-card h-100 stat-card-selectable" id="onAlert_card">
                        <div class="header stats-card-header text-dark">Scholarship Holders<br> On Alert</div>
                        <div class="head_total stats-card-value text-dark">-</div>
                        <div class="head_description stats-card-sub text-muted">Students at verge of loosing scholarship</div>
                        <hr class="SDU-stats-divider SDU-light">
                        <div class="SDU-stats-grid">
                           <div class="SDU-stats-chip SDU-light">
                                <div class="SDU-stats-chip-label" data-chip="academic">Academics</div>
                                <div class="SDU-stats-chip-value">-</div>
                            </div>
                            <div class="SDU-stats-chip SDU-light">
                                <div class="SDU-stats-chip-label" data-chip="hostel">Hostel</div>
                                <div class="SDU-stats-chip-value">-</div>
                            </div>
                            <div class="SDU-stats-chip SDU-light">
                                <div class="SDU-stats-chip-label" data-chip="meal">Meal</div>
                                <div class="SDU-stats-chip-value">-</div>
                            </div>
                            <div class="SDU-stats-chip SDU-light">
                                <div class="SDU-stats-chip-label" data-chip="material">Material</div>
                                <div class="SDU-stats-chip-value">-</div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-primary rounded-pill px-4 SDU-stats-btn" id="alert-view_btn">View All</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- School Wise Segregation (Third Section) -->
            <div class="mt-3">
                    <h3 class="mb-3">School Wise Segregation</h3>
                    <div class="row">
                        <!-- Table -->
                        <div class="col-lg-6 mb-3 mb-lg-0">
                            <div class="SDU-seg-table-wrapper h-100">
                                <table class="table mb-0 SDU-seg-table">
                                    <thead>
                                        <tr>
                                            <th>School Name</th>
                                            <th>Code</th>
                                            <th class="text-right pr-3">Number</th>
                                        </tr>
                                    </thead>
                                    <tbody id="school-wise-table">
                                        <tr>
                                            <td class="text-center">-</td>
                                            <td class="text-center">-</td>
                                            <td class="text-center">-</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">-</td>
                                            <td class="text-center">-</td>
                                            <td class="text-center">-</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">-</td>
                                            <td class="text-center">-</td>
                                            <td class="text-center">-</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">-</td>
                                            <td class="text-center">-</td>
                                            <td class="text-center">-</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">-</td>
                                            <td class="text-center">-</td>
                                            <td class="text-center">-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Chart -->
                       <div class="col-lg-6">
                            <div class="SDU-seg-chart h-100 d-flex align-items-center" style="overflow-x:auto;">
                            <svg id="schoolChart"
                                viewBox="0 0 360 220"
                                width="100%"
                                height="100%"
                                preserveAspectRatio="none">

                                <!-- grid lines -->
                                <g stroke="#E5E7EB" stroke-width="1">
                                    <line x1="45" y1="30" x2="340" y2="30"/>
                                    <line x1="45" y1="70" x2="340" y2="70"/>
                                    <line x1="45" y1="110" x2="340" y2="110"/>
                                    <line x1="45" y1="150" x2="340" y2="150"/>
                                    <line x1="45" y1="190" x2="340" y2="190"/>
                                </g>

                                <!-- axes -->
                                <line x1="45" y1="30" x2="45" y2="190" stroke="#D1D5DB"/>
                                <line x1="45" y1="190" x2="340" y2="190" stroke="#D1D5DB"/>

                                <!-- y-axis labels -->
                                <g fill="#6B7280" font-size="10" text-anchor="end" id="yLabels"></g>

                                <!-- bars -->
                                <g id="bars"
                                fill="#4153C8"
                                fill-opacity="0.35"
                                stroke="#4153C8"
                                stroke-width="2"></g>

                                <!-- x-axis labels -->
                                <g id="xLabels" fill="#6B7280" font-size="10"></g>
                            </svg>
                        </div>
                    </div>

                    </div>
            </div>

            <!-- Scholarship Criteria (Like second screenshot) -->
            <div class="mt-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h3 class="mb-0">Scholarship Criteria</h3>
                    {{-- <div class="dropdown">
                        <button class="btn SDU-btn-filter dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Scholarship Type
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#">Merit Base</a>
                            <a class="dropdown-item" href="#">Need Base</a>
                            <a class="dropdown-item" href="#">UNISA</a>
                        </div>
                    </div> --}}
                </div>

                <div class="SDU-criteria-table-wrapper" style="overflow: visible;">
                    <table class="table mb-0 SDU-criteria-table align-middle">
                        <thead>
                            <tr>
                                <th>Scholarship Name</th>
                                <th>Sponsor Body</th>
                                <th>CGPA Range</th>
                                <th>Scholarship Allocation Limit</th>
                                <th>Tuition Fee Coverage</th>
                                <th>Hostel Fee Coverage</th>
                                {{-- <th class="text-right pr-3">Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Merit Base</td>
                                <td>3.5 - 4.0</td>
                                <td>100%</td>
                                <td>Nov 23, 2025</td>
                                <td><span class="SDU-badge-pill-soft SDU-badge-green">Sent</span></td>
                                <td class="text-right pr-3">
                                    <div class="dropdown d-inline-block position-static">
                                        <button class="btn SDU-btn-action-dots" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">...</button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#">View</a>
                                            <a class="dropdown-item" href="#">Edit</a>
                                            <a class="dropdown-item text-danger" href="#">Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Need Base</td>
                                <td>2.5 - 4.0</td>
                                <td>75%</td>
                                <td>Nov 17, 2025</td>
                                <td><span class="SDU-badge-pill-soft SDU-badge-blue">Pending</span></td>
                                <td class="text-right pr-3">
                                    <div class="dropdown d-inline-block position-static">
                                        <button class="btn SDU-btn-action-dots" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">...</button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#">View</a>
                                            <a class="dropdown-item" href="#">Edit</a>
                                            <a class="dropdown-item text-danger" href="#">Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>UNISA</td>
                                <td>3.0 - 4.0</td>
                                <td>50%</td>
                                <td>Oct 31, 2025</td>
                                <td><span class="SDU-badge-pill-soft SDU-badge-green">Sent</span></td>
                                <td class="text-right pr-3">
                                    <div class="dropdown d-inline-block position-static">
                                        <button class="btn SDU-btn-action-dots" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">...</button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#">View</a>
                                            <a class="dropdown-item" href="#">Edit</a>
                                            <a class="dropdown-item text-danger" href="#">Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="modal fade" id="schoolDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content shadow-lg border-primary">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalTitle">� Scholarship Details - <span id="schoolName">Institute of Psychology</span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <!-- ================= Summary Overview ================= -->
        <div class="card border border-primary shadow-sm mb-4">
          <div class="card-body">
            <h6 class="fw-bold text-primary mb-3">Summary Overview</h6>

            <table id="overview-table" class="table table-sm table-bordered align-middle text-center mb-0">
              <thead class="table-primary">
                <tr>
                  <th>Total Scholarships Awarded</th>
                  <th>Total Scholarhip Holders</th>
                  <th>Single Scholarship Holders</th>
                  <th>Multiple Scholarship Holders</th>
                </tr>
              </thead>
              <tbody>
                    {{-- dynamic rows will be appended here --}}
              </tbody>
            </table>
          </div>
        </div>

        <!-- ================= Deduplicated Students List ================= -->
        <div class="card border border-primary shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="fw-bold text-primary mb-0">Scholarship Holders</h6>
            </div>

            <div class="studentsTableclass table-responsive">
              <table id="studentsTable" class="table table-striped table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Enrollment No</th>
                    <th>Student Name</th>
                    <th>Department</th>
                    <th>Program</th>
                    <th>CGPA</th>
                    <th>Awarded Percentage</th>
                    <th>Scholarships</th>
                  </tr>
                </thead>
                <tbody>
                    <!-- Dynamic student rows will be appended here -->
                </tbody>
              </table>
            </div>

            <!-- Lazy loading placeholder -->
            <div id="loadingIndicator" class="text-center py-3 d-none">
              <div class="spinner-border text-primary" role="status"></div>
              <p class="mt-2 mb-0 text-muted">Loading more students...</p>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
@section('javascript')
<script>
    function getRequest(endPoint,successBlock,failBlock){
        $.ajax({
            url: "{{url('/')}}"+endPoint,
            type: "GET",
            dataType: "json",
            success:successBlock
        }).fail(failBlock);
    }
    function getDepartments(){
        var school_id = $('#school_sel').val();
        getRequest('/api/getDepartmentsFeeStructureForm/get?school_id='+school_id,function(res){
            // console.log(res);
            if(res.status){
                let allDep = [];
                $.each(res.data, function(key, value) {
                    allDep.push(value.dept_school_id+'~~'+value.dept_id);
                });
                if(school_id == ''){
                        let html = `<option value=''>Select Department</option>`;
                        $.each(res.data,function( key, value ) {
                            html += '<option value="'+value.dept_id+'" >'+value.dept_name+'</option>';
                        });
                        $('select[name="department"]').html(html);
                }else{
                    let html = `<option value=''>Select Department</option>`;
                    html += `<option value='${JSON.stringify(allDep)}'>All Departments</option>`;
                    $.each(res.data,function( key, value ) {
                        html += '<option value="'+value.dept_id+'" >'+value.dept_name+'</option>';
                    });
                    $('select[name="department"]').html(html);
                }
                /////////
                let htmlProg = `<option value=''>Select Program</option>`;
                    $('select[name="program"]').html(htmlProg);
                    htmlProg = `<option value=''>Select Degree Level</option>`;
                    $('select[name="degree_level"]').html(htmlProg);
                action_onAllSchoolSelection(school_id);
                ////
            }
        },function(res){
            alert('Some Error found!');
        });
    }
    function getPrograms() {
        const session_id = $('#session_sel').val(); // Assuming session_id is still used (remove if not)
        const dept_id = $('#department').val();
        // console.log(session_id, dept_id);
        const url = `/api/getProgramFeeStructureForm/get?dept_id=${dept_id}&session_sel=${session_id}`; // Template literals for URL
        getRequest(url,function(res){
                    if(res.status){
                        // console.log(res.data);
                        let allProg = [];
                        let degreeLevels = [];
                        $.each(res.data, function(key, value) {
                            allProg.push(value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id);
                            degreeLevels.push(value.prog_degree_level);
                        });
                        // populateDegreeLevel(degreeLevels);
                        if(dept_id == ''){
                            let html = `<option value=''>Select Program</option>`;
                            $.each(res.data, function(key, value) {
                            // html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
                            html += `<option deg-level="${value.prog_degree_level}" value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name} &#x2192; ${value.prog_degree_level}</option>`;
                            });
                            $('select[name="program"]').html(html); // Update "program" select
                        }else{
                            let html = `<option value=''>Select Program</option>`;
                            html += `<option value='${JSON.stringify(allProg)}'>All Program</option>`;
                            $.each(res.data, function(key, value) {
                            // html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
                            html += `<option deg-level="${value.prog_degree_level}" value="${value.prog_school_id+'~~'+value.prog_dept_id+'~~'+value.prog_id}">${value.prog_name}  &#x2192; ${value.prog_degree_level}</option>`;
                            });
                            $('select[name="program"]').html(html); // Update "course" select
                        }
                        ////////
                        action_onAllDepartmentSelection(dept_id);
                        ///////

                    } else {
                        // console.error('Error fetching programs:', res.message); // Log error message
                        // Optionally display an error message to the user
                    }

                },function(res){
                    alert('Some Error found!');
        });
        // console.log(url);
        //   $.ajax({
        //     url: url,
        //     method: 'GET',
        //     success: function(response) {
        //       if (response.status) {
        //         let html = '<option value="">All Program</option>';
        //         $.each(response.data, function(key, value) {
        //           html += `<option value="${value.prog_id}">${value.prog_name}</option>`;
        //         });
        //         $('select[name="course"]').html(html); // Update "course" select
        //       } else {
        //         console.error('Error fetching programs:', response.message); // Log error message
        //         // Optionally display an error message to the user
        //       }
        //     },
        //     error: function(response) {
        //       console.error('Error fetching programs:', response.statusText); // Log error details
        //       // Optionally display a generic error message to the user
        //     }
        //   });
    }
    function action_onAllSchoolSelection(school_id){
        if(school_id != ''){
            school_ids_array = JSON.parse(school_id);
            if(Array.isArray(school_ids_array)){
                $('#department').prop('disabled', true);
                $('#program').prop('disabled', true);
                setTimeout(function() {
                    $('#department').val($('#department option:nth-child(2)').val());
                    getPrograms();
                    setTimeout(function() {
                        $('#program').val($('#program option:nth-child(2)').val());
                    }, 500);  // Adjust the delay as needed
                }, 500);  // Adjust the delay as needed
            }else{
                $('#department').prop('disabled', false);
                $('#program').prop('disabled', false);
            }
        }else{
            $('#department').prop('disabled', false);
            $('#program').prop('disabled', false);
        }

    }
    function action_onAllDepartmentSelection(dept_id){
        if(dept_id != ''){
            dept_ids_array = JSON.parse(dept_id);
            if(Array.isArray(dept_ids_array)){
                $('#program').prop('disabled', true);
                $('#program').val($('#program option:nth-child(2)').val());
            }else{
                $('#program').prop('disabled', false);
            }
        }else{
            $('#program').prop('disabled', false);
        }
        // console.log($('#program').val());

    }
    function resetAllCards() {
        $('.stat-card-selectable')
            .removeClass('SDU-stats-card-primary')
            .each(function () {
                $(this)
                    .find('.header')
                    .removeClass('SDU-stats-card-header')
                    .addClass('stats-card-header text-dark')
                    .end()
                    .find('.head_total')
                    .removeClass('SDU-stats-card-value')
                    .addClass('stats-card-value text-dark')
                    .end()
                    .find('.head_description')
                    .removeClass('SDU-stats-card-sub')
                    .addClass('stats-card-sub text-muted')
                    .end()
                    .find('.SDU-stats-divider')
                    .addClass('SDU-light')
                    .end()
                    .find('.SDU-stats-chip')
                    .addClass('SDU-light')
                    .end()
                    .find('.SDU-stats-btn')
                    .removeClass('btn-light')
                    .addClass('btn-primary');
            });
            // $('.SDU-stats-chip').removeClass('SDU-active');
    }
    function setCardActive($card) {
        $card
            .addClass('SDU-stats-card-primary')
            .find('.header')
            .removeClass('stats-card-header text-dark')
            .addClass('SDU-stats-card-header')
            .end()
            .find('.head_total')
            .removeClass('stats-card-value text-dark')
            .addClass('SDU-stats-card-value')
            .end()
            .find('.head_description')
            .removeClass('stats-card-sub text-muted')
            .addClass('SDU-stats-card-sub')
            .end()
            .find('.SDU-stats-divider')
            .removeClass('SDU-light')
            .end()
            .find('.SDU-stats-chip')
            .removeClass('SDU-light')
            .end()
            .find('.SDU-stats-btn')
            .removeClass('btn-primary')
            .addClass('btn-light')
            .end();
            // .find('.SDU-stats-chip').first().addClass('SDU-active');
    }
    function populateDashboard() {
       let filterParams = getFilterParams();
        // if (!session || !studentType) {
        //     alert('Please select both session and student type before applying filters.');
        //     return;
        // }
        $.ajax({
            url: "{{ route('getDashboardData') }}",
            type: "POST",
            data: filterParams,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            // beforeSend: function() {
            //     $('#apply-filters-btn')
            //         .prop('disabled', true)
            //         .html('<i class="fa fa-spinner fa-spin me-2"></i> Loading...');
            // },
            success: function(response) {
                console.log('Dashboard Data:', response);

                // Example if your API returns data like:
                // { total_awarded: 50, total_holders: 25, on_alert: 8, pending_projects: 4 }
                if (response) {
                    $('#total-awarded').text(response.summaryCount[0].total_scholarships ?? 0);
                    $('#academic-total-scholarships').text(response.summaryCount[0].academic_scholarship ?? 0);
                    $('#hostel-total-scholarships').text(response.summaryCount[0].hostel_scholarship ?? 0);

                    $('#total-holders').text(response.summaryCount[0].total_scholarships_students ?? 0);
                    $('#academic-total-students').text(response.summaryCount[0].academic_scholarship_students ?? 0);
                    $('#hostel-total-students').text(response.summaryCount[0].hostel_scholarship_students ?? 0);
                    $('#on-alert').text(response.on_alert ?? 0);
                    //setting fixed hidden inputs
                    // setFilterParams();

                }
            },
            error: function(xhr) {
                console.error('Error fetching dashboard data:', xhr);
                alert('Failed to load dashboard data.');
            },
            complete: function() {
                $('#apply-filters-btn')
                    .prop('disabled', false)
                    .html('<i class="fa fa-filter me-2"></i> Apply Filters');
            }
        });
    }
    function getFilterParams() {
        let session = $('#session_sel').val();
        let roadmap = $('#roadmap-select').val();
        let selectedScholarships = $('#sch_type').val() || []; // returns array
        let school_sel = $('#school_sel').val();
        let department = $('#department').val();
        let program = $('#program').val();

        return {
            session: session,
            roadmap: roadmap,
            scholarships: selectedScholarships,
            school: school_sel,
            department: department,
            program: program
        };
    }
    function get_schoolwiseScholarship_count(element){

        let parentCardId = element.closest('.SDU-stats-card').attr('id');
        let activeChip = element.closest('.SDU-stats-card').find('.SDU-stats-chip.SDU-active .SDU-stats-chip-label').data('chip'); // gets the data-chip value
        let filterParams = getFilterParams();
        filterParams.card_id = parentCardId;
        filterParams.active_chip = activeChip;
        $.ajax({
            url: "{{ route('getSchoolwiseCounts') }}",
            type: "GET",
            data:  filterParams,
            beforeSend: function() {
                $('#applyScholarshipFilter')
                    .prop('disabled', true)
                    .html('<i class="fa fa-spinner fa-spin me-2"></i> Loading...');
            },
            success: function(response) {
                if (response.status) {
                    console.log('schoolwise_scholarshipCount Data:', response.data);
                    renderSchoolwiseTable(response.data);
                    renderCriteriaTable(response.scholarshipCriteria);
                } else {
                    alert(response.message || 'Something went wrong while fetching data.');
                }
            },
            error: function(xhr) {
                console.error('Error fetching dashboard data:', xhr);
                let msg = 'Failed to load dashboard data.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alert(msg);
            },
            complete: function() {
                $('#applyScholarshipFilter')
                    .prop('disabled', false)
                    .html('<i class="fa fa-filter me-2"></i> Apply Filters');
            }
        });
    }
    function renderSchoolwiseTable(data) {
        // console.log('Rendering School-wise Table with data:', data);
        let html = '';
        // let selectedScholarships = $('#sch_type').val() || []; // [] if none selected
        // let schType = $('#scholarshipType-fixed').val();
        // console.log(selectedScholarships);
        data.forEach(item => {
            html += `
                <tr>

                    <td class="text-center align-middle">${item.school_name}</td>
                    <td class="text-center align-middle">${item.school_code}</td>
                    <td class="text-center align-middle fw-semibold">
                        <a href="#"
                        class="text-primary school-count-link"
                        data-school-id="${item.school_id}"
                        data-school-name="${item.school_name}">
                        ${item.total}
                        </a>
                    </td>
                </tr>
            `;
        });

        // Inject into table body
        $('#school-wise-table').html(html);
        buildSchoolChart(data);
    }
    function renderCriteriaTable(criteriaArray) {
        const tbody = document.querySelector('.SDU-criteria-table-wrapper tbody');
        if (!tbody) return;

        if (!criteriaArray || criteriaArray.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted">No scholarships found.</td>
                </tr>
            `;
            return;
        }

        let html = '';
        criteriaArray.forEach(item => {
            html += `
                <tr>
                    <td>${item.scholarship_name || '-'}</td>
                    <td>${item.fsp_sponsor_body || '-'}</td>
                    <td>${item.fsp_cgpa_min_limit || '-'} - 4.0</td>
                    <td>${item.fsp_no_of_scholarships || '-'}</td>
                    <td>${item.fsp_tuition_fee_percent || '0'}%</td>
                    <td>${item.fsp_hostel_coverage_percent || '0'}%</td>

                </tr>
            `;
            // <td class="text-right pr-3">
            //             <div class="dropdown d-inline-block position-static">
            //                 <button class="btn SDU-btn-action-dots" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">...</button>
            //                 <div class="dropdown-menu dropdown-menu-right">
            //                     <a class="dropdown-item" href="#">View</a>
            //                     <a class="dropdown-item" href="#">Edit</a>
            //                     <a class="dropdown-item text-danger" href="#">Delete</a>
            //                 </div>
            //             </div>
            //         </td>
        });

        tbody.innerHTML = html;
    }

    function buildSchoolChart(data) {
        const svg = document.getElementById('schoolChart');

        const svgNS = 'http://www.w3.org/2000/svg';

        const chartBottom = 190;
        const chartTop = 30;
        const chartHeight = chartBottom - chartTop;

        const barWidth = 22;        // smaller bars
        const barGap = 10;          // tighter spacing
        const startX = 60;

        const yLabels = document.getElementById('yLabels');
        const bars = document.getElementById('bars');
        const xLabels = document.getElementById('xLabels');

        // RESET CHART
        yLabels.innerHTML = '';
        bars.innerHTML = '';
        xLabels.innerHTML = '';

        // CALCULATE MAX VALUE
        const values = data.map(d => Number(d.total));
        const maxValue = Math.ceil(Math.max(...values) / 100) * 100 || 100;

        /* ----------------- UPDATE SVG WIDTH FOR SCROLL ----------------- */
        const totalChartWidth = startX + data.length * (barWidth + barGap) + 60;
        svg.setAttribute('width', totalChartWidth);

        // Make parent scrollable
        svg.parentElement.style.overflowX = "auto";
        svg.parentElement.style.display = "block";

        /* ----------------- Y AXIS LABELS ----------------- */
        for (let i = 0; i <= 4; i++) {
            const value = (maxValue / 4) * i;
            const y = chartBottom - (chartHeight / 4) * i;

            const label = document.createElementNS(svgNS, 'text');
            label.setAttribute('x', 40);
            label.setAttribute('y', y + 4);
            label.setAttribute('text-anchor', 'end');
            label.setAttribute('fill', '#6B7280');
            label.setAttribute('font-size', '8');   // smaller text
            label.textContent = Math.round(value);

            yLabels.appendChild(label);
        }

        /* ----------------- DRAW BARS ----------------- */
        data.forEach((item, index) => {
            const value = Number(item.total);
            const height = (value / maxValue) * chartHeight;

            const x = startX + index * (barWidth + barGap);
            const y = chartBottom - height;

            // BAR
            const rect = document.createElementNS(svgNS, 'rect');
            rect.setAttribute('x', x);
            rect.setAttribute('y', y);
            rect.setAttribute('width', barWidth);
            rect.setAttribute('height', height);
            rect.setAttribute('rx', 4);
            rect.setAttribute('fill', '#4153C8');
            rect.setAttribute('fill-opacity', '0.35');
            rect.setAttribute('stroke', '#4153C8');
            rect.setAttribute('stroke-width', '2');
            bars.appendChild(rect);

            // DOT
            const dot = document.createElementNS(svgNS, 'circle');
            dot.setAttribute('cx', x + barWidth / 2);
            dot.setAttribute('cy', y);
            dot.setAttribute('r', 3);
            dot.setAttribute('fill', '#4153C8');
            bars.appendChild(dot);

            // X-AXIS LABEL
            const xLabel = document.createElementNS(svgNS, 'text');
            xLabel.setAttribute('x', x + barWidth / 2);
            xLabel.setAttribute('y', 210);
                xLabel.setAttribute('text-anchor', 'middle');
                xLabel.setAttribute('fill', '#6B7280');
                xLabel.setAttribute('font-size', '8');   // smaller text
                xLabel.textContent = item.school_code;
                xLabels.appendChild(xLabel);
            });
    }

    function renderSchoolDetailsModal(data) {
        if (!data || data.length === 0) {
            $('#modalBody').html('<div class="text-center text-muted p-4">No data found for this selection.</div>');
            $('#schoolDetailsModal').modal('show');
            return;
        }

        // --- Summary calculation ---
        let scholarshipcount = data[0].total_scholarships_awarded_to_school;
        let uniqueStudents = data.length;
        let singleHolders = data.filter(s => s.scholarship_label !== 'MULTIPLE_SCHOLARSHIP_HOLDER').length;
        let multipleHolders = data.filter(s => s.scholarship_label === 'MULTIPLE_SCHOLARSHIP_HOLDER').length;

        // --- Update Summary Table ---
        let $overviewTbody = $('#overview-table tbody');
        $overviewTbody.empty().append(`
            <tr>
                <td><span class="fw-semibold text-dark">${scholarshipcount}</span></td>
                <td><span class="fw-semibold text-dark">${uniqueStudents}</span></td>
                <td><span class="fw-semibold text-success">${singleHolders}</span></td>
                <td><span class="fw-semibold text-warning">${multipleHolders}</span></td>
            </tr>
        `);

        // --- Students Table ---
        let $studentsTable = $('#studentsTable');
        let $studenttbody = $studentsTable.find('tbody');
        $studenttbody.empty();

        // Destroy previous DataTable if exists
        if ($.fn.DataTable.isDataTable('#studentsTable')) {
            $studentsTable.DataTable().clear().destroy();
        }

        // Append new rows
        data.forEach((item, i) => {
            $studenttbody.append(`
                <tr>
                    <td>${i + 1}</td>
                    <td>${item.enrollment_no}</td>
                    <td>${item.student_name}</td>
                    <td>${item.dept_name}</td>
                    <td>${item.prog_name}</td>
                    <td>${item.cgpa ?? '-'}</td>
                    <td>${item.scholarships_percentages ?? '-'}</td>
                    <td>${item.scholarships_list}</td>
                </tr>
            `);
        });

        // --- Initialize DataTable AFTER modal is visible ---
        $('#schoolDetailsModal')
            .off('shown.bs.modal') // avoid multiple bindings
            .on('shown.bs.modal', function () {
                $('#studentsTable').DataTable({
                    pageLength: 10,
                    lengthChange: false,
                    searching: true,
                    ordering: true,
                    info: true,
                    language: {
                        paginate: {
                            previous: "<i class='bi bi-chevron-left'></i>",
                            next: "<i class='bi bi-chevron-right'></i>"
                        }
                    }
                });
            });

        // --- Show Modal ---
        $('#schoolDetailsModal').modal('show');
    }
    $(document).ready(function() {
        populateDashboard();
        get_schoolwiseScholarship_count($('#scholarship-view_btn'));
        $('#sch_type').select2({
            placeholder: "All Scholarships",
            allowClear: true,
            width: '100%',
            closeOnSelect: false // ⬅️ keeps dropdown open after each selection

        });
        $('#sch_type').on('select2:select', function () {
            let container = $(this).next('.select2-container').find('.select2-selection__rendered');
            container.scrollLeft(container[0].scrollWidth);
        });
        $('.stat-card-selectable').on('click', function () {
            resetAllCards();           // deactivate all
            setCardActive($(this));    // activate clicked one
            // setFilterParams();
        });
        $('#apply-filters-btn').on('click', function() {
            populateDashboard();
        });
        // Handle chip and card selection
        $('.SDU-stats-chip').on('click', function (e) {
            e.stopPropagation(); // prevent triggering card click twice

            const $chip = $(this);
            const $card = $chip.closest('.stat-card-selectable');

            // � Deactivate all chips globally (optional)
            $('.SDU-stats-chip').removeClass('SDU-active');

            // � Activate this chip
            $chip.addClass('SDU-active');

            resetAllCards();           // deactivate all
            setCardActive($card);    // activate clicked one
            // setFilterParams();
        });
         // Apply Scholarship filter
        $('.SDU-stats-btn').on('click', function () {
            get_schoolwiseScholarship_count($(this)); // your existing function
        });
        // Delegate click event since rows are dynamic
        $(document).on('click', '.school-count-link', function(e) {
            e.preventDefault();
            let filterParams = getFilterParams(); // returns object like { session: [...], studentType: ..., schType: ... }
            let parentCardId = $('.SDU-stats-card-primary').attr('id');
            let activeChip = $('.SDU-active').find('.SDU-stats-chip-label').data('chip'); // gets the data-chip value
            // add school_id directly to that object
            filterParams.school_id = $(this).data('school-id');
            filterParams.card_id = parentCardId;
            filterParams.active_chip = activeChip;
            let schoolName =  $(this).data('school-name');

            // Optional: show loading spinner inside modal
            $('#modalTitle').text(`Scholarships for ${schoolName}`);

            $.ajax({
                url: "{{ route('getSchoolCountDetails') }}",
                type: 'GET',
                data: filterParams,
                beforeSend: function() {
                    $('#schoolDetailsModal').modal('show');
                    $('#modal-body').html(`
                        <div class="text-center p-5">
                            <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                            <p>Loading details...</p>
                        </div>
                    `);
                },
                success: function(res) {
                    if (res.status && res.data && res.data.length > 0) {
                        renderSchoolDetailsModal(res.data);
                    } else {
                        // Handle backend validation or empty results
                        let msg = res.message
                            ? `<div class="text-danger text-center p-4">${res.message}</div>`
                            : `<div class="text-center text-muted p-4">No data found for this selection.</div>`;
                        $('#modal-body').html(msg);
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching school details:', xhr);
                    let msg = 'Failed to load data.';

                    // Show detailed backend error message if available
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }

                    $('#modal-body').html(`
                        <div class="text-danger text-center p-4">${msg}</div>
                    `);
                }
            });

        });
    });
</script>
@endsection
