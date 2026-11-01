<?php


// print_r(auth()->user());

?>
<style>
    /* ==============================
       SIDEBAR DESIGN
    ============================== */

    .app-sidebar.app-sidebar3 {
        width: 255px;
        background: #ffffff !important;
        border-right: 1px solid #f0f0f0;
        box-shadow: 4px 0 18px rgba(0, 0, 0, 0.03);
        padding: 20px 14px;
    }

    /* Remove old sidebar background */
    .app-sidebar,
    .app-sidebar3,
    .side-menu {
        background: #ffffff !important;
    }

    /* User profile section */
    .app-sidebar__user {
        background: transparent !important;
        border-bottom: 1px solid #eeeeee;
        padding: 10px 5px 20px;
        margin-bottom: 15px;
    }

    .app-sidebar__user .avatar-xl {
        width: 65px;
        height: 65px;
        object-fit: cover;
        border: 3px solid #f3f3f3;
    }

    .app-sidebar__user .user-info h5 {
        color: #333333;
        font-size: 15px;
        margin-top: 8px;
    }

    .app-sidebar__user-name {
        color: #888888 !important;
        font-size: 12px;
    }

    /* Main menu label */
    .side-menu::before {
        content: "MAIN MENU";
        display: block;
        color: #a0a0a0;
        font-size: 10px;
        font-weight: 500;
        letter-spacing: 0.5px;
        padding: 5px 14px 10px;
    }

    .side-menu {
        list-style: none;
        padding: 0 !important;
        margin: 0 !important;
    }

    .side-menu>li.slide {
        margin-bottom: 3px;
    }

    /* Normal menu item */
    .side-menu__item {
        min-height: 40px;
        display: flex !important;
        align-items: center;
        position: relative;
        margin: 0;
        padding: 9px 13px !important;
        border-radius: 22px;
        background: transparent !important;
        color: #555555 !important;
        font-size: 14px;
        font-weight: 400;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }

    /* Menu icon */
    .side-menu__icon {
        width: 18px !important;
        height: 18px !important;
        min-width: 18px;
        margin-right: 11px !important;
        color: #555555 !important;
        stroke: currentColor;
        font-size: 17px !important;
        line-height: 18px;
    }

    /* Fix Font Awesome icon wrapper */
    .side-menu__item>span:first-child {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .side-menu__item>span:first-child .side-menu__icon {
        margin-right: 11px !important;
    }

    .side-menu__label {
        color: inherit !important;
        font-size: 14px;
        font-weight: 400;
        line-height: 20px;
        white-space: normal;
    }

    /* Hover */
    .side-menu__item:hover {
        background: #f1f7f5 !important;
        color: #176f65 !important;
        transform: translateX(2px);
    }

    .side-menu__item:hover .side-menu__icon,
    .side-menu__item:hover .side-menu__label {
        color: #176f65 !important;
    }

    /* Active item like screenshot */
    .side-menu__item.active,
    .side-menu__item.active:hover,
    .slide.is-expanded>.side-menu__item,
    .side-menu__item.home_btn.active {
        background: #19766b !important;
        color: #ffffff !important;
        box-shadow: none !important;
    }

    .side-menu__item.active .side-menu__icon,
    .side-menu__item.active .side-menu__label,
    .slide.is-expanded>.side-menu__item .side-menu__icon,
    .slide.is-expanded>.side-menu__item .side-menu__label {
        color: #ffffff !important;
        stroke: #ffffff !important;
    }

    /* Right arrow */
    .side-menu__item .angle {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        margin: 0 !important;
        color: #777777;
        font-size: 13px;
        transition: transform 0.2s ease;
    }

    .slide.is-expanded>.side-menu__item .angle {
        color: #ffffff;
        transform: translateY(-50%) rotate(90deg);
    }

    /* Submenu */
    .slide-menu {
        background: transparent !important;
        padding: 5px 0 7px 42px !important;
        margin: 0 !important;
        border: 0 !important;
    }

    .slide-menu li {
        margin: 2px 0;
    }

    .slide-item {
        display: block;
        position: relative;
        padding: 7px 10px !important;
        border-radius: 8px;
        color: #707070 !important;
        font-size: 13px;
        line-height: 18px;
        text-decoration: none !important;
        transition: all 0.2s ease;
    }

    .slide-item::before {
        content: "";
        position: absolute;
        left: -12px;
        top: 50%;
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #b4b4b4;
        transform: translateY(-50%);
    }

    .slide-item:hover,
    .slide-item.active {
        color: #19766b !important;
        background: #f2f7f6 !important;
        padding-left: 14px !important;
    }

    .slide-item:hover::before,
    .slide-item.active::before {
        background: #19766b;
    }

    /* Scrollbar */
    .app-sidebar::-webkit-scrollbar {
        width: 5px;
    }

    .app-sidebar::-webkit-scrollbar-track {
        background: transparent;
    }

    .app-sidebar::-webkit-scrollbar-thumb {
        background: #dddddd;
        border-radius: 10px;
    }

    .app-sidebar::-webkit-scrollbar-thumb:hover {
        background: #bbbbbb;
    }



    /* Mobile */
    @media (max-width: 991px) {
        .app-sidebar.app-sidebar3 {
            width: 255px;
            padding: 18px 14px;
        }
    }

    /* =====================================
   SIDEBAR LINK STATES
===================================== */

    /* Default menu item */
    .side-menu__item {
        min-height: 40px;
        display: flex !important;
        align-items: center;
        position: relative;
        padding: 9px 13px !important;
        border-radius: 22px;
        background-color: transparent !important;
        color: #555555 !important;
        font-size: 14px;
        font-weight: 400;
        text-decoration: none !important;
        transition:
            background-color 0.2s ease,
            color 0.2s ease,
            transform 0.2s ease;
    }

    /* Default label and icons */
    .side-menu__item .side-menu__label,
    .side-menu__item .side-menu__icon,
    .side-menu__item>span>i,
    .side-menu__item .angle {
        color: #555555 !important;
        stroke: currentColor;
    }

    /* Hover state */
    .side-menu__item:hover {
        background-color: #eef7f5 !important;
        color: #17675F !important;
        transform: translateX(2px);
    }

    .side-menu__item:hover .side-menu__label,
    .side-menu__item:hover .side-menu__icon,
    .side-menu__item:hover>span>i,
    .side-menu__item:hover .angle {
        color: #17675F !important;
        stroke: #17675F !important;
    }

    /* Active main menu and expanded parent */
    .side-menu__item.active,
    .side-menu__item.active:hover,
    .side-menu__item.active:focus,
    .slide.active>.side-menu__item,
    .slide.is-expanded>.side-menu__item,
    .slide.is-expanded>.side-menu__item:hover {
        background-color: #17675F !important;
        color: #ffffff !important;
        box-shadow: none !important;
        transform: none;
    }

    /* Active text, SVG, Font Awesome icon and arrow */
    .side-menu__item.active .side-menu__label,
    .side-menu__item.active .side-menu__icon,
    .side-menu__item.active>span>i,
    .side-menu__item.active .angle,
    .slide.active>.side-menu__item .side-menu__label,
    .slide.active>.side-menu__item .side-menu__icon,
    .slide.active>.side-menu__item>span>i,
    .slide.active>.side-menu__item .angle,
    .slide.is-expanded>.side-menu__item .side-menu__label,
    .slide.is-expanded>.side-menu__item .side-menu__icon,
    .slide.is-expanded>.side-menu__item>span>i,
    .slide.is-expanded>.side-menu__item .angle {
        /* color: #ffffff !important; */
        stroke: #ffffff !important;
    }

    /* Expanded parent arrow */
    .slide.is-expanded>.side-menu__item .angle {
        transform: translateY(-50%) rotate(90deg);
    }

    /* Submenu default */
    .slide-item {
        display: block;
        position: relative;
        padding: 7px 10px !important;
        border-radius: 8px;
        background-color: transparent !important;
        color: #707070 !important;
        font-size: 13px;
        line-height: 18px;
        text-decoration: none !important;
        transition:
            background-color 0.2s ease,
            color 0.2s ease,
            padding-left 0.2s ease;
    }

    /* Submenu hover */
    .slide-item:hover {
        background-color: #eef7f5 !important;
        color: #17675F !important;
        padding-left: 14px !important;
    }

    /* Active submenu */
    .slide-item.active,
    .slide-item.active:hover,
    .slide-item[aria-current="page"] {
        background-color: #17675F !important;
        color: #ffffff !important;
        padding-left: 14px !important;
    }

    /* Submenu dot */
    .slide-item::before {
        content: "";
        position: absolute;
        left: -12px;
        top: 50%;
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background-color: #b4b4b4;
        transform: translateY(-50%);
    }

    .slide-item:hover::before {
        background-color: #17675F;
    }

    .slide-item.active::before,
    .slide-item[aria-current="page"]::before {
        background-color: #17675F;
    }

    /* Default sidebar link */
    .side-menu__item {
        background-color: transparent !important;
        color: #555555 !important;
    }

    /* Default text and icon */
    .side-menu__item .side-menu__label,
    .side-menu__item .side-menu__icon,
    .side-menu__item>span>i,
    .side-menu__item .angle {
        color: #555555 !important;
        stroke: currentColor;
    }

    /* Hover state only */
    .side-menu__item:hover {
        background-color: #eef7f5 !important;
        color: #17675F !important;
    }

    .side-menu__item:hover .side-menu__label,
    .side-menu__item:hover .side-menu__icon,
    .side-menu__item:hover>span>i,
    .side-menu__item:hover .angle {
        color: #17675F !important;
        stroke: #17675F !important;
    }

    /* Apply active styling only to the current link */
    .side-menu__item.active,
    .side-menu__item.active:hover,
    .side-menu__item.active:focus {
        background-color: #17675F !important;
        color: #ffffff !important;
        box-shadow: none !important;
        transform: none !important;
    }

    /* Active link text and icons */
    .side-menu__item.active .side-menu__label,
    .side-menu__item.active .side-menu__icon,
    .side-menu__item.active>span>i,
    .side-menu__item.active .angle {
        color: #ffffff !important;
        stroke: #ffffff !important;
    }

    /* Dropdown is open, but it is not the current active link */
    .slide.is-expanded>.side-menu__item:not(.active) {
        background-color: transparent !important;
        color: #555555 !important;
    }

    .slide.is-expanded>.side-menu__item:not(.active) .side-menu__label,
    .slide.is-expanded>.side-menu__item:not(.active) .side-menu__icon,
    .slide.is-expanded>.side-menu__item:not(.active)>span>i {
        color: #555555 !important;
        stroke: currentColor !important;
    }

    /* Only rotate the dropdown arrow */
    .slide.is-expanded>.side-menu__item .angle {
        transform: translateY(-50%) rotate(90deg);
    }

    /* Active submenu link */
    .slide-item.active,
    .slide-item.active:hover,
    .slide-item[aria-current="page"] {
        background-color: #17675F !important;
        color: #ffffff !important;
    }


    /* Home icon default color: #252525 */
    .home-menu-icon {
        display: block !important;

        width: 24px !important;
        height: 22px !important;

        min-width: 24px !important;
        max-width: 24px !important;
        min-height: 22px !important;
        max-height: 22px !important;

        flex: 0 0 24px !important;
        flex-shrink: 0 !important;

        padding: 0 !important;
        margin-right: 13px !important;

        object-fit: contain;

        filter: brightness(0) saturate(100%) invert(12%) sepia(2%) saturate(10%) hue-rotate(314deg) brightness(93%) contrast(91%);
    }

    .side-menu__item.home_btn.active .home-menu-icon {
        filter: brightness(0) invert(1);
    }

    .home-menu-icon1 {
        width: 24px !important;
        height: 22px !important;
        min-width: 24px;
        min-height: 22px;
        max-width: 24px;
        max-height: 22px;

        /* object-fit: contain; */

        filter: brightness(0) saturate(100%) invert(12%) sepia(2%) saturate(10%) hue-rotate(314deg) brightness(93%) contrast(91%);

        transition: filter 0.2s ease;
    }

    /* Home icon on hover */
    .side-menu__item.home_btn:hover .home-menu-icon {
        filter: brightness(0) saturate(100%) invert(35%) sepia(20%) saturate(1264%) hue-rotate(124deg) brightness(88%) contrast(88%);
    }

    /* Home icon when the Home link is active */
    .side-menu__item.home_btn.active .home-menu-icon {
        filter: brightness(0) invert(1);
    }



    /* =========================================
   FINAL SIDEBAR ACTIVE-ROUTE FIX
========================================= */

    /* All non-current links must keep default styling */
    .side-menu__item:not(.current-route),
    .slide.active>.side-menu__item:not(.current-route),
    .slide.is-expanded>.side-menu__item:not(.current-route) {
        background-color: transparent !important;
        color: #555555 !important;
        box-shadow: none !important;
        transform: none !important;
    }

    /* Default text, icons and arrow */
    .side-menu__item:not(.current-route) .side-menu__label,
    .side-menu__item:not(.current-route) .side-menu__icon,
    .side-menu__item:not(.current-route)>span>i,
    .side-menu__item:not(.current-route) .angle {
        color: #555555 !important;
        stroke: currentColor !important;
    }

    /* Default PNG image color */
    .side-menu__item:not(.current-route) .home-menu-icon {
        filter: brightness(0) saturate(100%) invert(12%) sepia(2%) saturate(10%) hue-rotate(314deg) brightness(93%) contrast(91%) !important;
    }

    /* Hover only for non-active links */
    .side-menu__item:not(.current-route):hover {
        background-color: #eef7f5 !important;
        color: #17675F !important;
    }

    .side-menu__item:not(.current-route):hover .side-menu__label,
    .side-menu__item:not(.current-route):hover .side-menu__icon,
    .side-menu__item:not(.current-route):hover>span>i,
    .side-menu__item:not(.current-route):hover .angle {
        color: #17675F !important;
        stroke: #17675F !important;
    }

    /* Only exact current route */
    .side-menu__item.current-route,
    .side-menu__item.current-route:hover,
    .side-menu__item.current-route:focus {
        background-color: #17675F !important;
        color: #ffffff !important;
        box-shadow: none !important;
        transform: none !important;
    }

    /* Current-route text and SVG/Font Awesome icon */
    .side-menu__item.current-route .side-menu__label,
    .side-menu__item.current-route .side-menu__icon,
    .side-menu__item.current-route>span>i,
    .side-menu__item.current-route .angle {
        color: #ffffff !important;
        stroke: #ffffff !important;
    }

    /* Current-route PNG image */
    .side-menu__item.current-route .home-menu-icon {
        filter: brightness(0) invert(1) !important;
    }

    /* Expanded dropdown only rotates arrow */
    .slide.is-expanded>.side-menu__item:not(.current-route) .angle {
        color: #555555 !important;
        transform: translateY(-50%) rotate(90deg);
    }

    /* Active submenu route */
    /* .slide-item.current-route,
.slide-item.current-route:hover {
    background-color: #17675F !important;
    color: #ffffff !important;
    padding-left: 14px !important;
} */



    /* =====================================
   CONSISTENT SIDEBAR ITEM SPACING
===================================== */

    /* Equal vertical gap between every main menu item */
    .side-menu>li.slide {
        margin: 0 0 8px 0 !important;
        padding: 0 !important;
    }

    /* Equal height and alignment for every main link */
    .side-menu>li.slide>.side-menu__item {
        height: 46px !important;
        min-height: 46px !important;
        max-height: 46px !important;

        display: flex !important;
        align-items: center !important;

        padding: 0 13px !important;
        margin: 0 !important;

        line-height: 1 !important;
        box-sizing: border-box !important;
    }

    /* Make all PNG icons use the same box */
    .side-menu>li.slide>.side-menu__item>.home-menu-icon {
        width: 24px !important;
        height: 24px !important;

        min-width: 24px !important;
        max-width: 24px !important;

        min-height: 24px !important;
        max-height: 24px !important;

        flex: 0 0 24px !important;
        margin: 0 13px 0 0 !important;
        padding: 0 !important;

        object-fit: contain !important;
    }

    /* Make all SVG and font icons use the same box */
    .side-menu>li.slide>.side-menu__item>.side-menu__icon,
    .side-menu>li.slide>.side-menu__item>span>.side-menu__icon {
        width: 24px !important;
        height: 24px !important;

        min-width: 24px !important;
        max-width: 24px !important;

        min-height: 24px !important;
        max-height: 24px !important;

        flex: 0 0 24px !important;
        margin: 0 13px 0 0 !important;
        padding: 0 !important;
    }

    /* Equal text alignment */
    .side-menu>li.slide>.side-menu__item>.side-menu__label {
        display: flex !important;
        align-items: center !important;

        min-height: 24px !important;
        margin: 0 !important;
        padding: 0 !important;

        line-height: 24px !important;
    }

    /* Keep the right arrow vertically centered */
    .side-menu>li.slide>.side-menu__item>.angle {
        top: 50% !important;
        right: 14px !important;
        margin: 0 !important;
        transform: translateY(-50%);
    }

    /* Expanded arrow */
    .side-menu>li.slide.is-expanded>.side-menu__item>.angle {
        transform: translateY(-50%) rotate(90deg) !important;
    }

    /* Avoid extra gap from hidden submenus */
    .side-menu>li.slide>.slide-menu:not([style*="display: block"]) {
        margin: 0 !important;
    }

    /* Submenu spacing */
    .side-menu>li.slide>.slide-menu {
        padding-top: 6px !important;
        padding-bottom: 2px !important;
    }



    /* Inactive menu */
    .side-menu__item {
        color: #555555 !important;
    }

    .side-menu__item .side-menu__label,
    .side-menu__item .side-menu__icon,
    .side-menu__item .angle {
        color: #555555 !important;
        stroke: currentColor !important;
    }

    /* Hover (inactive) */
    .side-menu__item:hover:not(.active) {
        color: #555555 !important;
        background: transparent !important;
    }

    .side-menu__item:hover:not(.active) .side-menu__label,
    .side-menu__item:hover:not(.active) .side-menu__icon,
    .side-menu__item:hover:not(.active) .angle {
        color: #555555 !important;
    }

    /* Active menu */
    .side-menu__item.active,
    .slide.active-route>.side-menu__item {
        background: #17675F !important;
        color: #ffffff !important;
    }

    .side-menu__item.active .side-menu__label,
    .side-menu__item.active .side-menu__icon,
    .side-menu__item.active .angle,
    .slide.active-route>.side-menu__item .side-menu__label,
    .slide.active-route>.side-menu__item .side-menu__icon,
    .slide.active-route>.side-menu__item .angle {
        color: #ffffff !important;
        stroke: #ffffff !important;
    }


    /* =====================================
   SUBMENU ACTIVE STYLE - SECOND SCREEN
===================================== */

    /* Submenu container */
    .slide-menu {
        position: relative;
        padding-left: 42px !important;
    }

    /* Default submenu item */
    .slide-menu .slide-item {
        position: relative;
        background: transparent !important;
        color: #555555 !important;
        border-radius: 0 !important;
        padding: 9px 10px 9px 12px !important;
        margin: 0 !important;
    }

    /* Remove old round dot */
    .slide-menu .slide-item::before {
        display: none !important;
    }

    /* Active submenu item */
    .slide-menu .slide-item.current-route,
    .slide-menu .slide-item.active,
    .slide-menu .slide-item[aria-current="page"] {
        background: transparent !important;
        color: #17675F !important;
        font-weight: 500 !important;
        padding-left: 12px !important;
    }

    /* Left vertical green indicator */
    .slide-menu .slide-item.current-route::after,
    .slide-menu .slide-item.active::after,
    .slide-menu .slide-item[aria-current="page"]::after {
        content: "";
        position: absolute;
        left: -19px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #17675F;
        border-radius: 0;
    }

    /* Active submenu text */
    .slide-menu .slide-item.current-route span,
    .slide-menu .slide-item.active span,
    .slide-menu .slide-item[aria-current="page"] span {
        color: #17675F !important;
    }

    /* Hover only */
    .slide-menu .slide-item:hover:not(.current-route):not(.active) {
        background: transparent !important;
        color: #17675F !important;
        padding-left: 12px !important;
    }

    /* Open submenu left border */
    .slide.is-expanded .slide-menu {
        border-left: 3px solid #555555;
        margin-left: 18px;
        /* icon ke baad alignment */
        padding-left: 12px;
    }

    /* Active submenu ka border green rahe */
    /* .slide-item.active {
    border-left: 3px solid #17675F;
    margin-left: -15px;
    padding-left: 12px;
} */
    /* Hover effect for inactive menu items */
    /* Hover ONLY for non-active and non-current items */
    .side-menu__item:not(.active):not(.current-route):hover {
        background: #F3F6F9 !important;
        color: #555555 !important;
        border-radius: 22px;
    }

    .side-menu__item:not(.active):not(.current-route):hover .side-menu__label,
    .side-menu__item:not(.active):not(.current-route):hover .side-menu__icon,
    .side-menu__item:not(.active):not(.current-route):hover .angle {
        color: #555555 !important;
    }

    .side-menu__item:hover:not(.active) .side-menu__label,
    .side-menu__item:hover:not(.active) .side-menu__icon,
    .side-menu__item:hover:not(.active) .angle {
        color: #555555 !important;
    }

    /* Submenu hover */
    .slide-item:hover:not(.active) {
        background: #F3F6F9 !important;
        color: #555555 !important;
        border-radius: 6px;
    }

    .angle {
        width: 12px;
        height: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        line-height: 1;
    }

    /* ==============================
   IMPROVED SUBMENU STYLE
============================== */

    .slide-menu {
        position: relative;
        margin: 0 0 8px 21px !important;
        padding: 5px 0 5px 18px !important;
        /* border-left: 2px solid #d4dada !important; */
        background: transparent !important;
    }

    /* Each submenu row */
    .slide-menu li {
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Default submenu item */
    .slide-menu .slide-item {
        display: flex !important;
        align-items: center !important;
        min-height: 46px;
        padding: 10px 14px 10px 18px !important;
        margin: 0 !important;

        background: transparent !important;
        border-radius: 0 8px 8px 0 !important;

        color: #333333 !important;
        font-size: 14px;
        font-weight: 400;
        line-height: 20px;

        text-decoration: none !important;
        transition: background-color .2s ease, color .2s ease;
    }

    /* Remove old dot/indicator */
    .slide-menu .slide-item::before {
        display: none !important;
    }

    /* Hover */
    .slide-menu .slide-item:hover:not(.current-route):not(.active) {
        background: #eef4f3 !important;
        color: #17675F !important;
        padding-left: 18px !important;
    }

    /* Active/current submenu */
    .slide-menu .slide-item.current-route,
    .slide-menu .slide-item.active,
    .slide-menu .slide-item[aria-current="page"] {
        position: relative;
        background: transparent !important;
        color: #17675F !important;
        font-weight: 500 !important;
        padding-left: 18px !important;
    }

    /* Green section on the left border for active row */
    .slide-menu .slide-item.current-route::after,
    .slide-menu .slide-item.active::after,
    .slide-menu .slide-item[aria-current="page"]::after {
        content: "";
        position: absolute;
        left: -20px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #17675F;
    }

    /* Keep span text color consistent */
    .slide-menu .slide-item.current-route span,
    .slide-menu .slide-item.active span,
    .slide-menu .slide-item[aria-current="page"] span {
        color: #17675F !important;
    }

    .slide.is-expanded>.slide-menu>li {
        border-left: 2px solid #555555;
        padding-left: 16px;
    }

    /* Default submenu left line */
    .slide.is-expanded>.slide-menu>li {
        border-left: 2px solid #555555;
        padding-left: 16px;
    }

    /* Active submenu: same single line becomes green */
    .slide.is-expanded>.slide-menu>li:has(.slide-item.active),
    .slide.is-expanded>.slide-menu>li:has(.slide-item.current-route),
    .slide.is-expanded>.slide-menu>li:has(.slide-item[aria-current="page"]) {
        border-left-color: #17675F;
    }

    /* Remove all extra active borders/indicators */
    .slide-menu .slide-item.active,
    .slide-menu .slide-item.current-route,
    .slide-menu .slide-item[aria-current="page"] {
        border-left: none !important;
        margin-left: 0 !important;
    }

    .slide-menu .slide-item.active::after,
    .slide-menu .slide-item.current-route::after,
    .slide-menu .slide-item[aria-current="page"]::after {
        display: none !important;
    }

    .slide-menu:has(.slide-item.active),
    .slide-menu:has(.slide-item.current-route) {
        padding-left: 0 !important;
    }

    /* Non-active items only */
    .slide-item:not(.active):not(.current-route):hover {
        color: #17675F !important;
        background: transparent !important;
    }

    /* Active item - hover should NOT change */
    .slide-item.active:hover,
    .slide-item.current-route:hover {
        color: #17675F !important;
        /* Same as active color */
        background: transparent !important;
    }




    /* Equal spacing between all root menu items */
    .side-menu>.slide {
        margin: 6px 0 !important;
    }

    /* Remove extra space from first and last item */
    .side-menu>.slide:first-child {
        margin-top: 6px !important;
    }

    .side-menu>.slide:last-child {
        margin-bottom: 6px !important;
    }

    /* Same height for every root item */
    .side-menu>.slide>.side-menu__item {
        min-height: 46px;
        padding: 11px 18px;
        display: flex;
        align-items: center;
    }

    /* Active item should never change on hover */
    .side-menu__item.active,
    .side-menu__item.active:hover,
    .side-menu__item.current-route,
    .side-menu__item.current-route:hover {
        background: #17675F !important;
        color: #ffffff !important;
        transform: none !important;
    }

    .side-menu__item.active:hover .side-menu__label,
    .side-menu__item.active:hover .side-menu__icon,
    .side-menu__item.active:hover .angle,
    .side-menu__item.current-route:hover .side-menu__label,
    .side-menu__item.current-route:hover .side-menu__icon,
    .side-menu__item.current-route:hover .angle {
        color: #ffffff !important;
    }



    /* Parent root active when any submenu item is active */
    .side-menu>.slide:has(> .slide-menu .slide-item.active,
        > .slide-menu .slide-item.current-route,
        > .slide-menu .slide-item[aria-current="page"])>.side-menu__item {
        background: #17675F !important;
        color: #ffffff !important;
        transform: none !important;
    }

    /* Parent text, SVG icon and arrow */
    .side-menu>.slide:has(> .slide-menu .slide-item.active,
        > .slide-menu .slide-item.current-route,
        > .slide-menu .slide-item[aria-current="page"])>.side-menu__item .side-menu__label,

    .side-menu>.slide:has(> .slide-menu .slide-item.active,
        > .slide-menu .slide-item.current-route,
        > .slide-menu .slide-item[aria-current="page"])>.side-menu__item .side-menu__icon,

    .side-menu>.slide:has(> .slide-menu .slide-item.active,
        > .slide-menu .slide-item.current-route,
        > .slide-menu .slide-item[aria-current="page"])>.side-menu__item .angle {
        color: #ffffff !important;
        stroke: #ffffff !important;
    }

    /* Parent PNG icon */
    .side-menu>.slide:has(> .slide-menu .slide-item.active,
        > .slide-menu .slide-item.current-route,
        > .slide-menu .slide-item[aria-current="page"])>.side-menu__item .home-menu-icon {
        filter: brightness(0) invert(1) !important;
    }

    /* Parent active hover should remain unchanged */
    .side-menu>.slide:has(> .slide-menu .slide-item.active,
        > .slide-menu .slide-item.current-route,
        > .slide-menu .slide-item[aria-current="page"])>.side-menu__item:hover {
        background: #17675F !important;
        color: #ffffff !important;
        transform: none !important;
    }



    /* ==============================
   COLLAPSED SIDEBAR
============================== */

    /* Hide text */
    .app.sidenav-toggled .side-menu__label {
        display: none !important;
    }

    /* Hide arrow */
    .app.sidenav-toggled .angle {
        display: none !important;
    }

    /* Remove extra spacing from menu item */
    .app.sidenav-toggled .side-menu__item {
        justify-content: center !important;
        padding: 12px 0 !important;
    }

    /* Remove icon margins */
    .app.sidenav-toggled .side-menu__icon,
    .app.sidenav-toggled .home-menu-icon {
        margin: 0 !important;
    }

    /* Keep icon centered */
    .app.sidenav-toggled .side-menu__icon,
    .app.sidenav-toggled .home-menu-icon {
        min-width: auto !important;
    }

    .app.sidenav-toggled .home-menu-icon {
        margin-right: 0 !important;
        flex: 0 0 auto !important;
    }

</style>
<aside class="app-sidebar app-sidebar3">
    {{-- <div class="app-sidebar__user">
		<div class="dropdown user-pro-body text-center">
			<div class="user-pic">
				<img src="{{asset('/staffimages/' . auth()->user()->employee_id . '.jpg')}}" alt="user-img"
    class="avatar-xl rounded-circle mb-1">
    </div>
    <div class="user-info">
        <h5 class=" mb-1 font-weight-bold"><?= auth()->user()->full_name ?></h5>
        <span class="text-muted app-sidebar__user-name text-sm"><?= auth()->user()->designation ?></span>
    </div>
    </div>
    </div> --}}
    <ul class="side-menu">
        <li class="slide">
            <a class="side-menu__item home_btn" data-bm="home" href="{{ url('/') }}">
                <img src="{{ asset('images/homes.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
                <span class="side-menu__label">Home</span></a>
        </li>
        <li class="slide">
            <a class="side-menu__item " data-toggle="slide" href="#">
                {{-- <span><i class="fa fa-car side-menu__icon mt-1"></i></span> --}}
                <img src="{{ asset('images/carp.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
                <span class="side-menu__label">Car Pooling</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{url('/car-pooling')}}"><span>Ride Registration</span></a></li>
                {{-- <li><a class="slide-item" target="__blank" href="{{url('/car-pooling-search')}}"><span>Car Search
                </span></a>
        </li> --}}
    </ul>
    </li>

    @if(auth()->user()->group_id == 825)
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                </path>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
            <span class="side-menu__label">TA</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">


            <!-- <li><a class="slide-item"  href="{{url('reports/faculty/list')}}"><span>Faculty Assurance Report</span></a></li> https://devstudent.bnu.edu.pk/login-user-by-users-->
            <li><a class="slide-item" href="{{url('move-to-student?token=' . auth()->user()->api_token)}}"><span>Students</span></a>
            </li>
        </ul>
    </li>
    @else
    @if(auth()->user()->group_id == 1024)



    <li class="slide">
        <a class="side-menu__item " data-toggle="slide" href="#">
            {{-- <span><i class="fa fa-share-alt side-menu__icon mt-1"></i></span> --}}

            <img src="{{ asset('images/homes.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">SharePoint</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">


            <li><a class="slide-item" target="__blank" href="https://share.bnu.edu.pk/sites/facultynstaff"><span>Faculty SharePoint</span></a></li>
            <li><a class="slide-item" target="__blank" href="https://share.bnu.edu.pk/sites/students/"><span></span>Student SharePoint</a></li>
            <li><a class="slide-item" target="__blank" href="https://share.bnu.edu.pk/sites/Careers"><span></span>Job Portal</a></li>
            <li><a class="slide-item" target="__blank" href="https://share.bnu.edu.pk/sites/orip"><span></span>Orip</a>
            </li>


            @php
            $allowedSharePointEmployeeIds = [
            553,
            1313,
            1273,
            276,
            226,
            259,
            512,
            2815,
            2855,
            7335,
            5375,
            8157,
            9194,
            9096,
            9258,
            9241,
            9325,
            9328,
            9385,
            16
            ];

            $user = auth()->user();
            $hasSharePointAccess = in_array($user->employee_id, $allowedSharePointEmployeeIds) || $user->group_id == 644;
            @endphp

            @if ($hasSharePointAccess)
            <li>
                <a class="slide-item" target="_blank" href="http://share.bnu.edu.pk/sites/Careers">
                    <span>Job Portal</span>
                </a>
            </li>
            @endif



        </ul>

    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('staff-exit-interview-form')}}">

            <img src="{{ asset('images/seif.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Staff Exit Interview Form</span>
        </a>
    </li>




    <!---- -->


    <?php


				// $groupId = auth()->user()->group_id;
				// Group IDs: 644 - Software Team, 223 - Dean, 364 - Deans SCIT, 445 - HOD Ijaz
				// if (in_array($groupId, [644, 223, 364, 445]) || auth()->user()->username == 'hifsa.farooq') {

				$groupId = auth()->user()->group_id;
				$username = auth()->user()->username;
				// Group IDs allowed
				$allowedGroups = [644, 223, 364, 445];
				// Usernames allowed
				$allowedUsers = [
					'hifsa.farooq',
					'zara.amjad',
					'arez.ali',
					'ahsan.fazal',
					'sara.assad',
					'ghammaz.husnain',
					'zeeshan.sarwar',
					'nouman.ali',
					'amna.humayun',
					'nabeel.ahsan',
					'osama.tariq',
					'haider.ali1',
					'huda.sarfraz',
					'farhat_nadeem',
					'asim.irshad',
					'noreen.khalid',
					'ameer.hamza',
					'farhan.ali',
					'talia.noreen',
					'muhammad.ali1',
					'ammar.faiz',
					'umaima.mohsin',
					'sana.aziz',
					'zoona.kundi',
					'neha.qasim',


					'werdah.munib',
					'tabina.sirhindi',
					'harris.badar',
					'zeeshan.zaighum',
					'nasir.ali',
					'usman.rana',
					'sheharyar.zahid',
					'warda.shah',
					'nosharwhan.adil',
					'naveed.aasim',
					'farahat.ali',
					'rabia.noor',
					'sarmed.cheema',
					'sumaiya.naumaan',
					'natash.ali',
					'muhammad.adeel',
					'ayesha.seher',
					'zaman.aziz'

				];

				if (in_array($groupId, $allowedGroups) || in_array($username, $allowedUsers)) {


																																																																?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <!-- SVG Icon -->
            <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                </path>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
            <span class="side-menu__label">Student Progress</span>
            <i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i>
        </a>
        <ul class="slide-menu">
            <li>
                <a class="slide-item" href="<?php echo url('/reports/report-generator'); ?>">
                    <span>Student Progress Monitoring Portal</span>
                </a>
            </li>
        </ul>
    </li>
    <?php
				}
																																																																?>


    <!-- peer mentorship-->
    <?php
				if (in_array(auth()->user()->group_id, array(644)) || auth()->user()->username == 'neha.qasim') {
																																																																		?>


    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <circle cx="12" cy="7" r="4" stroke-width="2" />
                <path d="M4 21v-2a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v2" stroke-width="2" />
            </svg>
            <span class="side-menu__label">Peer Mentorship</span>
            <i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i>
        </a>
        <ul class="slide-menu">
            <?php {?>
            <li><a class="slide-item" href="{{url('pm-scholarship-students')}}"><span>Scholarship
                        students</span></a></li>
            <?php } ?>
        </ul>

        <ul class="slide-menu">
            <?php {?>
            <li><a class="slide-item" href="{{url('interview_schedules')}}"><span>Interview Schedules</span></a>
            </li>
            <?php } ?>
        </ul>

        <ul class="slide-menu">
            <?php {?>
            <li><a class="slide-item" href="{{url('mentors_list')}}"><span>Mentor List</span></a></li>
            <?php } ?>
        </ul>
        <ul class="slide-menu">
            <?php {?>
            <li><a class="slide-item" href="{{url('admin-mentors-list')}}"><span>Admin Mentor List</span></a></li>
            <?php } ?>
            {{-- <li><a class="slide-item" href="{{url('repeating-course')}}"><span>Repeating Course
                Report</span></a>
    </li>
    <li><a class="slide-item" href="{{url('graduate-summary-report')}}"><span>Graduate Summary
                Report</span></a></li> --}}
    </ul>
    <ul class="slide-menu">
        <?php {?>
        <li><a class="slide-item" href="{{url('be_mentors_list')}}"><span>Be Mentor List</span></a></li>
        <?php } ?>
        {{-- <li><a class="slide-item" href="{{url('repeating-course')}}"><span>Repeating Course
            Report</span></a></li>
        <li><a class="slide-item" href="{{url('graduate-summary-report')}}"><span>Graduate Summary
                    Report</span></a></li> --}}
    </ul>

    <ul class="slide-menu">
        <?php {?>
        <li><a class="slide-item" href="{{url('pm-students-requests')}}"><span>Student Request List</span></a>
        </li>
        <?php } ?>
        {{-- <li><a class="slide-item" href="{{url('repeating-course')}}"><span>Repeating Course
            Report</span></a></li>
        <li><a class="slide-item" href="{{url('graduate-summary-report')}}"><span>Graduate Summary
                    Report</span></a></li> --}}
    </ul>

    <ul class="slide-menu">
        <?php {?>
        <li><a class="slide-item" href="{{url('intro-session')}}"><span>Sessions</span></a></li>
        <?php } ?>
        {{-- <li><a class="slide-item" href="{{url('repeating-course')}}"><span>Repeating Course
            Report</span></a></li>
        <li><a class="slide-item" href="{{url('graduate-summary-report')}}"><span>Graduate Summary
                    Report</span></a></li> --}}
    </ul>




    </li>
    <?php } ?>
    <!-- peer mentorship ends here-->

    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span class="side-menu__label">University Events</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">
            <li><a class="slide-item" href="{{url('UniversityEvent/register')}}"><span>Add University
                        Event</span></a></li>
        </ul>
    </li>

    <li class="slide">
        <a class="side-menu__item" href="{{url('eportal-dashboard')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label"> E-Portal Requests</span>
        </a>
    </li>
    <li class="slide">
        <a class="side-menu__item" href="{{url('eportal-approval')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label"> E-Portal Approval</span>
        </a>
    </li>

    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            {{-- <span><i class="fa fa-money side-menu__icon mt-1"></i></span> --}}

            <img src="{{ asset('images/homes.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Student Fine</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i>
        </a>
        <ul class="slide-menu">
            <li><a class="slide-item" href="/students-fine"><span>Apply Student Fine</span></a></li>
        </ul>
    </li>

    <?php if (auth()->user()->username == 'wajeeha.nabeel') { ?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <span class="side-menu__label">Report Generator</span>
            <i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i>
        </a>
        <ul class="slide-menu">
            <li>
                <a class="slide-item" href="/student-profile-rec"><span>Student Profile Data</span>
                    <!-- Updated label for clarity --></a>
            </li>
        </ul>
    </li>
    <?php            } ?>

    @else

    <!-- vc if condition start -->
    <?php
					if (auth()->user()->group_id == "8") { ?>
    <?php if (auth()->user()->username == 'orip' || auth()->user()->username == 'mansoor.ahmad' || auth()->user()->username == 'babar.jamil' || auth()->user()->username == 'ayeda.hamed') {?>

    <li class="slide">
        <a class="side-menu__item" href="{{url('staff/task-list')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Task Management</span>
        </a>
    </li>
    <?php if (auth()->user()->username == 'ayeda.hamed') {?>
    <li class="slide">
        <a class="side-menu__item" href="{{url('UniversityEvent/register')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">University Event</span>
        </a>
    </li>
    <?php }?>
    @if(auth()->user()->username == 'babar.jamil')
    <li class="slide">
        <a class="side-menu__item" href="{{url('eportal-dashboard')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label"> E-Portal Requests</span>
        </a>
    </li>
    @endif

    <?php   }?>
    <?php if (auth()->user()->username == 'moeedyusuf' || auth()->user()->username == 'sumair.shafique') {?>
    {{-- VC reports generator --}}
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            {{-- <span><i class="fa fa-file-text-o side-menu__icon mt-1"></i></span> --}}

            <img src="{{ asset('images/reportsss.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <!-- Using 'file-text-o' for reports -->
            <span class="side-menu__label">Report Generator</span>
            <i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i>
        </a>
        <ul class="slide-menu">
            <li>
                <a class="slide-item" href="/reports/bnu-admission-reports-generator"><span>Attendance Analysis
                        Portal </span> <!-- Updated label for clarity --></a>
            </li>
            <li>
                <a class="slide-item" href="/reports/bnu-admission-reports-generator-detail"><span>Admission
                        Analysis Portal </span> <!-- Updated label for clarity --></a>
            </li>
            <li>
                <a class="slide-item" href="/student-profile-rec"><span>Student Profile Data</span>
                    <!-- Updated label for clarity --></a>
            </li>
            <li>
                <a class="slide-item" href="{{url('gymSubscriptionReport')}}"><span>Gym Report</span></a>
            </li>
            <li>
                <a class="slide-item" href="{{url('challan-report')}}"><span>Challan Report</span></a>
            </li>
            <li>
                <a class="slide-item" href="{{url('student-staff-exit-form-report')}}"><span>Student / Faculty /
                        Staff Exit Comments</span></a>
            </li>
        </ul>
    </li>

    <li class="slide link_for_web">
        <a class="side-menu__item" href="{{url('staff/todo-list')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">To-Do List</span>
        </a>
    </li>
    <li class="slide link_for_mobile">
        <a class="side-menu__item" href="{{url('staff/ProjectBoard')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">To-Do List</span>
        </a>
    </li>
    <li class="slide link_for_mobile">
        <a class="side-menu__item" href="{{url('staff/personal_todo-list')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Personal To-Do List</span>
        </a>
    </li>
    <li class="slide link_for_mobile">
        <a class="side-menu__item" href="{{url('staff/immediate_todo-list')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Immediate To-Do List</span>
        </a>
    </li>
    <li class="slide">
        <a class="side-menu__item" href="{{url('staff/task-list')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Task Management</span>
        </a>
    </li>
    <li class="slide ">
        <a class="side-menu__item" data-toggle="slide" href="#">

            <span class="side-menu__label">
                {{-- <i class="fa fa-list side-menu__icon mt-1"></i> --}}

                <img src="{{ asset('images/subsp.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
                Subscriptions</span><i class="angle fe fe-chevron-right"></i>
        </a>
        <ul class="slide-menu">
            <li class="sub-slide">
                <a class="slide-item" href="{{url('staff-subscription-form')}}"><span>Gym Subscription</span></a>
            </li>
        </ul>

    </li>


    <?php } ?>
    <?php if (auth()->user()->username != 'mansoor.ahmad' && auth()->user()->username != 'babar.jamil' && auth()->user()->username != 'ayeda.hamed') {?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">E-Portal</span>
            <i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i>
        </a>
        <ul class="slide-menu">
            <li>
                <a class="slide-item" href="{{url('vc/eportal-tracking')}}">
                    <span>E-Portal Tracking</span>
                </a>
            </li>
            <li>
                <a class="slide-item" href="{{url('vc/eportal-dashboard')}}">
                    <span>E-Portal Requests</span>
                </a>
            </li>
            <li>
                <a class="slide-item" href="{{url('vc/eportal-report')}}">
                    <span>E-Portal Report</span>
                </a>
            </li>
            <li>
                <a class="slide-item" href="{{url('vc/eportal-summary')}}">
                    <span>E-Portal Summary</span>
                </a>
            </li>
        </ul>
    </li>


    <li class="slide">
        <a class="side-menu__item " data-toggle="slide" href="#">
            {{-- <span><i class="fa fa-share-alt side-menu__icon mt-1"></i></span> --}}

            <img src="{{ asset('images/homes.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">SharePoint</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">
            <li><a class="slide-item" target="__blank" href="https://share.bnu.edu.pk/sites/facultynstaff"><span>Faculty SharePoint</span></a></li>
            <li><a class="slide-item" target="__blank" href="https://share.bnu.edu.pk/sites/students/"><span></span>Student SharePoint</a></li>
            <li><a class="slide-item" target="__blank" href="https://share.bnu.edu.pk/sites/orip"><span></span>Orip</a></li>
        </ul>
    </li>


    <?php } ?>
    <?php if (auth()->user()->username == 'aizaz.hussain' && auth()->user()->group_id == '8') {?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                </path>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
            <span class="side-menu__label">QA</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">
            <!-- <li><a class="slide-item"  href="{{url('reports/faculty/list')}}"><span>Faculty Assurance Report</span></a></li> -->
            <li><a class="slide-item" href="{{url('reports/faculty-evaluation')}}"><span>Faculty Evaluation
                        Report</span></a></li>
        </ul>
    </li>
    <li class="slide">
        <a class="side-menu__item " data-toggle="slide" href="#">
            {{-- <span><i class="fa fa-book  side-menu__icon mt-1"></i></span> --}}

            <img src="{{ asset('images/homes.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label"> Short Courses</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">
            <li>
                <a class="slide-item" href="{{url('short_courses')}}"><span>Add Course</span></a>
            </li>
            <li>
                <a class="slide-item" href="{{url('shortCourse_applicants')}}"><span>Applicants List</span></a>
            </li>
        </ul>
    </li>
    <li class="slide">
        <a class="side-menu__item" href="{{url('UniversityEvent/register')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">University Event</span>
        </a>
    </li>

    <?php } ?>

    <?php

					} else {

																																																																																																		?>
    <?php if (auth()->user()->group_id == 644 || auth()->user()->group_id == 121) {?>



    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('university-leaving-report')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Staff Exit Report</span>
        </a>
    </li>



    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('staff-exit-request-list')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Staff Exit Request List</span>
        </a>
    </li>




    <?php   }?>
    <?php if (auth()->user()->group_id == 644) {?>

    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('announcements')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Mobile Announcements</span>
        </a>
    </li>
    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('update-test-slip-date')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Admission Test Dates</span>
        </a>
    </li>

    <?php   }?>


    <?php if (auth()->user()->group_id == 644 || auth()->user()->username == 'haider.raza') {?>

    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('academic-session')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Academic calender</span>
        </a>
    </li>

    <?php   }?>





    <?php if (auth()->user()->group_id == 644 || auth()->user()->username == 'noreen') {?>
    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('exit-form-report')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14,2 14,8 20,8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10,9 9,9 8,9"></polyline>
            </svg>
            <span class="side-menu__label">Exit Form Report</span>
        </a>
    </li>
    <?php } ?>






    <?php            if (auth()->user()->group_id == 644 || auth()->user()->group_id == 964 || auth()->user()->group_id == 1024 || auth()->user()->group_id == 844 || auth()->user()->group_id == 324 || auth()->user()->group_id == 744 || auth()->user()->username == 'ahmer.sayeed' || auth()->user()->username == 'ali.sher' || auth()->user()->username == 'hassan.farooq') { ?>

    <li class="slide">
        <a class="side-menu__item " data-toggle="slide" href="#">
            {{-- <span><i class="fa fa-bed side-menu__icon mt-1"></i></span> --}}

            <img src="{{ asset('images/homes.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Hostel</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">



            <!-- <li><a class="slide-item" href="{{ url('/hostel-update-checkin-and-checkout-date') }}"><span>Hostel Allocation History</span></a></li> -->



            <?php                if (auth()->user()->group_id == 644 || auth()->user()->group_id == 324) { ?>
            <li><a class="slide-item" href="{{ url('/hostel-form-visibility') }}"><span>Hostel Form
                        Visibility</span></a></li>
            <?php                } ?>


            <?php                if (auth()->user()->group_id == 644 || auth()->user()->group_id == 284 || auth()->user()->group_id == 844 || auth()->user()->group_id == 744 || auth()->user()->username == 'ahmer.sayeed' || auth()->user()->username == 'ali.sher') { ?>
            <li><a class="slide-item" href="{{ url('/hostel-applicants') }}"><span>Hostel
                        Applicants</span></a></li>
            <?php                } ?>

            <?php                if (auth()->user()->group_id == 644 || auth()->user()->group_id == 284 || auth()->user()->group_id == 844 || auth()->user()->group_id == 744 || auth()->user()->username == 'ahmer.sayeed' || auth()->user()->username == 'ali.sher' || auth()->user()->username == 'hassan.farooq') { ?>
            <li><a class="slide-item" href="{{ url('/hostel-students') }}"><span>Hostel
                        Students</span></a></li>
            <?php                } ?>

            <?php                if (auth()->user()->group_id == 644 || auth()->user()->group_id == 284 || auth()->user()->group_id == 964 || auth()->user()->username == 'ahmer.sayeed' || auth()->user()->username == 'ali.sher') { ?>
            <li><a class="slide-item" href="{{ url('/hostel-summary') }}"><span>Hostel Summary</span></a>
            </li>
            <li><a class="slide-item" href="{{ url('/new-hostel-report') }}"><span>New Applicants Hostel
                        Summary</span></a>

            <li><a class="slide-item" target="__blank" href="{{ url('/hostel-vacate') }}"><span>Hostel
                        Vacate</span></a></li>
            <li><a class="slide-item" target="__blank" href="{{ url('/leave-requests') }}"><span>Hostel
                        Night leave</span></a></li>
            <li><a class="slide-item" href="{{ url('/facilities') }}"><span>Hostel Facilities</span></a>
            </li>
            <li><a class="slide-item" href="{{ url('/hostel-management') }}"><span>Hostel Type & Floor
                        Management</span></a></li>
            <li><a class="slide-item" href="{{ url('/hostel/checkin-checkout') }}"><span>Checkin
                        Checkout</span></a></li>


            <?php                } ?>
        </ul>
    </li>

    <?php            } ?>





    <?php if (auth()->user()->group_id == 644 || auth()->user()->username == 'sidra.tanveer' || auth()->user()->username == 'asbah.rehman' || auth()->user()->username == 'maheen.james' || auth()->user()->username == 'naureen.mirza' || auth()->user()->username == 'saqib.jamil' || auth()->user()->username == 'esha.abid' || auth()->user()->username == 'ahmad.arfeen' || auth()->user()->username == 'asif_nadeem' || auth()->user()->username == 'muhammad.nadeem' || auth()->user()->username == 'farzana' || auth()->user()->username == 'hassan.farooq' || auth()->user()->username == 'abdul.waheed' || auth()->user()->username == 'tayyaba.nawaz' || auth()->user()->username == 'talha.sarfraz' || auth()->user()->username == 'haider.raza' || auth()->user()->username == 'qasim.ali') {?>

    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span class="side-menu__label">Student Financial</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i>
        </a>
        <ul class="slide-menu">

            <?php if (auth()->user()->group_id == 644 || auth()->user()->username == 'farzana' || auth()->user()->username == 'hassan.farooq' || auth()->user()->username == 'talha.sarfraz' || auth()->user()->username == 'abdul.waheed' || auth()->user()->username == 'tayyaba.nawaz' || auth()->user()->username == 'haider.raza' || auth()->user()->username == 'sidra.tanveer' || auth()->user()->username == 'sumaira.arshad' || auth()->user()->username == 'asbah.rehman' || auth()->user()->username == 'naureen.mirza' || auth()->user()->username == 'maheen.james') {?>
            <li><a class="slide-item" href="{{url('finance/fee-policy')}}"><span>Enrollment Policy</span></a></li>
            {{-- <li><a class="slide-item" href="{{url('master-scholarship-criteria')}}"><span>Scholarship
                Criteria</span></a>
    </li> --}}
    <!-- Scholarship Criteria Parent -->
    {{-- <li class="sub-slide">
								<a class="sub-side-menu__item" data-toggle="sub-slide" href="#">
									<span>Scholarship Criteria</span>
								</a>

								<ul class="sub-slide-menu">
									<li>
										<a class="slide-item" href="{{ url('master-scholarship-criteria') }}">
    Scholarship Criteria
    </a>
    </li>
    <li>
        <a class="slide-item" href="{{ url('master-scholarship-policy') }}">
            Scholarship Policy
        </a>
    </li>
    </ul>
    </li> --}}
    <!-- End Scholarship Criteria -->
    <li><a class="slide-item" href="{{url('master-scholarship-criteria')}}"><span>Scholarship
                Criteria</span></a></li>
    <li><a class="slide-item" href="{{url('master-scholarship-policy')}}"><span>Scholarship
                Policy</span></a></li>
    <li><a class="slide-item" href="{{url('finance/assign-scholarship-index')}}"><span>Assign
                Scholarship</span></a></li>
    <li><a class="slide-item" href="{{url('finance/verify-scholarship-index')}}"><span>Scholarship
                Approval</span></a></li>
    <li><a class="slide-item" href="{{url('finance/other-scholarship-index')}}"><span>Other
                Scholarships</span></a></li>
    <li><a class="slide-item" href="{{url('studentLedger')}}"><span>Student Ledger</span></a></li>
    <li><a class="slide-item" href="{{url('challan-report')}}"><span>Challan Report</span></a></li>
    <li><a class="slide-item" href="{{ url('generate-credithrfee-challan') }}"><span>Generate
                Bulk Credit Hour Challan</span></a></li>
    <li><a class="slide-item" href="{{url('merit-scholarship-entry')}}"><span>Merit Scholarship
                Entry</span></a></li>


    <?php   }
							if (auth()->user()->group_id == 644 || auth()->user()->username == 'saqib.jamil' || auth()->user()->username == 'esha.abid' || auth()->user()->username == 'ahmad.arfeen' || auth()->user()->username == 'asif_nadeem' || auth()->user()->username == 'muhammad.nadeem' || auth()->user()->username == 'qasim.ali') {?>


    <li><a class="slide-item" href="{{url('master-fee-structure')}}"><span>Master Fee Structure</span></a>
    </li>
    <li><a class="slide-item" href="{{url('master-scholarship-form')}}"><span>Add Scholarship</span></a>
    </li>
    <li><a class="slide-item" href="{{url('master-hostelfee-structure')}}"><span>Hostel Fee
                Structure</span></a></li>
    <li><a class="slide-item" href="{{url('master-feeadjustment-form')}}"><span>Master Fee
                Adjustment</span></a></li>
    <li><a class="slide-item" href="{{url('feeadjustment')}}"><span>Fee Adjustment</span></a></li>
    <li><a class="slide-item" href="{{url('generate-fee-challan')}}"><span>Generate Bulk Challan</span></a>
    </li>
    <li><a class="slide-item" href="{{url('generate-credithrfee-challan')}}"><span>Generate Bulk Credit Hour
                Challan</span></a></li>
    <li><a class="slide-item" href="{{url('generate-convocation-challan')}}"><span>Generate Convocation
                Challan</span></a></li>
    <li><a class="slide-item" href="{{url('finance/generate-single-challan')}}"><span>Generate Single
                Challan</span></a></li>
    <li><a class="slide-item" href="{{url('challan-report')}}"><span>Challan Report</span></a></li>
    <li><a class="slide-item" href="{{url('challan-generated-details')}}"><span>Student Challan
                Detail</span></a></li>
    {{-- <li><a class="slide-item" href="{{url('middleware-challan_rectification-form')}}"><span>Challan
        Rectification</span></a></li> --}}
    <li><a class="slide-item" href="{{url('installment-form')}}"><span>Master Installment Form
                (CMS)</span></a></li>
    <li><a class="slide-item" href="{{url('student-manual-paid-mark')}}"><span>Manual Paid Mark</span></a>
    </li>
    <li><a class="slide-item" href="{{url('studentLedger')}}"><span>Student Ledger</span></a></li>
    <li><a class="slide-item" href="{{url('enrollment-summary-report')}}"><span>Enrollment
                Summary</span></a></li>
    <li><a class="slide-item" href="{{url('challan-detail-report')}}"><span>Challan Detail Report</span></a>
    </li>
    <li><a class="slide-item" href="{{url('donation-record-form')}}"><span>Donations</span></a></li>
    <li><a class="slide-item" href="{{url('finance/assign-scholarship-index')}}"><span>Assign
                Scholarship</span></a></li>
    <li><a class="slide-item" href="{{url('finance/verify-scholarship-index')}}"><span>Scholarship
                Approval</span></a></li>
    <li><a class="slide-item" href="{{url('student-record/student-cancel-challan')}}"><span>Bulk Challan
                Update & Cancellation</span></a></li>

    <li><a class="slide-item" href="{{url('merit-scholarship-challan-handle')}}"><span>Generate/Approve
                Scholarship Challan</span></a></li>
    <?php   }
							if (auth()->user()->group_id == 644 || auth()->user()->username == 'sidra.tanveer' || auth()->user()->username == 'asbah.rehman' || auth()->user()->username == 'naureen.mirza' || auth()->user()->username == 'maheen.james') {?>
    <li><a class="slide-item" href="{{url('studentLedger')}}"><span>Student Ledger</span></a></li>
    <li><a class="slide-item" href="{{url('installment-form-admission')}}"><span>Master Installment Form
                (Admission)</span></a></li>
    <li><a class="slide-item" href="{{url('admission-scholarship-criteria')}}"><span>Admission Scholarship
                Criteria</span></a></li>
    <li><a class="slide-item" href="{{url('admission-individual-scholarship-criteria')}}"><span>Individual
                Scholarship</span></a></li>
    <li><a class="slide-item" href="{{url('finance/verify-admissionScholarship')}}"><span>Admission
                Scholarship Approval</span></a></li>


    <?php } ?>
    <li><a class="slide-item" href="{{url('fee-waiveoff-form')}}"><span>Fee
                Waiver Form</span></a></li>
    </ul>
    </li>

    <?php } ?>


    <?php  if (in_array(auth()->user()->group_id, [2, 644, 324,884, 21])) {?>

    <ul class="side-menu" role="list">


        {{-- Store Procurement --}}
        @if(1==1 )
        @include('layouts.modules.store_procurement')
        @endif

        {{-- Add more modules here --}}

    </ul>

    <?php } ?>




    <?php            if (  auth()->user()->group_id == 644 || auth()->user()->username == 'sidra.tanveer' || auth()->user()->username == 'hur.hussain') { ?>
    <li class="slide">
        <a class="side-menu__item" href="{{ url('admission-survey') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Admission Information Source survey </span>
        </a>
    </li>

    <?php            } ?>



    <?php if (auth()->user()->username == 'farzana') {?>
    <li class="slide">
        <a class="side-menu__item" href="{{url('car-sticker-report')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Car Sticker Report</span>
        </a>
    </li>

    <?php   }?>
    <?php if (auth()->user()->username == 'bilal.saleem' || auth()->user()->username == 'farzana') {?>
    <li class="slide">
        <a class="side-menu__item" href="{{url('admission_applicant_data')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Applicant Data Report</span>
        </a>
    </li>

    <?php   }?>
    <li class="slide link_for_web">
        <a class="side-menu__item" href="{{url('dean-honor')}}">

            <img src="{{ asset('images/deanlist.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Dean Honor List</span>
        </a>
    </li>
    <li class="slide link_for_web">
        <a class="side-menu__item" href="{{url('staff/todo-list')}}">

            <img src="{{ asset('images/todos.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">To-do List</span>
        </a>
    </li>
    <li class="slide">
        <a class="side-menu__item" href="{{url('staff/task-list')}}">

            <img src="{{ asset('images/tasks.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Task Management</span>
        </a>
    </li>
    <?php if (auth()->user()->group_id == 644 || auth()->user()->username == 'shabana' || auth()->user()->username == 'ghulam.ghouse' || auth()->user()->username == 'zainab.ahmed') { ?>
    <li class="slide">
        <a class="side-menu__item" href="{{url('event-registrations')}}">

            <img src="{{ asset('images/homes.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Event Registrations</span>
        </a>
    </li>
    <?php } ?>

    <?php if (auth()->user()->group_id == 825) { ?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            {{-- <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
								viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
								stroke-linejoin="round">
								<path
									d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
								</path>
								<polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
								<line x1="12" y1="22.08" x2="12" y2="12"></line>
							</svg> --}}

            <img src="{{ asset('images/homes.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">TA</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">
            <!-- <li><a class="slide-item"  href="{{url('reports/faculty/list')}}"><span>Faculty Assurance Report</span></a></li> https://devstudent.bnu.edu.pk/login-user-by-users-->
            <li><a class="slide-item" href="{{url('move-to-student?token=' . auth()->user()->api_token)}}"><span>Students</span></a>
            </li>
        </ul>
    </li>
    <?php } ?>
    <?php
						if (auth()->user()->group_id != 884) {
							if (auth()->user()->group_id == 222 || auth()->user()->group_id == 2 || auth()->user()->group_id == 344 || auth()->user()->group_id == 644) { ?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">

            <img src="{{ asset('images/reportss.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Reports</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">
            <li><a class="slide-item" href="{{url('/reportgenerator/reportgenerator')}}"><span>Report
                        Generator</span></a></li>
            <!--<li><a class="slide-item" href="{{url('teacher/grading_status')}}"><span>Grading Status</span></a></li>
																																																																																																									<li><a class="slide-item" href="{{url('teacher/enrollment_status')}}"><span>Enrollment Status</span></a></li>
																																																																																																									<li><a class="slide-item" href="{{url('teacher/applicant_academic_profile')}}"><span>Applicant Academic Profile</span></a></li>-->
            <li><a class="slide-item" href="{{url('teacher/course_attendance_detail')}}"><span>Course Attendance
                        Detail</span></a></li>
            <li><a class="slide-item" href="{{url('teacher/hr_attendance_report')}}"><span>HR Attendance
                        Report</span></a></li>
            <?php if (auth()->user()->username == 'mehmil.ishtiaq') {?>
            <li><a class="slide-item" href="{{url('teacher/applicant_academic_profile_status')}}"><span>Applicant
                        Status</span></a></li>
            <?php } ?>
            <?php if (auth()->user()->employee_id == 2876 || auth()->user()->group_id == 644) {?>
            <li><a class="slide-item" href="{{url('teacher/academic_history2')}}"><span>Academic History</span></a>
            </li>
            <?php } ?>

            <!-- <li><a class="slide-item" href="{{url('teacher/attendance_summary_dept')}}"><span>Attendance Summary Department Wise</span></a></li>
																																																																																																									<li><a class="slide-item" href="{{url('teacher/student_semester_performance')}}"><span>Student Semester Performance</span></a></li>
																																																																																																									<li><a class="slide-item" href="{{url('teacher/faculty_course_attendance_report')}}"><span>Faculty Course Attendance</span></a></li>
																																																																																																									<li><a class="slide-item" href="{{url('teacher/acadmic_history')}}"><span>Acadmic History</span></a></li>
																																																																																																								-->
        </ul>
    </li>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">

            <img src="{{ asset('images/qas.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">QA</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">
            <!-- <li><a class="slide-item"  href="{{url('reports/faculty/list')}}"><span>Faculty Assurance Report</span></a></li> -->
            <li><a class="slide-item" href="{{url('reports/faculty-evaluation')}}"><span>Faculty Evaluation
                        Report</span></a></li>
        </ul>
    </li>
    <?php } else if (auth()->user()->group_id == 223 || auth()->user()->group_id == 364 || auth()->user()->group_id == 284 || auth()->user()->group_id == 824 || auth()->user()->group_id == 122) { ?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">

            <img src="{{ asset('images/reportss.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Reports</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">
            <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
            <li><a class="slide-item" href="{{url('/reportgenerator/reportgenerator')}}"><span>Report
                        Generator</span></a></li>
            <li><a class="slide-item" href="{{url('/faculty/course_offering')}}"><span>Course Offering
                        List</span></a></li>
            <li><a class="slide-item" href="{{url('teacher/grading_status')}}"><span>Grading Status</span></a></li>
            <li><a class="slide-item" href="{{url('teacher/enrollment_status')}}"><span>Enrollment Status</span></a>
            </li>
            <li><a class="slide-item" href="{{url('teacher/applicant_academic_profile')}}"><span>Applicant Academic
                        Profile</span></a></li>
            <li><a class="slide-item" href="{{url('teacher/applicant_academic_profile_status')}}"><span>Applicant
                        Status</span></a></li>
            <li><a class="slide-item" href="{{url('teacher/student_assessment_report')}}"><span>Student Assessment
                        Report</span></a></li>
            <li><a class="slide-item" href="{{url('teacher/attendance_summary_dept')}}"><span>Attendance Summary
                        Department Wise</span></a></li>
            <li><a class="slide-item" href="{{url('teacher/course_attendance_detail')}}"><span>Course Attendance
                        Detail</span></a></li>
            <li><a class="slide-item" href="{{url('teacher/student_semester_performance')}}"><span>Student Semester
                        Performance</span></a></li>
            <li><a class="slide-item" href="{{url('teacher/faculty_course_attendance_report')}}"><span>Faculty
                        Course Attendance</span></a></li>
            <li><a class="slide-item" href="{{url('teacher/acadmic_history')}}"><span>Acadmic History</span></a>
            </li>
            <li><a class="slide-item" href="{{url('teacher/nbs_report')}}"><span>NBS Summary Report</span></a></li>
            <li><a class="slide-item" href="{{url('teacher/faculty_course_detail')}}"><span>Faculty Course
                        Content</span></a></li>
            <?php if (auth()->user()->employee_id == 1294 or auth()->user()->employee_id == 6455) { ?>
            <li><a class="slide-item" href="{{url('teacher/hr_attendance_report')}}"><span>HR Attendance
                        Report</span></a></li>
            <?php } ?>
            <?php if (auth()->user()->username == 'hifsa.farooq' or auth()->user()->username == 'bilal.saleem') { ?>
            <li><a class="slide-item" href="{{url('teacher/admission_enrollment_status')}}"><span>Department wise
                        Report</span></a></li>
            <?php } ?>
        </ul>
    </li>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">

            <img src="{{ asset('images/qas.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">QA </span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">
            <li><a class="slide-item" href="{{url('reports/faculty/list')}}"><span>Faculty Assurance
                        Report</span></a></li>
            <li><a class="slide-item" href="{{url('reports/faculty-evaluation')}}"><span>Faculty Evaluation
                        Report</span></a></li>
        </ul>
    </li>
    <?php if (auth()->user()->username == 'ruhi.khalid') {?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Dean</span></a>
        <ul class="slide-menu">
            <li><a href="{{url('teacher/courses/approval')}}" class="slide-item">Result Approval</a></li>
        </ul>
    </li>
    <?php } else { ?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">

            <img src="{{ asset('images/hods.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">HOD</span></a>
        <ul class="slide-menu">
            <li><a href="{{url('teacher/courses/approval')}}" class="slide-item">Result Approval</a></li>
        </ul>
    </li>
    <?php } ?>
    <?php if (auth()->user()->group_id != 284 || auth()->user()->group_id == 122) { ?>
    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('/approve_ta')}}">

            <img src="{{ asset('images/approves.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Approve TA</span>
        </a>
    </li>
    <?php } ?>

    <?php }?>
    <?php


							// $groupId = auth()->user()->group_id;
							// Group IDs: 644 - Software Team, 223 - Dean, 364 - Deans SCIT, 445 - HOD Ijaz
							// if (in_array($groupId, [644, 223, 364, 445]) || auth()->user()->username == 'hifsa.farooq') {

							$groupId = auth()->user()->group_id;
							$username = auth()->user()->username;
							// Group IDs allowed
							$allowedGroups = [644, 223, 364, 445];
							// Usernames allowed
							$allowedUsers = [
								'hifsa.farooq',
								'zara.amjad',
								'arez.ali',
								'ahsan.fazal',
								'sara.assad',
								'ghammaz.husnain',
								'zeeshan.sarwar',
								'nouman.ali',
								'amna.humayun',
								'nabeel.ahsan',
								'osama.tariq',
								'haider.ali1',
								'huda.sarfraz',
								'farhat_nadeem',
								'asim.irshad',
								'noreen.khalid',
								'ameer.hamza',
								'farhan.ali',
								'talia.noreen',
								'muhammad.ali1',
								'ammar.faiz',
								'umaima.mohsin',
								'sana.aziz',
								'zoona.kundi',
								'neha.qasim',
								'noreen',
								'reema.qadeer',

								'werdah.munib',
								'tabina.sirhindi',
								'harris.badar',
								'zeeshan.zaighum',
								'nasir.ali',
								'usman.rana',
								'sheharyar.zahid',
								'warda.shah',
								'nosharwhan.adil',
								'naveed.aasim',
								'farahat.ali',
								'rabia.noor',
								'sarmed.cheema',
								'hina.razzaq',
								'bnu_hr',
								'sumaiya.naumaan',
								'natash.ali',
								'muhammad.adeel',
								'ayesha.seher',
								'zaman.aziz'


							];

							if (in_array($groupId, $allowedGroups) || in_array($username, $allowedUsers)) {


																																																																																																		?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <!-- SVG Icon -->

            <img src="{{ asset('images/studentp.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Student Progress</span>
            <i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i>
        </a>
        <ul class="slide-menu">
            <li>
                <a class="slide-item" href="<?php echo url('/reports/report-generator'); ?>">
                    <span>Student Progress Monitoring Portal</span>
                </a>
            </li>
        </ul>
    </li>
    <?php
							}
																																																																																																		?>
    <!-- GoToStaff -->
    <?php if (auth()->user()->group_id != 222 || auth()->user()->group_id != 223) {
								if (in_array(auth()->user()->group_id, array(141, 584, 201, 1206))) {?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                </path>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
            <span class="side-menu__label">QA </span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">
            <li><a class="slide-item" href="{{url('reports/faculty/list')}}"><span>Faculty Assurance
                        Report</span></a></li>
            <li><a class="slide-item" href="{{url('reports/faculty-evaluation')}}"><span>Faculty Evaluation
                        Report</span></a></li>
            <li><a class="slide-item" href="{{url('teacher/graduate_survey_report')}}"><span>Graduate Survey
                        Report</span></a></li>
            <li><a class="slide-item" href="{{url('student-record/student-course-disable')}}"><span>Update Course
                        Status</span></a></li>

            <?php if (auth()->user()->username == 'noreen') {?>
            <li><a class="slide-item" href="{{url('/faculty/course_offering_report')}}"><span>Course Offering
                        Report</span></a></li>
            <?php }?>

        </ul>
    </li>
    <?php }?>


    @if(auth()->user()->group_id == '644')
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            <span class="side-menu__label">Security Settings</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i>
        </a>
        <ul class="slide-menu">
            <li>
                <a class="slide-item" href="{{url('allowed-inspect-users')}}">
                    <span>Inspection Access Management</span>
                </a>
            </li>
            <li>
                <a class="slide-item" href="{{url('error-logs')}}">
                    <span>System Logs</span>
                </a>
            </li>
            <li>
                <a class="slide-item" href="{{url('exam-sections')}}">
                    <span>Exam Sections</span>
                </a>
            </li>
            <li>
                <a class="slide-item" href="{{url('page-permissions')}}">
                    <span>Page Permissions</span>
                </a>
            </li>
            <li>
                <a class="slide-item" href="{{url('eportal-hod-dean-list')}}">
                    <span>ePortal HOD/Dean List</span>
                </a>
            </li>
        </ul>
    </li>
    @endif

    <?php }
						}
						if (auth()->user()->group_id == 884 || auth()->user()->group_id == 644 || auth()->user()->group_id == 2) {?>
    <li class="slide">
        <a class="side-menu__item " data-toggle="slide" href="#">
            {{-- <span><i class="fa fa-car side-menu__icon mt-1"></i></span> --}}

            <img src="{{ asset('images/carp.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Car Pooling</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">
            <li><a class="slide-item" href="{{url('/car-pooling')}}"><span>Ride Registration</span></a></li>
            <li><a class="slide-item" target="__blank" href="{{url('/car-pooling-pdf')}}"><span>Generate
                        Report</span></a></li>
            <li><a class="slide-item" target="__blank" href="{{url('/car-pooling-search')}}"><span>Car Search
                    </span></a></li>
        </ul>
    </li>
    <?php if (auth()->user()->username != 'ashfaque.hussain') {?>
    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('/car-registration')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Car Registration</span>
        </a>
    </li>
    <?php } ?>
    <!-- <li class="slide">
																																																<a class="side-menu__item" target="_blank" href="{{url('/search-registered-car')}}">
																																																	<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
																																																		stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
																																																		class="side-menu__icon">
																																																		<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
																																																		<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
																																																	</svg>
																																																	<span class="side-menu__label">Search Registered Car</span>
																																																</a>
																																															</li> -->
    <?php } else { ?>
    <li class="slide" style="display:none;">
        <a class="side-menu__item " data-toggle="slide" href="#">
            {{-- <span><i class="fa fa-car side-menu__icon mt-1"></i></span> --}}

            <img src="{{ asset('images/carp.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Car Pooling</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">
            <li><a class="slide-item" href="{{url('/car-pooling')}}"><span>Ride Registration</span></a></li>
        </ul>
    </li>
    <?php } ?>
    <!-- //// exam report  -->
    <?php
						if (in_array(auth()->user()->group_id, array(201, 424, 644, 2, 1064))) {
																																																																																																?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
            </path>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
            <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
            <span class="side-menu__label">EX. Reports </span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i>
        </a>
        <ul class="slide-menu">
            <?php if (auth()->user()->username != 'maham.nadeem') {?>
            <li><a class="slide-item" href="{{url('reports/exam/cgpa-summary')}}"><span>CGPA Summary
                        Report</span></a></li>
            <?php } ?>
            <li><a class="slide-item" href="{{url('repeating-course')}}"><span>Repeating Course Report</span></a>
            </li>
            <li><a class="slide-item" href="{{url('graduate-summary-report')}}"><span>Graduate Summary
                        Report</span></a></li>
            <li><a class="slide-item" href="{{url('exam-report-student')}}"><span>Missing Doc Summary
                        Report</span></a></li>
        </ul>
    </li>
    <?php } ?>

    <?php
						if (in_array(auth()->user()->group_id, array(141, 584, 305))) {
																																																																																																?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
            </path>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
            <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
            <span class="side-menu__label">EX. Reports </span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i>
        </a>
        <ul class="slide-menu">
            <li><a class="slide-item" href="{{url('repeating-course')}}"><span>Repeating Course Report</span></a>
            </li>
        </ul>
    </li>
    <?php } ?>
    <li class="slide ">
        <a class="side-menu__item" data-toggle="slide" href="#">

            <img src="{{ asset('images/subs.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">
                {{-- <i class="fa fa-list side-menu__icon mt-1"></i> --}}

                Subscriptions</span><i class="angle fe fe-chevron-right"></i>
        </a>
        @if (auth()->user()->group_id == 644 || auth()->user()->id == 23858 || auth()->user()->username == 'somia.maqsood' || auth()->user()->username == 'abdul.waheed' || auth()->user()->username == 'tayyaba.nawaz' || auth()->user()->username == 'hassan.farooq' || auth()->user()->username == 'talha.sarfraz')
        {{-- 644 is group id of ITRC and 23858 is user id of Rabia Shabir--}}
        <ul class="slide-menu">
            <li class="sub-slide">
                <a class="slide-item" href="{{url('sports-applicant-form')}}"><span>Add Sports/Scholarship
                        Students</span></a>
            </li>
        </ul>
        @endif
        @if (auth()->user()->group_id == 644 || auth()->user()->group_id == 324) {{-- 644 is group id of ITRC and
							324 is group id of accounts --}}
        <ul class="slide-menu">
            <li class="sub-slide">
                <a class="slide-item" href="{{url('subscription-master-form')}}"><span>Subscription Master
                        Form</span></a>
            </li>
        </ul>
        <ul class="slide-menu">
            <li class="sub-slide">
                <a class="slide-item" href="{{url('user-subscriptions')}}"><span>Student Subscriptions</span></a>
            </li>
        </ul>
        @endif
        @if (auth()->user()->group_id == 644 || auth()->user()->group_id == 324 || auth()->user()->username == 'saqib.zia' || auth()->user()->username == 'ahmer.sayeed' || auth()->user()->username == 'asif.khan'){{--
							644 is group id of ITRC and 324 is group id of accounts --}}
        <ul class="slide-menu">
            <li class="sub-slide">
                <a class="slide-item" href="{{url('staff-subscriptions')}}"><span>Staff Subscriptions</span></a>
            </li>
        </ul>
        @endif
        @if (auth()->user()->group_id == 644)
        <ul class="slide-menu">
            <li class="sub-slide">
                <a class="slide-item" href="{{url('rfid-assignment-form')}}"><span>RFID Setup</span></a>
            </li>
        </ul>
        @endif
        <ul class="slide-menu">
            <li class="sub-slide">
                <a class="slide-item" href="{{url('staff-subscription-form')}}"><span>Gym Subscription</span></a>
            </li>
        </ul>

    </li>

    <?php
						$allowedGroupIds = [644,]; // 644 - Software Team
						$allowedUsernames = [
							'haider.raza',
							'aiman.adnan',
							'somia.maqsood',
							'noreen',
							'sheeraz',
							'naureen.mirza',
							'asbah.rehman',
							'sidra.tanveer',
							'zonia.akbar',
							'abdul.waheed',
							'tayyaba.nawaz',
							'hassan.farooq',
							'maheen.james',
							'saleem',
							'arshad.khurshid'
						];
						if (in_array(auth()->user()->group_id, $allowedGroupIds) || in_array(auth()->user()->username, $allowedUsernames)) {
																																																																																																?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <!-- Better Icon for Documents -->
            <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8 2H16C17.1 2 18 2.9 18 4V20C18 21.1 17.1 22 16 22H8C6.9 22 6 21.1 6 20V4C6 2.9 6.9 2 8 2Z">
                </path>
                <polyline points="14 2 14 6 10 6 10 2"></polyline>
            </svg>
            <span class="side-menu__label">Missing Documents</span>
            <i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i>
        </a>
        <ul class="slide-menu">
            <li>
                <a class="slide-item" href="<?php echo url('/admission_qualification'); ?>">
                    <span>Missing Documents Verification</span>
                </a>
            </li>
        </ul>
    </li>
    <?php
						}
																																																																																																?>













    <?php
						$allowedGroupIds = [644]; // 644 - Software Team

						// Users for HOD Approval
						$hodApprovalUsers = [
							'farzana',
							'zaeem',
							'asif_nadeem',
							'noreen',
							'zeeshan.zaighum',
							'maheen.abid',
							'nouman.ali',
							'waqas.sajjad',
							'shabana',
							'ijaz.hussain',
							'faizan.naveed',
							'arez.ali',
							'sousan.qadeer'
						];

						// Users for NBS RO Approval
						$nbsRoApprovalUsers = [
							'hassan.farooq',
							'talha.sarfraz'
						];
						// Users for NBS Admission Approval
						$nbsAdmissionApprovalUsers = [
							'sidra.tanveer',
							'maheen.james'
						];

						// Check user
						$user = auth()->user();
						if (
							in_array($user->group_id, $allowedGroupIds) ||
							in_array($user->username, array_merge($hodApprovalUsers, $nbsRoApprovalUsers, $nbsAdmissionApprovalUsers))
						) {
																																																																																																																?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">

            <img src="{{ asset('images/nbss.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">NBS Verification</span>
            <i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i>
        </a>
        <ul class="slide-menu">
            <?php                if (in_array($user->username, $hodApprovalUsers) || in_array($user->group_id, $allowedGroupIds)) { ?>
            <li>
                <a class="slide-item" href="<?php                    echo url('/nbs-hod-approval'); ?>">
                    <span>NBS HOD Approval</span>
                </a>
            </li>
            <?php                } ?>

            <?php                if (in_array($user->username, $nbsRoApprovalUsers) || in_array($user->group_id, $allowedGroupIds)) { ?>
            <li>
                <a class="slide-item" href="<?php                    echo url('/nbs-ro-approval'); ?>">
                    <span>NBS Approval</span>
                </a>
            </li>
            <?php                } ?>

            <?php                if (in_array($user->username, $nbsAdmissionApprovalUsers) || in_array($user->group_id, $allowedGroupIds)) { ?>
            <li>
                <a class="slide-item" href="<?php                    echo url('/nbs-admission-approval'); ?>">
                    <span>NBS Approval</span>
                </a>
            </li>
            <?php                } ?>
        </ul>
    </li>
    <?php            } ?>



    {{-- Library Side Bars --}}
    <?php if (auth()->user()->group_id == 764 || auth()->user()->group_id == 644 || auth()->user()->group_id == 2) { ?>
    <li class="slide ">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <line x1="4" y1="21" x2="4" y2="14"></line>
                <line x1="4" y1="10" x2="4" y2="3"></line>
                <line x1="12" y1="21" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12" y2="3"></line>
                <line x1="20" y1="21" x2="20" y2="16"></line>
                <line x1="20" y1="12" x2="20" y2="3"></line>
                <line x1="1" y1="14" x2="7" y2="14"></line>
                <line x1="9" y1="8" x2="15" y2="8"></line>
                <line x1="17" y1="16" x2="23" y2="16"></line>
            </svg>
            <span class="side-menu__label">Library</span><i class="angle fe fe-chevron-down"></i>
        </a>
        <ul class="slide-menu">
            <li class="sub-slide">
                <a class="sub-side-menu__item" data-toggle="sub-slide" href="#"><span class="sub-side-menu__label" style="font-weight:bold;">Acquisition</span></a>
            </li>
        </ul>
        <ul class="slide-menu">

            <li class="sub-slide">
                <a class="sub-side-menu__item" data-toggle="sub-slide" href="#"><span class="sub-side-menu__label " style="font-weight:bold;">Cataloging</span><i class="sub-angle fe fe-chevron-down"></i></a>
                <ul class="sub-slide-menu">
                    <li class="slide">
                        <ul class="slide-menu">
                            <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                            <li><a class="slide-item" href="{{url('/library/catalog')}}"><span>Catalog</span></a>
                            </li>

                        </ul>
                        <ul class="slide-menu">
                            <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                            <li><a class="slide-item" href="{{url('/library/add-book-item')}}"><span>New Item
                                        Entry</span></a></li>

                        </ul>
                        <ul class="slide-menu">
                            <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                            <li><a class="slide-item" href="{{url('/library/view-book-item')}}"><span>Update / View
                                        Book Entry</span></a></li>

                        </ul>
                        <ul class="slide-menu">
                            <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                            <li><a class="slide-item" href="{{url('/library/spine-barcode')}}"><span>Spine and
                                        Barcode</span></a></li>

                        </ul>
                        <ul class="slide-menu">
                            <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                            <li><a class="slide-item" href="{{url('/library/daily-library-visitor')}}"><span>Daily
                                        Library Visitor</span></a></li>

                        </ul>
                    </li>
                </ul>
            </li>
            <li class="sub-slide">
                <a class="sub-side-menu__item" data-toggle="sub-slide" href="#"><span class="sub-side-menu__label " style="font-weight:bold;">Circulation</span><i class="sub-angle fe fe-chevron-down"></i></a>
                <ul class="sub-slide-menu">
                    <ul class="slide-menu">
                        <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                        <li><a class="slide-item" href="{{url('/library/librarycirculation')}}"><span>Circulation</span></a></li>

                    </ul>
                    <ul class="slide-menu">
                        <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                        <li><a class="slide-item" href="{{url('/library/library-criteria')}}"><span>Books issuing
                                    Criteria</span></a></li>

                    </ul>
                    <ul class="slide-menu">
                        <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                        <li><a class="slide-item" href="{{url('/library/circulation-book-history')}}"><span>
                                    Circulation History</span></a></li>

                    </ul>
                    <ul class="slide-menu">
                        <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                        <li><a class="slide-item" href="{{url('/library/overdue-book')}}"><span> Over Due
                                    Books</span></a></li>

                    </ul>
                    <ul class="slide-menu">
                        <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                        <!-- <li><a class="slide-item" href="{{url('/library/datacorrection')}}"><span>Data Correction</span></a></li> -->

                    </ul>
                    <ul class="slide-menu">
                        <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                        <li><a class="slide-item" href="{{url('/library/return-book')}}"><span>Fine
                                    Management</span></a></li>

                    </ul>


            </li>

        </ul>
    <li class="sub-slide">
        <a class="sub-side-menu__item" data-toggle="sub-slide" href="#"><span class="sub-side-menu__label " style="font-weight:bold;">Reminders</span><i class="sub-angle fe fe-chevron-down"></i></a>
        <ul class="sub-slide-menu">
            <li class="slide">
                <ul class="slide-menu">
                    <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                    <li><a class="slide-item" href="{{url('/library/inbox')}}"><span>Inbox</span></a></li>

                </ul>
            </li>
        </ul>
    </li>

    <li class="sub-slide">
        <a class="sub-side-menu__item" data-toggle="sub-slide" href="#"><span class="sub-side-menu__label " style="font-weight:bold;">Reports</span><i class="sub-angle fe fe-chevron-down"></i></a>
        <ul class="sub-slide-menu">
            <li class="slide">
                <ul class="slide-menu">
                    <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
                    <li><a class="slide-item" href="{{url('/library/report_cataloging')}}"><span>Cataloging
                                Reports</span></a></li>
                    {{-- <li><a class="slide-item" href="{{url('/library/inbox')}}"><span>Circulation
                        Reports</span></a>
            </li> --}}
        </ul>
    </li>
    </ul>
    </li>
    <ul class="slide-menu">
        <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
        <li><a class="slide-item" href="{{url('/library/room-allocation')}}"><span>Library Room
                    Allocation</span></a></li>
    </ul>
    </li>
    </ul>
    </li>
    <?php } ?>
    {{-- Library Side Bar Ends here --}}
    <!-- GoToStaff -->
    <?php if (!(in_array(2, [222, 825])) && auth()->user()->group_id != 825) {
							//if (auth()->user()->group_id !=222 &&auth()->user()->group_id !=223  && auth()->user()->group_id != 825) {
																																																																																																?>
    <li class="slide">
        <a class="side-menu__item" target="_blank" href="<?= config('app.staff_url') ?>/index.php?key=<?= auth()->user()->api_token ?>&username=<?= auth()->user()->username ?>&time=<?= rand(999, 999999) ?>">

            <img src="{{ asset('images/stafs.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Staff</span>
        </a>
    </li>

    <?php } ?>


    <?php if (auth()->user()->group_id == 644 || auth()->user()->group_id == 2 || auth()->user()->group_id == 122 || auth()->user()->username == 'haider.raza') { ?>

    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('/enrollment-start-stop')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Enrollment Start Stop</span>
        </a>
    </li>

    <?php } ?>

    <?php if (auth()->user()->group_id == 644 || auth()->user()->group_id == 284) { ?>


    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span class="side-menu__label">Enrollments</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">

            <li><a class="slide-item" href="{{url('/road-map-wise-offering')}}"><span> Roadmap Wise Offering</span></a>
            </li>
            <li><a class="slide-item" href="{{url('/road-map-wise-verification')}}"><span> Verify Offering</span></a>
            </li>
        </ul>
    </li>

    <?php } ?>




    <?php if (auth()->user()->group_id == 284 || auth()->user()->group_id == 122) {?>
    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('/attendance_update_ex')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Attendance Exemption</span>
        </a>
    </li>
    <?php } ?>

    <?php if (auth()->user()->group_id == 644 || auth()->user()->employee_id == 1294 || auth()->user()->group_id == 624 || auth()->user()->group_id == 744 || auth()->user()->group_id == 365 || auth()->user()->group_id == 284 || auth()->user()->group_id == 122) {?>
    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('staff/student-financial')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Student Financial</span>
        </a>
    </li>
    <?php } ?>


    <?php
						$allowed_usernames = [
							'somia.maqsood',
							'abdul.waheed',
							'tayyaba.nawaz',
							'talha.sarfraz',
							'hassan.farooq',
							'sidra.tanveer',
							'asbah.rehman',
							'naureen.mirza',
							'saqib.jamil',
							'esha.abid',
							'asif_nadeem',
							'ahmad.arfeen',
							'maheen.james'
						];

						if (in_array(auth()->user()->username, $allowed_usernames) || auth()->user()->group_id == 644) {
																																																																																																?>
    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('student-record/student-record')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Student Record Mgt</span>
        </a>
    </li>
    <?php } ?>

    <?php if (auth()->user()->group_id == '404' || auth()->user()->group_id == '644') {?>
    <li class="slide">
        <a class="side-menu__item" href="{{url('/softwarelist')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Software Entry </span>
        </a>
    </li>
    <?php } ?>

    <?php if (auth()->user()->group_id == '181' || auth()->user()->group_id == '644' || auth()->user()->username == 'saqib.zia' || auth()->user()->username == 'ali.kashif' || auth()->user()->username == 'irfan.bajwa' || auth()->user()->username == 'nasir.iqbal' || auth()->user()->username == 'asif') {?>
    <li class="slide">
        <a class="side-menu__item" href="{{url('/emp-employee-details')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Faculty Profile </span>
        </a>
    </li>
    <?php } ?>

    <?php if (auth()->user()->group_id == 644 || auth()->user()->employee_id == 1294 || auth()->user()->group_id == 324) {?>
    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('accounts/student-financial')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Student Financial Accounts</span>
        </a>
    </li>
    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('staff/student-financial')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Student Financial</span>
        </a>
    </li>
    <?php } ?>






    <li class="slide">
        <a class="side-menu__item " data-toggle="slide" href="#">
            {{-- <span><i class="fa fa-desktop  side-menu__icon mt-1"></i></span> --}}

            <img src="{{ asset('images/eportels.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">E-Portal</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">


            @if(auth()->user()->username == 'haider.raza' || auth()->user()->group_id == 644)
            <li><a class="slide-item" href="{{ url('university-holidays-management') }}"><span>University
                        Holidays</span></a></li>
            @endif



            <?php
						$userDeptId = auth()->user()->dept_id;
						$userGroupId = auth()->user()->group_id;
						$allowedDeptIds = [41, 23, 24, 141, 61];
						$allowedGroupId = '644';
						if (in_array($userDeptId, $allowedDeptIds) || $userGroupId == $allowedGroupId) {?>
            {{-- <li><a class="slide-item" href="{{url('eportal-request')}}"><span>Admin to School</span></a>
    </li> --}}

    <?php  } ?>

    <!-- <?php if (auth()->user()->username == 'zonia.akbar') {?>
																																																																																																	<?php } elseif (auth()->user()->username == 'farzana') {?>
																																																																																																		<?php } ?> -->
    <li><a class="slide-item" href="{{url('eportal-approval')}}"><span>E-Portal Approval</span></a></li>
    <li><a class="slide-item" href="{{url('eportal-dashboard')}}"><span>E-Portal Requests</span></a></li>
    <?php if (auth()->user()->username == 'saqib.jamil' || auth()->user()->username == 'ahmad.arfeen' || auth()->user()->username == 'bilal.saleem' || auth()->user()->username == 'esha.abid' || auth()->user()->username == 'qasim.ali') {?>
    <li><a class="slide-item" href="{{url('eportal-report-student')}}"><span>Student Exit Report</span></a></li>
    <?php } ?>

    <?php if (auth()->user()->username == 'farzana' || auth()->user()->username == 'noreen' || auth()->user()->username == 'asif_nadeem' || auth()->user()->username == 'zaeem' || auth()->user()->username == 'ahmer.sayeed') { ?>

    <li><a class="slide-item" href="{{url('eportal-report-staff')}}"><span>E-Portal Report</span></a></li>
    <?php } ?>
    </ul>
    </li>

    <?php if (auth()->user()->username == 'ahmad.arfeen' || auth()->user()->username == 'asif_nadeem' || auth()->user()->group_id == 644 || auth()->user()->group_id == 324 || auth()->user()->username == 'farhan.iftikhar' || auth()->user()->username == 'qasim.ali') {?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
            </svg>
            <span class="side-menu__label">Account Reports</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">
            <!-- <li class="side-menu-label1"><a href="#"></a>Dashboard</li> -->
            <li><a class="slide-item" target="_blank" href="{{url('fine-challan-student-list')}}">
                    <span class="side-menu__label">Online Payment List</span></a>
            </li>
            <li><a class="slide-item" target="_blank" href="{{url('blocked-student-list')}}">
                    <span class="side-menu__label">Blocked Students</span></a>
            </li>
            <li><a class="slide-item" target="_blank" href="{{url('students_fee_challan')}}">
                    <span class="side-menu__label">Students Fee Challan</span></a>
            </li>
            <?php if (auth()->user()->username == 'ahmad.arfeen' || auth()->user()->username == 'asif_nadeem' || auth()->user()->group_id == 644 || auth()->user()->username == 'farhan.iftikhar') {?>
            <li><a class="slide-item" href="{{url('car-sticker-report')}}">
                    <span class="side-menu__label">Car Sticker Report</span>
                </a></li>
            <li><a class="slide-item" href="{{url('admission_applicant_data')}}">
                    <span class="side-menu__label">Applicant Data Report</span>
                </a></li>
            <?php } ?>

            <?php if (auth()->user()->employee_id == 1294 or auth()->user()->employee_id == 6455) { ?>
            <li><a class="slide-item" href="{{url('teacher/hr_attendance_report')}}"><span>HR Attendance
                        Report</span></a></li>
            <?php } ?>
        </ul>
    </li>
    <?php } ?>


    <?php if (auth()->user()->group_id == 644 || auth()->user()->group_id == 142) {?>
    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('exam/allow-exam-ips')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Allow Exams IPs</span>
        </a>
    </li>
    <?php } ?>



    <li class="slide">
        <a class="side-menu__item " data-toggle="slide" href="#">
            {{-- <span><i class="fa fa-share-alt side-menu__icon mt-1"></i></span> --}}

            <img src="{{ asset('images/sharepoints.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">SharePoint</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">


            <li><a class="slide-item" target="__blank" href="https://share.bnu.edu.pk/sites/facultynstaff"><span>Faculty
                        SharePoint</span></a></li>
            <li><a class="slide-item" target="__blank" href="https://share.bnu.edu.pk/sites/students/"><span></span>Student SharePoint</a></li>
            <li><a class="slide-item" target="__blank" href="https://share.bnu.edu.pk/sites/orip"><span></span>Orip</a>
            </li>


            @php
            $allowedSharePointEmployeeIds = [
            553,
            1313,
            1273,
            276,
            226,
            259,
            512,
            2815,
            2855,
            7335,
            5375,
            8157,
            9194,
            9096,
            9258,
            9241,
            9325,
            9328,
            9385,
            16
            ];

            $user = auth()->user();
            $hasSharePointAccess = in_array($user->employee_id, $allowedSharePointEmployeeIds) || $user->group_id == 644;
            @endphp

            @if ($hasSharePointAccess)
            <li>
                <a class="slide-item" target="_blank" href="http://share.bnu.edu.pk/sites/Careers">
                    <span>Job Portal</span>
                </a>
            </li>
            @endif

            @if ($hasSharePointAccess)
            <li><a class="slide-item" target="__blank" href="https://share.bnu.edu.pk/sites/orip"><span></span>Orip</a>
            </li>

            @endif


        </ul>
    </li>



    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            {{-- <span><i class="fa fa-money side-menu__icon mt-1"></i></span> --}}

            <img src="{{ asset('images/studentf.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Student Fine</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i>
        </a>
        <ul class="slide-menu">
            <li><a class="slide-item" href="/students-fine"><span>Apply Student Fine</span></a></li>

            <?php if (auth()->user()->group_id == 644 || auth()->user()->username == 'asif_nadeem') { ?>
            <li><a class="slide-item" href="/student-fine-categories"><span>Add New Category</span></a></li>
            <li><a class="slide-item" href="/student-fine-sub-categories"><span>Add New Fine</span></a></li>
            <li><a class="slide-item" href="/fine-challan-student-list"><span>Student List</span></a></li>
            <?php } ?>

            <?php
						$usernamesFines = ['asif_nadeem', 'esha.abid', 'saqib.jamil', 'ahmad.arfeen', 'amir.sheikh', 'hassan.farooq'];
						$roUsernamesFines = ['zonia.akbar', 'haider.raza', 'tayyaba.nawaz', 'qasim.ali'];
						if (auth()->user()->group_id == 644 || in_array(auth()->user()->username, $usernamesFines) || in_array(auth()->user()->username, $roUsernamesFines)) { ?>
            <li><a class="slide-item" href="/fine-challan-generated-list"><span>Students Fine Details</span></a></li>
            <?php } ?>
        </ul>
    </li>


    <?php if (auth()->user()->username == 'naveed') { ?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            {{-- <span><i class="fa fa-file-text-o side-menu__icon mt-1"></i></span> --}}

            <img src="{{ asset('images/reportsss.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Report Generator</span>
            <i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i>
        </a>
        <ul class="slide-menu">
            <li>
                <a class="slide-item" href="/student-profile-rec"><span>Student Profile Data</span>
                    <!-- Updated label for clarity --></a>
            </li>
        </ul>
    </li>
    <?php } ?>

    <?php if (
							auth()->check() && (auth()->user()->group_id == 644 || auth()->user()->group_id == '223' ||
								in_array(auth()->user()->username, ['asif_nadeem', 'noreen', 'farzana', 'sidra.tanveer', 'naureen.mirza']) ||
								in_array(auth()->user()->username, ['rashid.rana', 'amra.raza', 'bushra.rahman', 'ruhi.khalid', 'shafaat.ahmed']) ||
								in_array(auth()->user()->username, ['sana.aziz', 'ammar.faiz', 'reema.qadeer', 'arshad.khurshid']))
						) { ?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            {{-- <span><i class="fa fa-file-text-o side-menu__icon mt-1"></i></span> --}}

            <img src="{{ asset('images/reportsss.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Report Generator</span>
            <i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i>
        </a>
        <ul class="slide-menu">
            <li>
                <a class="slide-item" href="/reports/bnu-admission-reports-generator">
                    <span>BNU Report Generator</span>
                </a>
            </li>
            <li>
                <a class="slide-item" href="/reports/bnu-admission-reports-generator-detail">
                    <span>Admission Analysis Portal</span> <!-- Updated label for clarity -->
                </a>
            </li>

            <?php if (auth()->user()->group_id == 644 || in_array(auth()->user()->username, ['asif_nadeem', 'noreen', 'farzana'])) { ?>
            <li>
                <a class="slide-item" href="/student-profile-rec"><span>Student Profile Data</span>
                    <!-- Updated label for clarity --></a>
            </li>
            <?php } ?>

        </ul>
    </li>
    <?php } ?>
    <?php if (auth()->user()->group_id == 644) {?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span class="side-menu__label">University Events</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">
            <li><a class="slide-item" href="{{url('UniversityEvent/register')}}"><span>Add University Event</span></a>
            </li>

            <li><a class="slide-item" href="{{url('UniversityEvent/event-approval')}}"><span> University Event
                        List</span></a></li>

        </ul>
    </li>
    <?php } ?>
    <?php if (auth()->user()->username == 'haider.raza' || auth()->user()->username == 'afaq.aliyan' || auth()->user()->username == 'zonia.akbar' || auth()->user()->username == 'muddassar.javed' || auth()->user()->username == 'quratul.ain' || auth()->user()->username == 'abdur.rehman' || auth()->user()->username == 'mahreen.zaib' || auth()->user()->username == 'rabia.shabbir' || auth()->user()->username == 'bilal.saleem' || auth()->user()->username == 'zaeem' || auth()->user()->username == 'umar.imam' || auth()->user()->username == 'sidra.tanveer' || auth()->user()->username == 'sheeraz' || auth()->user()->username == 'hina.razzaq' || auth()->user()->username == 'fariha.nadeem' || auth()->user()->username == 'ali.kashif' || auth()->user()->username == 'saqib.zia' || auth()->user()->username == 'naveed') {?>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span class="side-menu__label">University Events</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">
            <li><a class="slide-item" href="{{url('UniversityEvent/register')}}"><span>Add University Event</span></a>
            </li>
            <?php if (auth()->user()->username == 'haider.raza' || auth()->user()->username == 'bilal.saleem' || auth()->user()->username == 'zonia.akbar') {?>
            <li><a class="slide-item" href="{{url('UniversityEvent/event-approval')}}"><span> University Event
                        List</span></a></li>
            <?php } ?>
        </ul>
    </li>
    <?php if (auth()->user()->username == 'haider.raza' || auth()->user()->username == 'bilal.saleem') {?>
    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('admission_qualification')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Applicant Data Uploads</span>
        </a>
    </li>
    <?php } ?>
    <?php } ?>

    <?php if (auth()->user()->username == 'ijaz.hussain') {?>
    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('admission_qualification')}}">

            <img src="{{ asset('images/homes.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Applicant Documents Status</span>
        </a>
    </li>
    <?php } ?>




    <?php if (auth()->user()->username == 'umar.imam' || auth()->user()->username == 'bilal.saleem') {?>
    <li class="slide">
        <a class="side-menu__item" href="{{url('alumni')}}">
            <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span class="side-menu__label">Alumni</span>
        </a>
    </li>
    <?php } ?>



    <?php if (auth()->user()->username == 'saqib.zia' || auth()->user()->group_id == 644 || auth()->user()->username == 'ali.sher') {?>
    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('lowerstaff_exist_form')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Lower Staff Exist Form</span>
        </a>
    </li>
    <?php } ?>


    <?php
					}

					//vc end else part--------------------------------------
																																																																																																?>
    <?php if (auth()->user()->username != 'moeedyusuf') {?>
    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('staff-exit-interview-form')}}">

            <img src="{{ asset('images/seif.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Staff Exit Interview Form</span>
        </a>
    </li>
    <?php } ?>


    <?php if (auth()->user()->username != 'mansoor.ahmad') {?>
    <li class="slide">
        <a class="side-menu__item " data-toggle="slide" href="#">
            {{-- <span><i class="fa fa-desktop  side-menu__icon mt-1"></i></span> --}}

            <img src="{{ asset('images/homes.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label"> MCM Portal</span><i class="angle fa fa-angle-right" style="
    font-size: 20px;
" style="
    font-size: 20px;
"></i></a>
        <ul class="slide-menu">

            <li><a class="slide-item" href="{{url('admin/eportal-dashboard')}}"><span>MCM Portal Dashboard</span></a>
            </li>
            <?php

						$userEmpType = auth()->user()->sis_emp_type;
						$userGroupId = auth()->user()->group_id;
						$allowedEmpType = [4, 5];
						$allowedGroupId = '644';


						if (in_array($userEmpType, $allowedEmpType) || $userGroupId == $allowedGroupId) {?>
            <li><a class="slide-item" href="{{url('eportal-request')}}"><span>MCM Portal Request</span></a></li>

            <?php  } ?>

        </ul>
    </li>
    <?php if (auth()->user()->username != 'moeedyusuf') {?>
    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('visiting_faculty')}}">

            <img src="{{ asset('images/homes.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Visiting Faculty</span>
        </a>
    </li>
    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('staff-exit-interview-form')}}">

            <img src="{{ asset('images/seif.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Staff Exit Interview Form</span>
        </a>
    </li>
    <?php } ?>

    <?php } ?>
    @endif
    @endif
    {{-- TA IF ENDED --}}
    <?php
$allowedUsers = [
	'ijaz.hussain',
	'bushra.rahman',
	'shafaat.ahmed',
	'tahir.kamran',
	'omer.hassan',
	'rashid.rana',
	'ruhi.khalid',
	'amra.raza',
	'shabana',
	'naureen.mirza'
];

if (auth()->user()->group_id == 644 || in_array(auth()->user()->username, $allowedUsers)) {
    ?>
    <li class="slide">
    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('challan-report')}}">

            <img src="{{ asset('images/challanr.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Challan Report</span>
        </a>
    </li>

    <?php } ?>
    <?php
$allowedUsers = [
	'muddassar.javed'
];

if (in_array(auth()->user()->username, $allowedUsers)) {
    ?>

    <li class="slide">
        <a class="side-menu__item" target="_blank" href="{{url('reports/faculty/list')}}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="side-menu__icon">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            <span class="side-menu__label">Faculty Assurance Report</span>
        </a>
    </li>

    <?php } ?>
    <?php
$allowedUsers = [
	'omar.naeem',
	'fatima.khalid',
	'muhammad.zafran',
	'mohaira.ahmad',
	'sana.iqbal',
	'ali.abbas',
	'naeem.khan',
	'ali.murtaza',
	'ahsan.ahmad',
	'imran.sharif',
	'fareed.qureshi',
	'ijaz.hussain',
	'umar.farooq',
	'anwar.haq',
	'quratul.ain',
	'shazil.ali',
	'rameen.shahram',
	'ali.hafeez',
	'sumaiya.naumaan',
	'muhammad.abubakar',
	'hasnain.ahmad',
	'rabia.malik',
	'nayab.tariq',
	'umar.farooq',
	'amber.razzaq',
	'shazia'

];

if (in_array(auth()->user()->username, $allowedUsers)) {
    ?>

    <li class="slide" style="margin-bottom:100px !important;">
        <a class="side-menu__item" target="_blank" href="{{url('road-map-wise-offering')}}">

            <img src="{{ asset('images/roadmaps.png') }}" class="home-menu-icon" alt="home" width='24' height='22'>
            <span class="side-menu__label">Roadmap Session</span>
        </a>
    </li>

    <?php } ?>
    </ul>
    <div class="app-sidebar-help">
        <div class="dropdown text-center">
            <div class="help d-flex">
                <a href="#" class="nav-link p-0 help-dropdown" data-toggle="dropdown">
                    <span class="font-weight-bold">Help Info</span> <i class="fa fa-angle-down ml-2"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow p-4">
                    <div class="border-bottom pb-3">
                        <h4 class="font-weight-bold">Help</h4>
                        <a class="text-primary d-block" href="#">Contact us</a>
                        <a class="text-primary d-block" href="#">cmshelpdesk@bnu.edu.pk</a>
                        <a class="text-primary d-block" href="#">Tel: 04238100156 Ext:300, 102, 101</a>
                    </div>
                </div>
                <div class="ml-auto">
                    <a class="nav-link icon p-0" href="#">
                        <svg class="header-icon" x="1008" y="1248" viewBox="0 0 24 24" height="100%" width="100%" preserveAspectRatio="xMidYMid meet" focusable="false">
                            <path opacity=".3" d="M12 6.5c-2.49 0-4 2.02-4 4.5v6h8v-6c0-2.48-1.51-4.5-4-4.5z"></path>
                            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-11c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2v-5zm-2 6H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6zM7.58 4.08L6.15 2.65C3.75 4.48 2.17 7.3 2.03 10.5h2a8.445 8.445 0 013.55-6.42zm12.39 6.42h2c-.15-3.2-1.73-6.02-4.12-7.85l-1.42 1.43a8.495 8.495 0 013.54 6.42z">
                            </path>
                        </svg>
                        <span class="pulse "></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</aside>
<script>
    const isMobile = window.matchMedia('(max-width: 768px)').matches;

    const slideForWeb = document.querySelector('.link_for_web');
    const slideForMobile = document.querySelector('.link_for_mobile');

    if (isMobile) {
        slideForWeb.style.display = 'none';
        slideForMobile.style.display = 'block';
    } else {
        slideForWeb.style.display = 'block';
        slideForMobile.style.display = 'none';
    }

</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.app-sidebar.app-sidebar3');

        if (!sidebar) {
            return;
        }

        const normalizePath = function(path) {
            path = path || '/';

            path = path.split('?')[0].split('#')[0];

            if (path.length > 1) {
                path = path.replace(/\/+$/, '');
            }

            return path || '/';
        };

        const currentPath = normalizePath(window.location.pathname);

        const allLinks = sidebar.querySelectorAll(
            '.side-menu__item[href], .slide-item[href]'
        );

        /* Remove active state from every sidebar link */
        allLinks.forEach(function(link) {
            link.classList.remove('active');
            link.removeAttribute('aria-current');
        });

        allLinks.forEach(function(link) {
            const href = link.getAttribute('href');

            /* Ignore dropdown toggles and JavaScript links */
            if (
                !href ||
                href === '#' ||
                href.startsWith('javascript:')
            ) {
                return;
            }

            try {
                const linkUrl = new URL(href, window.location.origin);

                /* Ignore external website links */
                if (linkUrl.origin !== window.location.origin) {
                    return;
                }

                const linkPath = normalizePath(linkUrl.pathname);

                if (linkPath === currentPath) {
                    link.classList.add('active');
                    link.setAttribute('aria-current', 'page');

                    /* Open the parent dropdown when a submenu link is active */
                    if (link.classList.contains('slide-item')) {
                        const parentSlide = link.closest('.slide');

                        if (parentSlide) {
                            parentSlide.classList.add('is-expanded');

                            const parentLink = parentSlide.querySelector(
                                ':scope > .side-menu__item'
                            );

                            const submenu = parentSlide.querySelector(
                                ':scope > .slide-menu'
                            );

                            if (parentLink) {
                                parentLink.setAttribute(
                                    'aria-expanded'
                                    , 'true'
                                );
                            }

                            if (submenu) {
                                submenu.style.display = 'block';
                            }
                        }
                    }
                }
            } catch (error) {
                console.warn('Invalid sidebar link:', href);
            }
        });
    });

</script>
{{-- <script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.querySelector('.app-sidebar.app-sidebar3');
    const icon = document.querySelector('.home-menu-icon');
    const toggleButton = document.querySelector('.app-sidebar__toggle');

    if (!sidebar || !icon) {
        return;
    }

    function updateSidebarIcon() {
        const sidebarWidth = sidebar.getBoundingClientRect().width;
        const isSidebarClosed = sidebarWidth <= 120;

        if (isSidebarClosed) {
            // Sidebar closed
            icon.style.setProperty('padding-right', '0px', 'important');
            icon.style.setProperty('width', '24px', 'important');
            icon.style.setProperty('min-width', '24px', 'important');
            icon.style.setProperty('max-width', '24px', 'important');
        } else {
            // Sidebar open
            // icon.style.setProperty('padding-right', '13px', 'important');
            icon.style.setProperty('width', '24px', 'important');
            icon.style.setProperty('min-width', '24px', 'important');
            icon.style.setProperty('max-width', '24px', 'important');
        }
    }

    updateSidebarIcon();

    if (toggleButton) {
        toggleButton.addEventListener('click', function () {
            setTimeout(updateSidebarIcon, 50);
            setTimeout(updateSidebarIcon, 200);
            setTimeout(updateSidebarIcon, 400);
        });
    }

    const observer = new MutationObserver(function () {
        setTimeout(updateSidebarIcon, 50);
    });

    observer.observe(document.body, {
        attributes: true,
        attributeFilter: ['class', 'style'],
        subtree: true
    });

    window.addEventListener('resize', updateSidebarIcon);
});
</script> --}}


{{-- <script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.querySelector('.app-sidebar.app-sidebar3');
    const icon = document.querySelector('.home-menu-icon');
    const toggleButton = document.querySelector('.app-sidebar__toggle');

    if (!sidebar || !icon) {
        return;
    }

    function updateSidebarIcon() {
        const sidebarWidth = sidebar.getBoundingClientRect().width;
        const isSidebarClosed = sidebarWidth <= 120;

        /* Keep icon size fixed in both states */
        icon.style.setProperty('width', '24px', 'important');
        icon.style.setProperty('height', '22px', 'important');

        icon.style.setProperty('min-width', '24px', 'important');
        icon.style.setProperty('max-width', '24px', 'important');

        icon.style.setProperty('min-height', '22px', 'important');
        icon.style.setProperty('max-height', '22px', 'important');

        icon.style.setProperty('padding', '0px', 'important');
        icon.style.setProperty('flex', '0 0 24px', 'important');

        if (isSidebarClosed) {
            /* Sidebar closed */
            icon.style.setProperty('margin-right', '0px', 'important');
        } else {
            /* Sidebar open */
            icon.style.setProperty('margin-right', '13px', 'important');
        }
    }

    updateSidebarIcon();

    if (toggleButton) {
        toggleButton.addEventListener('click', function () {
            setTimeout(updateSidebarIcon, 50);
            setTimeout(updateSidebarIcon, 200);
            setTimeout(updateSidebarIcon, 400);
        });
    }

    const observer = new MutationObserver(function () {
        setTimeout(updateSidebarIcon, 50);
    });

    observer.observe(document.body, {
        attributes: true,
        attributeFilter: ['class', 'style'],
        subtree: true
    });

    window.addEventListener('resize', updateSidebarIcon);
});
</script> --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.app-sidebar.app-sidebar3');

        if (!sidebar) {
            return;
        }

        function normalizePath(path) {
            path = path || '/';
            path = path.split('?')[0].split('#')[0];

            if (path.length > 1) {
                path = path.replace(/\/+$/, '');
            }

            return path || '/';
        }

        function applyCurrentRoute() {
            const currentPath = normalizePath(window.location.pathname);

            const links = sidebar.querySelectorAll(
                '.side-menu__item[href], .slide-item[href]'
            );

            /* Clear old active/current classes */
            links.forEach(function(link) {
                link.classList.remove('active');
                link.classList.remove('current-route');
                link.removeAttribute('aria-current');
            });

            links.forEach(function(link) {
                const href = link.getAttribute('href');

                if (
                    !href ||
                    href === '#' ||
                    href.startsWith('javascript:')
                ) {
                    return;
                }

                try {
                    const linkUrl = new URL(href, window.location.origin);

                    if (linkUrl.origin !== window.location.origin) {
                        return;
                    }

                    const linkPath = normalizePath(linkUrl.pathname);

                    /* Exact URL match only */
                    if (linkPath !== currentPath) {
                        return;
                    }

                    link.classList.add('current-route');
                    link.setAttribute('aria-current', 'page');

                    /* Expand dropdown without making parent active */
                    if (link.classList.contains('slide-item')) {
                        const parentSlide = link.closest('.slide');

                        if (parentSlide) {
                            parentSlide.classList.add('is-expanded');

                            const parentLink = parentSlide.querySelector(
                                ':scope > .side-menu__item'
                            );

                            const submenu = parentSlide.querySelector(
                                ':scope > .slide-menu'
                            );

                            if (parentLink) {
                                parentLink.classList.remove('active');
                                parentLink.classList.remove('current-route');
                                parentLink.setAttribute(
                                    'aria-expanded'
                                    , 'true'
                                );
                            }

                            if (submenu) {
                                submenu.style.display = 'block';
                            }
                        }
                    }
                } catch (error) {
                    console.warn('Invalid sidebar URL:', href);
                }
            });
        }

        applyCurrentRoute();

        /*
         * Run again after theme/sidebar scripts finish,
         * because the theme may add its own active classes.
         */
        setTimeout(applyCurrentRoute, 200);
        setTimeout(applyCurrentRoute, 500);
    });

</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('.slide-item.active, .slide-item.current-route, .slide-item[aria-current="page"]')
            .forEach(function(activeSubmenu) {

                const parentSlide = activeSubmenu.closest('.slide');

                if (!parentSlide) return;

                const parentLink = parentSlide.querySelector(':scope > .side-menu__item');

                if (parentLink) {
                    parentLink.classList.add('active');
                    parentLink.classList.add('current-route');
                }

                parentSlide.classList.add('is-expanded');

                const submenu = activeSubmenu.closest('.slide-menu');

                if (submenu) {
                    submenu.style.display = 'block';
                }
            });

    });


    document.addEventListener('DOMContentLoaded', function() {
        const app = document.querySelector('.app');

        if (!app) return;

        function updateSidebarState() {
            const isCollapsed = app.classList.contains('sidenav-toggled');

            document.querySelectorAll('.home-menu-icon').forEach(function(icon) {
                icon.style.setProperty(
                    'margin'
                    , isCollapsed ? '0' : '0 13px 0 0'
                    , 'important'
                );
            });

            document.querySelectorAll('.side-menu__icon').forEach(function(icon) {
                icon.style.setProperty(
                    'margin'
                    , isCollapsed ? '0' : '0 13px 0 0'
                    , 'important'
                );
            });

            document.querySelectorAll('.side-menu__label').forEach(function(label) {
                label.style.setProperty(
                    'display'
                    , isCollapsed ? 'none' : 'flex'
                    , 'important'
                );
            });

            document.querySelectorAll('.angle').forEach(function(arrow) {
                arrow.style.setProperty(
                    'display'
                    , isCollapsed ? 'none' : 'inline-flex'
                    , 'important'
                );
            });

            document.querySelectorAll('.side-menu__item').forEach(function(item) {
                item.style.setProperty(
                    'justify-content'
                    , isCollapsed ? 'center' : 'flex-start'
                    , 'important'
                );

                item.style.setProperty(
                    'padding-left'
                    , isCollapsed ? '0' : '13px'
                    , 'important'
                );

                item.style.setProperty(
                    'padding-right'
                    , isCollapsed ? '0' : '13px'
                    , 'important'
                );
            });
        }

        updateSidebarState();

        const observer = new MutationObserver(updateSidebarState);

        observer.observe(app, {
            attributes: true
            , attributeFilter: ['class']
        });
    });

</script>
